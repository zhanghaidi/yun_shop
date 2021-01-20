<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/12/20
 * Time: 上午10:27
 */

namespace Yunshop\VideoDemand\services;


use app\common\facades\Setting;
use app\common\models\notice\MessageTemp;

class MessageService
{
    /**
     * @param $data
     * @param $member
     * @param string $uniacid
     * 讲师分红订单通知
     */
    public static function orderReward($data, $member, $uniacid = '')
    {
        if ($uniacid) {
            Setting::$uniqueAccountId = $uniacid;
        }

        $levelReturnNotice = Setting::get('plugin.video_demand');
        $temp_id = $levelReturnNotice['lecturer_reward_order'];
        if (!$temp_id) {
            return;
        }
        static::messageNotice($temp_id, $data, $member, $uniacid);
    }

    /**
     * @param $data
     * @param $member
     * @param string $uniacid
     * 讲师分红订单结算通知
     */
    public static function orderRewardSettle($data, $member, $uniacid = '')
    {
        if ($uniacid) {
            Setting::$uniqueAccountId = $uniacid;
        }

        $levelReturnNotice = Setting::get('plugin.video_demand');
        $temp_id = $levelReturnNotice['reward_order_settle'];
        if (!$temp_id) {
            return;
        }
        static::messageNotice($temp_id, $data, $member, $uniacid);
    }

    /**
     * @param $data
     * @param $member
     * @param string $uniacid
     * 会员打赏通知
     */
    public static function reward($data, $member, $uniacid = '')
    {
        if ($uniacid) {
            Setting::$uniqueAccountId = $uniacid;
        }

        $levelReturnNotice = Setting::get('plugin.video_demand');
        $temp_id = $levelReturnNotice['reward'];
        if (!$temp_id) {
            return;
        }
        static::messageNotice($temp_id, $data, $member, $uniacid);
    }

    /**
     * @param $temp_id
     * @param $data
     * @param $member
     * @param string $uniacid
     */
    public static function messageNotice($temp_id, $data, $member, $uniacid = '')
    {
        $params = [
            ['name' => '昵称', 'value' => $member['nickname']],
            ['name' => '时间', 'value' => date('Y-m-d H:i:s', time())],
            ['name' => '商品名称', 'value' => $data['goods_name']],
            ['name' => '订单金额', 'value' => $data['order_price']],
            ['name' => '分红金额', 'value' => $data['amount']],
            ['name' => '打赏金额', 'value' => $data['amount']],
        ];

        $msg = MessageTemp::getSendMsg($temp_id, $params);

        if (!$msg) {
            return;
        }
        \app\common\services\MessageService::notice(MessageTemp::$template_id, $msg, $member->uid, $uniacid);
    }

}