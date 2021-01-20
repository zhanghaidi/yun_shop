<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/25
 * Time: 上午11:39
 */

namespace Yunshop\Micro\frontend\controllers\MicroShop;


use app\common\components\ApiController;
use app\common\models\Goods;
use app\common\requests\Request;
use Yunshop\Micro\common\models\MicroShopGoods;

class SearchGoodsController extends ApiController
{
    public function index(Request $request)
    {
        if (empty($request->shop_id)) {
            return $this->errorJson('shop_id错误', [
                'status'    => -1
            ]);
        }
        $micro_goods = MicroShopGoods::uniacid()->where('shop_id', $request->shop_id)->get();
        if ($micro_goods->isEmpty()) {
            return $this->errorJson('未找到商品', [
                'status'    => -1
            ]);
        }
        $goods_ids = $micro_goods->pluck('goods_id');
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
            ->whereIn('yz_goods.id', $goods_ids)
            ->orderBy($order_field, $order_by)
            ->paginate(20)->toArray();

        if ($list['total'] > 0) {
            $data = collect($list['data'])->map(function($rows) {
                return collect($rows)->map(function($item, $key) {
                    if ($key == 'thumb' && preg_match('/^images/', $item)) {
                        return replace_yunshop(yz_tomedia($item));
                    } else {
                        return $item;
                    }
                });
            })->toArray();

            $list['data'] = $data;
        }

        return $this->successJson('成功', $list);
    }
}