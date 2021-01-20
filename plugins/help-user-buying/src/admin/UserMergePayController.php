<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/12
 * Time: 14:22
 */

namespace Yunshop\HelpUserBuying\admin;

use app\common\events\payment\ChargeComplatedEvent;
use app\common\exceptions\AppException;
use app\common\services\password\PasswordService;
use app\common\services\PayFactory;
use app\frontend\models\OrderPay;
use app\frontend\modules\order\controllers\MergePayController;
use app\frontend\modules\order\services\OrderService;

class UserMergePayController extends MergePayController
{
    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws AppException
     * @throws \app\common\exceptions\PaymentException
     * @throws \app\common\exceptions\ShopException
     */
    public function credit2()
    {
        if (\Setting::get('shop.pay.credit') == false) {
            throw new AppException('商城未开启余额支付');
        }


        $orderPay = OrderPay::find(request()->input('order_pay_id'));
        $result = $orderPay->getPayResult(PayFactory::PAY_CREDIT);
        if (!$result) {
            throw new AppException('余额扣除失败,请联系客服');
        }
        $orderPay->pay();

        event(new ChargeComplatedEvent([
            'order_pay_id' => $orderPay->id
        ]));

        $trade = \Setting::get('shop.trade');

        $redirect = '';

        if (!is_null($trade) && isset($trade['redirect_url']) && !empty($trade['redirect_url'])) {
            $redirect = $trade['redirect_url'];
        }

        return $this->successJson('成功', ['redirect' => $redirect]);
    }

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
}