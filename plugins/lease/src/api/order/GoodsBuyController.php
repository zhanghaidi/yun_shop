<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/4/9
 * Time: 下午3:28
 */

namespace Yunshop\LeaseToy\api\order;

use app\common\components\ApiController;
use Yunshop\LeaseToy\models\order\Goods;
use Yunshop\LeaseToy\models\order\PreOrder;
use Yunshop\LeaseToy\models\order\PreOrderGoods;
use Yunshop\LeaseToy\models\order\GoodsOption;
use app\frontend\modules\member\services\MemberCartService;

class GoodsBuyController extends ApiController
{

    protected $goods_buy;

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

        // app('OrderManager')->bind('MemberCart', function ($orderManager, $attributes) {
        //     return new MemberCart($attributes);
        // });
        //$this->goods_buy = new \app\frontend\modules\order\controllers\GoodsBuyController;

        parent::__construct();
    }

    /**
     * @return \Illuminate\Support\Collection
     * @throws \app\common\exceptions\AppException
     */
    protected function getMemberCarts()
    {

        $goods_params = [
            'goods_id' => request()->input('goods_id'),
            'total' => request()->input('total'),
            'option_id' => request()->input('option_id'),
        ];

        $result = collect();
        $result->push(MemberCartService::newMemberCart($goods_params));
        return $result;

    }

    /**
     * @throws \app\common\exceptions\ShopException
     */
    protected function validateParam(){

        $this->validate([
            'goods_id' => 'required|integer',
            'options_id' => 'integer',
            'total' => 'integer|min:1',
        ]);

    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \app\common\exceptions\AppException
     * @throws \app\common\exceptions\ShopException
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
}