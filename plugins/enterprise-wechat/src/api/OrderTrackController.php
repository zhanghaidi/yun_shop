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
    public function sendOrderTrack()
    {
        $member_id = \YunShop::app()->getMemberId();

        $orderData = array(
            "shop_id" => "10820686",     //shop_id	str	否	店铺id，为店铺的唯一编号，若存在店铺数据，需携带此参数用于辨识区分店铺
            "shop_name" => "养居益商城",      //shop_name	str	否	店铺名称
            "item_id" => '19',   //item_id	str	是	商品id
            "item_name" => "测试商品", //item_name	str	是	商品名称
            "item_price" => "22.02",//item_price	str	是	商品价格
            "amount" => 1,//amount	int	是	购买数量
            "payment_amount" => "40",//payment_amount	int	是	购买总金额
            //"discount_amount"=>"0.00",//discount_amount	int	否	优惠金额
            "payment_channel" => "微信",//payment_channel	str	否	支付渠道
            "order_id" => "SN20200706181820Xw",//order_id	str	是	订单号
            "order_status" => "查看",//order_status	str	是	订单状态
            "create_time" => 1607393333,//create_time	int	是	订单创建时间
            "paid_time" => 1607393333,//paid_time	int	是	订单支付时间
            "unionid" => "oauhut_9G96tG9xMF3poiEKyzBNI",//unionid	str	是	客户的unionid
            "order_type" => "用户足迹",//order_type	str	是	订单类型，限制不超过12个字节（英文1字节，汉字2字节）此参数对应侧边栏的订单名称的显示
            //shop_fields	ShopField[]否	店铺信息自定义字段列表，非店铺基本字段。字段说明见 ShopField数据模型
            "shop_fields" => array(
                [
                    "field_name" => "养居益商城",
                    "value" => "营业中",
                ],
                [
                    "field_name" => "店铺评分",
                    "value" => "4.5",
                ]

            ),
            //item_fields	ItemField[]	否	商品信息自定义字段列表，非商品的基本字段。字段说明见 ItemField数据模型
            "item_fields" => array(
                [
                    "field_name" => "商品描述",
                    "value" => "商品足迹",
                ],
                [
                    "field_name" => "商品类别",
                    "value" => "实体商品",
                ]
            ),
            //order_fields	OrderField[]	否	订单信息自定义字段，非订单基本字段列表。字段说明见 OrderField数据模型
            "order_fields" => array(
                [
                    "field_name" => "买家手机号",
                    "value" => "13607697385",
                ],

            )
        );

        $res = QyWeiBanService::importOrder($orderData);

        return $this->successJson($res['errmsge'], '');
    }

    public function getOrderTrack()
    {
        $res = QyWeiBanService::getOrderList();

        return $this->successJson($res['errmsge'], $res['order_info']);
    }

    public function removeOrderTrack()
    {
        $order_id = trim(input('order_id'));
        if (empty($order_id)) {
            return $this->errorJson('订单id不能为空', '');
        }
        $res = QyWeiBanService::removeOrder($order_id);

        return $this->successJson($res['errmsge'], '');
    }

    public function batchOrderTrack()
    {
        $orderList = array();
        $res = QyWeiBanService::sendOrderList($orderList);

        return $this->successJson($res['errmsge'], '');
    }

}