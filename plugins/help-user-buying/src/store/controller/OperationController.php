<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/24
 * Time: 11:10
 */

namespace Yunshop\HelpUserBuying\store\controller;

use app\common\services\finance\BalanceChange;
use app\frontend\modules\order\services\OrderService;
use app\common\models\Order;
use app\common\models\PayType;
use app\common\exceptions\AppException;
use app\common\services\credit\ConstService;


//todo blank 2019/03/12 废弃此类
class OperationController extends \app\backend\modules\order\controllers\OperationController
{
    public function pay()
    {
        $pay_type = request()->input('pay_type');
        $order_id = request()->input('order_id');


        if (!in_array($pay_type, [PayType::COD, PayType::CREDIT, PayType::BACKEND])) {
            throw new AppException('支付方式不支持');
        }

        if ($pay_type == PayType::CREDIT) {
            return $this->buyingCredit2();
        }
        $this->param['pay_type_id'] = $pay_type;
        OrderService::orderPay($this->param);
        return $this->successJson();

    }

    public function buyingCredit2()
    {
        if (\Setting::get('shop.pay.credit') == false) {
            throw new AppException('商城未开启余额支付');

        }

        $data = [
            'member_id'     => $this->order->uid,
            'remark'        => '代客下单使用余额支付订单：'.$this->order->id,
            'source'        => ConstService::SOURCE_CONSUME,
            'relation'      => $this->order->order_sn,
            'operator'      => ConstService::OPERATOR_SHOP,
            'operator_id'   => ConstService::OPERATOR_ORDER,
            'change_value'  => $this->order->price,
        ];

        $result = (new BalanceChange())->consume($data);

        if ($result === true) {
            OrderService::orderPay(['order_id' => $this->order->id, 'order_pay_id' => 0, 'pay_type_id' => PayType::CREDIT]);
            return $this->successJson();
        } else {
            throw new AppException($result);
        }
    }
}