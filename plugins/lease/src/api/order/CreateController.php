<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/3/8
 * Time: 11:44
 */
namespace Yunshop\LeaseToy\api\order;

use Yunshop\LeaseToy\models\MemberCart;
use Yunshop\LeaseToy\models\order\Goods;
use Yunshop\LeaseToy\models\order\PreOrder;
use Yunshop\LeaseToy\models\order\PreOrderGoods;

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
        app('OrderManager')->bind('MemberCart', function ($orderManager, $attributes) {
            return new MemberCart($attributes);
        });
        parent::__construct();
    }

}

