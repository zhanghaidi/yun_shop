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
            ->take(20)
            ->get();

        foreach ($ret as $k => $v) {
            //if ($now_time - $v['pay_time'] > 1800) {
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
            //}
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
        var_dump(json_encode($params));
        die;
        $ret = $this->generate_signature();
        // var_dump($ret);die;
//        $url = 'https://open.erp321.com/api/open/query.aspx';        //请求网址
//        $result = $this->post($url, $params, $ret, 'jushuitan.orders.upload');

        if (!empty($result) && $result['code'] == 0) {
            $data['jushuitan_status'] = '1';
            DB::table('yz_order')->where(['id' => $order_data['id']])->update($data);
            echo '订单上传成功' . $order_data['id'];
        } else {
            echo $result;
        }

    }


    //计算验签
    public function generate_signature($params = null)
    {

        $sign_str = '';
        // ksort($system_params);
        $system_params = array(
            'method' => 'jushuitan.orders.upload',
            'partnerid' => $this->cig['partnerid'],
            'ts' => time(),
            'token' => $this->cig['token'],

        );
        //奇门接口
        if (strstr($system_params['method'], 'jst')) {
            $method = str_replace('jst.', '', $system_params['method']);
            $jstsign = $method . $this->config->partner_id . "token" . $this->config->token . "ts" . $system_params['ts'] . $this->config->partner_key;

            if ($this->config->debug_mode) echo '计算jstsign源串->' . $jstsign;

            $system_params['jstsign'] = md5($jstsign);

            //如果有业务参数则合并
            if ($params != null) {
                $system_params = array_merge($system_params, $params);
                ksort($system_params);

                foreach ($system_params as $key => $value) {
                    if (is_array($value)) {
                        $sign_str .= $key . join(',', $value);
                        continue;
                    }
                    $sign_str .= $key . strval($value);
                }
            }

            $system_params['sign'] = strtoupper(md5($this->config->taobao_secret . $sign_str . $this->config->taobao_secret));
        } else  //普通接口
        {
            $no_exists_array = array('method', 'sign', 'partnerid', 'partnerkey');

            $sign_str = $system_params['method'] . $system_params['partnerid'];

            foreach ($system_params as $key => $value) {

                if (in_array($key, $no_exists_array)) {
                    continue;
                }
                $sign_str .= $key . strval($value);
            }

            $sign_str .= $this->cig['partnerkey'];
            $system_params['sign'] = md5($sign_str);
        }

        return $system_params;

    }


    //发送请求
    public function post($url, $data, $url_params, $action)
    {
        $post_data = '';
        try {
            if (strstr($action, 'jst')) {
                foreach ($data as $key => $value) {
                    if (is_array($value)) {
                        $url_params[$key] = join(',', $value);
                        continue;
                    }
                    $url_params[$key] = $value;
                }
            } else {
                $post_data = json_encode($data);

            }

            $url .= '?' . http_build_query($url_params);
            if ($this->config->debug_mode) echo $url;
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/x-www-form-urlencoded'
            ));

            $result = curl_exec($ch);
            if (curl_errno($ch)) {
                print curl_error($ch);
            }
            curl_close($ch);
            return json_decode($result, true);

        } catch (Exception $e) {
            return null;
        }

    }


    public function sendorder()
    {
        $order_sn = $this->param['so_id'];
        if (!empty($order_sn) && $order_sn) {
            $order = Db::table('yz_order')->where(['order_sn' => $order_sn, 'status' => 1])->first();
            if (!empty($order) && $order) {
                $data['order_id'] = $order['id'];
                $data['express_code'] = $this->param['lc_id'];
                $data['express_company_name'] = $this->param['logistics_company'];
                $data['express_sn'] = $this->param['l_id'];
                $data['confirmsend'] = "yes";
                // OrderService::orderSend($data);
                $this->ju_log("订单{$order_sn}发货成功",1);
                echo json_encode(['code'=>"0",'msg'=>'执行成功'],JSON_UNESCAPED_UNICODE);
            } else {
                $this->ju_log("订单{$order_sn}发货失败：订单已发货，或被删除");
            }
        } else {
            $this->ju_log("订单发货失败：订单回调有误！");
        }

    }


    public function ju_log($query='',$type='')
    {
        if($type==1){
            $logFile = fopen(
                storage_path('logs/jushuitan' . DIRECTORY_SEPARATOR . date('Y-m-d') . '_success.log'),
                'a+'
            );
        }else{
            $logFile = fopen(
                storage_path('logs/jushuitan' . DIRECTORY_SEPARATOR . date('Y-m-d') . '_failed.log'),
                'a+'
            );
        }

        fwrite($logFile, date('Y-m-d H:i:s') . ': ' . $query . PHP_EOL);
        fclose($logFile);
    }


}