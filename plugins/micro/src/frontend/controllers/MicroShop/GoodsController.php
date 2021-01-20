<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/6/2
 * Time: 下午5:21
 */

namespace Yunshop\Micro\frontend\controllers\MicroShop;


use app\common\components\ApiController;
use app\common\exceptions\AppException;
use app\common\models\Goods;
use Yunshop\Micro\common\models\GoodsMicro;
use Yunshop\Micro\common\models\MicroShop;
use Yunshop\Micro\common\models\MicroShopGoods;
use Illuminate\Support\Facades\DB;

class GoodsController extends ApiController
{
    public function searchGoods()
    {
        $micro_shop = MicroShop::getMicroShopByMemberId(\YunShop::app()->getMemberId());
        $requestSearch = \YunShop::request()->search;

        $order_field = \YunShop::request()->order_field;
        if (!in_array($order_field, ['price', 'show_sales', 'comment_num'])){
            $order_field = 'display_order';
        }
        $order_by = (\YunShop::request()->order_by == 'asc') ? 'asc' : 'desc';

        if ($requestSearch) {
            $requestSearch = array_filter($requestSearch, function ($item) {
                return !empty($item) && $item !== 0;
            });

            $categorySearch = array_filter(\YunShop::request()->category, function ($item) {
                return !empty($item);
            });

            if ($categorySearch) {
                $requestSearch['category'] = $categorySearch;
            }
        }
        $list = Goods::Search($requestSearch)->select('*', 'yz_goods.id as goods_id')
            ->where("status", 1)
            ->orderBy($order_field, $order_by)
            ->paginate(20);
        if (empty($list)) {
            $this->errorJson('没有找到商品.');
        }
        $micro_set = \Setting::get('plugin.micro');
        $permission = [
            'set'   => $micro_set,
            'micro' => $micro_shop
        ];
        $list->map(function($goods)use($permission){
            $bonus_money = 0;
            $goods_model = GoodsMicro::getGoodsMicro($goods->goods_id)->first();
            $goods_set = unserialize($goods_model->set);
            if ($permission['set']['is_open_bonus'] == 1) {
                if ($goods_model) {
                    if ($goods_model->is_open_bonus == 1) {
                        $bonus_money = number_format($goods->price * $permission['micro']->hasOneMicroShopLevel->bonus_ratio / 100, 2);
                        if ($goods_model->independent_bonus == 1) {
                            if (!empty($goods_set) && isset($goods_set[$permission['micro']->hasOneMicroShopLevel->id])) {
                                $bonus_money = number_format($goods->price * $goods_set[$permission['micro']->hasOneMicroShopLevel->id] / 100, 2);
                            }
                        }
                    }
                }
            }
            $goods->thumb = yz_tomedia($goods->thumb);
            return $goods->bonus_money = $bonus_money;
        });
        $list = $list->toArray();
        return $this->successJson('成功', $list);
    }

    public function getGoods()
    {
        /*plugin.micro.frontend.controllers.MicroShop.goods.getGoods
        plugin.micro.frontend.controllers.micro-shop.goods.get-goods*/
        $shop_id = intval(request()->shop_id);
        $page = intval(request()->page);
        if (!$page) {
            $page = 1;
        }
        if (!$shop_id) {
            throw new AppException('shop_id不能为空');
        }
        $micro_shop = MicroShop::getMicroShopById($shop_id);
        if (!$micro_shop) {
            throw new AppException('未找到微店');
        }
        $goods_list = MicroShopGoods::getGoodsByMemberId($micro_shop->member_id);
        $goods_ids = $goods_list->map(function($goods){
            return $goods->goods_id;
        });
        $field = ['id as goods_id', 'thumb', 'title', 'price', 'market_price'];
        $goods_builder = Goods::uniacid()->select(DB::raw(implode(',', $field)))
            ->where("status", 1)
            ->whereIn('id', $goods_ids->toArray());
        $goodsList = $goods_builder->paginate(20, '', '', $page);

        $goods_count = $goods_builder->count();

        if ($goods_count > 0) {
            $list = $goodsList->toArray();
            $data = collect($list['data'])->map(function ($rows) {
                return collect($rows)->map(function ($item, $key) {
                    if ($key == 'thumb' && preg_match('/^images/', $item)) {
                        return replace_yunshop(yz_tomedia($item));
                    } else {
                        return $item;
                    }
                });
            })->toArray();
        }
        $page_count = ceil($goods_count / 2);
        return $this->successJson('获取商品列表成功', [
            'goods_list' => $data,
            'goods_count' => $goods_count,
            'page_count' => $page_count,
            'page' => $page
        ]);
    }
}