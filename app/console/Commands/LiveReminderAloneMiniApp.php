<?php
namespace app\Console\Commands;

use app\common\models\live\CloudLiveRoom;
use app\Console\Commands\CourseReminderAloneMiniApp;
use app\Jobs\SendTemplateMsgJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LiveReminderAloneMiniApp extends Command
{
    protected $signature = 'command:livereminder-aloneminiapp';

    protected $description = '云直播开播提醒命令行工具 - 多个小程序';

    public function handle()
    {
        $nowTime = time();
        $waitSeconds = 60 * 1;
        $checkTimeRange = [$nowTime - 86400 * 60, $nowTime - $waitSeconds];
        // $checkTimeRange = [$nowTime - $waitSeconds - 60, $nowTime - $waitSeconds];

        // 1、查询开始时间距离当前时间2分钟之内开播的直播 where('live_status', 101)暂时不卡播放状态
        $startLiveRoom = CloudLiveRoom::select('id', 'uniacid', 'name', 'live_status', 'start_time', 'anchor_name')
            ->whereBetween('start_time', $checkTimeRange)->get()->toArray();
        $roomIds = array_column($startLiveRoom, 'id');
        if (!isset($roomIds[0])) {
            return 0;
        }

        // 2、查询订阅开播直播间的用户
        $userRs = DB::table('yz_cloud_live_room_subscription')->select('id', 'room_id', 'user_id')
            ->whereIn('room_id', $roomIds)
            ->where('status', 1)->get()->toArray();
        $userIds = array_column($userRs, 'user_id');
        $userIds = array_values(array_unique(array_filter($userIds)));
        if (!isset($userIds[0])) {
            return 0;
        }

        // 3、查询用户openid
        $wxappUser = DB::table('diagnostic_service_user')
            ->select('id', 'ajy_uid', 'uniacid', 'openid', 'shop_openid', 'unionid')
            ->whereIn('ajy_uid', $userIds)->get()->toArray();
        $wechatUser = DB::table('mc_mapping_fans')->select('uniacid', 'uid', 'unionid', 'openid')
            ->whereIn('uid', $userIds)
            ->where('follow', 1)->get()->toArray();

        // 4、获取所有的小程序、及其公众号APPID
        $miniAppRs = CourseReminderAloneMiniApp::getAloneMiniApp();

        foreach ($userRs as $v1) {
            // 5.1 获取直播间信息 和 uniacid
            $tempRoom = [];
            foreach ($startLiveRoom as $v2) {
                if ($v1['room_id'] != $v2['id']) {
                    continue;
                }
                $tempRoom = $v2;
                break;
            }
            if (!isset($tempRoom['id'])) {
                continue;
            }

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

            $tempWxapp = [];
            foreach ($wxappUser as $v4) {
                if ($v1['user_id'] != $v4['ajy_uid']) {
                    continue;
                }
                $tempWxapp = $v4;
                break;
            }
            $tempWechat = [];
            foreach ($wechatUser as $v5) {
                if ($v1['user_id'] != $v5['uid']) {
                    continue;
                }
                $tempWechat = $v5;
                break;
            }

            $tempOpenid = '';
            if (isset($tempWechat['uid'])) {
                $tempOpenid = $tempWechat['openid'];
                $type = 'wechat';
            } else {
                if (isset($tempWxapp['shop_openid']) && $tempWxapp['shop_openid'] != '') {
                    $tempOpenid = $tempWxapp['shop_openid'];
                } else {
                    $tempOpenid = $tempWxapp['openid'];
                }
                $type = 'wxapp';
            }

            $jobParam = $this->makeJobParam($type, $tempApp, $tempRoom);

            Log::info('模板消息内容:' . $type . $tempOpenid,
                ['wxapp' => $tempWxapp, 'wechat' => $tempWechat, 'template' => $jobParam]
            );

            $job = new SendTemplateMsgJob(
                $type, $jobParam['options'],
                $jobParam['template_id'], $jobParam['notice_data'],
                $tempOpenid, '', $jobParam['page'],
                isset($jobParam['miniprogram']) ? $jobParam['miniprogram'] : []
            );
            $dispatch = dispatch($job);
            Log::info('队列已添加:' . $type, ['job' => $job, 'dispatch' => $dispatch]);
        }
    }

    private function makeJobParam($type, $appRs, $roomRs)
    {
        if (!in_array($type, ['wechat', 'wxapp'])) {
            return [];
        }

        // 云直播间
        $livePath = '/pages/cloud-live/live-player/live-player?tid=';
        // 直播间路径
        $jumpTail = $livePath . $roomRs['id'];

        $param = [
            'page' => $jumpTail,
        ];
        if ($type == 'wechat') {
            if (!isset($appRs['wechat']['value']['app_id'])) {
                return [];
            }
            $param['options'] = [
                'app_id' => $appRs['wechat']['value']['app_id'],
                'secret' => $appRs['wechat']['value']['app_secret'],
            ];

            $first = '尊敬的用户,您订阅的直播间开始直播啦~';
            $remark = '【' . $roomRs['name'] . '】正在进行中,观看直播互动享更多福利优惠~';
            // TODO 模板消息ID
            $param['template_id'] = '';
            $param['notice_data'] = [
                'first' => ['value' => $first, 'color' => '#173177'],
                'keyword1' => ['value' => '【' . $roomRs['name'] . '】', 'color' => '#173177'],
                'keyword2' => ['value' => '长期有效', 'color' => '#173177'],
                'keyword3' => ['value' => '更新中', 'color' => '#173177'],
                'remark' => ['value' => $remark, 'color' => '#173177'],
            ];
            $param['miniprogram'] = [
                'miniprogram' => [
                    'appid' => $appRs['min_app']['value']['key'],
                    'pagepath' => $param['page'],
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

            $thing1 = '直播间开播提醒';
            // TODO 模板消息ID
            $param['template_id'] = '';
            $param['notice_data'] = [
                'thing1' => ['value' => $thing1, 'color' => '#173177'],
                'thing2' => ['value' => '【' . $roomRs['name'] . '】', 'color' => '#173177'],
                'name3' => ['value' => $roomRs['anchor_name'], 'color' => '#173177'],
                'thing4' => ['value' => date('Y-m-d H:i', $roomRs['start_time']), 'color' => '#173177'],
            ];
        }
        return $param;
    }
}
