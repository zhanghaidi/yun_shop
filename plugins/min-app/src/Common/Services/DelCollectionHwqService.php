<?php

/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/11/24
 * Time: 下午4:06
 */

namespace Yunshop\MinApp\Common\Services;

use Ixudra\Curl\Facades\Curl;

class DelCollectionHwqService
{
    private $memberId;
    private $goodsId;
    private $openId;
    private $goodsMoel;

    /**
     * 删除
     * DelCollectionHwqService constructor.
     * @param $cart
     */
    public function __construct($cart)
    {
        header("Content-type: text/html; charset=utf-8");
        $this->member_id = $cart['member_id'];
        $this->goods_id = $cart['goods_id'];
        $goodsModel = Goods::uniacid()->with(['hasManyParams' => function ($query) {
            return $query->select('goods_id', 'title', 'value');
        }]);
       \Log::info('hwq删除', $cart['goods_id']);
        $this->goodsModel = $goodsModel;
        if ($this->verify()) {
            $orderList = $this->handle();

        }
        $access_token = $this->getAccessToken();

        if(!$access_token){
            return false;
        }
        $data = $this->json_yang($orderList);
        $url = 'https://api.weixin.qq.com/mall/deleteshoppinglist?access_token=' . $access_token;
//        dd($url);

        $result = Curl::to($url)
            ->withData($data)
            ->asJsonResponse(true)
            ->post();
         \Log::info('好物圈删除购物车', $result);
        return true;
    }

    public function getAccessToken()
    {
        $set = \Setting::get('plugin.min_app');
        if(!$set){
            return;
            \Log::info('微信小程序配置',$set);
        }
        if($set['switch'] == 0){
            return;
            \Log::info('hwq未开启小程序插件',$set);
        }
        if(!$set['key']){
            return;
            \Log::info('hwq小程序appid不存在',$set);
        }
        if(!$set['secret']){
            return;
            \Log::info('hwqSecret不存在',$set);
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
     * @return array
     */
    public function handle()
    {
        return [
            "user_open_id" => $this->openId,
            "sku_product_list" => [
                [
                    "item_code" => $this->goodsModel->id,
                    "sku_id" => $this->goodsModel->id
                ]
            ]
        ];
    }

    private function json_yang($array)
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
        $user = MemberMiniAppModel::getFansById($this->memberId);
        \Log::info('用戶信息', $user);
        if ($user && $user->openid) {
            $this->openId = $user->openid;
        } else {
            return false;
        }
        if($this->order->pay_time->toArray()['timestamp'] == 0){
            return false;
        }
        return true;
    }
}