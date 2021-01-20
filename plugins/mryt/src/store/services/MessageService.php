<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/9/13
 * Time: 下午7:14
 */

namespace Yunshop\Mryt\store\services;



use app\backend\modules\member\models\Member;
use app\common\models\notice\MessageTemp;

class MessageService extends \app\common\services\MessageService
{
    public static function becomeStore($uid, $uniacid)
    {
        $set = \Setting::get('plugin.store');
        $noticeMember = self::getMember($uid);
        if (!$noticeMember->hasOneFans->openid) {
            return;
        }
        if (!$set['become_store']) {
            return;
        }
        $params = [
            ['name' => '昵称', 'value' => $noticeMember->hasOneFans->nickname],
            ['name' => '时间', 'value' => date('Y-m-d H:i:s', time())]
        ];
        $msg = MessageTemp::getSendMsg($set['become_store'], $params);
        if (!$msg) {
            return;
        }
        self::notice(MessageTemp::$template_id, $msg, $uid, $uniacid);
    }

    public static function rejectStore($uid, $uniacid)
    {
        $set = \Setting::get('plugin.store');
        $noticeMember = self::getMember($uid);
        if (!$noticeMember->hasOneFans->openid) {
            return;
        }
        if (!$set['reject_store']) {
            return;
        }
        $params = [
            ['name' => '昵称', 'value' => $noticeMember->hasOneFans->nickname],
            ['name' => '时间', 'value' => date('Y-m-d H:i:s', time())]
        ];
        $msg = MessageTemp::getSendMsg($set['reject_store'], $params);
        if (!$msg) {
            return;
        }
        self::notice(MessageTemp::$template_id, $msg, $uid, $uniacid);
    }

    private static function getMember($uid)
    {
        return Member::getMemberByUid($uid)->with('hasOneFans')->first();
    }

    public static function enterNotice($store, $validityAt)
    {
        $set = \Setting::get('plugin.store');
        $noticeMember = self::getMember($store->uid);
        if (!$noticeMember->hasOneFans->openid) {
            return;
        }
        if (!$set['enter_notice']) {
            return;
        }
        //$validityAt = date('Y-m-d H:i:s', strtotime('+'.$store->validity.'year', strtotime($store->created_at->toDateTimeString())));
        $params = [
            ['name' => '昵称', 'value' => $noticeMember->hasOneFans->nickname],
            ['name' => '时间', 'value' => date('Y-m-d H:i:s', time())],
            ['name' => '门店名称', 'value' => $store->store_name],
            ['name' => '到期时间', 'value' => date('Y-m-d H:i:s', $validityAt)]
        ];

        $msg = MessageTemp::getSendMsg($set['enter_notice'], $params);
        if (!$msg) {
            return;
        }
        self::notice(MessageTemp::$template_id, $msg, $store->uid, $store->uniacid);
    }
}