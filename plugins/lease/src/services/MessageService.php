<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/3/12
 */
namespace Yunshop\LeaseToy\services;

use app\common\facades\Setting;
use app\common\models\notice\MessageTemp;
use app\common\services\MessageService as Message;

class MessageService
{
    static public function leaseReturn($member, $data)
    {

        if(!$member->hasOneFans->openid) {
            return;
        }

        $temp_id = Setting::get('plugin.lease_toy')['return_success'];
        if (!$temp_id) return;



        $goods_title = '';
        foreach ( $data['orderGoods'] as $key => $value) {
            $goods_title .= '【' . $value['title'] . '*' . $value['total'] . '】';
            
        }

        // '昵称', '订单号', '商品详情','归还押金', '时间',
        $params = [
            ['name' => '昵称', 'value' => $member->hasOneFans->nickname],
            ['name' => '订单号', 'value' => $data['lease']->order_sn],
            ['name' => '商品详情', 'value' => $goods_title],

            ['name' => '归还押金', 'value' => $data['lease']->return_deposit],
            ['name' => '时间', 'value' => $data['lease']->return_time->toDateTimeString()],
        ];

        $msg = MessageTemp::getSendMsg($temp_id, $params);

        if (!$msg) return;

        if ($member->hasOneFans->follow) {
            Message::notice(MessageTemp::$template_id, $msg, $member->uid);
        }
    }
}