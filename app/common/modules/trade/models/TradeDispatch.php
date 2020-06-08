<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/11/23
 * Time: 5:11 PM
 */

namespace app\common\modules\trade\models;

use app\common\models\BaseModel;
use app\common\models\DispatchType;
use app\frontend\modules\order\models\PreOrder;


class TradeDispatch extends BaseModel
{
    protected $appends = ['delivery_method'];

    /**
     * @var Trade
     */
    private $trade;

    public function init(Trade $trade)
    {
        $this->trade = $trade;
        $this->setRelation('default_member_address', $this->getMemberAddress());
        return $this;
    }

    /**
     * @return mixed
     */
    private function getMemberAddress()
    {
        return $this->trade->orders->first()->orderAddress->getMemberAddress();
    }

    protected function _gteDeliveryMethod()
    {
        $parameter = [];
        $configs = \app\common\modules\shop\ShopConfig::current()->get('shop-foundation.order-delivery-method');
        if ($configs) {
            /**
             * @var PreOrder $firstOrder
             */
            $firstOrder = $this->trade->orders->first();

            $dispatchTypesSetting = $firstOrder->orderGoods->first()->goods->dispatchTypeSetting();

            // 配置过的配送类
            $orderDispatchTypes = [];
            foreach ($configs as $dispatchTypeCode => $dispatchTypeClass) {
                if (!class_exists($dispatchTypeClass)) {
                    break;
                }
                $dispatchTypeSetting = $dispatchTypesSetting[$dispatchTypeCode];
                $orderDispatchType = new $dispatchTypeClass($dispatchTypeSetting);

                if ($orderDispatchType->enable()) {

                    $orderDispatchTypes[] = $orderDispatchType;
                }
            }

            // 排序
            array_sort($orderDispatchTypes, function ($orderDispatchType) {
                return $orderDispatchType->sort();
            });

            // 返回参数
            $parameter = array_map(function ($orderDispatchType) {
                return $orderDispatchType->data();
            }, $orderDispatchTypes);
        }

        return $parameter;
    }

    public function getDeliveryMethodAttribute()
    {
        return $this->_gteDeliveryMethod();
    }


}