<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/10/28
 * Time: 4:29 PM
 */

namespace Yunshop\Mryt\services;


use app\common\facades\Setting;
use app\common\models\Member;
use app\common\models\notice\MessageTemp;

class MessageService extends \app\common\services\MessageService
{
    public static function upgrateMessage($uniacid, $data, $uid)
    {
        $set = self::getSet($uniacid);
        $temp_id = $set['mryt_upgrate_message'];
        if (!$temp_id) {
            return;
        }
        $params = [
            ['name' => '会员昵称', 'value' => $data['member']['nickname']],
            ['name' => '插件名称', 'value' => $set['name']],
            ['name' => '时间', 'value' => date('Y-m-d H:i:s', time())],
            ['name' => '旧等级', 'value' => $data['oldLevel']['level_name']],
            ['name' => '新等级', 'value' => $data['newLevel']['level_name']],
            ['name' => '旧等级提成比例', 'value' => $data['oldLevel']['team_manage_ratio']],
            ['name' => '新等级提成比例', 'value' => $data['newLevel']['team_manage_ratio']],
            ['name' => '旧等级直推奖', 'value' => $data['oldLevel']['direct']],
            ['name' => '新等级直推奖', 'value' => $data['newLevel']['direct']],
            ['name' => '旧等级团队奖', 'value' => $data['oldLevel']['team']],
            ['name' => '新等级团队奖', 'value' => $data['newLevel']['team']],
            ['name' => '旧等级感恩奖', 'value' => $data['oldLevel']['thankful']],
            ['name' => '新等级感恩奖', 'value' => $data['newLevel']['thankful']],
            ['name' => '旧等级育人奖比例', 'value' => $data['oldLevel']['train_ratio']],
            ['name' => '新等级育人奖比例', 'value' => $data['newLevel']['train_ratio']],
            ['name' => '旧等级平级奖', 'value' => $data['oldLevel']['tier_amount']],
            ['name' => '新等级平级奖', 'value' => $data['newLevel']['tier_amount']]
        ];
        $msg = MessageTemp::getSendMsg($temp_id, $params);
        if (!$msg) {
            return;
        }
        self::notice(MessageTemp::$template_id, $msg, $uid, $uniacid);
    }

    public static function awardMessage($uniacid, $uid, $type)
    {
        $set = self::getSet($uniacid);
        $temp_id = $set['mryt_award_message'];
        if (!$temp_id) {
            return;
        }
        $member = Member::select(['uid', 'nickname'])->where('uid', $uid)->first();
        $params = [
            ['name' => '昵称', 'value' => $member->nickname],
            ['name' => '插件名称', 'value' => $set['name']],
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
        return Setting::get('plugin.mryt_set');
    }
}