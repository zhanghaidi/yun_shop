<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019/1/16
 * Time: 5:28 PM
 */

namespace Yunshop\Nominate\services;


use app\common\models\Member;
use app\common\models\notice\MessageTemp;
use app\common\facades\Setting;

class MessageService extends \app\common\services\MessageService
{
    public static function awardMessage($uniacid, $uid, $type)
    {
        $set = self::getSet($uniacid);
        $temp_id = $set['nominate_award_message'];
        if (!$temp_id) {
            return;
        }
        $member = Member::select(['uid', 'nickname'])->where('uid', $uid)->first();
        $params = [
            ['name' => '昵称', 'value' => $member->nickname],
            ['name' => '插件名称', 'value' => $set['plugin_name']],
            ['name' => '时间', 'value' => date('Y-m-d H:i:s', time())],
            ['name' => '类型-金额', 'value' => $type],
        ];
        $msg = MessageTemp::getSendMsg($temp_id, $params);
        if (!$msg) {
            return;
        }
        self::notice(MessageTemp::$template_id, $msg, $uid, $uniacid);
    }

    private static function getSet($uniacid)
    {
        \YunShop::app()->uniacid = $uniacid;
        Setting::$uniqueAccountId = $uniacid;
        return Setting::get('plugin.nominate');
    }
}