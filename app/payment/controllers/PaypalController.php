<?php
/**
 * Created by PhpStorm.
 * User: blank
 * Date: 2020/4/22
 * Time: 10:23
 */

namespace app\payment\controllers;


use app\common\helpers\Url;
use app\common\models\AccountWechats;
use app\common\services\PayFactory;
use app\common\services\utils\EncryptUtil;
use app\payment\PaymentController;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use Yunshop\PayPal\models\PayPalLog;
use Yunshop\PayPal\models\PayPalOrder;

class PaypalController extends PaymentController
{


    /** 正式验证地址 URL */
    const VERIFY_URI = 'https://ipnpb.paypal.com/cgi-bin/webscr';
    /** 沙箱验证地址 URL */
    const SANDBOX_VERIFY_URI = 'https://ipnpb.sandbox.paypal.com/cgi-bin/webscr';

    /** 验证成功 值*/
    const VALID = 'VERIFIED';
    /** 验证失败 值 */
    const INVALID = 'INVALID';



    public $parameter;


    //支付回调
    public function payNotify()
    {
        $parameter = $this->getIpnData();

        $this->setI($parameter['custom']);


        $bool = $this->verifyIPN();


        if ($bool) {

            $payOrder = PayPalOrder::uniacid()->where('transaction_id', $parameter['txn_id'])->first();

            if (empty($payOrder)) {
                \Log::debug('-----------PayPal记录为空------------>>');
                exit('failure');
            }

            if ($payOrder->status == PayPalOrder::STATUS_SUCCESS) {
                \Log::debug('-----------PayPal已支付成功------------>>', $payOrder->toArray());
                exit('success');
            }
            $data = [
                'total_fee'    => $parameter['mc_gross'],
                'out_trade_no' => $parameter['invoice'],
                'trade_no'     => $parameter['txn_id'],
                'unit'         => 'yuan',
                'pay_type'     => 'PayPal支付',
                'pay_type_id'  => PayFactory::PAY_PAL,
            ];

            \Log::debug('-----------PayPal支付成功------------>>', $data);


            $payOrder->status = PayPalOrder::STATUS_SUCCESS;
            $payOrder->save();


            $this->payResutl($data);

            exit('success');
        }

        \Log::debug('------------PayPal支付回调失败------------------>>');
        exit('failure');

    }

    //退款回调
    public function refundNotify()
    {

    }

    public function setI($i)
    {

        \Setting::$uniqueAccountId = \YunShop::app()->uniacid = $i;

        AccountWechats::setConfig(AccountWechats::getAccountByUniacid(\YunShop::app()->uniacid));
    }

    //同步步回调 - 用于确认用户取消支付
    public function cancelPay()
    {
        \Log::debug('---------paypal支付取消-------------', $_GET);
        redirect(Url::absoluteApp('member/', ['i' => \YunShop::app()->uniacid]))->send();
    }


    public function getApiContext()
    {
        $set = \Setting::get('plugin.pay_pal');
        $apiContext = new ApiContext(new OAuthTokenCredential($set['client_id'], $set['client_secret']));

        return $apiContext;
    }

    //同步步回调 - 用于确认用户是否付款
    public function confirmPay()
    {

        if (empty($i) && !isset($_GET['paymentId']) && !isset($_GET['PayerID'])) {
            \Log::debug('-----PayPal支付确认失败-----', $_GET);
            redirect(Url::absoluteApp('member/payErr', ['i' => \YunShop::app()->uniacid]))->send();exit();
        }

        $i = intval($_GET['i']);

        $this->setI($i);

        $paymentId = trim($_GET['paymentId']);
        $PayerID = trim($_GET['PayerID']);

        $salt_sign = trim($_GET['s']);

        $payPalLog = PayPalLog::uniacid()->where('payment_id',$paymentId)->first();


        if (is_null($payPalLog)) {
            \Log::debug('------PayPal支付确认支付记录不存在---------', ['payment_id'=> $paymentId]);
            redirect(Url::absoluteApp('member/payErr', ['i' => \YunShop::app()->uniacid]))->send();exit();
            //exit('支付记录不存在');
        }

        $verify_salt =  EncryptUtil::decryptECB($salt_sign,$payPalLog['aes_key']);
        if ($verify_salt['code'] === false && $verify_salt['data'] != $payPalLog['aes_key']) {
            \Log::debug('------PayPal支付确认盐验证失败---------');
            redirect(Url::absoluteApp('member/payErr', ['i' => \YunShop::app()->uniacid]))->send();exit();
            //exit('验证失败');
        }

        if ($payPalLog->status == PayPalLog::STATUS_SUCCESS) {
            //已支付订单
            \Log::debug('------PayPal支付确认---订单已支付---------',$_GET);
            redirect(Url::absoluteApp('member/payErr', ['i' => \YunShop::app()->uniacid]))->send();exit();
            //exit('订单已支付');
        }


        $apiContext = $this->getApiContext();
        $payment = Payment::get($paymentId, $apiContext);
        $execute = new PaymentExecution();
        $execute->setPayerId($PayerID);

        try {
            $payment->execute($execute, $apiContext);
            $paymentArray = $payment->toArray();
            
            \Log::debug('------PayPal confirm--------', $payment->toArray());
            //dump($paymentArray);

            //更新支付记录
            $this->updateLog($payPalLog, ['state' =>  $paymentArray['state'], 'payer_id'=>$PayerID, 'status'=> PayPalLog::STATUS_SUCCESS]);

            //保存支付信息,付款成功之后会有sale_id需要保存起来 退款需要
            $this->savePayPalOrder($paymentArray);

            //echo '支付成功，支付ID【' . $paymentId . '】,支付人ID【' . $PayerID . '】';die;
            redirect(Url::absoluteApp('member/payYes', ['i' => \YunShop::app()->uniacid]))->send();exit();

        } catch (\Exception $e) {
            $this->updateLog($payPalLog, ['state' => 'failure', 'payer_id'=>$PayerID, 'status'=> PayPalLog::STATUS_FAILED]);

            //echo '支付失败，支付ID【' . $paymentId . '】,支付人ID【' . $PayerID . '】';dd($e);

            redirect(Url::absoluteApp('member/payErr', ['i' => \YunShop::app()->uniacid]))->send();exit();
        }
    }

