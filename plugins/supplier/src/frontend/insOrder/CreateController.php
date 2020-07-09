<?php
/**
 * Created by PhpStorm.
 * User: weifeng
 * Date: 2020-03-20
 * Time: 18:06
 *
 *    .--,       .--,
 *   ( (  \.---./  ) )
 *    '.__/o   o\__.'
 *       {=  ^  =}
 *        >  -  <
 *       /       \
 *      //       \\
 *     //|   .   |\\
 *     "'\       /'"_.-~^`'-.
 *        \  _  /--'         `
 *      ___)( )(___
 *     (((__) (__)))     梦之所想,心之所向.
 */

namespace Yunshop\Supplier\frontend\insOrder;


use app\common\exceptions\ShopException;
use app\frontend\models\Member;
use app\frontend\modules\member\services\MemberCartService;
use app\frontend\modules\memberCart\MemberCartCollection;
use Yunshop\Supplier\frontend\insOrder\order\Goods;
use Yunshop\Supplier\frontend\insOrder\order\PreOrder;
use Yunshop\Supplier\frontend\insOrder\order\PreOrderGoods;

class CreateController extends \app\frontend\modules\order\controllers\CreateController
{
    public function __construct()
    {
        app('GoodsManager')->bind('Goods', function ($goodsManager, $attributes) {
            return new Goods($attributes);
        });

        app('OrderManager')->bind('PreOrder', function ($orderManager, $attributes) {
            return new PreOrder($attributes);
        });

        app('OrderManager')->bind('PreOrderGoods', function ($orderManager, $attributes) {
            return new PreOrderGoods($attributes);
        });

        parent::__construct();
    }

    protected function getMemberCarts()
    {
        $goods = \Yunshop\Supplier\frontend\insOrder\Goods::where(['plugin_id' => 93])->first();
        if (!$goods) {
            $goods_data = [
                'uniacid' => \YunShop::app()->uniacid,
                'brand_id' => 0,
                'type' => 2,
                'status' => 1,
                'display_order' => 0,
                'title' => '供应商保单',
                'thumb' => '',
                'sku' => '个',
                'price' => 1,
                'market_price' => 1,
                'cost_price' => 1,
                'stock' => 9999999,
                'is_plugin' => 0,
                'plugin_id' => 93,
            ];

            $goods = \app\frontend\models\Goods::create($goods_data);

            \Yunshop\Supplier\common\models\InsuranceGoods::create([
                'goods_id' => $goods->id,
            ]);
        }

        if (empty($goods)) {
            throw new ShopException('保单订单信息数据为空');
        }

        $goods_id = $goods->id;

        $goodsParams = [
            'goods_id' => $goods_id,
        ];

        $result = new MemberCartCollection();
        $result->push(MemberCartService::newMemberCart($goodsParams));

        return $result;
    }
/*
    public function index()
    {
        if (request()->type == 9) {
            $member_id = \Yunshop::request()->buy_id;
            if ($member_id) {
                $member = Member::find($member_id);
            } else {
                $member = (new Member(['uid' => 0]));
            }
            $trade = $this->getMemberCarts()->getTrade($member)->toArray();
        } else {
            $trade = $this->getMemberCarts()->getTrade()->toArray();
        }

        foreach ($trade['orders'] as $order) {
            foreach ($order['order_deductions'] as $deductions) {
                $trade['order_deductions'][] = $deductions;
            }
        }
        foreach ($trade['amount_items'] as $key => $amount_item) {
            if (in_array($amount_item['code'], ['total_goods_price', 'total_dispatch_price'])) {
                unset($trade['amount_items'][$key]);
            }
        }
        $trade['amount_items'] = array_values($trade['amount_items']);
dd($trade);
        return $this->successJson('成功', $trade);
    }
*/
}