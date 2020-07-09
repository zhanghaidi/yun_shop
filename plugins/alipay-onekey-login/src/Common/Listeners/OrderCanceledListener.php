<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/16
 * Time: 16:00
 */

namespace Yunshop\Love\Common\Listeners;


use app\common\events\order\AfterOrderCanceledEvent;
use Illuminate\Contracts\Events\Dispatcher;
use Yunshop\Love\Common\Services\CommonService;
use Yunshop\Love\Backend\Modules\Love\Models\LoveRechargeRecords;
use Yunshop\Love\Common\Services\SetService;
use Yunshop\Love\Common\Services\Love\RechargeService;
use Yunshop\Love\Common\Services\ConstService;
use Yunshop\Love\Common\Services\LoveChangeService;

class OrderCanceledListener
{
    protected $orderModel;

    protected $love = 0;

    protected $rechargeModel;

    public function subscribe(Dispatcher $events)
    {
        $events->listen(AfterOrderCanceledEvent::class,static::class.'@orderCancel');
    }

    //订单关闭 爱心值抵扣回滚
    public function orderCancel($event)
    {

        $open = SetService::getLoveSet('order_love_deduction');

        if (empty($open)) {
            return;
        }

        $this->orderModel = $event->getOrderModel();



        $loveDeduction = $this->getOrderPointDeduction($this->orderModel->deductions);
        if (!$loveDeduction) {
            return;
        }

        //todo 放弃使用充值的方式返回，使用交易取消返回
        //$this->tryRecharge();

        $this->orderReturnLove();
    }


    protected function orderReturnLove()
    {
        $love_name = CommonService::getLoveName();
        $loveData = [
            'member_id' =>$this->orderModel->uid,
            'change_value' => $this->love,
            'operator' => ConstService::OPERATOR_ORDER,
            'operator_id' => $this->orderModel->id,
            'remark' => '订单：'.$this->orderModel->order_sn.'关闭，返还'.$love_name.'抵扣：'. $this->love,
            'relation' => ''
        ];

        (new LoveChangeService())->revokeAward($loveData);
    }

    private function tryRecharge()
    {
        $this->rechargeModel = new LoveRechargeRecords();

        $this->rechargeModel->fill($this->getRechargeData());

        try {
            (new RechargeService($this->rechargeModel))->tryOrderRecharge();
        } catch (\Exception $e) {
            \Log::debug('=======订单{'.$this->orderModel->id.'}爱心值返还出错=========', $e->getMessage());
        }
    }

    private function getOrderPointDeduction($orderDeductions)
    {
        $love = 0;
        if ($orderDeductions) {
            foreach ($orderDeductions as $key => $deduction) {
                if ($deduction['code'] == 'love') {
                    $love = $deduction['coin'];
                    break;
                }
            }
        }
        return $this->love = $love;
    }

    /**
     * @return array
     */
    private function getRechargeData()
    {
        return [
            'type'          => 1,       //todo 后台充值、商城付款，应该在支付模型中设置常量
            'status'        => LoveRechargeRecords::STATUS_ERROR,
            'remark'        => '订单：'.$this->orderModel->order_sn.'关闭，返还爱心值抵扣：'. $this->love,
            'uniacid'       => \YunShop::app()->uniacid,
            'member_id'     => $this->orderModel->uid,
            'order_sn'      => $this->orderModel->order_sn,
            'value_type'    => 1,
            'change_value'  => $this->love,
        ];
    }
}