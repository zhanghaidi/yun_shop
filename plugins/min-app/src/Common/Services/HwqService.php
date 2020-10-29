<?php

/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/11/24
 * Time: 下午4:06
 */

namespace Yunshop\MinApp\Common\Services;

use Ixudra\Curl\Facades\Curl;
use app\common\models\MemberMiniAppModel;
use app\common\models\Category;

class HwqService
{
    private $order;
    private $openId;

    /**
     * 好物圈订单导入
     * HwqService constructor.
     * @param $order
     */
    public function __construct($order)
    {
        header("Content-type: text/html; charset=utf-8");
        $this->order = $order;
        if ($this->verify()) {
            $orderList = $this->handle($order);
        }
        if(!$orderList){
            \Log::info('未发现用户信息');
            return true;
        }
        if(!$orderList['order_list']['0']['ext_info']['product_info']['item_list']['0']['poi_list']){
            unset($orderList['order_list']['0']['ext_info']['product_info']['item_list']['0']['poi_list']);
        }
        if(!$orderList['order_list']['0']['trans_id']){
            unset($orderList['order_list']['0']['trans_id']);
        }
        $data = $this->transitionNnicode($orderList);
        $access_token = $this->getAccessToken();
//        \Log::info('access_token', $access_token);
        if(!$access_token){
            \Log::info('access_token不存在');
            return true;
        }
        $url = 'https://api.weixin.qq.com/mall/importorder?action=add-order&is_history=1&access_token=' .$access_token;
        if($data !== 'null'){
            $result = Curl::to($url)
                ->withData($data)
                ->asJsonResponse(true)
                ->post();
        }
        \Log::info('好物圈同步结果', $result);
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
        //fixby-zhd-小程序access_token统一使用微擎生产线上api 2020-10-29

        //$url =  'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$set['key'].'&secret='.$set['secret'];
        //$result = Curl::to($url)->asJsonResponse(true)->get();
        //return $result['access_token'];

        $url =  'https://www.aijuyi.net/api/accesstoken.php?type=4&appid='.$set['shop_key'].'&secret='.$set['shop_secret'];
        $result = Curl::to($url)->asJsonResponse(true)->get();

        return $result['accesstoken'];
    }

