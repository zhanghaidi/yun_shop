<?php
/**
 * Created by PhpStorm.
 * User: blank
 * Date: 2020/2/11
 * Time: 14:53
 */

namespace app\payment\controllers;


use app\common\helpers\Url;
use app\common\services\Pay;
use app\common\services\PayFactory;
use app\payment\PaymentController;
use app\common\modules\yop\sdk\Util\YopSignUtils;
use app\common\models\AccountWechats;
use Illuminate\Support\Facades\DB;
use Yunshop\YopPro\models\AccountDivided;
use Yunshop\YopPro\models\AccountDividedLog;
use Yunshop\YopPro\models\Merchant;
use Yunshop\YopPro\models\YopProOrder;
use Yunshop\YopPro\models\YopProOrderRefund;
use Yunshop\YopPro\services\WithdrawService;

class YopproController extends PaymentController
{
    protected $set;

    protected  $parameters;

    public function __construct()
    {
        parent::__construct();

        if (!app('plugins')->isEnabled('yop-pro')) {
            echo 'Not turned on yop pro';
            exit();
        }

        $this->set = $this->getMerchantNo();

        if (empty(\YunShop::app()->uniacid)) {
            \Setting::$uniqueAccountId = \YunShop::app()->uniacid = $this->set['uniacid'];
            AccountWechats::setConfig(AccountWechats::getAccountByUniacid(\YunShop::app()->uniacid));
        }

        $this->init();
    }

    protected function getMerchantNo()
    {
        \Log::debug('--------------易宝入网参数--------------', $_REQUEST);

        $app_key = $_REQUEST['customerIdentification'];

        $merchant_no = substr($app_key,  strrpos($app_key, 'OPR:')+4);
        $model = DB::table('yz_yop_pro_set')->where('parent_merchant_no', $merchant_no)->first();
        if (empty($model)) {exit('商户不存在');}

        return is_array($model)?$model:$model->toArray();
    }

    private function init()
    {
        $yop_data = $_REQUEST['response'];
        if ($yop_data) {
            $response = YopSignUtils::decrypt($yop_data, $this->set['parent_private_key'], $this->set['yop_public_key']);
            $this->parameters = json_decode($response, true);
        }
    }

    //子商户入网
    public function merNotifyUrl()
    {

        \Log::debug('子商户入网:'.$this->parameters['requestNo'], $this->parameters);


        $merchant = Merchant::where('requestNo', $this->parameters['requestNo'])->first();

        if (empty($merchant)) {
            exit('Merchant does not exist');
        }

        $status = $this->merNetInStatus();

        $merchant->status = $status;
        $merchant->remark = $this->parameters['remark'] ?: '';
        $bool = $merchant->save();
        if ($bool) {
            echo 'SUCCESS';exit();
        } else {
            echo '保存出错';exit();
        }
    }

    protected function merNetInStatus()
    {
        $status = Merchant::INVALID;
        if (!empty($this->parameters['merNetInStatus'])) {
            switch ($this->parameters['merNetInStatus']) {
                case 'PROCESS_SUCCESS': //审核通过
                    $status = Merchant::PROCESS_SUCCESS;
                    break;
                case 'PROCESS_REJECT': //审核拒绝
                    $status = Merchant::PROCESS_REJECT;
                    break;
                case 'PROCESS_BACK': //审核回退
                    $status = Merchant::PROCESS_BACK;
                    break;
                case 'PROCESSING_PRODUCT_INFO_SUCCESS': //审核中-产品提前开通
                    $status = Merchant::PROCESSING_PRODUCT_INFO_SUCCESS;
                    break;
                default:
                    break;
            }
        }

        return $status;
    }


    //聚合报备
    public function backUrl()
    {
        \Log::debug('-------------聚合报备---------------', $this->parameters);

        $this->debug('聚合报备:'.$this->parameters['merchantNo'], $this->parameters);

        $merchant = Merchant::where('merchantNo', $this->parameters['merchantNo'])->first();

        if (empty($merchant)) {
            exit('Merchant does not exist');
        }

        $report_status = $this->reportStatusCode();

        $merchant->report_status = $report_status;
        $bool = $merchant->save();
        if ($bool) {
            echo 'SUCCESS';exit();
        } else {
            echo '保存出错';exit();
        }
    }


    protected function reportStatusCode()
    {
        switch ($this->getParameter('reportStatusCode')) {
            //报备成功
            case '':
            case 'NULL':
            case '0000':
                $report_status = Merchant::BACK_SUCCESS;
                break;
            //处理中
            case '1111':
            case '1112':
            case '3333':
            case '710001':
                $report_status = Merchant::BACK_WAIT;
                break;
            //失败
            default:
                $report_status = Merchant::BACK_FAIL;
                break;
        }

        return $report_status;
    }


