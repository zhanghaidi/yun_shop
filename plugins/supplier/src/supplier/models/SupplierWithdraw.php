<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/25
 * Time: 下午5:21
 */

namespace Yunshop\Supplier\supplier\models;



use Yunshop\Supplier\common\services\withdraw\CalculationWithdrawService;
use Yunshop\Supplier\common\models\SupplierWithdraw as Withdraw;
use Yunshop\Supplier\common\events\SupplierAutomaticWithdrawEvent;
use app\common\helpers\Url;

class SupplierWithdraw extends \Yunshop\Supplier\common\models\SupplierWithdraw
{
    /**
     * @name 添加提现记录
     * @author yangyang
     * @param $apply_data
     */
    public static function createApply($apply_data)
    {
        $SupplierWithdraw = SupplierWithdraw::create($apply_data);

        app('plugins')->isEnabled('converge_pay') && \Setting::get('plugin.supplier.audit_free') == 1 && $SupplierWithdraw['type'] == '5' ? \Setting::set('plugin.convergePay_set.notifyWithdrawUrl', Url::shopSchemeUrl('payment/convergepay/notifyUrlWithdraw.php')) : null;
        event(new SupplierAutomaticWithdrawEvent($SupplierWithdraw));

        //created 观察者 通知用户

        self::updateOrderApplyStatus($apply_data);
    }

    /**
     * @name 更改supplier_order的apply_status
     * @author yangyang
     * @param $apply_data
     */
    public static function updateOrderApplyStatus($apply_data)
    {
        $order_ids = explode(',', $apply_data['order_ids']);
        SupplierOrder::updateApplyStatus(1, $order_ids);
    }

    /**
     * @name 获取此次提现的订单信息
     * @author yangyang
     * @param $supplier_id
     * @param null $type
     * @param $limit_time
     * @return array|mixed
     */
    public static function getSureOrderInformation($supplier_id, $type = null, $limit_time = null)
    {
        $order_information = SupplierOrder::getSupplierOrder($supplier_id, $limit_time);
        return CalculationWithdrawService::calculation($order_information, $type);
    }

    /**
     * @name 获取最后一次提现记录
     * @author yangyang
     * @return mixed
     */
    public static function getLastWithdraw($supplier_id)
    {
        $result = Withdraw::select()->where('supplier_id', $supplier_id)->orderBy('id', 'desc')->first();
        return $result;
    }


    //统计当天的微信/支付宝提现次数
    public static function  successfulWithdrawals($pay_type,$start,$end){

        if($pay_type = 'wechat'){
            $pay_type = 2;
        }elseif($pay_type = 'alipay'){
            $pay_type = 3;
        }

        
        return self::where([
            ['member_id',\YunShop::app()->getMemberId()],
            //['status','=', 3],
            ['type','=',$pay_type],
            ['created_at','>=',$start],
            ['created_at','<=',$end]
        ])->count();

    }
}