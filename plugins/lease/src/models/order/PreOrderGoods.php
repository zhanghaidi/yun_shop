<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/3/7
 * Time: 下午5:19
 */

namespace Yunshop\LeaseToy\models\order;

use app\common\exceptions\AppException;
use Illuminate\Database\Eloquent\Collection;
use app\frontend\models\orderGoods\PreOrderGoodsDiscount;
use Yunshop\LeaseToy\models\LeaseToyGoodsModel;
use Yunshop\LeaseToy\models\orderGoods\NormalOrderGoodsPrice;
use Yunshop\LeaseToy\models\orderGoods\NormalOrderGoodsOptionPrice;
use Yunshop\LeaseToy\models\LeaseTermModel;
use Yunshop\LeaseToy\services\OrderLevelRightsService;
use Yunshop\LeaseToy\models\orderGoods\LeaseToyOrderGoodsModel;
use app\common\models\Member;
use Yunshop\LeaseToy\services\LeaseToyRightsService;

/**
 * Class PreOrderGoods
 * @package Yunshop\StoreCashier\frontend\Order\Models
 * @property Collection orderGoodsExpansions
 * @property CashierGoods cashierGoods
 */
class PreOrderGoods extends \app\frontend\modules\orderGoods\models\PreOrderGoods
{
    //等级免单
    protected $lease_rights;
    //租期
    public $lease_term;

    public $lease_goods;

    public $days;
    
    public function __construct(array $attributes = [])
    {
        $this->lease_rights = json_decode(request()->input('lease_rights'),true);
        $this->lease_term = json_decode(request()->input('lease_term'),true);
        parent::__construct($attributes);

//        $this->leaseToyGoods();
        // $this->hasOneLeaseGoods;
        $this->setRelation('orderLeaseGoods', $this->newCollection());
        //订单商品计算完才赋值
        $this->orderLeaseToyGoodsFree();
        
    }

    public function leaseToydays()
    {
        $days = [
            'days' => 1,
            'lease_term_id' => 0,
            'term_discount' => 0,
        ];

        //是否有使用权益，是就按会员有效期当租期
        if (!empty($this->lease_rights) && is_array($this->lease_rights)) {
            //会员等级有效期
            $level_validity = Member::select('uid')->with(['yzMember' => function ($query) {
                return $query->select(['member_id', 'validity'])->uniacid();
            }])->find(\YunShop::app()->getMemberId());
            $this->lease_term['days'] = $level_validity->yzMember->validity;
        }

        if (!empty($this->lease_term['lease_term_id'])) {

            $term = LeaseTermModel::find($this->lease_term['lease_term_id']);
            $days = [
                'lease_term_id' => $term->id,
                'days' => $term->term_days,
                'term_discount' => (float)$term->term_discount,
            ];
        } elseif (!empty($this->lease_term['days'])) {

            $term = LeaseTermModel::getDays($this->lease_term['days']);
            $days = [
                'lease_term_id' => 0,
                'days' => $this->lease_term['days'],
                'term_discount' => (float)$term['term_discount'],
            ];
        }
        return $this->days = $days;
    }

     /**
     * 订单扩展模型
     * 
     */
    public function leaseToyGoods()
    {
        $leaseToyGoods = [
            'rent_free' => [],
            'deposit_free' => [],
        ];

        if (!empty($this->lease_rights) && is_array($this->lease_rights)) {
            $this->lease_rights =  OrderLevelRightsService::getSupportRightsGoods($this->lease_rights);
            
            //等级权益
            $level = LeaseToyRightsService::getMemberRights(\Yunshop::app()->getMemberId());
            //使用权益商品总数
            $goodsTotal = array_sum(array_map(function($total){
                return $total['total'];
            }, $this->lease_rights));
            //等级权益免租金
            if ($goodsTotal > $level['rent_free']) {
                $leaseToyGoods['rent_free'] = $this->getRentFree($level['rent_free']);
            } else {
                foreach ($this->lease_rights as $value) {
                    if ($value['goods_id'] == $this->goods_id) {
                        $leaseToyGoods['rent_free'] = ['goods_id' => $this->goods_id, 'free_num'=> $value['total']];
                        break;
                    }
                }
            }

            //等级权益免押金
            if ($goodsTotal > $level['deposit_free']) {
                $leaseToyGoods['deposit_free'] = $this->getDepositFree($level['deposit_free']);
            } else {
                foreach ($this->lease_rights as $value) {
                    if ($value['goods_id'] == $this->goods_id) {
                        $leaseToyGoods['deposit_free'] = ['goods_id' => $this->goods_id, 'free_num'=> $value['total']];
                        break;
                    }
                }
            }

            //会员等级有效期
            /*$level_validity = Member::select('uid')->with(['yzMember' => function ($query) {
                return $query->select(['member_id','validity'])->uniacid();
            }])->find(\YunShop::app()->getMemberId());
            $this->lease_term['days'] = $level_validity->yzMember->validity;*/

        }
        //单独提出，该判断必须在商品价格计算出来前执行
        /*if (!empty($this->lease_term['lease_term_id'])) {

            $term = LeaseTermModel::find($this->lease_term['lease_term_id']);
            $leaseToyGoods['lease_term'] = [
                    'lease_term_id' => $term->id,
                    'days' => $term->term_days,
                    'term_discount' => (float)$term->term_discount,
                ];
        } elseif (!empty($this->lease_term['days'])) {

            $term = LeaseTermModel::getDays($this->lease_term['days']);
            $leaseToyGoods['lease_term'] = [
                    'lease_term_id' => 0,
                    'days' => $this->lease_term['days'],
                    'term_discount' => (float)$term['term_discount'],
                ];
        }*/
        $this->lease_goods = $leaseToyGoods;
    }

