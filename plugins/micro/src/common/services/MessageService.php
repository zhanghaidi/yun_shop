<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/20
 * Time: 下午5:28
 */

namespace Yunshop\Micro\common\services;

use app\backend\modules\member\models\Member;
use app\common\models\notice\MessageTemp;
use Setting;
use Yunshop\Micro\common\models\MicroShop;

class MessageService extends \app\common\services\MessageService
{
    /**
     * @name 成为微店通知
     * @author
     * @param $member_id
     */
    public static function becomeMicro($member_id)
    {
        $micro_model = MicroShop::getMicroShopByMemberId($member_id);
        $temp_id = Setting::get('plugin.micro')['micro_become_micro'];
        if (!$temp_id) {
            return;
        }
        $params = [
            ['name' => '商城名称', 'value' => \Setting::get('shop.shop')['name']],
            ['name' => '昵称', 'value' => $micro_model->hasOneMember->nickname],
            ['name' => '时间', 'value' => date('Y-m-d H:i:s', time())],
            ['name' => '店主等级', 'value' => $micro_model->hasOneMicroShopLevel->level_name],
            ['name' => '分红比例', 'value' => $micro_model->hasOneMicroShopLevel->bonus_ratio],
        ];
        $msg = MessageTemp::getSendMsg($temp_id, $params);
        if (!$msg) {
            return;
        }
        self::notice(MessageTemp::$template_id, $msg, $member_id, $micro_model->uniacid);
    }

    /**
     * @name 微店升级通知
     * @author
     * @param $micro_model
     */
    public static function upgradeMicro($micro_model)
    {
        if (!\YunShop::notice()->getNotSend('micro.upgrade_micro_title')) {
            return;
        }
        $new_micro = MicroShop::getMicroShopByMemberId($micro_model->member_id);
        $temp_id = Setting::get('plugin.micro')['micro_micro_upgrade'];
        if (!$temp_id) {
            return;
        }
        $params = [
            ['name' => '昵称', 'value' => $micro_model->hasOneMember->nickname],
            ['name' => '时间', 'value' => date('Y-m-d H:i:s', time())],
            ['name' => '店主等级', 'value' => $new_micro->hasOneMicroShopLevel->level_name],
            ['name' => '分红比例', 'value' => $new_micro->hasOneMicroShopLevel->bonus_ratio]
        ];
        $msg = MessageTemp::getSendMsg($temp_id, $params);
        if (!$msg) {
            return;
        }
        self::notice(MessageTemp::$template_id, $msg, $micro_model->member_id, $micro_model->uniacid);
    }

    /**
     * @name 分红订单通知
     * @author
     * @param $log_model
     */
    public static function bonusOrder($log_model)
    {
        if (!\YunShop::notice()->getNotSend('micro.bonus_order_title')) {
            return;
        }
        $temp_id = Setting::get('plugin.micro')['micro_order_bonus'];
        if (!$temp_id) {
            return;
        }
        $params = [
            ['name' => '昵称', 'value' => $log_model->hasOneMember->nickname],
            ['name' => '时间', 'value' => date('Y-m-d H:i:s', time())],
            ['name' => '分红金额', 'value' => $log_model->bonus_money]
        ];
        $msg = MessageTemp::getSendMsg($temp_id, $params);
        if (!$msg) {
            return;
        }
        self::notice(MessageTemp::$template_id, $msg, $log_model->member_id, $log_model->uniacid);
    }

    /**
     * @name 下级微店分红通知
     * @author
     * @param $log_model
     */
    public static function lowerBonusOrder($log_model)
    {
        if (!\YunShop::notice()->getNotSend('micro.lower_bonus_order_title')) {
            return;
        }
        $temp_id = Setting::get('plugin.micro')['micro_lower_bonus'];
        if (!$temp_id) {
            return;
        }
        $params = [
            ['name' => '昵称', 'value' => $log_model->hasOneMember->nickname],
            ['name' => '时间', 'value' => date('Y-m-d H:i:s', time())],
            ['name' => '分红金额', 'value' => $log_model->lower_level_bonus_money],
            ['name' => '下级昵称', 'value' => $log_model->lower_level_nickname]
        ];
        $msg = MessageTemp::getSendMsg($temp_id, $params);
        if (!$msg) {
            return;
        }
        self::notice(MessageTemp::$template_id, $msg, $log_model->member_id, $log_model->uniacid);
    }

    /**
     * @name 微店分红结算通知
     * @author
     * @param $member_id
     * @param $bonus_total
     * @param $uniacid
     */
    public static function microBonusApply($member_id, $bonus_total, $uniacid)
    {
        $noticeMember = Member::getMemberByUid($member_id)->with('hasOneFans')->first();
        $member = $noticeMember->hasOneFans;
        $temp_id = Setting::get('plugin.micro')['micro_bonus_settlement'];
        if (!$temp_id) {
            return;
        }
        $params = [
            ['name' => '昵称', 'value' => $member->nickname],
            ['name' => '时间', 'value' => date('Y-m-d H:i:s', time())],
            ['name' => '分红金额', 'value' => $bonus_total]
        ];
        $msg = MessageTemp::getSendMsg($temp_id, $params);
        if (!$msg) {
            return;
        }
        self::notice(MessageTemp::$template_id, $msg, $member_id, $uniacid);
    }

    /**
     * @name 上级微店分红结算通知
     * @author
     * @param $member_id
     * @param $bonus_total
     * @param $uniacid
     */
    public static function agentMicroBonusApply($member_id, $bonus_total, $uniacid)
    {
        $noticeMember = Member::getMemberByUid($member_id)->with('hasOneFans')->first();
        $member = $noticeMember->hasOneFans;
        $temp_id = Setting::get('plugin.micro')['micro_agent_bonus_settlement'];
        if (!$temp_id) {
            return;
        }
        $params = [
            ['name' => '昵称', 'value' => $member->nickname],
            ['name' => '时间', 'value' => date('Y-m-d H:i:s', time())],
            ['name' => '分红金额', 'value' => $bonus_total]
        ];
        $msg = MessageTemp::getSendMsg($temp_id, $params);
        if (!$msg) {
            return;
        }
        self::notice(MessageTemp::$template_id, $msg, $member_id, $uniacid);
    }
}