<?php
namespace app\Console\Commands;

use app\Console\Commands\CourseReminderAloneMiniApp;
// use app\Jobs\SendTemplateMsgJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

// use Illuminate\Support\Facades\Log;

class SignReminderAloneMiniApp extends Command
{
    protected $signature = 'command:signreminder-aloneminiapp';

    protected $description = '签到提醒命令行工具 - 多个小程序';

    public function handle()
    {
        $nowTime = strtotime(date('Y-m-d', time()));
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

        // 3、获取签到配置的消息模板
        $messageTemplateRs = DB::table('yz_message_template')
            ->where('notice_type', 'order_not_paid')->get()->toArray();

        // 4、获取所有的小程序、及其公众号APPID
        $miniAppRs = CourseReminderAloneMiniApp::getAloneMiniApp();
    }
}
