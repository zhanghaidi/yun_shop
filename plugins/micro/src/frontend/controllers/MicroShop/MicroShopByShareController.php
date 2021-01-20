<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/23
 * Time: 下午4:39
 */

namespace Yunshop\Micro\frontend\controllers\MicroShop;


use app\common\components\ApiController;
use Setting;
use Yunshop\Micro\common\models\MicroShop;
use Yunshop\Micro\common\models\MicroShopCarousel;
use Yunshop\Micro\common\models\MicroShopGoods;
use app\common\models\Goods;
use Illuminate\Support\Facades\DB;
use app\frontend\modules\goods\models\Category;

class MicroShopByShareController extends ApiController
{
    protected $publicAction = ['index'];
    protected $ignoreAction = ['index'];
    private $member_id;

    public function index()
    {
        $shop_id = \YunShop::request()->shop_id;
        if (!$shop_id) {
            return $this->errorJson('shop_id参数不能为空', [
                'status'    => -1
            ]);
        }
        $micro_shop = MicroShop::getMicroShopById($shop_id);

        if (!$micro_shop) {
            return $this->errorJson('未找到此微店', [
                'status'    => -1
            ]);
        }
        $shop_id = $micro_shop->id;
        $this->member_id = $micro_shop->member_id;
        $set = Setting::get('shop.category');
        $set['cat_adv_img'] = replace_yunshop(yz_tomedia($set['cat_adv_img']));

        $data = [
            'ads' => $this->getAds(),
            'carousel' => $this->getCarousel(),
            'category' => $this->getCategoryList(),
            'set' => $set,
            'goods' => $this->getGoods()['goods_list'],
            'goods_count' => $this->getGoods()['goods_count'],
            //'shop_avatar' => $micro_shop->shop_avatar, // todo 先用会员的头像
            'shop_avatar' => $micro_shop->hasOneMember->avatar,
            'shop_background' => $micro_shop->shop_background,
            'nickname' => $micro_shop->hasOneMember->nickname,
            'signature' => $micro_shop->signature,
            'shop_logo' => replace_yunshop(yz_tomedia(\Setting::get('shop')['logo'])),
            'shop_name' => \Setting::get('shop')['name'],
            'shop_id'   => $shop_id,
            'is_micro_shop' => MicroShop::getMicroShopByMemberId(\YunShop::app()->getMemberId()) ? true : false,
        ];
        return $this->successJson('成功', $data);
    }

    private function getGoods()
    {
        $goods_list = MicroShopGoods::getGoodsByMemberId($this->member_id);
        $goods_ids = $goods_list->map(function($goods){
            return $goods->goods_id;
        });
        $field = ['id as goods_id', 'thumb', 'title', 'price', 'market_price'];
        $goods_builder = Goods::uniacid()->select(DB::raw(implode(',', $field)))
            ->where("status", 1)
            ->whereIn('id', $goods_ids->toArray());
        $goodsList = $goods_builder->limit('20')->get();
        if (!$goodsList->isEmpty()) {
            foreach ($goodsList = $goodsList->toArray() as &$value) {
                $value['thumb'] = replace_yunshop(yz_tomedia($value['thumb']));
            }
        }
        $goods_count = $goods_builder->count();
        return [
            'goods_list' => $goodsList,
            'goods_count' => $goods_count
        ];
    }

    private function getCategoryList()
    {
        $category_data = $this->getCaregoryData();
        if (!$category_data) {
            return [];
        }
        //$category_ids = array_merge($category_data['parent_ids'], $category_data['child_ids']);
        $request = Category::getRecommentCategoryList()
            ->whereIn('id', $category_data['parent_ids'])
            ->get();
        foreach ($request as &$item) {
            $item['thumb'] = replace_yunshop(yz_tomedia($item['thumb']));
            $item['adv_img'] = replace_yunshop(yz_tomedia($item['adv_img']));
        }

        return $request;
    }

    private function getAds()
    {
        $ads = MicroShopCarousel::getSlidesIsEnabled()->isCarousel()->get();
        if($ads){
            $ads = $ads->toArray();
            foreach ($ads as &$item)
            {
                $item['thumb'] = replace_yunshop(yz_tomedia($item['thumb']));
            }
        }
        return $ads;
    }

    private function getCarousel()
    {
        $slide = MicroShopCarousel::getSlidesIsEnabled()->isCarousel(1)->get();
        if($slide){
            $slide = $slide->toArray();
            foreach ($slide as &$item)
            {
                $item['thumb'] = replace_yunshop(yz_tomedia($item['thumb']));
            }
        }
        return $slide;
    }

    private function getCaregoryData()
    {
        $goods_list = MicroShopGoods::getGoodsByMemberId($this->member_id);
        if (!$goods_list->isEmpty()) {
            $category_ids = $goods_list->map(function ($item){
                //return $item->hasOneGoods->belongsToCategorys->first()->category_ids;
                if ($item->hasOneGoods) {
                    $cayegorys = $item->hasOneGoods->belongsToCategorys;
                    if (!$cayegorys->isEmpty()) {
                        return $item->hasOneGoods->belongsToCategorys->first()->category_ids;
                    }
                }
            });
            if (!$category_ids->isEmpty()) {
                $parent_ids = '';
                //$child_ids = '';
                foreach ($category_ids->toArray() as $id) {
                    $ids = explode(',', $id);
                    foreach ($ids as $key => $v) {
                        if ($key == 0) {
                            if (empty($parent_ids)) {
                                $parent_ids .= $v;
                            } else {
                                $parent_ids .= ',' . $v;
                            }
                        }
                        /*else {
                            if (empty($child_ids)) {
                                $child_ids .= $v;
                            } else {
                                $child_ids .= ',' . $v;
                            }
                        }*/
                    }
                }
                $parent_ids = array_unique(explode(',', $parent_ids));
                //$child_ids = array_unique(explode(',', $child_ids));
                return [
                    'parent_ids'    => $parent_ids,
                    //'child_ids'     => $child_ids
                ];
            }/* else {
                return $this->errorJson('未获取到分类', [
                    'status'    => -1
                ]);
            }*/
        }/* else {
            return $this->errorJson('未获取到分类', [
                'status'    => -1
            ]);
        }*/
    }
}