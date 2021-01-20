<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/3/7
 * Time: 下午5:19
 */

namespace Yunshop\LeaseToy\api\order;

use app\common\components\ApiController;
use app\frontend\modules\memberCart\MemberCartCollection;
use Yunshop\LeaseToy\models\MemberCart;
use Yunshop\LeaseToy\models\order\Goods;
use Yunshop\LeaseToy\models\order\PreOrder;
use Yunshop\LeaseToy\models\order\PreOrderGoods;
use Yunshop\LeaseToy\models\order\GoodsOption;
use app\common\exceptions\AppException;

class CartBuyController extends ApiController
{
    public function __construct()
    {
        app('GoodsManager')->bind('Goods', function ($goodsManager, $attributes) {
            return new Goods($attributes);
        });
        app('GoodsManager')->bind('GoodsOption', function ($goodsManager, $attributes) {
            return new GoodsOption($attributes);
        });
        app('OrderManager')->bind('PreOrder', function ($orderManager, $attributes) {
            return new PreOrder($attributes);
        });

        app('OrderManager')->bind('PreOrderGoods', function ($orderManager, $attributes) {
            return new PreOrderGoods($attributes);
        });
        app('OrderManager')->bind('MemberCart', function ($orderManager, $attributes) {
            return new MemberCart($attributes);
        });
        parent::__construct();
    }

    /**
     * @return mixed
     * @throws AppException
     */
    public function index()
    {
        $this->validateParam();
        $trade = $this->getMemberCarts()->getTrade();
        $total_deposit = $trade->orders->sum(function($lease_order) {
            if ($lease_order['order']['has_one_lease_order']) {
                return $lease_order['order']['has_one_lease_order'][0]['deposit_total'];
            }
            return 0;
        });
        $trade['total_deposit'] = $total_deposit;
        return $this->successJson('成功', $trade);

    }
    protected function validateParam(){
        $this->validate([
            'cart_ids' => 'required',
        ]);
    }

    /**
     * 从url中获取购物车记录并验证
     * @return MemberCartCollection|mixed
     * @throws AppException
     */
    protected function getMemberCarts()
    {
        static $memberCarts;
        $cartIds = [];
        if (!is_array($_GET['cart_ids'])) {
            $cartIds = explode(',', $_GET['cart_ids']);
        }

        if (!count($cartIds)) {
            throw new AppException('参数格式有误');
        }
        if(!isset($memberCarts)){
            $memberCarts = app('OrderManager')->make('MemberCart')->whereIn('id', $cartIds)->get();
            $memberCarts = new MemberCartCollection($memberCarts);
            $memberCarts->loadRelations();
        }

        $memberCarts->validate();
        if ($memberCarts->isEmpty()) {
            throw new AppException('未找到购物车信息');
        }

        if ($memberCarts->isEmpty()) {

            throw new AppException('请选择下单商品');
        }
        return $memberCarts;
    }
}