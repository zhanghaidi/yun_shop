<?php

/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/11/24
 * Time: 下午4:06
 */

namespace Yunshop\MinApp\Common\Services;

use Ixudra\Curl\Facades\Curl;
use app\common\models\Category;
use app\common\models\Goods;
//use app\common\models\Category;
use app\common\models\MemberMiniAppModel;

class CollectionHwqService
{
    private $memberId;
    private $goodsId;
    private $openId;

    /**
     * CollectionHwqService constructor.
     * @param $cartModel
     */
    public function __construct($cartModel)
    {
        $this->memberId = $cartModel['member_id'];
        $this->goods_id = $cartModel['goods_id'];
        $goodsModel = Goods::uniacid()->with(['hasManyParams' => function ($query) {
            return $query->select('goods_id', 'title', 'value');
        }])
            ->with('belongsToCategorys')
            ->find($this->goods_id);
//        \Log::info('hwq收藏', $cart['goods_id']);
        $access_token = $this->getAccessToken();
        if(!$access_token){
            return false;
        }
        if ($this->verify($goodsModel)) {
            $orderList = $this->handle($goodsModel);
        }

        if (!$orderList) {
            return false;
        }

        $data = $this->transitionNnicode($orderList);

        $url = 'https://api.weixin.qq.com/mall/addshoppinglist?access_token='.$access_token;
        if($data !== 'null'){
            $result = Curl::to($url)->withData($data)->asJsonResponse(true)->post();
        }
        \Log::info('好物圈添加购物车', $result);
        return true;
    }

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
     * @param $goodsModel
     * @return array
     */
    public function handle($goodsModel)
    {

        $category_ids = explode(',', $goodsModel->hasManyGoodsCategory[0]->category_ids);
        $categoryList = Category::whereIn('id',$category_ids)->get();

        //排序
        foreach($categoryList as $key => $value){
            $categoryLists [] = $value['name'];
        }

        //门店商品，存在经纬度
        if($goodsModel->plugin_id = 32 and  app('plugins')->isEnabled('store-cashier') == 1){
            $store = \Yunshop\StoreCashier\common\models\Store::getStoreByCashierId($goodsModel->id)->first();
            $stores = [
                [
                    'longitude' => $store->longitude,
                    'latitude' => (int)$store->latitude,
                    'radius' => '5',
                    'business_name' => $store->store_name,//门店名称
                    'branch_name' => '无',// 分店名称
                    'address' => $store->address,//门店地址
                ]
            ];
            $brandInfo = [
                        "logo" => $store->thumb,
                        "name" => $store->store_name
            ];
        }

        if(count($goodsModel->hasManyParams) and $goodsModel->hasManyParams !== null) {
            foreach ($goodsModel['hasManyParams'] as $key => $value) {
                $arr[$key] =
                    [
                        'name' => $value->title,
                        'value'=> $value->value,
                    ];

            }
        } else {
            $arr = [];
        }
        if($goodsModel->updated_at){
            $updatedAt = strtotime($goodsModel->updated_at);
        }else{
            $updatedAt = 0;
        }

        return [
            "user_open_id" => $this->openId,
            "sku_product_list" => [
                [
                    "item_code" => $goodsModel->id,
                    "title" => $goodsModel->title,
                    "desc" => $goodsModel->desc ?: '无',
                    "category_list" => $categoryLists,
                    "image_list" => $goodsModel->thumb,
                    "src_wxapp_path" => "/pages/detail_v2/detail_v2?id=".$goodsModel->id,
                    "attr_list" => $arr,
                    "update_time" => $updatedAt,
                    "sku_info" => [
                        "sku_id" => $goodsModel->id,
                        "price" => $goodsModel->price * 100,
                        "original_price" => $goodsModel->original->price * 100,
                        "status" => $goodsModel->status,
                        "poi_list" =>$stores,
                        "sku_attr_list" => $arr,
                    ],
                    "brand_info" => $brandInfo,
                ]
            ]
        ];
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
        $user = MemberMiniAppModel::getFansById($this->memberId);
//        \Log::info('用戶信息', $user);
        if ($user->openid) {
            $this->openId = $user->openid;
        } else {
            return false;
        }
        return true;
    }

}