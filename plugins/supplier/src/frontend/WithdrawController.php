<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/31
 * Time: 15:26
 */

namespace Yunshop\Supplier\frontend;


use app\common\components\ApiController;
use Setting;
use app\common\exceptions\AppException;
use app\frontend\modules\withdraw\models\Withdraw;
use app\frontend\modules\withdraw\services\StatisticalPresentationService;
use Yunshop\Supplier\common\models\Supplier;
use Yunshop\Supplier\common\services\VerifyWithdraw;
use Yunshop\Supplier\supplier\models\SupplierWithdraw;

class WithdrawController extends ApiController
{
    public function index()
    {
        $set = $this->getSupplier();

        $cost_money = $this->getAvailableAmount();
        $service_money = $set['service_money']; //手续费
        $service_type = $set['service_type'];

        // 供应商基础设置
        $type = [];
        if (in_array(1, $set['withdraw_types'])) {
            $type['1'] = '手动提现';
        }
        if (in_array(2, $set['withdraw_types'])) {
            $type['2'] = '微信提现';
        }
        if (in_array(3, $set['withdraw_types'])) {
            $type['3'] = '支付宝提现';
        }
        if (in_array(5, $set['withdraw_types'])) {
            $type['5'] = '汇聚提现';
        }

        //总数据
        $data = [
            'service_money' => $service_money,
            'service_type' => $service_type,
            'cost_money' => $cost_money,
            'type' => $type
        ];
        return $this->successJson('ok', $data);
    }

    public function withdraw()
    {
        $error_code = 0;
        $success_code = 1;
        $member_id = $this->getMemberId();

        $supplier_model = $supplier_model = Supplier::getSupplierByMemberId($member_id);;

        $result = VerifyWithdraw::verifyWithdraw($supplier_model->id);
        if ($result) {
            return $this->errorJson('不满足提现规则！', $result);
        }

        if ((\YunShop::request()->apply_type == 1) && !$supplier_model->bank_username) {
            return $this->errorJson('请完善收款信息', -1);
        }

        $order_information = SupplierWithdraw::getSureOrderInformation($supplier_model->id, '', 1);
        $this->cashLimitation($order_information);

        $set = $this->getSupplier();
        $poundage = $this->getPoundage($order_information['total_profit']);
        $money = $order_information['total_profit'] - $poundage;

        $money = $money <= 0 ? 0 : $money;
        //总数据
        $apply_data = [
            'supplier_id'   => $supplier_model->id,
            'member_id'     => $member_id,
            'status'        => 1,
            'service_type'  => $set['service_type'],
            'service_money' => $set['service_money'],
            'apply_money'   => $order_information['total_profit'],
            'money'         => $money,
            'order_ids'     => $order_information['order_ids'],
            'uniacid'       => \YunShop::app()->uniacid,
            'apply_sn'      => SupplierWithdraw::ApplySn(),
            'type'          => \YunShop::request()->apply_type
        ];
        SupplierWithdraw::createApply($apply_data);
        return $this->successJson('提现成功，等待审核', $success_code);
    }

    //提现限制
    private function cashLimitation($order_information)
    {
        $set   = Setting::get('plugin.supplier');
        $start = strtotime(date("Y-m-d"),time());
        $end   = $start+60*60*24;
        if( \YunShop::request()->apply_type == 2){
            $wechat_min =  $set['wechat_min'] ;
            $wechat_max =  $set['wechat_max'] ;
            $wechat_frequency =  floor($set['wechat_frequency'] ?: 10);

            //统计用户今天提现的次数
            $statisticalPresentationService = new StatisticalPresentationService;
            $today_withdraw_count = $statisticalPresentationService->statisticalPresentation('wechat') + 1;
            if( $today_withdraw_count <= $wechat_frequency ){
                if( $order_information['total_profit'] < $wechat_min && !empty($wechat_min)){
                    throw new AppException("提现到微信单笔提现额度最低{$wechat_min}元");
                }elseif( $order_information['total_profit'] > $wechat_max && !empty($wechat_max)){
                    throw new AppException("提现到微信单笔提现额度最高{$wechat_max}元");
                }
            }else{
                return $this->errorJson('提现失败,每日提现到微信次数不能超过'.$wechat_frequency.'次');
            }
        }elseif(\YunShop::request()->apply_type == 3){
            $alipay_min =  $set['alipay_min'] ;
            $alipay_max =  $set['alipay_max'] ;
            $alipay_frequency = floor($set['alipay_frequency'] ?: 10 );

            //统计用户今天提现的次数
            $statisticalPresentationService = new StatisticalPresentationService;
            $today_withdraw_count = $statisticalPresentationService->statisticalPresentation('alipay') + 1;
            if( $today_withdraw_count <= $alipay_frequency  ){
                if( $order_information['total_profit']  < $alipay_min && !empty($alipay_min)){
                    throw new AppException("提现到支付宝单笔提现额度最低{$alipay_min}元");
                }elseif( $order_information['total_profit']  > $alipay_max && !empty($alipay_max)){
                    throw new AppException("提现到支付宝单笔提现额度最高{$alipay_max}元");
                }
            }else{
                return $this->errorJson('提现失败,每日提现到支付宝次数不能超过'.$alipay_frequency.'次');
            }

        }
    }

    /**
     *  获取手续费
     *
     * @param $set
     * @param $total_profit
     * @return float|int
     */
    public function getPoundage($total_profit)
    {
        $set = $this->getSupplier();

        if ($set['service_type'] == 0) {
            return $set['service_money'];
        } else {
           return ($total_profit * $set['service_money'] / 100);
        }
    }

    /**
     * 获取可用金额
     *
     * @param $member_id
     * @return array|mixed
     */
    public function getAvailableAmount()
    {
        $member_id = $this->getMemberId();

        $supplier_model = Supplier::getSupplierByMemberId($member_id);

        return SupplierWithdraw::getSureOrderInformation($supplier_model->id, 'profit', ''); //可提现金额
    }

    /**
     * 获取基础设置
     *
     * @return mixed
     */
    private function getSupplier()
    {
        return \Setting::get('plugin.supplier');
    }

    /**
     * 获取会员 id
     * @return int
     */
    private function getMemberId()
    {
        return \YunShop::app()->getMemberId();
    }

    public function convergeWithdraw()
    {
        $data['cost_money'] = number_format($this->getAvailableAmount(), 2);
        $data['poundage']   = number_format($this->getPoundage($data['cost_money']), 2);
        $data['actual_amount'] = bcsub($data['cost_money'], $data['poundage'],2);

        return $this->successJson('获取数据成功', $data);
    }
}