    /**
     * 异步支付成功通知
     */
    public function notifyUrl()
    {

        $this->debug('-------支付------', $this->parameters);

        $this->log($this->getParameter('orderId'), $this->parameters);


        $this->savePayOrder();

        //$this->withdrawUpdate();

        $data = [
            'total_fee'    => floatval($this->getParameter('orderAmount')),
            'out_trade_no' => $this->getParameter('orderId'),
            'trade_no'     => $this->getParameter('uniqueOrderNo'),
            'unit'         => 'yuan',
            'pay_type'     => '易宝Pro微信',
            'pay_type_id'  => PayFactory::YOP_PRO_WECHAT,
        ];

        //支付产品为用户扫码 支付类型为支付宝
        if ($this->paymentProduct() == YopProOrder::SCCANPAY && $this->platformType() == YopProOrder::TYPE_ALIPAY) {
            $data['pay_type'] = '易宝Pro支付宝';
            $data['pay_type_id'] = PayFactory::YOP_PRO_ALIPAY;
        }

        $this->payResutl($data);

        echo 'SUCCESS'; exit();
    }

    //同步通知
    public function redirectUrl()
    {
        //$url = str_replace('https','http', Url::shopSchemeUrl("?menu#/member/payYes?i={$uniacid}"));
        //redirect($url)->send();

        redirect(Url::absoluteApp('member/orderList/0', ['i' => \YunShop::app()->uniacid]))->send();
    }

    //订单超时通知地址
    public function timeoutNotifyUrl()
    {
        $this->debug('<-------易宝支付订单超时通知-------------->', $this->parameters);

        AccountDividedLog::where('pay_sn', $this->getParameter('orderId'))->delete();
    }

    /**
     * 修改分账记录状态
     */
    protected function withdrawUpdate()
    {
        $accountDividedLog = AccountDividedLog::where('pay_sn', $this->getParameter('orderId'))->get();

        (new WithdrawService($accountDividedLog))->handle();
    }

    /**
     * 保存支付订单记录
     * @return YopProOrder
     */
    protected function savePayOrder()
    {
        $pay_order = YopProOrder::paySn($this->getParameter('orderId'))->first();
        if(!is_null($pay_order)) {
           return $pay_order;
        }

        $data = [
            'uniacid'=> \YunShop::app()->uniacid,
            'merchantNo' => $this->getParameter('merchantNo'),
            'pay_sn' => $this->getParameter('orderId'),
            'yop_order_no' => $this->getParameter('uniqueOrderNo'),
            'order_amount' => $this->getParameter('orderAmount'),
            'pay_amount' => $this->getParameter('payAmount'),
            'pay_success_at' => strtotime($this->getParameter('paySuccessDate')),
            'platform_type' => $this->platformType(),
            'payment_product' => $this->paymentProduct(),
        ];

        $yop_order =  new YopProOrder();

        $yop_order->fill($data);

        $yop_order->save();

        return $yop_order;
    }

    //平台分类 支付类型
    protected function platformType()
    {
        $status = 0;
        if (!empty($this->getParameter('platformType'))) {
            switch ($this->getParameter('platformType')) {
                case 'WECHAT': //微信
                    $status = YopProOrder::TYPE_WECHAT;
                    break;
                case 'ALIPAY': //支付宝
                    $status = YopProOrder::TYPE_ALIPAY;
                    break;
                case 'NET':
                    $status = YopProOrder::TYPE_NET;
                    break;
                case 'NCPAY':
                    $status = YopProOrder::TYPE_NCPAY;
                    break;
                case 'CFL':
                    $status = YopProOrder::TYPE_CFL;
                    break;
                default:
                    $status = 0;
                    break;
            }
        }

        return $status;
    }

    //支付产品
    protected function paymentProduct()
    {
        $status = 0;
        if (!empty($this->getParameter('paymentProduct'))) {
            switch ($this->getParameter('paymentProduct')) {
                case 'WECHAT_OPENID': //微信公众号
                    $status = YopProOrder::WECHAT_OPENID;
                    break;
                case 'SCCANPAY': //用户扫码
                    $status = YopProOrder::SCCANPAY;
                    break;
                case 'MSCANPAY': //商家扫码
                    $status = YopProOrder::MSCANPAY;
                    break;
                case 'ZFB_SHH': //支付宝生活号
                    $status = YopProOrder::ZFB_SHH;
                    break;
                case 'ZF_ZHZF': //商户账户支付
                    $status = YopProOrder::ZF_ZHZF;
                    break;
                case 'EWALLETH5': //钱包H5支付
                    $status = YopProOrder::EWALLETH5;
                    break;
                default:
                    $status = 0;
                    break;
            }
        }

        return $status;
    }