    public function payErr()
    {
        redirect(Url::absoluteApp('member/payErr', ['i' => \YunShop::app()->uniacid]))->send();exit();
    }

    public function updateLog(PayPalLog $payPalLog, $data)
    {
        $payPalLog->fill($data);

        $payPalLog->save();

        return $payPalLog;
    }

    public function savePayPalOrder($paymentArray)
    {
        $data = [
            'uniacid' => \YunShop::app()->uniacid,
            'pay_sn' => $paymentArray['transactions'][0]['invoice_number'],
            'currency' => $paymentArray['transactions'][0]['amount']['currency'],
            'payment_id' => $paymentArray['id'],
            'intent' => $paymentArray['intent'],
            'transaction_id' =>  $paymentArray['transactions'][0]['related_resources'][0][$paymentArray['intent']]['id'],
        ];

        if (is_null(PayPalOrder::uniacid()->where('transaction_id', $data['transaction_id'])->first())) {
            PayPalOrder::create($data);
        }
    }


    public function getIpnData()
    {
        $json = file_get_contents('php://input');

        //file_put_contents(storage_path("logs/paypal.log"), $json);

        //获取字符集
        $result1  = strstr($json, 'charset');
        $result2 = substr($result1,0,strpos($result1, '&'));
        $charset = substr($result2,strlen('charset='));


        $raw_post_array = explode('&', $json);
        $myPost = array();
        foreach ($raw_post_array as $keyval) {
            $keyval = explode('=', $keyval);
            if (count($keyval) == 2) {
                if (empty($charset) || strtolower($charset) == 'utf-8') {
                    $value = rawurldecode($keyval[1]);
                } else {
                    $value = iconv($charset, 'UTF-8',  rawurldecode($keyval[1])); //字符集转换为utf-8
                }
                $myPost[$keyval[0]] =$value;
            }
        }
        if (empty($myPost)) {
            \Log::debug('<<-------------PayPal IPN data is null----------',$json);
        }

        \Log::debug('<<------PayPal IPN-------------', $myPost);

        $this->parameter = $myPost;

        return $this->parameter;
    }

    protected function getAllParameter()
    {
        return $this->parameter;
    }


    public function getUrl($sandbox = true)
    {

        $app = \Setting::get('plugin.pay_pal.app');

        if ($app == 'live') {
            return self::VERIFY_URI;
        }

        return self::SANDBOX_VERIFY_URI;

//        if ($sandbox) {
//            return self::SANDBOX_VERIFY_URI;
//        }
//        return self::VERIFY_URI;
    }

    /**
     * @return bool
     */
    public function verifyIPN()
    {


        $str = file_get_contents('php://input');
        $req = 'cmd=_notify-validate&'.$str;


        // Build the body of the verification post request, adding the _notify-validate command.
//        $req = 'cmd=_notify-validate';
//        $myPost = $this->getAllParameter()?:$this->getIpnData();
//        $get_magic_quotes_exists = false;
//        if (function_exists('get_magic_quotes_gpc')) {
//            $get_magic_quotes_exists = true;
//        }
//        foreach ($myPost as $key => $value) {
//
//
//            if ($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
//                $value = rawurlencode(stripslashes($value));
//            } else {
//                $value = rawurlencode($value);
//            }
//            $req .= strlen($req) == 0 ? "" : "&";
//            $req .= "$key=$value";
//        }

        \Log::debug('-------------validate--req-----------------------',$req);
        // Post the data back to PayPal, using curl. Throw exceptions if errors occur.
        $ch = curl_init($this->getUrl());


        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
        curl_setopt($ch, CURLOPT_SSLVERSION, 6);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

        // This is often required if the server is missing a global cert bundle, or is using an outdated one.
//        if ($this->use_local_certs) {
//            curl_setopt($ch, CURLOPT_CAINFO, __DIR__ . "/cert/cacert.pem");
//        }
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'User-Agent: PHP-IPN-VerificationScript',
            'Connection: Close',
        ));
        $res = curl_exec($ch);
        \Log::debug('----PayPal--verifies-----', $res);
        if ( !($res)) {
            $errno = curl_errno($ch);
            $errstr = curl_error($ch);
            curl_close($ch);
            \Log::debug('---PayPal verifies error------',['error'=> $errno, 'msg' => $errstr]);
            //throw new \Exception("cURL error: [$errno] $errstr");
        }

        $info = curl_getinfo($ch);
        $http_code = $info['http_code'];
        if ($http_code != 200) {
            \Log::debug('---PayPal responded with http code ------',$http_code);
            //throw new \Exception("PayPal responded with http code $http_code");
        }

        curl_close($ch);

        // Check if PayPal verifies the IPN data, and if so, return true.
        if ($res == self::VALID) {
            return true;
        } else {
            return false;
        }
    }
}