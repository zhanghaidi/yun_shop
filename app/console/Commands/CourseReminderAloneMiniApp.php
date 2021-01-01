<?php
namespace app\Console\Commands;

use app\Jobs\SendTemplateMsgJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

// use Illuminate\Support\Facades\App;

class CourseReminderAloneMiniApp extends Command
{
    protected $signature = 'command:coursereminder-aloneminiapp';

    protected $description = '课程提醒命令行工具';

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
        $subscribedUser = DB::table('yz_appletslive_room_subscription')->select('uniacid', 'room_id', 'user_id')
            ->whereIn('room_id', $roomIds)->get()->toArray();
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
        // TODO 为什么不用uid，而用这个unionid，uid不可靠还是不同应用的uid不同？
        $wechatUser = DB::table('mc_mapping_fans')->select('uniacid', 'uid', 'unionid', 'openid')
            ->whereIn('unionid', $subscribedUnionid)
            ->where('follow', 1)->get()->toArray();

        // 4、获取所有的小程序、及其公众号APPID
        $miniAppRs = self::getAloneMiniApp();
        // var_dump($miniAppRs);

        $jobListRs = [];
        foreach ($replayPublishSoon as $v1) {
            // 5.1、获取课程的uniacid
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
            // var_dump($tempRoom);

            // 5.2、获取uniacid的小程序 和 公众号 配置
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
            // var_dump($tempApp);

            foreach ($subscribedUser as $v4) {
                // TODO ims_yz_appletslive_room_comment 和 ims_yz_appletslive_room_subscription 两个表中的 uniacid 字段逻辑全部需要修改

                // if ($tempRoom['uniacid'] != $v4['uniacid']) {
                //     continue;
                // }
                if ($tempRoom['id'] != $v4['room_id']) {
                    continue;
                }

                $tempUser = [
                    'user_id' => $v4['user_id'],
                ];
                foreach ($wxappUser as $v5) {
                    if ($v4['user_id'] != $v5['ajy_uid']) {
                        continue;
                    }
                    // if ($v4['uniacid'] != $tempRoom['uniacid']) {
                    //     continue;
                    // }
                    $tempUser['unionid'] = $v5['unionid'];
                    $tempUser['wxapp_openid'] = $v5['openid'];
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
                    if ($v6['uniacid'] != $tempRoom['uniacid']) {
                        continue;
                    }
                    $tempUser['wechat_openid'] = $v6['openid'];
                    break;
                }

                $type = ($tempUser['wechat_openid'] != '') ? 'wechat' : 'wxapp';
                $jobParam = $this->makeJobParam($type, $tempApp, $tempRoom['name'], $v1);

                // var_dump($tempUser);
                // var_dump($jobParam);

                $jobListRs[] = [
                    'type' => $type,
                    'openid' => ($tempUser['wechat_openid'] != '') ? $tempUser['wechat_openid'] : $tempUser['wxapp_openid'],
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
                $jobRs['openid'], '', $jobRs['page']
            );
            $dispatch = dispatch($job);
            Log::info("模板消息内容:", $jobRs);
            if ($jobRs['type'] == 'wechat') {
                Log::info("队列已添加:发送公众号模板消息", ['job' => $job, 'dispatch' => $dispatch]);
            } elseif ($jobRs['type'] == 'wxapp') {
                Log::info("队列已添加:发送小程序订阅模板消息", ['job' => $job, 'dispatch' => $dispatch]);
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
        $miniAppRs = array();
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

            $miniAppRs[$v['uniacid']][$v['key']] = $v;
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

    private function makeJobParam($type, $appRs, $roomName, $replayRs)
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
            // TODO 模板消息ID
            $param['template_id'] = '';
            $param['notice_data'] = [
                'first' => ['value' => $first, 'color' => '#173177'],
                'keyword1' => ['value' => '【' . $roomName . '】', 'color' => '#173177'],
                'keyword2' => ['value' => '长期有效', 'color' => '#173177'],
                'keyword3' => ['value' => '更新中', 'color' => '#173177'],
                'remark' => ['value' => $remark, 'color' => '#173177'],
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
            // TODO 模板消息ID
            $param['template_id'] = '';
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