    /**
     *
     * 组装好物圈需要的数据
     * @param $order
     * @return array
     */
    public function handle($order)
    {
        $category_ids = explode(',', $this->order->hasManyOrderGoods[0]->hasOneGoods->hasManyGoodsCategory[0]->category_ids);
        $categoryList = Category::whereIn('id',$category_ids)->get();
        //排序
        foreach($categoryList as $key => $value){
            $categoryLists [] = $value['name'];
        }
        if(!$categoryLists){
            $categoryLists = ['无'];
        }

        if ($this->order->hasOnePayType->code == 'wechatPay' or 'wechatPay' or 'wechatApp' or 'huanxunWx') {
            $transId = $this->order->hasOneOrderPay->pay_sn;
        }

        //门店
        if ($this->order->plugin_id == 32 and app('plugins')->isEnabled('store-cashier') == 1) {
            $store_id = \Yunshop\StoreCashier\common\models\StoreGoods::getGoodsById($this->order->hasManyOrderGoods[0]->goods_id);
            $store = \Yunshop\StoreCashier\common\models\Store::getStoreByCashierId($store_id);
            $stores = [
                [
                    'longitude' => $store->longitude,
                    'latitude' => (int)$store->latitude,
                    'radius' => '5',
                    'business_name' => $store->store_name,//门店名称
                    'branch_name' => '无',// 分店名称
                    'address' => $order->address->address,//门店地址
                ]
            ];
        }

        $stock_attr_info = $this->order->hasManyOrderGoods[0]->hasOneGoods->hasManyParams;
        if (empty($stock_attr_info) and $stock_attr_info !== null ) {
            foreach ($stock_attr_info as $key => $value) {
                $arr[$key] = [
                    'attr_name' => [
                        'name' => $value->title
                    ],
                    'attr_value' => [
                        'name' => $value->value
                    ]
                ];

            }
        } else {
            $arr = [];
        }
        if ($this->order->dispatch_type_id == 1) {
            if($this->order->status == 1 ){
                $express_info = [];
            }else{
                $express_info = [ //object	快递信息
                    'name' => $this->order->orderAddress->realname,//收件人姓名
                    'phone' => $this->order->orderAddress->mobile,//收件人联系电话
                    'address' => $this->order->orderAddress->address, //	收件人地址
                    'price' => $this->order->dispatch_price, //运费（单位分） //必传参数
                    'express_package_info_list' => [ //包裹中的物品信息
                        [
                            'express_company_id' => $this->ExpressCompanyId($this->order->express->express_company_name),//快递公司id
                            'express_company_name' => $this->order->express->express_company_name ?: '顺丰',//	快递公司名
                            'express_code' => $this->order->express->express_sn ?: '1234567',//快递单号
                            'ship_time' => strtotime($this->order->orderAddress->created_at),//发货时间
                            'express_page' => [ //object	快递详情页（小程序页面）'
                                "path" => "packageA/member/order/logistics/logistics?id=".$this->order->id,
                            ],
                            'express_goods_info_list' => [[
                                'item_code' => $this->order->hasManyOrderGoods[0]->goods_id,   //物品id
                                'sku_id' => '1123122',//	sku_id
                            ]],
                        ]
                    ]
                ];
            }
        } else {
            $express_info = ['price' => '0'];
        }

        $types['path'] = 'pages/detail_v2/detail_v2?id='.$this->order->hasManyOrderGoods[0]->goods_id;

        return ['order_list' => [
            [
                'order_id' => $this->order->order_sn, //订单编号
                'create_time' => strtotime($this->order->create_time),// 订单创建时间
                'pay_finish_time' => strtotime($this->order->pay_time),//支付完成时间
                'desc' => $this->order->hasOneOrderRemark->remark ?: '无',//订单备注 可选
                'fee' => $this->order->price * 100,//订单金额(单位：分)
                'trans_id' => $transId, //微信支付订单id，对于使用微信支付的订单，该字段必填
                'status' => $this->order->is_virtual ? 100 : 3,//todo 我们商城-1取消状态，0待付款，1为已付款，2为已发货，3为已完成，微信：订单状态，3：支付完成 4：已发货 5：已退款 100: 已完成
                'ext_info' => [
                    'product_info' => [
                        'item_list' => [[
                            'item_code' => $this->order->hasManyOrderGoods[0]->goods_id,    //物品id(商品id)，要求appid下全局唯一
                            'sku_id' => 100,//sku_id
                            'amount' => $this->order->hasManyOrderGoods[0]->total, // 商品数量
                            'total_fee' => ($this->order->order_goods_price / $this->order->hasManyOrderGoods[0]->total) * 100, // 商品单价
                            'thumb_url' => $this->order->hasManyOrderGoods[0]->thumb,//商品图片
                            'title' => $this->order->hasManyOrderGoods[0]->title,//（商品）物品名称
                            'desc' => '111', //物品详细描述ww
                            'unit_price' => ($this->order->order_goods_price / $this->order->hasManyOrderGoods[0]->total) * 100,//实际售价
                            'original_price' => ($this->order->order_goods_price / $this->order->hasManyOrderGoods[0]->total) * 100, // 物品原价，单位：分
                            'stock_attr_info' => $arr, //商品属性列表,
                            'category_list' => $categoryLists, // 物品类目列表
                            'item_detail_page' => $types,
                            'poi_list' => $stores,
                        ]
                        ]
                    ],
                    'express_info' => $express_info,
                    'promotion_info' => [ //object	订单优惠信息
                        'discount_fee' => $this->order->discount_price ?: 0, //$order->discounts 优惠金额
                    ],

                    //todo 待确认商家联系电话
                    'brand_info' => [ //object	商家信息
                        "phone" => "13988888888", //联系电话，必须提供真实有效的联系电话，缺少联系电话或联系电话不正确将影响物品曝光
                        "contact_detail_page" => [ //联系商家页面
                            "path" => "/pages/homepage/index"
                        ],
                    ],
                    'payment_method' => $transId ? 1 : 2,//订单支付方式，0：未知方式 1：微信支付 2：其他支付方式
//                    'user_open_id' => $this->openId,//用户的openid，参见openid说明
               'user_open_id'=> $this->openId,
                    'order_detail_page' => [ //订单详情页（小程序页面）
                        "path" => "/packageA/member/myOrder_v2/myOrder_v2"
                    ]
                ]
            ]
        ]
        ];

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
        ];
        return $arr[$expressCompanyName] ?: 9999;
    }

    /**
     * 获取oppoid 并验证
     *
     * @return bool
     */
    private function verify()
    {
        $user = MemberMiniAppModel::getFansById($this->order->uid);
//        \Log::info('用戶信息', $user);
        if ($user->openid) {
            $this->openId = $user->openid;
        } else {
            return false;
        }
        return true;
    }

    /**
     * 解决中文乱码
     *
     * @param $array
     * @return string
     */
    private function transitionNnicode($array)
    {
        $this->arrayRecursive($array, 'urlencode', true);
        $json = json_encode($array);
        return urldecode($json);
    }

    /**
     * 循环转码，不乱码
     * @param $array
     * @param $function
     * @param bool $apply_to_keys_also
     */
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
}