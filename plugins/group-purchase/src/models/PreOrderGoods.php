<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/3/28
 * Time: 15:46
 */

namespace Yunshop\GroupPurchase\models;

use Illuminate\Database\Eloquent\Collection;
use Yunshop\StoreCashier\common\models\CashierGoods;

/**
 * Class PreOrderGoods
 * @package Yunshop\StoreCashier\frontend\Order\Models
 * @property Collection orderGoodsExpansions
 * @property CashierGoods cashierGoods
 */
class PreOrderGoods extends \app\frontend\modules\orderGoods\models\PreOrderGoods
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setRelation('orderGoodsExpansions', $this->newCollection());
        $this->pushExpansions();
    }

    /**
     * 订单扩展模型
     */
    private function pushExpansions()
    {
        $orderGoodsExpansions = [];

        $profit = unserialize($this->cashierGoods->profit)?:[];
        if (isset($profit['full-return']['is_open'])) {
            // 订单满额返现开启关闭
            $orderGoodsExpansions[] = [
                'key' => 'full_return_is_open',
                'value' => $profit['full-return']['is_open'],
                'plugin_code' => 'full-return'
            ];
        }
        array_map(function ($orderGoodsExpansion) {
            $orderGoodsExpansionObj = new PreOrderGoodsExpansion($orderGoodsExpansion);
            $this->orderGoodsExpansions->push($orderGoodsExpansionObj);
        }, $orderGoodsExpansions);

    }
}