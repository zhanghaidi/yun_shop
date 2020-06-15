<?php

namespace app\frontend\modules\order\controllers;

use app\common\components\ApiController;
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


    public function index()
    {
        $now_time = time();
        $ret = DB::table('yz_order as o')
            // ->join('yz_order_goods as s', 'o.id', '=', 's.order_id')
            ->join('yz_order_address as p', 'o.id', '=', 'p.order_id')
            ->select('o.price', 'o.note', 'o.goods_total', 'o.create_time', 'o.order_sn', 'o.id', 'p.address', 'p.mobile', 'p.realname', 'o.pay_time')
            ->where('o.status', 1)
            ->where('o.jushuitan_status', 0)
            ->orderBy('o.create_time', 'DESC')
            ->take(10)
            ->get();

        if (!empty($ret)) {
            foreach ($ret as $k => $v) {
                if ($now_time - $v['pay_time'] > 300) {
                    $addres = explode(" ", $v['address']);
                    $order_goods = Db::table('yz_order_goods')->where(['order_id' => $v['id']])->get();
                    $array = [];
                    foreach ($order_goods as $key => $val) {
                        $array[] =
                            [
                                'sku_id' => 'TP0024',
                                'shop_sku_id' => 'SKU A1',
                                'amount' => floatval($val['goods_price']),
                                'base_price' => floatval($val['goods_price']),
                                'qty' => $val['total'],
                                'name' => $val['title'],
                                'outer_oi_id' => strval($val['id']),
                                'properties_value' => $val['goods_option_title']
                            ];

                    }

                    $this->jushuitan($v, $addres[0], $addres[1], $addres[2], $addres[3], $array);
                }
            }
        }


    }


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
            echo $result;
        }

    }


    public function sendorder()
    {
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
                } elseif ($lc_id == 'JD') {
                    $data['express_code'] = 'debangwuliu';//京东快递
                    $data['express_company_name'] = '京东快递';
                } else {
                    $data['express_code'] = $lc_id;
                    $data['express_company_name'] = '自提自送';
                }
                $data['order_id'] = $order['id'];
                $data['express_code'] = $this->param['lc_id'];
                $data['express_sn'] = $this->param['l_id'];
                $data['confirmsend'] = "yes";
                OrderService::orderSend($data);
                $this->ju_log("订单{$order_sn}发货成功", 1);
                echo json_encode(['code' => "0", 'msg' => '执行成功'], JSON_UNESCAPED_UNICODE);
            } else {
                $this->ju_log("订单{$order_sn}发货失败：订单已发货，或被删除");
            }
        } else {
            $this->ju_log("订单发货失败：订单回调有误！");
        }

    }


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


    public function refund_order()
    {
        $refund_sn = $this->param['outer_as_id'];
        if (!empty($refund_sn)) {
            $data['status'] = '8';
            DB::table('yz_order_refund')->where(['refund_sn' => $refund_sn])->update($data);
            echo json_encode(['code' => "0", 'msg' => '执行成功'], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['code' => "1", 'msg' => '接收失败'], JSON_UNESCAPED_UNICODE);
        }

    }

}