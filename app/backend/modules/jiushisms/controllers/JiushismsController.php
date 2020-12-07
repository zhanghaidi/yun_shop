<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/10/23 下午2:26
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\backend\modules\jiushisms\controllers;


use app\backend\modules\member\models\Member;
use app\common\components\BaseController;
use app\common\helpers\Url;
use app\common\services\txyunsms\SmsSingleSender;
use model\Db;

class JiushismsController extends BaseController
{

    public function sendsms()
    {
        $post = request()->input();

        if ($post['submit']) {
            try {
                //sms_send 是否开启
                $smsSet = \Setting::get('shop.sms');
                //是否设置
                if ($smsSet['type'] != 5 || empty($smsSet['tx_templateJiushiSmsCode'])) {
                    return false;
                }
                $mobile = trim($post['mobile']);
                if (empty($mobile)) {
                    return $this->message('手机号不能为空', Url::absoluteWeb(''), 'danger');
                }
                if(empty($post['jiushi_wechat'])){
                    return $this->message('灸师企业微信号不能为空', Url::absoluteWeb(''), 'danger');
                }
                //组装变量
                $param =  [$post['jiushi_wechat'],$mobile];

                //初始化发短息类
                $ssender = new SmsSingleSender(trim($smsSet['tx_sdkappid']), trim($smsSet['tx_appkey']));
                $response = $ssender->sendWithParam('86', $mobile, $smsSet['tx_templateJiushiSmsCode'],
                    $param, $smsSet['tx_signname'], "", "");  // 签名参数不能为空串
                $response = json_decode($response);

                if ($response->result == 0 && $response->errmsg == 'OK') {
                    //插入短信记录表
                    $insert_data = [
                        'mobile' => $mobile,
                        'content' => $post['jiushi_wechat'],
                        'result' => $response->errmsg,
                        'createtime' => time()
                    ];
                    return $this->message('发送成功！', Url::absoluteWeb('jiushisms.jiushisms.sendsms'), 'success');
                } else {
                    \Log::debug($response->errmsg);
                    $insert_data = [
                        'mobile' => $mobile,
                        'content' => $post['jiushi_wechat'],
                        'result' => $response->errmsg,
                        'createtime' => time()
                    ];
                    Db::table('yz_sendsms_log')->insert($insert_data);
                    return $this->message('发送失败！'.$response->errmsg, Url::absoluteWeb('jiushisms.jiushisms.sendsms'), 'danger');
                }
            } catch (\Exception $e) {
                return $this->message('发送失败！'.$response->errmsg, Url::absoluteWeb(''), 'danger');
            }
        }

        return view('jiushisms.sendsms')->render();
    }

    public function smslist(){

        return view('jiushisms.smslist')->render();

    }
}
