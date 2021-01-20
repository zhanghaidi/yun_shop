<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/3/6
 * Time: 14:22
 */

namespace Yunshop\LeaseToy\models\order;

use app\common\exceptions\AppException;
use Yunshop\LeaseToy\models\LeaseOrderModel;
use Yunshop\LeaseToy\models\order\PreOrderGoods;

class PreOrder extends \app\frontend\modules\order\models\PreOrder
{

    protected $attributes = [
        'plugin_id' => LeaseOrderModel::PLUGIN_ID,
    ];

    public function beforeCreating()
    {
        parent::beforeCreating();

        $this->setRelation('hasOneLeaseOrder',$this->newCollection());
    }


      /**
     * 订单插入数据库,触发订单生成事件
     * 判断租金是否大于起租金
     * @return mixed
     * @throws AppException
     */
    public function generate()
    {
        $arr = json_decode(request()->input('lease_rights'),true);
        $set = \Setting::get('plugin.lease_toy');
        if (!isset($arr) || empty($arr)) {
            if ($this->order_goods_price < $set['min_money']) {
                throw new AppException('订单总租金小于('. $set['min_money'].'￥)起租金额不能下单');
            }
        }elseif ($this->haveRightsGoods()) {
            if ($this->order_goods_price < $set['min_money']) {
                throw new AppException('订单总租金小于('. $set['min_money'].'￥)起租金额不能下单');
            }
        }

        return parent::generate();
    }

    //该订单是否拥有权益商品
    protected function haveRightsGoods()
    {
        $result = $this->orderGoods->sum(function ($aOrderGoods) {
            return $aOrderGoods->rightsGoods();
        });

        return ($result > 0) ? false : true;
    }


    protected function getPrice()
    {

        $price = parent::getPrice();
        //'订单最终金额' + '押金总和'
        $this->price = $price + $this->getDeposit();
        return $this->price;
        
        // if(isset($this->price)){
        //     return $this->price;
        // }

        // //订单最终价格 = 商品最终价格 - 订单优惠 - 订单抵扣 + 订单运费
        // $this->price = max($this->getOrderGoodsPrice() - $this->getDiscountAmount() + $this->getDispatchPrice(), 0);
        // $this->price = $this->price - $this->getDeductionPrice();
        // return $this->price;
    }

    public function afterCreating()
    {
        parent::afterCreating();

        $preLeaseOrder = new LeaseOrderModel([
            'member_id' => $this->uid,
            'order_sn' => $this->order_sn,
            'uniacid' => $this->uniacid,
           'deposit_total' => $this->getDeposit(),
           'return_days' => $this->orderGoods->first()->days['days'],
        ]);
        $preLeaseOrder->setOrder($this);
    }

    /**
     * 获取订单押金总和
     * @return [type] [description]
     */
    protected function getDeposit()
    {
        $result = $this->orderGoods->sum(function ($aOrderGoods) {
            return $aOrderGoods->getLeaseGoodsDeposit();
        });

        return $result;
    }

    public function hasOneLeaseOrder()
    {
        return $this->hasOne('Yunshop\LeaseToy\models\LeaseOrderModel', 'order_id', 'order_id');
    }
}