<?php

namespace Yunshop\EnterpriseWechat\api;

use app\common\components\ApiController;
use app\common\facades\Setting;
use Yunshop\EnterpriseWechat\services\QyWeiBanService;

/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/6/16
 * Time: 下午2:29
 */
class OrderTrackController extends ApiController
{
    public function sendOrderTrack(){
        $member_id = \YunShop::app()->getMemberId();

        $order = '';

        $orderData = array(
            "shop_id"=>"xj184389276483",
            "shop_name"=> "测试的店铺",
            "item_id"=>"200401635358",
            "item_name"=>"贺卡",
            "item_price"=>"22.02",
            "amount"=>2,
            "payment_amount"=>"40",
            "discount_amount"=>"4.04",
            "payment_channel"=>"支付宝",
            "order_id"=>"876842208482419280",
            "order_status"=>"已付款，待发货",
            "create_time"=>1593565941,
            "paid_time"=>1593565949,
            "unionid"=>"oz9xvw_jfkmHMGWVSn5CatKSTOMU",
            "order_type"=>"测试订单类型",
            "shop_fields"=>array(
                [
                    "field_name"=>"店铺状态",
                    "value"=>"营业中",
                ],
                [
                    "field_name"=>"店铺评分",
                    "value"=>"4.3",
                ]

            ),

            "item_fields"=>array(
                [
                "field_name"=>"商品描述",
                "value"=>"编程视频",
                ],
                [
                "field_name"=>"商品类别",
                "value"=>"虚拟商品",
                ]
            ),

            "order_fields"=>array(
                [
                    "field_name"=>"订单备注",
                    "value"=>"发邮箱123@qq.com",
                ],
                [
                    "field_name"=>"买家手机号",
                    "value"=>"173****9527",
                ]
            )
        );

        var_dump(json_encode($orderData));die;

        QyWeiBanService::importOrder($orderData);
    }

}