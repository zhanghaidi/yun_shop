<?php
namespace app\Console\Commands;

use app\Console\Commands\CourseReminderAloneMiniApp;
use app\Jobs\SendTemplateMsgJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SignReminderAloneMiniApp extends Command
{
    protected $signature = 'command:signreminder-aloneminiapp';

    protected $description = '签到提醒命令行工具 - 多个小程序';

    public function handle()
    {
        $nowTime = strtotime(date('Y-m-d', time()));
        // $betweenDaySign = 60;
        $betweenDaySign = 3;
        $startTime = strtotime(date('Y-m-d', strtotime('-' . $betweenDaySign . ' day')));
        $whereBetweenSign = [$startTime, $nowTime];

        // 1、查询所有最近三天有签到过的会员 更新时间在三天之内的会员
        $signUsers = DB::table('yz_sign')->select('id', 'uniacid', 'member_id', 'cumulative_number', 'updated_at')
            ->whereBetween('updated_at', $whereBetweenSign)->get()->toArray();

        $userIds = array_column($signUsers, 'member_id');
        $userIds = array_values(array_unique(array_filter($userIds)));
        if (!isset($userIds[0])) {
            return 0;
        }

        // 2、查询签到用户的小程序用户信息(openid)
        $wxappUser = DB::table('diagnostic_service_user')->select('id', 'ajy_uid', 'uniacid', 'openid', 'unionid', 'nickname')
            ->whereIn('ajy_uid', $userIds)->get()->toArray();
        $subscribedUnionid = array_column($wxappUser, 'unionid');
        if (!isset($subscribedUnionid[0])) {
            return 0;
        }
        $wechatUser = DB::table('mc_mapping_fans')->select('uniacid', 'uid', 'unionid', 'openid', 'nickname')
            ->whereIn('unionid', $subscribedUnionid)
            ->where('follow', 1)->get()->toArray();

        // 3、获取签到配置的消息模板(消息模板)
        $settingRs = DB::table('yz_setting')->select('id', 'uniacid', 'key', 'value')
            ->whereIn('key', ['reminder_wechat', 'reminder_minapp'])
            ->where('group', 'sign')->get()->toArray();
        $wechatMsgTemplateIds = $minappMsgTemplateIds = [];
        foreach ($settingRs as $v) {
            if ($v['value'] <= 0) {
                continue;
            }

            if ($v['key'] == 'reminder_wechat') {
                $wechatMsgTemplateIds[] = $v['value'];
            }
            if ($v['key'] == 'reminder_minapp') {
                $minappMsgTemplateIds[] = $v['value'];
            }
        }
        $wechatMsgTemplate = $minappMsgTemplate = [];
        if (isset($wechatMsgTemplateIds[0])) {
            $wechatMsgTemplate = DB::table('yz_message_template')->whereIn('id', $wechatMsgTemplateIds)->get()->toArray();
        }
        if (isset($minappMsgTemplateIds[0])) {
            $minappMsgTemplate = DB::table('yz_mini_app_template_message')->whereIn('id', $minappMsgTemplateIds)->get()->toArray();
        }

        // 4、获取所有的小程序、及其公众号APPID
        $miniAppRs = CourseReminderAloneMiniApp::getAloneMiniApp();

        foreach ($signUsers as $v1) {
            // 5.1、获取uniacid的小程序 和 公众号 配置
            $tempApp = [];
            foreach ($miniAppRs as $v2) {
                if (!isset($v2['min_app']['uniacid'])) {
                    continue;
                }
                if ($v1['uniacid'] != $v2['min_app']['uniacid']) {
                    continue;
                }
                $tempApp = $v2;
                break;
            }
            if (!isset($tempApp['min_app']['id'])) {
                continue;
            }
            $tempAppIds = array_column($tempApp['account'], 'uniacid');

            // 5.2、获取当前应用的课程开播通知的消息模板
            $tempWechatTemplateId = $tempMinappTemplateId = 0;
            foreach ($settingRs as $v4) {
                if ($tempApp['min_app']['uniacid'] != $v4['uniacid']) {
                    continue;
                }
                if ($v4['key'] == 'reminder_wechat') {
                    $tempWechatTemplateId = $v4['value'];
                }
                if ($v4['key'] == 'reminder_minapp') {
                    $tempMinappTemplateId = $v4['value'];
                }
            }
            $tempWechatMsgTemplate = $tempMinappMsgTemplate = [];
            if ($tempWechatTemplateId > 0) {
                foreach ($wechatMsgTemplate as $v41) {
                    if ($tempWechatTemplateId != $v41['id']) {
                        continue;
                    }
                    $tempWechatMsgTemplate = $v41;
                    break;
                }
            }
            if ($tempMinappTemplateId > 0) {
                foreach ($minappMsgTemplate as $v42) {
                    if ($tempMinappTemplateId != $v42['id']) {
                        continue;
                    }
                    $tempMinappMsgTemplate = $v42;
                    break;
                }
            }

            // 5.3、获取当前用户的openid
            $tempUser = [
                'user_id' => $v1['member_id'],
            ];
            foreach ($wxappUser as $v5) {
                if ($v1['member_id'] != $v5['ajy_uid']) {
                    continue;
                }
                if (!in_array($v5['uniacid'], $tempAppIds)) {
                    continue;
                }
                $tempUser['unionid'] = $v5['unionid'];
                $tempUser['wxapp_openid'] = $v5['openid'];
                $tempUser['wxapp_nickname'] = $v5['nickname'];
                break;
            }
            if (!isset($tempUser['unionid']) || $tempUser['unionid'] == '') {
                continue;
            }
            $tempUser['wechat_openid'] = '';
            foreach ($wechatUser as $v6) {
                if ($tempUser['unionid'] != $v6['unionid']) {
                    continue;
                }
                if (!in_array($v6['uniacid'], $tempAppIds)) {
                    continue;
                }
                $tempUser['wechat_openid'] = $v6['openid'];
                $tempUser['wechat_nickname'] = $v6['nickname'];
                break;
            }

            $type = ($tempUser['wechat_openid'] != '') ? 'wechat' : 'wxapp';
            $tempOpenid = $tempNickname = $tempTemplateId = '';
            if ($type == 'wechat') {
                $tempOpenid = $tempUser['wechat_openid'];
                $tempNickname = $tempUser['wechat_nickname'];
                if (!isset($tempWechatMsgTemplate['id'])) {
                    continue;
                }
                $tempTemplateId = $tempWechatMsgTemplate['template_id'];
            } else {
                $tempOpenid = $tempUser['wxapp_openid'];
                $tempNickname = $tempUser['wxapp_nickname'];
                if (!isset($tempMinappMsgTemplate['id'])) {
                    continue;
                }
                $tempTemplateId = $tempMinappMsgTemplate['template_id'];
            }
            if ($tempTemplateId == '') {
                continue;
            }

            $jobParam = $this->makeJobParam($type, $tempApp, $tempNickname, $tempTemplateId);

            $job = new SendTemplateMsgJob(
                $type, $jobParam['options'],
                $jobParam['template_id'], $jobParam['notice_data'],
                $tempOpenid, '', $jobParam['page'],
                isset($jobParam['miniprogram']) ? $jobParam['miniprogram'] : []
            );
            $dispatch = dispatch($job);
            if ($type == 'wechat') {
                Log::info("签到提醒消息队列已添加:发送公众号模板消息", ['source' => $jobParam, 'job' => $job, 'dispatch' => $dispatch]);
            } elseif ($type == 'wxapp') {
                Log::info("签到提醒消息队列已添加:发送小程序订阅模板消息", ['source' => $jobParam, 'job' => $job, 'dispatch' => $dispatch]);
            }
        }
    }

    private function makeJobParam($type, $appRs, $nickname, $templateId)
    {
        if (!in_array($type, ['wechat', 'wxapp'])) {
            return [];
        }

        // 签到小程序跳转地址地址
        $signPath = 'pages/template/rumours/index?_source=share&_s_path=/pages/rumours/signin/index';

        $param = [
            'page' => $signPath,
        ];
        if ($type == 'wechat') {
            if (!isset($appRs['wechat']['value']['app_id'])) {
                return [];
            }
            $param['options'] = [
                'app_id' => $appRs['wechat']['value']['app_id'],
                'secret' => $appRs['wechat']['value']['app_secret'],
            ];

            $first = $nickname . '您好,您签到领取的健康金到账啦，今天的健康金还没领取哦，赶快来签到~';
            $remark = '坚持签到即可奖励健康金，更多惊喜等着你~';
            $param['notice_data'] = [
                'first' => ['value' => $first, 'color' => '#173177'],
                'keyword1' => ['value' => '领取健康金资格审核通过，点击领取守护家人健康~', 'color' => '#173177'],
                'keyword2' => ['value' => date('Y-m-d H:i', time()), 'color' => '#173177'],
                'remark' => ['value' => $remark, 'color' => '#173177'],
            ];
            $param['miniprogram'] = [
                'miniprogram' => [
                    'appid' => $appRs['min_app']['value']['key'],
                ],
            ];
        } else {
            if (!isset($appRs['min_app']['value']['key'])) {
                return [];
            }
            $param['options'] = [
                'app_id' => $appRs['min_app']['value']['key'],
                'secret' => $appRs['min_app']['value']['secret'],
            ];

            $thing1 = '签到领取健康金';
            $thing2 = '点击领取守护家人健康';
            $param['notice_data'] = [
                'thing1' => ['value' => $thing1, 'color' => '#173177'],
                'thing2' => ['value' => $thing2, 'color' => '#173177'],
                'thing8' => ['value' => '坚持签到,有更多惊喜', 'color' => '#173177'],
            ];
        }
        $param['template_id'] = $templateId;
        return $param;
    }
}
