<?php


namespace app\frontend\modules\order\services;


use app\common\services\txyunsms\SmsSingleSender;

class SmsMessageService
{
    private $orderModel;
    function __construct($orderModel,$formId = '',$type = 1)
    {
        $this->orderModel = $orderModel;
    }

    public function sent()
    {
        \Log::debug('订单发货短信通知');
        try {
            $set = \Setting::get('shop.sms');
            if ($set['type'] == 3 && $set['aly_templateSendMessageCode']) {
                return $this->aliYun($set);
            } elseif ($set['type'] == 5 && $set['tx_templateSendMessageCode']) {
                return $this->txYun($set);
            } else {
                \Log::debug('模板未设置');
                return false;
            }
        } catch (\Exception $e) {
            \Log::debug($e->getMessage());
            return false;
        }
    }

    public function aliYun($set)
    {
        //查询手机号
        $mobile = \app\common\models\Member::find($this->orderModel->uid)->mobile;
        //todo 发送短信
        $aly_sms = new \app\common\services\aliyun\AliyunSMS(trim($set['aly_appkey']), trim($set['aly_secret']));
        $response = $aly_sms->sendSms(
            $set['aly_signname'], // 短信签名
            $set['aly_templateSendMessageCode'], // 发货提醒短信
            $mobile, // 短信接收者
            Array(  // 短信模板中字段的值
                "shop" => \Setting::get('shop.shop')['name'],
            )
        );
        if ($response->Code == 'OK' && $response->Message == 'OK') {
            \Log::debug('模板阿里云短信发送成功');
        } else {
            \Log::debug($response->Message);
        }
        return true;
    }

    public function txYun($set)
    {
        //查询手机号
        $mobile = \app\common\models\Member::find($this->orderModel->uid)->mobile;
        $ssender = new SmsSingleSender(trim($set['tx_sdkappid']), trim($set['tx_appkey']));
        $response = $ssender->sendWithParam('86', $mobile, $set['tx_templateSendMessageCode'],
            [\Setting::get('shop.shop')['name']], $set['tx_signname'], "", "");  // 签名参数不能为空串
        $response = json_decode($response);
        if ($response->result == 0 && $response->errmsg == 'OK') {
            \Log::debug('模板腾讯云短信发送成功');
        } else {
            \Log::debug($response->errmsg);
        }
        return true;
    }

}