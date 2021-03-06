<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/26
 * Time: 10:38
 */

namespace Yunshop\JdSupply\frontend\order;


use app\common\exceptions\AppException;
use app\common\models\Member;
use app\common\modules\orderGoods\OrderGoodsCollection;
use Illuminate\Http\Request;
use Yunshop\JdSupply\models\Goods;
use Yunshop\JdSupply\models\JdGoods;
use Yunshop\JdSupply\models\JdGoodsOption;
use Yunshop\JdSupply\models\JdSupplyOrder;
use Yunshop\JdSupply\services\JdGoodsService;
use Yunshop\JdSupply\services\JdOrderValidate;


class PreOrder extends \app\frontend\modules\order\models\PreOrder
{
    public $jd_order_goods;

    public $jd_request;

    public $set;

    public $source;

    public $freight;

    protected $attributes = [
        'plugin_id' => JdSupplyOrder::PLUGIN_ID,
    ];


    /**
     * @param Member $member
     * @param OrderGoodsCollection $orderGoods
     * @param Request|null $request
     * @return $this|void
     * @throws \app\common\exceptions\ShopException
     */
    public function init(Member $member, OrderGoodsCollection $orderGoods,$request = null)
    {
        $this->jd_order_goods = $orderGoods;
        $jd_goods = JdGoods::where('goods_id',$orderGoods[0]->goods_id)->first();
        $this->source = $jd_goods->source;
        $this->set = \Setting::get('plugin.jd_supply');
        parent::init($member, $orderGoods, $request); // TODO: Change the autogenerated stub
    }

    public function beforeCreating()
    {
        parent::beforeCreating();
        //订单验证
        if ($this->orderAddress->mobile || $this->orderAddress->province_id) {
            $this->jd_request = JdOrderValidate::orderValidate($this);
            foreach ($this->jd_request['param']['data'] as $data) {
                $this->freight += $data['freight'];
            }
        }

    }

    public function afterCreating()
    {
        parent::afterCreating();

        $this->setRelation('jdSupplyOrder', new PreJdSupplyOrder());

        $a = $this->jdSupplyOrderGoods();
        $this->setRelation('jdSupplyOrderGoods', $a);

    }

    protected function initAttributes()
    {
        parent::initAttributes();
        $this->jdSupplyOrder->initAttributes($this);
    }

    protected function jdSupplyOrderGoods()
    {

        $result = $this->orderGoods->map(function ($item) {
            $jd_goods_option = JdGoodsOption::getJdGoods($item->goods_id, $item->goods_option_id)->first();
            if (!$jd_goods_option) {
                throw new AppException("商品不存在");
            }

            //风控策略
            $flag = JdGoodsService::controlMethod($item->goodsOption->product_price,$item->goodsOption->cost_price,$item->goods);
            if (!$flag) {
                throw new AppException("风控策略");
            }
            $data = [
                'goods_id' => (int)$item->goods_id,
                'jd_goods_id' => $jd_goods_option->jd_goods_id,
                'jd_option_id' => $jd_goods_option->jd_option_id,
                'total' => (int)$item->total,
            ];

            $orderGoods = new PreJdSupplyOrderGoods($data);

            return $orderGoods;
        });

        return new JdOrderGoodsCollection($result);
    }

    public function getOrderDispatch()
    {

        if (!isset($this->orderDispatch)) {
            $this->orderDispatch = new JdOrderDispatch($this);
        }
        return $this->orderDispatch;
    }


    protected function getShopName()
    {
        return $this->set['shop_name'] ? :'聚合供应链';
    }

    /**
     * 运费
     * @return float|int|number
     */
    public function getDispatchAmount()
    {
        //京东单独计算，阿里不变
        if ($this->source == 2 && $this->set['freight_method'] == 1) {
            if ($this->goods_price < 49) {
                $this->freight = 800;
            } elseif ($this->goods_price >= 49 && $this->goods_price <99) {
                $this->freight = 600;
            } else {
                $this->freight = 0;
            }
        }
        return $this->freight/100;
    }
}