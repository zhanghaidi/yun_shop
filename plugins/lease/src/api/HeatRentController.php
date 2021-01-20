<?php
/**
 *  Author: 芸众商城 www.yunzshop.com
 *  Date: 2018/4/2
 */

namespace Yunshop\LeaseToy\api;

use app\common\components\ApiController;
use Yunshop\LeaseToy\models\Order;
use Yunshop\LeaseToy\models\Goods;

class HeatRentController extends ApiController
{
    
    public function index()
    {
        $orders = Order::pluginId()->where('created_at', '<',  strtotime("-1 day"))->with('hasManyOrderGoods')->limit(10)->get();

        $whereIds = $this->getGoodsIds($orders);

        $goods = Goods::pluginId()->whereIn('id', $whereIds)->get();

        foreach ($goods as &$value) {
            if (isset($value)) {
                $value->thumb = yz_tomedia($value->thumb);
            }
        }

        $this->successJson('ok', $goods);
    }

    public function getGoodsIds($orders)
    {
        $goodsIds = [];
        foreach ($orders as $order) {
            $goodsId = $order->hasManyOrderGoods->implode('goods_id', ',');
            $goodsIds =  array_merge($goodsIds, explode(',', $goodsId));
        }
        return array_unique($goodsIds);

    }
}