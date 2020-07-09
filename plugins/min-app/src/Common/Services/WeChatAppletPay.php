<?php
/**
 * Created by PhpStorm.
 * User: king/QQ:995265288
 * Date: 2019/4/28
 * Time: 3:24 PM
 */

namespace Yunshop\MinApp\Common\Services;


use app\common\exceptions\ShopException;
use app\common\helpers\Client;
use app\common\models\MemberMiniAppModel;
use app\common\services\finance\Withdraw;
use app\common\services\Pay;
use EasyWeChat\Foundation\Application;
use Illuminate\Support\Facades\Log;

class WeChatAppletPay extends Pay
{
    public function doRefund($out_trade_no, $totalMoney, $refundMoney)
    {
    }

    public function buildRequestSign()
    {
    }

    public function doPay($data = [])
    {
    }

    public function doWithdraw($memberId, $outTradeNo, $money, $desc = '', $type = 1)
    {
        $remark = "小程序微信钱包提现,订单号:{$outTradeNo},提现金额:{$money}";
        //支付日志
        $payOrderModel = $this->withdrawlog(Pay::PAY_TYPE_WITHDRAW, "微信", $money, $remark, $outTradeNo, Pay::ORDER_STATUS_NON, $memberId);

        //支付参数
        $pay = $this->payParams();
        if (empty($pay['weixin_cert']) || empty($pay['weixin_key'])) {
            throw new ShopException('\'未上传完整的小程序微信支付证书，请到【应用】->【小程序】中上传!\'');
        }
        $openid = $this->fansOpenid($memberId);
        if (!$openid) {
            throw new ShopException('小程序提现用户不存在');
        }

        $app = $this->getEasyWeChatApp($pay);

        $merchantPay = $app->merchant_pay;

        $merchantPayData = [
            'partner_trade_no' => empty($out_trade_no) ? time() . Client::random(4, true) : $out_trade_no,
            'openid'           => $openid,
            'check_name'       => 'NO_CHECK',
            'amount'           => $money * 100,
            'desc'             => empty($desc) ? '佣金提现' : $desc,
            'spbill_create_ip' => self::getClientIP(),
        ];

        //请求数据日志
        $this->payRequestDataLog($payOrderModel->id, $payOrderModel->type, $payOrderModel->type, json_encode($merchantPayData));

        $payResult = $merchantPay->send($merchantPayData);

        if (isset($payResult->partner_trade_no)) {
            $payResult = $merchantPay->query($payResult->partner_trade_no);
        }

        //响应数据
        $this->payResponseDataLog($payOrderModel->out_order_no, $payOrderModel->type, json_encode($payResult));

        Log::debug('---提现状态---', [$payResult->status]);

        if (isset($payResult->status) && ($payResult->status == 'PROCESSING' || $payResult->status == 'SUCCESS' || $payResult->status == 'SENDING' || $payResult->status == 'SENT')) {
            \Log::debug('提现返回结果', $payResult->toArray());

            $this->changeOrderStatus($payOrderModel, Pay::ORDER_STATUS_COMPLETE, $payResult->payment_no);

            $this->payResponseDataLog($outTradeNo, '微信提现', json_encode($payResult));

            //todo 余额+收入 提现成功处理，需要系统梳理一下 190808 LiBaoJia
            Withdraw::paySuccess($payResult->partner_trade_no);

            return ['errno' => 0, 'message' => '小程序微信提现成功'];
        }
        return ['errno' => 1, 'message' => '小程序微信接口错误:' . $payResult->return_msg . '-' . $payResult->err_code_des];
    }

    private function changeOrderStatus($model, $status, $trade_no)
    {
        $model->status = $status;
        $model->trade_no = $trade_no;
        $model->save();
    }

    /**
     * 创建支付对象
     *
     * @param $pay
     * @param string $notifyUrl
     *
     * @return Application
     */
    public function getEasyWeChatApp($pay, $notifyUrl = '')
    {
        $options = [
            'app_id'  => $pay['weixin_appid'],
            'secret'  => $pay['weixin_secret'],
            'payment' => [
                'merchant_id' => $pay['weixin_mchid'],
                'key'         => $pay['weixin_apisecret'],
                'cert_path'   => $pay['weixin_cert'],
                'key_path'    => $pay['weixin_key'],
                'notify_url'  => $notifyUrl
            ]
        ];
        return new Application($options);
    }


    /**
     * 支付参数
     *
     * @return array
     * @throws ShopException
     */
    private function payParams()
    {
        $appletSet = \Setting::get('plugin.min_app');

        if (is_null($appletSet) || 0 == $appletSet['switch']) {
            throw new ShopException('未开启小程序');
        }
        if (empty($appletSet['mchid']) || empty($appletSet['api_secret'])) {
            throw new ShopException('未设置小程序支付参数');
        }
        return [
            'weixin_appid'     => $appletSet['key'],
            'weixin_secret'    => $appletSet['secret'],
            'weixin_mchid'     => $appletSet['mchid'],
            'weixin_apisecret' => $appletSet['api_secret'],
            'weixin_cert'      => $appletSet['apiclient_cert'],
            'weixin_key'       => $appletSet['apiclient_key']
        ];
    }


    private function fansOpenid($memberId)
    {
        return MemberMiniAppModel::where('member_id', $memberId)->first()->openid;
    }

}
