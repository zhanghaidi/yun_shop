<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019/6/3
 * Time: 下午3:10
 */

namespace app\common\services\wechat;


use app\common\exceptions\AppException;
use app\common\helpers\Url;
use app\common\models\OrderPay;
use app\common\models\PayType;
use app\common\services\Pay;
use app\common\services\wechat\lib\WxPayApi;
use app\common\services\wechat\lib\WxPayConfig;
use app\common\services\wechat\lib\WxPayException;
use app\common\services\wechat\lib\WxPayMicroPay;
use app\framework\Http\Request;
use Illuminate\Support\Facades\DB;


class HkScanPayService extends Pay
{
    public $set = null;
    public $config = null;

    public function __construct()
    {
//        $this->config = new WxPayConfig();
//        $this->set = $set = \Setting::get('shop.wechat_set');
    }

    /**
     * 支付
     * @param array $data
     * @return mixed|string
     * @throws AppException
     * @throws \app\common\services\wechat\lib\WxPayException
     */

    public function doPay($data = [])
    {
        $op = '微信香港扫码支付'.' 订单号：' . $data['order_no'];
        $pay_order_model = $this->log(1, '微信香港扫码支付', $data['amount'] / 100, $op, $data['order_no'], Pay::ORDER_STATUS_NON, \YunShop::app()->getMemberId());
        $set = \Setting::get('plugin.hk_pay_set');
        $pars['service'] = 'pay.weixin.native.intl';
        $pars['mch_id'] = $set['mch_id'];
        $pars['out_trade_no'] = $data['order_no'];
        $pars['body'] = $data['body'];
        $pars['total_fee'] = $data['amount'] * 100;
        $pars['mch_create_ip'] = \Request::getClientIp();
        $pars['notify_url'] = Url::shopSchemeUrl('payment/hkscan/notifyUrl.php');
        $pars['nonce_str'] = $this->getNonceStr();
        $pars['attach'] = \YunShop::app()->uniacid;
        ksort($pars, SORT_STRING);
        $string1 = '';
        foreach($pars as $k => $v) {
            $string1 .= "{$k}={$v}&";
        }
        $string1 .= "key={$set['key']}";

        $pars['sign'] = strtoupper(md5($string1));
        $post = $this->array2xml($pars);
        $result = $this->postXmlCurl($post,'https://gateway.wepayez.com/pay/gateway');
        $disableEntities = libxml_disable_entity_loader(true);
        $data = json_decode(json_encode(simplexml_load_string($result, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        libxml_disable_entity_loader($disableEntities);
        if ($data['status'] == 0 && $data['result_code'] == 0) {
            $sign = $data['sign'];
            //验签
            unset($data['sign']);
            $string1 = '';
            foreach($data as $k => $v) {
                $string1 .= "{$k}={$v}&";
            }
            $string1 .= "key={$set['key']}";
            if ($sign == strtoupper(md5($string1))) {
                \Log::debug('验签成功');
                return ['qrcode'=>$data['code_img_url']];
            }
        } else {
            \Log::debug('微信港版支付，请求错误',$data);
            throw new AppException('获取支付参数失败');
        }
    }


    /**
     * 提现
     */
    public function doWithdraw($member_id, $out_trade_no, $money, $desc, $type)
    {

    }


    /**
     * 退款
     */
    public function doRefund($out_trade_no, $totalmoney, $refundmoney)
    {
        $out_refund_no = $this->setUniacidNo(\YunShop::app()->uniacid);
        $op = '港版微信退款 订单号：' . $out_trade_no . '退款单号：' . $out_refund_no . '退款总金额：' . $totalmoney;
        if (empty($out_trade_no)) {
            throw new AppException('参数错误');
        }
        $pay_type_id = OrderPay::get_paysn_by_pay_type_id($out_trade_no);
        $pay_type_name = PayType::get_pay_type_name($pay_type_id);
        $pay_order_model = $this->refundlog(Pay::PAY_TYPE_REFUND, $pay_type_name, $refundmoney, $op, $out_trade_no, Pay::ORDER_STATUS_NON, 0);

        $set = \Setting::get('plugin.hk_pay_set');
        $pars['service'] = 'unified.trade.refund';
        $pars['mch_id'] = $set['mch_id'];
        $pars['out_trade_no'] = $out_trade_no;
        $pars['out_refund_no'] = $out_refund_no;
        $pars['total_fee'] = $totalmoney * 100;
        $pars['refund_fee'] = $refundmoney * 100;
        $pars['op_user_id'] = $set['mch_id'];
        $pars['nonce_str'] = $this->getNonceStr();
        $pars['sign'] = $this->getSign($pars,$set['key']);
        $post = $this->array2xml($pars);
        $result = $this->postXmlCurl($post,'https://gateway.wepayez.com/pay/gateway');
        $disableEntities = libxml_disable_entity_loader(true);
        $data = json_decode(json_encode(simplexml_load_string($result, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        libxml_disable_entity_loader($disableEntities);
        $this->payResponseDataLog($out_trade_no, '港版微信退款', json_encode($data));

        if ($data['status'] == 0 && $data['result_code'] == 0) {
            $sign = $data['sign'];
            unset($data['sign']);
            //验签
            if ($sign == $this->getSign($data,$set['key'])) {
                //查询
                $json['service'] = 'unified.trade.refundquery';
                $json['mch_id']  = $set['mch_id'];
                $json['out_trade_no'] = $out_trade_no;
                $json['nonce_str'] = $this->getNonceStr();
                $json['sign'] = $this->getSign($json,$set['key']);

                $post = $this->array2xml($json);
                $result = $this->postXmlCurl($post,'https://gateway.wepayez.com/pay/gateway');
                $disableEntities = libxml_disable_entity_loader(true);
                $data = json_decode(json_encode(simplexml_load_string($result, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
                libxml_disable_entity_loader($disableEntities);
                \Log::debug('微信港版支付退款查询',$data);
                if ($data['refund_status_0'] == 'PROCESSING' || $data['refund_status_0'] == 'SUCCESS') {
                    $this->changeOrderStatus($pay_order_model, Pay::ORDER_STATUS_COMPLETE, $data['transaction_id']);
                    \Log::debug('微信港版支付验签成功,退款');
                    return true;
                } else {
                    throw new AppException('微信港版支付退款错误');
                }
            }
        } else {
            \Log::debug('微信港版支付退款，请求错误',$data);
            throw new AppException('微信港版支付退款错误');
        }
    }

    private function changeOrderStatus($model, $status, $trade_no)
    {
        $model->status = $status;
        $model->trade_no = $trade_no;
        $model->save();
    }

    private function getSign($data,$key)
    {
        ksort($data, SORT_STRING);
        $string1 = '';
        foreach($data as $k => $v) {
            $string1 .= "{$k}={$v}&";
        }
        $string1 .= "key={$key}";
        return strtoupper(md5($string1));
    }

    /**
     * 构造签名
     *
     * @return mixed
     */
    function buildRequestSign()
    {
        // TODO: Implement buildRequestSign() method.
    }

    /**
     *获取带参数的请求URL
     */
    function getRequestURL() {

        $this->buildRequestSign();

        $reqPar =json_encode($this->parameters);
        \Log::debug('-----请求参数----', $reqPar);

        $requestURL = $this->getGateURL() . "?data=".base64_encode($reqPar);

        return $requestURL;
    }

    function setOpenId($data)
    {
        if (!$this->set['is_independent'] && $this->set['sub_appid'] && $this->set['sub_mchid']) {
            $data['openid'] = $data['sub_openid'];
        }
        return $data;
    }

    function array2xml($arr, $level = 1) {
        $s = $level == 1 ? "<xml>" : '';
        foreach ($arr as $tagname => $value) {
            if (is_numeric($tagname)) {
                $tagname = $value['TagName'];
                unset($value['TagName']);
            }
            if (!is_array($value)) {
                $s .= "<{$tagname}>" . (!is_numeric($value) ? '<![CDATA[' : '') . $value . (!is_numeric($value) ? ']]>' : '') . "</{$tagname}>";
            } else {
                $s .= "<{$tagname}>" . $this->array2xml($value, $level + 1) . "</{$tagname}>";
            }
        }
        $s = preg_replace("/([\x01-\x08\x0b-\x0c\x0e-\x1f])+/", ' ', $s);
        return $level == 1 ? $s . "</xml>" : $s;
    }
    public function getNonceStr($length = 32)
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str ="";
        for ( $i = 0; $i < $length; $i++ )  {
            $str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);
        }
        return $str;
    }

    private function postXmlCurl($xml, $url, $useCert = false, $second = 30)
    {
        $ch = curl_init();
        $curlVersion = curl_version();
      
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,TRUE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2);//严格校验
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        
        //post提交方式
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        //运行curl
        $data = curl_exec($ch);
        //返回结果
        if($data){
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            curl_close($ch);
            throw new WxPayException("curl出错，错误码:$error");
        }
    }

}