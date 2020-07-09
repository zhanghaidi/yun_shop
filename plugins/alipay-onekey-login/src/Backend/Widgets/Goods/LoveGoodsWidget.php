<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/6/27 上午11:06
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace Yunshop\Love\Backend\Widgets\Goods;


use app\common\components\Widget;
use Yunshop\Commission\models\AgentLevel;
use Yunshop\Love\Common\Models\GoodsLove;
use Yunshop\Love\Common\Services\SetService;

class LoveGoodsWidget extends Widget
{
    public function run()
    {
        $goodsModel = GoodsLove::ofGoodsId($this->goods_id)->first();
        $pluginCommission = \YunShop::plugin()->get('commission');
        $goods = $goodsModel ? $goodsModel->toArray() : $this->getDefaultGoodsData();
        $goods['commission'] = unserialize($goods['commission']);
        if ($pluginCommission) {
            $commission_level = AgentLevel::getLevels()->get();
        }
        $commission_set = \Setting::get('plugin.commission');
        return view('Yunshop\Love::Backend.Widgets.loveGoods',[
            'goods'     => $goods,
            'set'       => $this->getLoveSet(),
            'pluginCommission'  =>  $pluginCommission,
            'levels'  =>  $commission_level,
            'commission_set'  =>  $commission_set,
        ])->render();
    }


    /**
     * 商品提交数据验证
     * @param $goodsId
     * @param $data
     * @param $operate
     * @return bool
     */
    public function relationValidator($goodsId, $data, $operate)
    {

        $data = LoveGoodsWidget::submitData($goodsId, $data);

        $flag = false;
        $_model = new GoodsLove();
        $_model->fill($data);
        $validator = $_model->validator($data);
        if ($validator->fails()) {
            $_model->error($validator->messages());
        } else {
            return true;
        }
        return $flag;
    }


    /**
     * 保存商品提交数据，todo 查询return false 时候商品保存情况
     * @param $goodsId
     * @param $data
     * @param $operate
     * @return bool|null
     */
    public function relationSave($goodsId, $data, $operate)
    {
        if(!$goodsId){
            return false;
        }
        // edit 2018-06-01 by Yy
        // content 门店编辑商品没有爱心值选项, admin保存之后门店再保存widget没值,取默认.
        if (!$data) {
            return false;
        }
        $goodsModel = false;
        if ($operate != 'created') {
            $goodsModel = GoodsLove::ofGoodsId($goodsId)->first();
        }
        !$goodsModel && $goodsModel = new GoodsLove();

        if ($operate == 'deleted') {
            return $goodsModel->delete();
        }

        $data = LoveGoodsWidget::submitData($goodsId, $data);
        $data['parent_award_proportion'] = $data['parent_award_proportion'] ? $data['parent_award_proportion'] : 0;
        $goodsModel->fill($data);
        if ($goodsModel->save()) {
            return true;
        }
        return false;
    }


    /**
     * 提交数据重构
     * @param $goodsId
     * @param array $data
     * @return array
     */
    private static function submitData($goodsId, array $data)
    {
        $data['commission'] = serialize($data['commission']);
        foreach ($data as $key => $value) {
            if (strlen($value) < 1) {
                $data[$key] = 0;
            }
        }
        return [
            'uniacid'               => \YunShop::app()->uniacid,
            'goods_id'              => $goodsId,
            'award'                 => isset($data['award']) ? $data['award'] : 0,
            'deduction'             => isset($data['deduction']) ? $data['deduction'] : 0,
            'award_proportion'      => isset($data['award_proportion']) ? $data['award_proportion'] : 0,
            'deduction_proportion'  => isset($data['deduction_proportion']) ? $data['deduction_proportion'] : 0,
            'deduction_proportion_low'  => isset($data['deduction_proportion_low']) ? $data['deduction_proportion_low'] : 0,
            'parent_award'          => isset($data['parent_award']) ? $data['parent_award'] : 0,
            'parent_award_proportion'  => isset($data['parent_award_proportion']) ? $data['parent_award_proportion'] : 0,
            'second_award_proportion'  => isset($data['second_award_proportion']) ? $data['second_award_proportion'] : 0,
            'third_award_proportion'   => isset($data['third_award_proportion']) ? $data['third_award_proportion'] : 0,
            'parent_award_fixed'       => isset($data['parent_award_fixed']) ? $data['parent_award_fixed'] : 0,
            'second_award_fixed'       => isset($data['second_award_fixed']) ? $data['second_award_fixed'] : 0,
            'third_award_fixed'        => isset($data['third_award_fixed']) ? $data['third_award_fixed'] : 0,
            'love_accelerate'        => isset($data['love_accelerate']) ? $data['love_accelerate'] : 0,
            'activation_state'       =>isset($data['activation_state']) ? $data['activation_state'] : 0,
            'commission_level_give' => isset($data['commission_level_give']) ? $data['commission_level_give'] : 0,
            'commission'            => isset($data['commission']) ? $data['commission'] : '',
        ];

    }

    /**
     * 获取默认商品数据，当这个商品没有爱心值设置时
     * @return array
     */
    private function getDefaultGoodsData()
    {
        return [
            'award'                 => '0',
            'parent_award'          => '0',
            'deduction'             => '0',
            'award_proportion'      => '0',
            'parent_award_proportion'   => '0',
            'second_award_proportion'   => '0',
            'deduction_proportion'  => '0',
            'deduction_proportion_low'  => '0',
            'commission'            => [
                'rule' => [

                ],
            ],
        ];
    }

    /**
     * 获取爱心值购物赠送，购物抵扣开关设置
     * @return array
     */
    private function getLoveSet()
    {
        return [
            'award'     => SetService::getAwardStatus(),
            'deduction' => SetService::getDeductionStatus(),
            'parent_award' => SetService::getParentAwardStatus(),
//            'deduction' => SetService::getDeductionStatus(),
            'activation_state' => SetService::getActivationState(),
            'commission_level_give' => SetService::getCommissionLevelGiveStatus(),
        ];
    }

}