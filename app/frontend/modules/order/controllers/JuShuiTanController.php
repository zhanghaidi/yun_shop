<?php

namespace app\frontend\modules\order\controllers;

use app\common\components\ApiController;
use app\common\exceptions\AppException;
use app\frontend\models\Order;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use app\frontend\modules\order\services\OrderService;


class JuShuiTanController extends ApiController
{
    protected $order_data;
    protected $cig;
    protected $param;

    public function __construct()
    {
        $this->cig = config('jushuitan');
        $this->param = request()->input();


    }

    //发送聚水潭接口
    public function index()
    {
        $orders = \app\common\models\Order::where(['status' => 1, 'jushuitan_status' => 0])->where('pay_time', '<=', time()-1800)->with('address', 'hasManyOrderGoods', 'hasOneOrderPay')
            ->orderBy('create_time', 'ASC')->chunk(100, function ($orders) {
                foreach ($orders as $order) {
                    if(empty($order->has_one_refund_apply)){
                        $address = explode(" ", $order->address->address);
                        $goods = $order->hasManyOrderGoods->toArray();
                        $items = [];
                        foreach ($goods as $k => $val) {
                            $items[] = [
                                //'shop_sku_id' => 'SKU A1',
                                'sku_id' => $val['goods_sn'],   //ERP内商品编码 长度<=40 （必传项）
                                'shop_sku_id' => $val['goods_sn'],      //店铺商品编码 长度<=128 （必传项）
                                //'i_id' => '',  //ERP内款号/货号 长度<=40
                                'amount' => floatval($val['goods_price']), //decimal应付金额，保留两位小数，单位（元）；备注：可能存在人工改价 （必传项）
                                'base_price' => floatval($val['goods_price']), //decimal基本价（拍下价格），保留两位小数，单位（元） （必传项）
                                'qty' => intval($val['total']), //int数量 （必传项）
                                'name' => $val['title'], //string商品名称 长度<=100 （必传项）
                                'outer_oi_id' => $order->hasOneOrderPay->pay_sn, //string商家系统订单商品明细主键,为了拆单合单时溯源，最长不超过 50,保持唯一 （必传项）
                                'properties_value' => $val['goods_option_title']  //string商品属性；长度<=100 （非必传）
                            ];
                        }
                        $params = array([
                            'pay' => [
                                'outer_pay_id' => $order->hasOneOrderPay->pay_sn,//string 外部支付单号，最大50 （必传项）$order->order_sn,
                                'pay_date' => $order->pay_time->toDateTimeString(), //string支付日期 （必传项）
                                'amount' => floatval($order->price), //decimal支付金额 （必传项）
                                'payment' => $order->hasOneOrderPay->pay_type_name, //string支付方式，最大20 （必传项）
                                'seller_account' => $order->address->mobile, //string卖家支付账号，最大 50 （必传项）
                                'buyer_account' => $order->shop_name //string买家支付账号，最大 200 （必传项）
                            ],
                            'shop_id' => 10820686, //int店铺编号 （必传项）
                            'so_id' => $order->order_sn,  //string订单编号 （必传项）
                            'order_date' => $order->pay_time->toDateTimeString(),//stringCarbon::$order->create_time, //订单日期 （必传项）
                            'shop_status' => 'WAIT_SELLER_SEND_GOODS',  //string（必传项）订单：等待买家付款=WAIT_BUYER_PAY，等待卖家发货=WAIT_SELLER_SEND_GOODS,等待买家确认收货=WAIT_BUYER_CONFIRM_GOODS, 交易成功=TRADE_FINISHED, 付款后交易关闭=TRADE_CLOSED,付款前交易关闭=TRADE_CLOSED_BY_TAOBAO；发货前可更新
                            'shop_buyer_id' => $order->address->mobile, //string买家帐号 长度 <= 50 （必传项）
                            'receiver_state' => $address[0], //string收货省份 长度 <= 50；发货前可更新 （必传项）
                            'receiver_city' => $address[1], //string收货市 长度<=50；发货前可更新 （必传项）
                            'receiver_district' => $address[2], //string收货区/街道 长度<=50；发货前可更新 （必传项）
                            'receiver_address' => $address[3], //string收货地址 长度<=200；发货前可更新 （必传项）
                            'receiver_name' => $order->address->realname, //string收件人 长度<=50；发货前可更新 （必传项）
                            'receiver_phone' => $order->address->mobile, //string联系电话 长度<=50；发货前可更新 （必传项）
                            'receiver_mobile' => $order->address->mobile, //string 手机号
                            'pay_amount' => floatval($order->price),  //decimal应付金额，保留两位小数，单位元） （必传项）
                            'freight' => floatval($order->dispatch_price),    //decimal运费 （必传项）
                            'shop_modified' => date('Y-m-d H:i:s', time()), //string订单修改日期 （必传项）
                            'buyer_message' => $order->note, //string 买家留言 长度<=400；可更新 （非必传）
                            'remark' => $order->hasOneOrderRemark->remark, //string 卖家备注 长度<=150；可更新
                            'items' => $items,  //商品明细 （必传项）
                        ]);
                        $result = OrderService::post($params, 'jushuitan.orders.upload');

                        if (!empty($result) && $result['code'] == 0) {
                            $order->jushuitan_status = 1;
                            $order->save();
                        }
                    }
                }
            });
    }

