<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/3/28
 * Time: 15:32
 */

namespace Yunshop\GroupPurchase\admin;

use app\frontend\modules\member\services\MemberCartService;
use Yunshop\GroupPurchase\models\Goods;
use Yunshop\GroupPurchase\models\GroupPurchase;
use Yunshop\GroupPurchase\models\PreOrder;
use Yunshop\GroupPurchase\models\PreOrderGoods;
use app\frontend\modules\order\controllers\CreateController;

class CreatedController extends \app\frontend\modules\order\controllers\CreateController
{
    public function __construct()
    {
//        app('GoodsManager')->bind('Goods', function ($goodsManager, $attributes) {
//            return new Goods($attributes);
//        });
//
//        app('OrderManager')->bind('PreOrder', function ($orderManager, $attributes) {
//            return new PreOrder($attributes);
//        });
//
//        app('OrderManager')->bind('PreOrderGoods', function ($orderManager, $attributes) {
//            return new PreOrderGoods($attributes);
//        });
        parent::__construct();

    }

    protected function getMemberCarts()
    {
        $goodsId = GroupPurchase::getGoodsId();
        if(!isset($goodsId)){
            return '请配置拼团设置信息';
        }
        $goodsParams = [
            'goods_id' => $goodsId,
        ];
        $result = collect();
        $result->push(MemberCartService::newMemberCart($goodsParams));
        return $result;
    }
}