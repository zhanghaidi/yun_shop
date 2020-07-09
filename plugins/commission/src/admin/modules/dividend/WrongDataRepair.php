<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/11/2
 * Time: 4:14 PM
 */

namespace Yunshop\Commission\admin\modules\dividend;

use Yunshop\AreaDividend\models\AreaDividend;
use Yunshop\AreaDividend\models\Order;
use Yunshop\Commission\models\Commission;
use Yunshop\Commission\models\CommissionOrder;

class WrongDataRepair
{
    public function handle()
    {
        // todo 找到订单下单时间早于 分红用户成为区域代理时间的 分红记录

        $errorCommissionOrder = CommissionOrder::repetition()->get();
        $errorCommissionOrder->each(function (CommissionOrder $commissionOrder) {

            // 如果已结算 删除收入记录
            $commissionOrder->rollBack();
        });

        // todo 删除订单对应的分红日志
    }
}