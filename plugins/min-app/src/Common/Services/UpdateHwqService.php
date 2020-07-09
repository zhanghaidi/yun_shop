<?php

namespace Yunshop\MinApp\Common\Services;

use Ixudra\Curl\Facades\Curl;
use app\common\models\MemberMiniAppModel;


class UpdateHwqService
{
    private $order;
    private $open_id;

    public function __construct($order)
    {
        header("Content-type: text/html; charset=utf-8");
        \Log::info('hwq更新订单', $order);

        $this->order = $order;

        if ($this->verify($order)) {
            $orderList = $this->handle($order);
        }
        if(!$orderList){
            \Log::info('未发现用户信息');
            return true;
        }
        $access_token = $this->getAccessToken();
        if(!$access_token){
            \Log::info('access_token不存在');
            return true;
        }
        $data = $this->transitionNnicode($orderList);
        \Log::info('data', $data);
        $url = 'https://api.weixin.qq.com/mall/importorder?action=update-order&is_history=1&access_token='.$access_token;

        $result = Curl::to($url)->withData($data)->asJsonResponse(true)->post();

        \Log::info('好物圈更新结果', $result);
        return true;
    }

    /**
     * 获取access_token
     * @return mixed
     */
    public function getAccessToken()
    {
        $set = \Setting::get('plugin.min_app');
        if(!$set){
            \Log::info('微信小程序配置',$set);
            return;

        }
        if($set['switch'] == 0){
            \Log::info('hwq未开启小程序插件',$set);
            return;

        }
        if(!$set['key']){
            \Log::info('hwq小程序appid不存在',$set);
            return;
        }
        if(!$set['secret']){
            \Log::info('hwqSecret不存在',$set);
            return;
        }

        $url =  'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$set['key'].'&secret='.$set['secret'];

        $result = Curl::to($url)->asJsonResponse(true)->get();

        return $result['access_token'];
    }

    /**
     * @param $order
     * @return array
     */
    public function handle($order)
    {
        if ($this->order->dispatch_type_id == 1) {
            $express_info = [ //object	快递信息
                'name' => $this->order->orderAddress->realname,//收件人姓名
                'phone' => $this->order->orderAddress->mobile,//收件人联系电话
                'address' => $this->order->orderAddress->address, //	收件人地址
                //TODO
                'price' => $this->order->dispatch_price, //运费（单位分） //必传参数
                'express_package_info_list' => [ //包裹中的物品信息
                    [
                        'express_company_id' => $this->ExpressCompanyId($this->order->express->express_company_name),//快递公司id
                        'express_company_name' => $this->order->express->express_company_name ?: '顺丰',//	快递公司名
                        'express_code' => $this->order->express->express_sn ?: '1234567',//快递单号
                        'ship_time' => strtotime($this->order->orderAddress->created_at),//发货时间
                        'express_page' => [ //object	快递详情页（小程序页面）'
                            "path" => "pages/index/index",
                        ],
                        'express_goods_info_list' => [[
                            'item_code' => $this->order->hasManyOrderGoods[0]->goods_id,   //物品id
                            'sku_id' => '1123122',//	sku_id
                        ],
                        ]
                    ]
                ]
            ];
        }
        $data = [
            'order_list' => [[
                'order_id' => $this->order->order_sn, //订单id
                //                'trans_id' => '', //微信支付订单id，对于使用微信支付的订单，该字段必填
                'status' => $this->getStatus($order->status),
                'ext_info' => [
                    'express_info' => $express_info,
                    'user_open_id' => $this->open_id,//用户的openid，参见openid说明
                    'order_detail_page' => [ //订单详情页（小程序页面）
                        "path" => "/packageA/member/myOrder_v2/myOrder_v2"
                    ]
                ]
            ]
            ]
        ];

        if(!$express_info){
            unset($data['order_list']['0']['ext_info']['express_info']);
        }
       return $data;
    }


    /**
     * 根据快递名称获取微信提供的快递ID
     * @param $expressCompanyName
     * @return mixed
     */
    private function ExpressCompanyId($expressCompanyName)
    {
        $arr = [
            'EMS' => '2000',
            '圆通' => '2001',
            'DHL' => '2002',
            '中通' => '2004',
            '韵达' => '2005',
            '畅灵' => '2006',
            '百世汇通' => '2008',
            '德邦' => '2009',
            '申通' => '2010',
            '顺丰速运' => '2011',
            '顺兴' => '2012',
            '如风达' => '2014',
            '优速' => '2015',
//              '其他快递公司名字（例如：京东物流'=>'9999'
        ];
        return $arr[$expressCompanyName] ?: 9999;
    }

    /**
     * 转换订单状态
     * @param $status
     * @return int
     */
    private function getStatus($status)
    {
        //todo 我们商城-1取消状态，0待付款，1为已付款，2为已发货，3为已完成， 微信：订单状态，3：支付完成 4：已发货 5：已退款 100: 已完成
        switch ($status) {
            case 2:
                return 4;
                break;
            case -1:
                return 5;
                break;
            case 3:
                return 100;
                break;
        }

    }

    private function transitionNnicode($array)
    {
        $this->arrayRecursive($array, 'urlencode', true);
        $json = json_encode($array);
        return urldecode($json);
    }

    private function arrayRecursive(&$array, $function, $apply_to_keys_also = false)
    {
        static $recursive_counter = 0;
        if (++$recursive_counter > 1000) {
            die('possible deep recursion attack');
        }
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $this->arrayRecursive($array[$key], $function, $apply_to_keys_also);
            } else {
                $array[$key] = $function($value);
            }

            if ($apply_to_keys_also && is_string($key)) {
                $new_key = $function($key);
                if ($new_key != $key) {
                    $array[$new_key] = $array[$key];
                    unset($array[$key]);
                }
            }
        }
        $recursive_counter--;
    }

    /**
     * @name 验证
     * @author
     * @return bool
     */
    private function verify()
    {
        $user = MemberMiniAppModel::getFansById($this->order->uid);
//        \Log::info('用戶信息', $user);
        if ($user->openid) {
            $this->open_id = $user->openid;
        } else {
            return false;
        }
        return true;
    }
}