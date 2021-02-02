<?php
namespace app\Console\Commands;

use app\Jobs\SendTemplateMsgJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CourseReminderAloneMiniApp extends Command
{
    protected $signature = 'command:coursereminder-aloneminiapp';

    protected $description = '课程提醒命令行工具 - 多个小程序';

    public function handle()
    {
        $nowTime = time();
        $waitSeconds = 60 * 1;
        // $checkTimeRange = [$nowTime - 86400 * 60, $nowTime - $waitSeconds];
        $checkTimeRange = [$nowTime - $waitSeconds - 60, $nowTime - $waitSeconds];

        // 1、查询距离当前时间点n~n+1分钟之间即将发布的视频
        $replayPublishSoon = DB::table('yz_appletslive_replay')
            ->select('id', 'rid', 'title', 'doctor', 'publish_time')
            ->whereBetween('publish_time', $checkTimeRange)
            ->where('delete_time', 0)->get()->toArray();
        if (!isset($replayPublishSoon[0])) {
            return 0;
        }
        $roomIds = array_column($replayPublishSoon, 'rid');
        $roomIds = array_values(array_unique(array_filter($roomIds)));
        if (!isset($roomIds[0])) {
            return 0;
        }

        // 2、查询即将发布的视频关联的课程
        $relaRoom = DB::table('yz_appletslive_room')->select('id', 'uniacid', 'name')
            ->whereIn('id', $roomIds)
            ->where('delete_time', 0)->get()->toArray();
        $roomIds = array_column($relaRoom, 'id');
        if (!isset($roomIds[0])) {
            return 0;
        }

        // 3、查询关注了这些课程的所有小程序用户
        $subscribedUser = DB::table('yz_appletslive_room_subscription')->select('id', 'uniacid', 'room_id', 'user_id')
            ->whereIn('room_id', $roomIds)
            ->where('status', 1)->get()->toArray();
        if (!isset($subscribedUser[0])) {
            return 0;
        }
        $userIds = array_column($subscribedUser, 'user_id');
        $userIds = array_values(array_unique(array_filter($userIds)));
        if (!isset($userIds[0])) {
            return 0;
        }

        // 3.1、存在已关注课程的用户，查询用户openid
        $wxappUser = DB::table('diagnostic_service_user')->select('id', 'ajy_uid', 'uniacid', 'openid', 'unionid')
            ->whereIn('ajy_uid', $userIds)->get()->toArray();
        $subscribedUnionid = array_column($wxappUser, 'unionid');
        if (!isset($subscribedUnionid[0])) {
            return 0;
        }
        $wechatUser = DB::table('mc_mapping_fans')->select('uniacid', 'uid', 'unionid', 'openid')
            ->whereIn('unionid', $subscribedUnionid)
            ->where('follow', 1)->get()->toArray();

        // 4、获取所有的小程序、及其公众号APPID
        $miniAppRs = self::getAloneMiniApp();

        // 5、获取小程序直播的配置项信息(消息模板)
        $settingRs = DB::table('yz_setting')->select('id', 'uniacid', 'value')->where([
            'group' => 'plugin',
            'key' => 'appletslive',
        ])->get()->toArray();
        $wechatMsgTemplateIds = $minappMsgTemplateIds = [];
        foreach ($settingRs as $k => $v) {
            $v['value'] = unserialize($v['value']);
            if ($v['value'] == false) {
                continue;
            }
            if (isset($v['value']['wechat_template']) && $v['value']['wechat_template'] > 0) {
                $wechatMsgTemplateIds[] = $v['value']['wechat_template'];
            }
            if (isset($v['value']['minapp_template']) && $v['value']['minapp_template'] > 0) {
                $minappMsgTemplateIds[] = $v['value']['minapp_template'];
            }
            $settingRs[$k]['value'] = $v['value'];
        }
        $wechatMsgTemplate = $minappMsgTemplate = [];
        if (isset($wechatMsgTemplateIds[0])) {
            $wechatMsgTemplate = DB::table('yz_message_template')->whereIn('id', $wechatMsgTemplateIds)->get()->toArray();
        }
        if (isset($minappMsgTemplateIds[0])) {
            $minappMsgTemplate = DB::table('yz_mini_app_template_message')->whereIn('id', $minappMsgTemplateIds)->get()->toArray();
        }

        $jobListRs = [];
        foreach ($replayPublishSoon as $v1) {
            // 6.1、获取课程的uniacid
            $tempRoom = [];
            foreach ($relaRoom as $v2) {
                if ($v1['rid'] != $v2['id']) {
                    continue;
                }
                $tempRoom = $v2;
                break;
            }
            if (!isset($tempRoom['id'])) {
                continue;
            }

            // 6.2、获取uniacid的小程序 和 公众号 配置
            $tempApp = [];
            foreach ($miniAppRs as $v3) {
                if (!isset($v3['min_app']['uniacid'])) {
                    continue;
                }
                if ($tempRoom['uniacid'] != $v3['min_app']['uniacid']) {
                    continue;
                }
                $tempApp = $v3;
                break;
            }
            if (!isset($tempApp['min_app']['id'])) {
                continue;
            }
            $tempAppIds = array_column($tempApp['account'], 'uniacid');

            // 6.3、获取当前应用的课程开播通知的消息模板
            $tempWechatTemplateId = $tempMinappTemplateId = 0;
            foreach ($settingRs as $v4) {
                if ($tempApp['min_app']['uniacid'] != $v4['uniacid']) {
                    continue;
                }
                if (isset($v4['value']['wechat_template'])) {
                    $tempWechatTemplateId = $v4['value']['wechat_template'];
                }
                if (isset($v4['value']['minapp_template'])) {
                    $tempMinappTemplateId = $v4['value']['minapp_template'];
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

            foreach ($subscribedUser as $v5) {
                // TODO ims_yz_appletslive_room_comment 和 ims_yz_appletslive_room_subscription 两个表中的 uniacid 字段逻辑全部需要修改

                // if ($tempRoom['uniacid'] != $v5['uniacid']) {
                //     continue;
                // }
                if ($tempRoom['id'] != $v5['room_id']) {
                    continue;
                }

                $tempUser = [
                    'user_id' => $v5['user_id'],
                ];
                foreach ($wxappUser as $v6) {
                    if ($v5['user_id'] != $v6['ajy_uid']) {
                        continue;
                    }
                    if (!in_array($v6['uniacid'], $tempAppIds)) {
                        continue;
                    }
                    $tempUser['unionid'] = $v6['unionid'];
                    $tempUser['wxapp_openid'] = $v6['openid'];
                    break;
                }
                if (!isset($tempUser['unionid']) || $tempUser['unionid'] == '') {
                    continue;
                }
                $tempUser['wechat_openid'] = '';
                foreach ($wechatUser as $v7) {
                    if ($tempUser['unionid'] != $v7['unionid']) {
                        continue;
                    }
                    if (!in_array($v7['uniacid'], $tempAppIds)) {
                        continue;
                    }
                    $tempUser['wechat_openid'] = $v7['openid'];
                    break;
                }

                $type = ($tempUser['wechat_openid'] != '') ? 'wechat' : 'wxapp';
                // TODO 暂时仅使用模板的模板ID，其内消息定义暂不使用
                $tempTemplateId = '';
                if ($type == 'wechat') {
                    if (!isset($tempWechatMsgTemplate['id'])) {
                        continue;
                    }
                    $tempTemplateId = $tempWechatMsgTemplate['template_id'];
                } else {
                    if (!isset($tempMinappMsgTemplate['id'])) {
                        continue;
                    }
                    $tempTemplateId = $tempMinappMsgTemplate['template_id'];
                }
                if ($tempTemplateId == '') {
                    continue;
                }
                $jobParam = $this->makeJobParam($type, $tempApp, $tempTemplateId, $tempRoom['name'], $v1);

                $jobListRs[] = [
                    'type' => $type,
                    'openid' => ($type == 'wechat') ? $tempUser['wechat_openid'] : $tempUser['wxapp_openid'],
                    'options' => $jobParam['options'],
                    'template_id' => $jobParam['template_id'],
                    'notice_data' => $jobParam['notice_data'],
                    'page' => $jobParam['page'],
                ];
            }
        }

        foreach ($jobListRs as $jobRs) {
            $job = new SendTemplateMsgJob(
                $jobRs['type'], $jobRs['options'],
                $jobRs['template_id'], $jobRs['notice_data'],
                $jobRs['openid'], '', $jobRs['page'],
                isset($jobParam['miniprogram']) ? $jobParam['miniprogram'] : []
            );
            $dispatch = dispatch($job);
            if ($jobRs['type'] == 'wechat') {
                Log::info("课程开播提醒消息队列已添加:发送公众号模板消息", ['source' => $jobRs, 'job' => $job, 'dispatch' => $dispatch]);
            } elseif ($jobRs['type'] == 'wxapp') {
                Log::info("课程开播提醒消息队列已添加:发送小程序订阅模板消息", ['source' => $jobRs, 'job' => $job, 'dispatch' => $dispatch]);
            }
        }
    }

    /**
     * 获取独立的小程序，其上配置的小程序APPID和公众号APPID
     */
    public static function getAloneMiniApp()
    {
        $listRs = DB::table('yz_setting')->select('id', 'uniacid', 'key', 'value')
            ->whereIn('key', ['min_app', 'wechat'])
            ->where([
                'group' => 'plugin',
                'type' => 'array',
            ])->orderBy('id', 'asc')->get()->toArray();
        $miniAppRs = [];
        $openidIds = [];
        foreach ($listRs as $v) {
            if (!isset($v['id'])) {
                continue;
            }
            !isset($miniAppRs[$v['uniacid']]) && $miniAppRs[$v['uniacid']] = [];

            $v['value'] = unserialize($v['value']);
            if ($v['value'] == false) {
                continue;
            }
            $v['value'] = array_filter($v['value']);
            if (isset($v['value']['key'])) {
                $openidIds[] = $v['value']['key'];
            }
            if (isset($v['value']['shop_key'])) {
                $openidIds[] = $v['value']['shop_key'];
            }
            if (isset($v['value']['app_id'])) {
                $openidIds[] = $v['value']['app_id'];
            }

            $miniAppRs[$v['uniacid']][$v['key']] = $v;
        }

        if (isset($openidIds[0])) {
            $wxappRs = DB::table('account_wxapp')->select('uniacid', 'key')
                ->whereIn('key', $openidIds)->get()->toArray();
            $wechatRs = DB::table('account_wechats')->select('uniacid', 'key')
                ->whereIn('key', $openidIds)->get()->toArray();

            foreach ($miniAppRs as $k1 => $v1) {
                $miniAppRs[$k1]['account'] = [];

                foreach ($wxappRs as $v2) {
                    $item = [
                        'key' => $v2['key'],
                        'uniacid' => $v2['uniacid'],
                    ];
                    if ($v1['min_app']['value']['key'] != '' && $v1['min_app']['value']['key'] == $v2['key']) {
                        $item['type'] = 'wxapp,main';
                    } elseif ($v1['min_app']['value']['shop_key'] != '' && $v1['min_app']['value']['shop_key'] == $v2['key']) {
                        $item['type'] = 'wxapp,shop';
                    }
                    if (!isset($item['type'])) {
                        continue;
                    }

                    if (!isset($v1['min_app']['value']['shop_key']) || $v1['min_app']['value']['shop_key'] == '') {
                        $item['type'] = 'wxapp';
                    }

                    $miniAppRs[$k1]['account'][] = $item;
                }

                foreach ($wechatRs as $v3) {
                    if ($v1['wechat']['value']['app_id'] != '' && $v1['wechat']['value']['app_id'] == $v3['key']) {
                        $miniAppRs[$k1]['account'][] = [
                            'key' => $v3['key'],
                            'uniacid' => $v3['uniacid'],
                            'type' => 'wechat',
                        ];
                    }
                }
            }
        }

        foreach ($miniAppRs as $k => $v) {
            if (!isset($v['min_app']) || !isset($v['wechat'])) {
                unset($miniAppRs[$k]);
            }
            if (!isset($v['min_app']['value']['key']) ||
                !isset($v['min_app']['value']['secret'])
            ) {
                unset($miniAppRs[$k]);
            }
            if (!isset($v['wechat']['value']['app_id']) ||
                !isset($v['wechat']['value']['app_secret'])
            ) {
                unset($miniAppRs[$k]);
            }

        }
        return $miniAppRs;
    }

    private function makeJobParam($type, $appRs, $templateId, $roomName, $replayRs)
    {
        if (!in_array($type, ['wechat', 'wxapp'])) {
            return [];
        }

        // 课程详情
        $coursePath = '/pages/course/CouRse/index';

        $jumpPage = '/pages/template/rumours/index?share=1&shareUrl=';
        $jumpTail = $coursePath . '?tid=' . $replayRs['rid'];

        $param = [
            'page' => $jumpPage . urlencode($jumpTail),
        ];
        if ($type == 'wechat') {
            if (!isset($appRs['wechat']['value']['app_id'])) {
                return [];
            }
            $param['options'] = [
                'app_id' => $appRs['wechat']['value']['app_id'],
                'secret' => $appRs['wechat']['value']['app_secret'],
            ];

            $first = '尊敬的用户,您订阅的课程有新视频要发布啦~';
            $remark = '最新视频【' . $replayRs['title'] . '】将于' . date('Y-m-d H:i', $replayRs['publish_time']) . '倾情发布!';
            $param['template_id'] = $templateId;
            $param['notice_data'] = [
                'first' => ['value' => $first, 'color' => '#173177'],
                'keyword1' => ['value' => '【' . $roomName . '】', 'color' => '#173177'],
                'keyword2' => ['value' => '长期有效', 'color' => '#173177'],
                'keyword3' => ['value' => '更新中', 'color' => '#173177'],
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

            $thing1 = '课程更新';
            $thing2 = mb_substr($replayRs['title'], 0, 5, 'utf-8');
            $param['template_id'] = $templateId;
            $param['notice_data'] = [
                'thing1' => ['value' => $thing1, 'color' => '#173177'],
                'thing2' => ['value' => '【' . $thing2 . '】', 'color' => '#173177'],
                'name3' => ['value' => $replayRs['doctor'], 'color' => '#173177'],
                'thing4' => ['value' => date('Y-m-d H:i', $replayRs['publish_time']), 'color' => '#173177'],
            ];
        }
        return $param;
    }
}