    //发送聚水潭子方法
    public function jushuitan($order_data, $province = '', $city = '', $district = '', $address = '', $array)
    {
        $params = array(
            [

                "pay" => [
                    "outer_pay_id" => $order_data['order_sn'],
                    "pay_date" => date('Y-m-d h:i:s', $order_data['pay_time']),
                    "amount" => floatval($order_data['price']),
                    "payment" => "微信",
                    "buyer_account" => $order_data['mobile'],
                    "seller_account" => "艾居益商城"
                ],
                "shop_id" => 10820686,
                "so_id" => $order_data['order_sn'],
                "order_date" => date('Y-m-d h:i:s', $order_data['create_time']),
                "shop_status" => "WAIT_SELLER_SEND_GOODS",
                "shop_buyer_id" => $order_data['mobile'],
                "receiver_state" => $province,
                "receiver_city" => $city,
                "receiver_district" => $district,
                "receiver_address" => $address,
                "receiver_name" => $order_data['realname'],
                "receiver_phone" => $order_data['mobile'],
                "receiver_mobile" => $order_data['mobile'],
                "pay_amount" => floatval($order_data['price']),
                "freight" => 0.0,
                "shop_modified" => date('Y-m-d h:i:s', time()),
                'buyer_message' => $order_data['note'],
                "items" => $array,
            ]
        );

        $result = OrderService::post($params, 'jushuitan.orders.upload');
        if (!empty($result) && $result['code'] == 0) {
            $data['jushuitan_status'] = '1';
            DB::table('yz_order')->where(['id' => $order_data['id']])->update($data);
            echo '订单上传成功' . $order_data['id'];
        } else {
            echo $result['msg'];
        }

    }

