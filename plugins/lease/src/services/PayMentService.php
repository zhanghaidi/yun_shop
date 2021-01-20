<?php

namespace Yunshop\LeaseToy\services;


use app\common\exceptions\AdminException;
use app\common\models\PayType;
use Illuminate\Support\Facades\DB;
use app\common\services\credit\ConstService;
use app\common\services\finance\BalanceChange;
use Yunshop\LeaseToy\models\LeaseOrderModel;

class PayMentService
{
    protected $leaseReturn;

    public function pay($id)
    {

        $this->leaseReturn = LeaseOrderModel::find($id);

        switch ($this->leaseReturn->return_pay_type_id) {
            case PayType::CREDIT:
                $info = $this->balance();
                break;
            case -1:
                $info = true;
                break;
            default:
                throw new AdminException('未选择退还方式');
                break;
        }

        return $info;
    }

    private function balance()
    {

        $leaseReturn = $this->leaseReturn;

        $data = [
            'member_id' => $leaseReturn->member_id,
            'remark' => '订单(ID' . $leaseReturn->order_id . ')余额支付退还租赁(ID' . $leaseReturn->id . ')' . $leaseReturn->return_deposit,
            'source' => ConstService::SOURCE_CANCEL_CONSUME,
            'relation' => $leaseReturn->order_sn,
            'operator' => ConstService::OPERATOR_ORDER,
            'operator_id' => $leaseReturn->member_id,
            'change_value' => $leaseReturn->return_deposit
        ];
        $result = (new BalanceChange())->cancelConsume($data);


        if ($result !== true) {
            throw new AdminException($result);
        }
        return $result;
    }
}