    //订单实时分账返回
    public function divideNotifyUrl()
    {

        $this->debug('<---'.$this->getParameter('orderId').'--订单实时分账返回', $this->parameters);

        $accountDivided = AccountDivided::uniacid()->where('divide_no', $this->getParameter('divideRequestId'))->first();

        //记录存在只修改状态
        if ($accountDivided) {
            $this->debug('请求以存在--'.$this->getParameter('divideRequestId').'------>', $this->parameters);
            $accountDivided->status = $this->getParameter('status') == 'SUCCESS'?1:0;
            $accountDivided->save();
            echo 'SUCCESS'; exit();
        }

        $data = [
            'uniacid'    => \YunShop::app()->uniacid,
            'merchantNo' => $this->getParameter('merchantNo'),
            'pay_sn'     => $this->getParameter('orderId'),
            'yop_order_no'  => $this->getParameter('uniqueOrderNo'),
            'divide_no'     => $this->getParameter('divideRequestId'),
            'divide_detail' => json_decode($this->getParameter('divideDetail'), true),
            'status' => $this->getParameter('status') == 'SUCCESS'?1:0,
        ];

        AccountDivided::create($data);

        $this->withdrawUpdate();

        echo 'SUCCESS'; exit();
    }

    //订单清算通知地址
    public function csUrl()
    {
        $yop_order =  YopProOrder::where('yop_order_no', $this->getParameter('uniqueOrderNo'))
            ->where('pay_sn', $this->getParameter('orderId'))->first();

        if (!$yop_order) {
            \Log::debug('易宝专业版订单不存在无法清算',$this->parameters);
            exit('Record does not exist');
        }

        $data = [
            'status' => 1,
            'cs_at' => strtotime($this->getParameter('csSuccessDate')),
            'merchant_fee' => $this->getParameter('merchantFee'),
            'customer_fee' => $this->getParameter('customerFee'),
        ];

        $yop_order->fill($data);

        $yop_order->save();

        echo 'SUCCESS';exit();

    }


    //订单退款
    public function refundUrl()
    {
        $this->debug('-------------易宝订单退款通知---------------');
        $yop_refund = YopProOrderRefund::getRefundAnnal($this->getParameter('orderId'), $this->getParameter('refundRequestId'))
            ->where('status', YopProOrderRefund::REFUND)->first();

        if (!$yop_refund) {

            \Log::debug('易宝订单退款记录不存在pay_sn:'.$this->getParameter('orderId'),$this->parameters);
            exit('Record does not exist');
        }

        if ($yop_refund->status != YopProOrderRefund::REFUND) {
            \Log::debug('<-----------退款已处理---------------->', ['id'=>$yop_refund->id, 'pay_sn'=>$this->getParameter('orderId')]);
            echo 'SUCCESS';exit();
        }
        $yop_refund->status = $this->refundStatus();

        if ($this->getParameter('refundSuccessDate')) {
            $yop_refund->refund_at = strtotime($this->getParameter('refundSuccessDate'));
        }

        if ($this->getParameter('errorMessage')) {
            $yop_refund->error_message = $this->getParameter('errorMessage');
        }

        $yop_refund->save();

        echo 'SUCCESS';exit();
    }

    //退款状态
    protected function refundStatus()
    {
        $status = 0;
        if (!empty($this->parameters['status'])) {
            switch ($this->parameters['status']) {
                case 'FAILED':
                    $status = YopProOrderRefund::REFUND_FAILED;
                    break;
                case 'SUCCESS':
                    $status = YopProOrderRefund::REFUND_SUCCESS;
                    break;
                case 'CANCEL':
                    $status = YopProOrderRefund::REFUND_CANCEL;
                    break;
                default:
                    $status = 0;
                    break;
            }
        }

        return $status;
    }


    /**
     * 获取参数值
     * @param $parameter
     * @return string
     */
    public function getParameter($parameter)
    {
        return isset($this->parameters[$parameter])?$this->parameters[$parameter] : '';
    }

    //返回日志
    protected function debug($desc, $params = [])
    {
        \Yunshop\YopPro\common\YopProLog::log($desc, $params);
    }

    /**
     * 支付日志
     * @param $out_trade_no
     * @param $data
     * @param string $msg
     */
    public function log($out_trade_no, $data, $msg = '易宝pro支付')
    {
        //访问记录
        Pay::payAccessLog();
        //保存响应数据
        Pay::payResponseDataLog($out_trade_no, $msg, json_encode($data));
    }
}