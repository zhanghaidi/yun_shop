<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019/4/24
 * Time: 下午3:10
 */

namespace app\payment\controllers;

use app\common\helpers\Url;
use app\common\models\AccountWechats;
use app\common\modules\orderGoods\OrderGoodsCollection;
use app\common\services\Pay;
use app\common\services\wechat\lib\WxPayConfig;
use app\common\services\wechat\lib\WxPayResults;
use app\payment\PaymentController;
use Yunshop\ConvergePay\services\NotifyService;
use app\common\events\withdraw\WithdrawSuccessEvent;
use Yunshop\StoreCashier\frontend\store\models\PreOrder;
use Yunshop\StoreCashier\frontend\store\models\PreOrderGoods;

class HkscanController extends PaymentController
{
    private $attach = [];
    private $parameter = [];


    public function preAction()
    {
        parent::preAction();

        if (empty(\YunShop::app()->uniacid)) {
            $post = $this->getResponseResult();
            $this->attach = $post['attach'];
            \Setting::$uniqueAccountId = \YunShop::app()->uniacid = $this->attach;
        }
    }

    /**
     * @throws \app\common\exceptions\AppException
     * @throws \app\common\exceptions\ShopException
     * @throws \app\common\services\wechat\lib\WxPayException
     */
    public function notifyUrl()
    {
        $post = $this->getResponseResult();
        \Log::info('港版微信支付回调结果', $post);
        $this->log($post);
        //验签
        $result = $this->getSignResult($post);

        if ($result) {
            $data = [
                'total_fee'    => $post['total_fee'] ,
                'out_trade_no' => $post['out_trade_no'],
                'trade_no'     => 'hk_pay',
                'unit'         => 'fen',
                'pay_type'     => '港版扫码支付',
                'pay_type_id'     => 56
            ];
            $this->payResutl($data);
            echo "success";
        } else {
            echo "fail";
        }
    }


    /**
     * 支付日志
     *
     * @param $post
     */
    public function log($post)
    {
        //访问记录
        Pay::payAccessLog();
        //保存响应数据
        Pay::payResponseDataLog($post['out_trade_no'], '微信港版扫码支付', json_encode($post));
    }


    /**
     * 签名验证
     *
     * @return bool
     */
    public function getSignResult($data)
    {
        $sign = $data['sign'];
        $set = \Setting::get('plugin.hk_pay_set');
        //验签
        unset($data['sign']);
        $string1 = '';
        foreach($data as $k => $v) {
            $string1 .= "{$k}={$v}&";
        }
        $string1 .= "key={$set['key']}";
        if ($sign == strtoupper(md5($string1))) {
            \Log::debug('验签成功');
            return true;
        }
        return false;
    }

    /**
     * 获取回调结果
     *
     * @return array|mixed|\stdClass
     */
    public function getResponseResult()
    {
        $input = file_get_contents('php://input');
        if (!empty($input) && empty($_POST['out_trade_no'])) {
            //禁止引用外部xml实体
            $disableEntities = libxml_disable_entity_loader(true);

            $data = json_decode(json_encode(simplexml_load_string($input, 'SimpleXMLElement', LIBXML_NOCDATA)), true);

            libxml_disable_entity_loader($disableEntities);

            if (empty($data)) {
                exit('fail');
            }
            if ($data['status'] != 0 || $data['result_code'] != 0 || $data['pay_result'] != 0) {
                exit('fail');
            }
            $post = $data;
        } else {
            $post = $_POST;
        }

        return $post;
    }
}