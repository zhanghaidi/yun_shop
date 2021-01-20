<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/9/18
 * Time: 下午6:08
 */

namespace Yunshop\Mryt\store\services;


use Yunshop\Mryt\store\models\Store;
use Yunshop\mryt\store\models\StoreGoods;
use Yunshop\Mryt\store\models\Order;
use app\common\models\Address;
use app\common\models\Street;
use app\frontend\modules\goods\models\Comment;
use Yunshop\Love\Common\Models\GoodsLove;

class GetStoreStatisticService
{
    /**
     * @name 门店商品总数
     * @author
     * @param $store_id
     * @return mixed
     */
    public static function getGoodsTotal($store_id)
    {
        return StoreGoods::getGoodsIdsByStoreId($store_id)->count();
    }

    /**
     * @name 门店订单总数
     * @author
     * @param $store_id
     * @param array $search
     * @return mixed
     */
    public static function getOrderCount($store_id, $search = [])
    {
        return Order::getStoreOrderList($search, ['store' => ['store_id' => $store_id]])->count();
    }

    /**
     * @name 门店地址
     * @author
     * @param $store
     * @return string
     */
    public static function getStoreAddress($store)
    {
        $areaList = Address::whereIn('id', [$store->province_id, $store->city_id, $store->district_id, $store->street_id])->pluck('areaname');
        return $areaList->push($store->address)->implode(' ');
    }

    /**
     * @name 门店商品id集合
     * @author
     * @param $store_id
     * @return mixed
     */
    public static function getGoodsIds($store_id)
    {
        return StoreGoods::getGoodsIdsByStoreId($store_id);
    }

    /**
     * @name 门店商品评价
     * @author
     * @param $store_id
     * @return array
     */
    public static function getCommentData($store_id)
    {
        $goods_ids = self::getGoodsIds($store_id);
        if ($goods_ids->isEmpty()) {
            return [
                'average_score' => 0,
                'comment_total' => 0
            ];
        }
        $score_total = self::commentBuilder($store_id, $goods_ids)->sum('level');
        $comment_total = self::commentBuilder($store_id, $goods_ids)->count();
        $average_score = floor($score_total / $comment_total * 10) / 10;
        $new_comment = self::commentBuilder($store_id, $goods_ids)->orderBy('created_at', 'acs')->first();
        return [
            'average_score' => $average_score,
            'comment_total' => $comment_total,
            'new_comment' => $new_comment
        ];
    }

    /**
     * @name 门店评价构造器
     * @author
     * @param $store_id
     * @param array $goods_ids
     * @return mixed
     */
    public static function commentBuilder($store_id, $goods_ids = [])
    {
        if (!$goods_ids) {
            $goods_ids = self::getGoodsIds($store_id);
        }

        return Comment::select(
            'id', 'order_id', 'goods_id', 'uid', 'nick_name', 'head_img_url', 'content', 'level',
            'images', 'created_at', 'type')
            ->uniacid()
            ->with(['hasManyReply'=>function ($query) {
                return $query->where('type', 2)
                    ->orderBy('created_at', 'asc');
            }])
            ->whereIn('goods_id', $goods_ids)
            ->where('comment_id', 0);
    }

    public static function getTimeStamp()
    {
        $thismonth = date('m');
        $thisyear = date('Y');
        if ($thismonth == 1) {
            $lastmonth = 12;
            $lastyear = $thisyear - 1;
        } else {
            $lastmonth = $thismonth - 1;
            $lastyear = $thisyear;
        }
        $lastStartDay = $lastyear . '-' . $lastmonth . '-1';
        $lastEndDay = $lastyear . '-' . $lastmonth . '-' . date('t', strtotime($lastStartDay));
        return [
            'start' => $lastStartDay,
            'end' => date('Y-m-d H:i:s', strtotime($lastEndDay)+86400)
        ];
    }

    public static function getStoreDispatch($store)
    {
        $dispatch = '支持';
        if ($store->dispatch_type['0'] == 1) {
            $dispatch .= '快递、';
        }
        if ($store->dispatch_type['1'] == 2) {
            $dispatch .= '自提、';
        }
        if ($store->dispatch_type['2'] == 3) {
            $dispatch .= '核销';
        }
        return $dispatch;
    }

    public static function getCitys()
    {
        $city_ids = Store::select('city_id')->pluck('city_id');
        $city_names = Address::select('areaname', 'id')->whereIn('id', $city_ids)->get();
        return $city_names;
    }

    public static function getSearchTime()
    {
        return [
            'time_range' => [
                'field' => 'finish_time',
                'start' => GetStoreStatisticService::getTimeStamp()['start'],
                'end' => GetStoreStatisticService::getTimeStamp()['end']
            ]
        ];
    }

    public static function getStoreCity($store_id)
    {
        $store_address = Store::find($store_id);

        $data['city']   = Address::where('id', $store_address->city_id)->value('areaname');
        $data['detailed_address'] = $store_address->address;
        
        return $data;
    }


    /**
     * 门店营销积分设置
     */
    public static function getPointSet($store_id)
    {

        $point_name = '积分';

        $info = [
            'point_switch' => 0, //是否开启赠送积分 0否 1是
            'point' => 0,
            'point_deduct_switch' => 0, //是否开启积分抵扣 0否 1是
            'max_point_deduct' => 0,
        ];

        $shopSet = \Setting::get('shop.shop');
        
        if (!empty($shopSet['credit1'])) {
            $point_name = $shopSet['credit1'];
        };

        $store_cashier = Store::find($store_id);
        $data = $store_cashier->hasOneCashier->hasOneSale;

        if ((int)$data->max_point_deduct) {
            $info['point_deduct_switch'] = 1;
            $info['max_point_deduct'] = $point_name.'最高抵扣'.$data->max_point_deduct;
        }

        if ((int)$data->point) {
            $info['point_switch'] = 1;
            $info['point'] = '赠送'.$point_name.$data->point;
        }


        return $info;
    }

    /**
     * 获取门店营销爱心值设置
     */
    public static function getLoveSet($goods_id)
    {

        $name = '爱心值';

        $data = [
            'deduction' => 0, //是否开启爱心值抵扣 0否，1是
            'deduction_proportion' => $name.'最高抵扣0%', //爱心值最高抵扣
            'award' => 0, //是否开启爱心值奖励 0否，1是
            'award_proportion' => '赠送'.$name.'0%', //奖励爱心值
        ];

        $exist_love = app('plugins')->isEnabled('love');
        if ($exist_love) {
            $love_name = \Setting::get('love.name');
            if (!empty($love_name)) {
                $name = $love_name;
            }
            if ($goods_id) {
                $item = GoodsLove::ofGoodsId($goods_id)->first();

                $data['deduction'] = $item->deduction;
                $data['deduction_proportion'] = $name.'最高抵扣'.floor($item->deduction_proportion).'%';
                $data['award'] = $item->award;
                $data['award_proportion'] = '赠送'.$name.floor($item->award_proportion).'%';
            }
        }
        return $data;
    }

}