    //聚水潭返回物流信息方法
    public function sendorder()
    {
        //↓↓↓↓↓这个是物流回调地址，追加打印的数据，聚水潭发货以后这个接口接收，芸众根目录可查看发过来的订单数据（正式上线删除）
        file_put_contents('ceshi.txt', print_r($this->param, true));
        \Log::info('聚水潭回传参数', $this->param);
        $order_sn = $this->param['so_id'];

        if (!empty($order_sn) && $order_sn) {
            $order = Db::table('yz_order')->where(['order_sn' => $order_sn, 'status' => 1])->first();
            if (!empty($order) && $order) {
                $lc_id = $this->param['lc_id'];
                if ($lc_id == 'ZTO.8' || $lc_id == 'ZTO.5' || $lc_id == 'ZTO.2' || $lc_id == 'ZTO.1' || $lc_id == 'ZTO') {
                    $data['express_code'] = 'zhongtong'; //中通速递
                    $data['express_company_name'] = '中通速递';
                } elseif ($lc_id == 'YMDD') {
                    $data['express_code'] = 'yimidida';//壹米滴答
                    $data['express_company_name'] = '壹米滴答';
                } elseif ($lc_id == 'TTKDEX') {
                    $data['express_code'] = 'tiantian';//天天快递
                    $data['express_company_name'] = '天天快递';
                } elseif ($lc_id == 'STO') {
                    $data['express_code'] = 'shentong';//申通快递
                    $data['express_company_name'] = '申通快递';
                } elseif ($lc_id == 'SF.9' || $lc_id == 'SF.10' || $lc_id == 'SF.1' || $lc_id == 'SF') {
                    $data['express_code'] = 'shunfeng';     //顺丰速运
                    $data['express_company_name'] = '顺丰速运';
                } elseif ($lc_id == 'POSTB.5' || $lc_id == 'POSTB') {
                    $data['express_code'] = 'youzhengguonei'; //邮政快递
                    $data['express_company_name'] = '邮政快递包裹';
                } elseif ($lc_id == 'HTKY') {
                    $data['express_code'] = 'huitongkuaidi';//百世快递
                    $data['express_company_name'] = '百世快递';
                } elseif ($lc_id == 'DBL') {
                    $data['express_code'] = 'debangwuliu';//德邦物流
                    $data['express_company_name'] = '德邦物流';
                } else {
                    $data['express_code'] = $lc_id;
                    $data['express_company_name'] = '自提自送';
                }
                $data['order_id'] = $order['id'];
                $data['express_sn'] = $this->param['l_id'];
                $data['confirmsend'] = "yes";
                OrderService::orderSend($data);

                \Log::info('订单状态更改', $data);
                OrderService::orderMess($order_sn, $order, 1);
                $this->ju_log("订单：{$order_sn}发货成功,物流编号：{$lc_id},物流名称：{$data['express_company_name']}", 1);
                //echo json_encode(['code' => "0", 'msg' => '执行成功'], JSON_UNESCAPED_UNICODE);
                return response()->json([
                    'code' => '0',
                    'msg' => '执行成功',
                ], 200, ['charset' => 'utf-8']);

            } else {
                \Log::info('订单不存在、发货状态已更改', $order);
                $this->ju_log("订单{$order_sn}发货失败：订单已发货，或被删除");
            }
        } else {
            $this->ju_log("订单发货失败：订单号不存在！");
        }

    }

    //聚水潭发送日志
    public function ju_log($query = '', $type = '')
    {
        if ($type == 1) {
            $logFile = fopen(
                storage_path('logs/jushuitan' . DIRECTORY_SEPARATOR . date('Y-m-d') . '_success.log'),
                'a+'
            );
        } else {
            $logFile = fopen(
                storage_path('logs/jushuitan' . DIRECTORY_SEPARATOR . date('Y-m-d') . '_failed.log'),
                'a+'
            );
        }

        fwrite($logFile, date('Y-m-d H:i:s') . ': ' . $query . PHP_EOL);
        fclose($logFile);
    }

    //退货信息修改
    public function refund_order()
    {
        $refund_sn = $this->param['outer_as_id'];
        if (!empty($refund_sn)) {
            $data['status'] = '8';
            DB::table('yz_order_refund')->where(['refund_sn' => $refund_sn])->update($data);
            //echo json_encode(['code' => "0", 'msg' => '执行成功'], JSON_UNESCAPED_UNICODE);
            return response()->json([
                'code' => '0',
                'msg' => '执行成功',
            ], 200, ['charset' => 'utf-8']);
        } else {
            // echo json_encode(['code' => "1", 'msg' => '接收失败'], JSON_UNESCAPED_UNICODE);
            return response()->json([
                'code' => '1',
                'msg' => '接收失败',
            ], 200, ['charset' => 'utf-8']);
        }

    }

}