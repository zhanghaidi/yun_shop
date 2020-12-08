<?php

namespace Yunshop\EnterpriseWechat\api;

use app\common\components\ApiController;
use app\common\facades\Setting;
use Yunshop\EnterpriseWechat\services\QyWeiBanService;
use Illuminate\Support\Facades\DB;

/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/6/16
 * Time: 下午2:29
 */
class OrderTrackController extends ApiController
{
    //订单导入
    public function sendOrderTrack()
    {
        $member_id = \YunShop::app()->getMemberId();

        $input = request()->all();

        \Log::info('接收小程序传递的参数：'.json_encode($input));
        $goods_id = $input['goods_id'];
        $action = $input['action']; //动作类型 1：查看 2、收藏 3、加购 4：下单 5：支付
        $val = $input['val']; //根据action记录相应参数：加购记录加购商品数 下单记录订单编号
        $user = DB::table('diagnostic_service_user')->select('ajy_uid','unionid')->where('ajy_uid', $member_id)->first();
        $goods = DB::table('yz_goods')->select('id','title','price')->where('id', $goods_id)->first();

        $orderData = array(
            "shop_id" => "10820686",     //shop_id	str	否	店铺id，为店铺的唯一编号，若存在店铺数据，需携带此参数用于辨识区分店铺
            "shop_name" => "养居益商城",      //shop_name	str	否	店铺名称
            "item_id" => $goods_id,   //item_id	str	是	商品id
            "item_name" => $goods['title'], //item_name	str	是	商品名称
            "item_price" => $goods['price'],//item_price	str	是	商品价格
            "amount" => 1,//amount	int	是	购买数量
            "payment_amount" => $goods['price'],//payment_amount	int	是	购买总金额
            "order_id" => date('Y-m-d H:i:s').'-'.$member_id.'-'.$goods_id,//order_id	str	是	订单号
            "order_status" => "浏览",//order_status	str	是	订单状态
            "create_time" => 1607393333,//create_time	int	是	订单创建时间
            "paid_time" => 1607393333,//paid_time	int	是	订单支付时间
            "unionid" => $user['unionid'],//unionid	str	是	客户的unionid
            "order_type" => "用户足迹",//order_type	str	是	订单类型，限制不超过12个字节（英文1字节，汉字2字节）此参数对应侧边栏的订单名称的显示
            //"discount_amount"=>"0.00",//discount_amount	int	否	优惠金额
            //"payment_channel" => "微信",//payment_channel	str	否	支付渠道
            //shop_fields	ShopField[]否	店铺信息自定义字段列表，非店铺基本字段。字段说明见 ShopField数据模型
            /*"shop_fields" => array(
                [
                    "field_name" => "养居益商城",
                    "value" => "营业中",
                ],
                [
                    "field_name" => "店铺评分",
                    "value" => "4.5",
                ]

            ),*/
            //item_fields	ItemField[]	否	商品信息自定义字段列表，非商品的基本字段。字段说明见 ItemField数据模型
            /*"item_fields" => array(
                [
                    "field_name" => "商品描述",
                    "value" => "商品足迹",
                ],
                [
                    "field_name" => "商品类别",
                    "value" => "实体商品",
                ]
            ),*/
            //order_fields	OrderField[]	否	订单信息自定义字段，非订单基本字段列表。字段说明见 OrderField数据模型
            /*"order_fields" => array(
                [
                    "field_name" => "买家手机号",
                    "value" => "13607697385",
                ],

            )*/
        );

        if($action == 1){
            $orderData['order_status'] = '查看';
        }elseif ($action == 2){
            $orderData['order_status'] = '收藏';
        }elseif ($action == 3){
            $orderData['order_status'] = '加购';
            $orderData['amount'] = intval($val); //加购件数
        }elseif ($action == 4){
            $orderData['order_status'] = '下单';
        }elseif ($action == 5){
            $orderData['order_status'] = '支付';
        }

        $res = QyWeiBanService::importOrder($orderData);

        return $this->successJson($res['errmsge'], '');
    }

    //获取订单列表
    public function getOrderTrack()
    {
        $res = QyWeiBanService::getOrderList();

        return $this->successJson($res['errmsge'], $res['order_info']);
    }

    //根据订单号移除订单
    public function removeOrderTrack()
    {
        $order_id = trim(input('order_id'));
        if (empty($order_id)) {
            return $this->errorJson('订单id不能为空', '');
        }
        $res = QyWeiBanService::removeOrder($order_id);

        return $this->successJson($res['errmsge'], '');
    }

    //批量导入
    public function batchOrderTrack()
    {
        $orderList = array();
        $res = QyWeiBanService::sendOrderList($orderList);

        return $this->successJson($res['errmsge'], '');
    }

}