    //租金
    public function getRentFree($rentFree = 0)
    {
        $goodsRentRights = OrderLevelRightsService::getGoodsPriceSort($this->lease_rights, $rentFree);
        foreach ($goodsRentRights as $value) {
            if ($value['goods_id'] == $this->goods_id) {
                return $value;
            }
        }
        return ['goods_id' => $this->goods_id, 'free_num'=> 0];
    }

    //押金
    private function getDepositFree($depositFree = 0)
    {
        $goodsDepositRights = OrderLevelRightsService::getGoodsDepositSort($this->lease_rights, $depositFree);
        foreach ($goodsDepositRights as $value) {
            if ($value['goods_id'] == $this->goods_id) {
                return $value;
            }
        }
        return ['goods_id' => $this->goods_id, 'free_num'=> 0];
    }


    //商品优惠后单价与是否免权益
    private function orderLeaseToyGoodsFree()
    {   
        $preLeaseToyOrderGoods = new LeaseToyOrderGoodsModel([
            'goods_id' => $this->goods_id,
            'deposit' => $this->hasOneLeaseGoods->goods_deposit,
            'free_deposit' => $this->getLeaseGoodsDeposit(),
            'lease_price' => $this->getDiscountGold(),
            'lease_total' => $this->total,
            'return_days' => $this->lease_goods['lease_term']['days'],
            'lease_rent_free' => empty($this->lease_goods['rent_free']) ? 0: $this->lease_goods['rent_free']['free_num'],
            'lease_deposit_free' => empty($this->lease_goods['deposit_free']) ? 0: $this->lease_goods['deposit_free']['free_num'],
        ]);
        $preLeaseToyOrderGoods->setOrderGoods($this);

    }
// lease_rights=[{"goods_id":"100","total":"2"}]&lease_term = {"days":"40","lease_term_id":"1"}

    /**
     * 设置价格计算者
     */
    public function setPriceCalculator()
    {
        if ($this->isOption()) {
            $this->priceCalculator = new NormalOrderGoodsOptionPrice($this);

        } else {
            $this->priceCalculator = new NormalOrderGoodsPrice($this);

        }

    }

    /**
     * 设置价格计算者
     */
    public function _getPriceCalculator()
    {
        if ($this->isOption()) {
            $priceCalculator = new NormalOrderGoodsOptionPrice($this);

        } else {
            $priceCalculator = new NormalOrderGoodsPrice($this);
        }
        return $priceCalculator;
    }

    /**
     * 获取价格计算者
     * @return NormalOrderGoodsPrice
     */
    protected function getPriceCalculator()
    {
        if (!isset($this->priceCalculator)) {
            $this->priceCalculator = $this->_getPriceCalculator();
        }
        return $this->priceCalculator;
    }

    //是否是权益商品
    public function rightsGoods()
    {
        return $this->hasOneLeaseGoods->is_rights;
    }

    //获取优惠后的单价
    public function getDiscountGold()
    {
        $goods_price = $this->getPriceCalculator()->discountGold();

        return sprintf('%.2f',$goods_price);
    }

    //获取单个商品押金
    public function getLeaseGoodsDeposit()
    {
        return $this->getPriceCalculator()->getGoodsDeposit();
    }


    public function hasOneLeaseGoods()
    {
        return $this->hasOne('Yunshop\LeaseToy\models\LeaseToyGoodsModel', 'goods_id', 'goods_id');
    }

    public function orderLeaseGoods()
    {
        return $this->hasOne('Yunshop\LeaseToy\models\orderGoods\LeaseToyOrderGoodsModel', 'order_goods_id', 'id');
    }
}