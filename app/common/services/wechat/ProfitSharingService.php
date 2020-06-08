<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2019/10/21
 * Time: 11:04
 */

namespace app\common\services\wechat;


use app\common\modules\wechat\models\WechatProfitSharingLog;
use app\common\services\wechat\lib\WxPayApi;
use app\common\services\wechat\lib\WxPayConfig;
use app\common\services\wechat\lib\WxPayException;
use app\common\services\wechat\lib\WxPayProfitSharing;

class ProfitSharingService
{
    /**
     * @param $data
     * @return mixed
     * @throws lib\WxPayException
     */
    public static function addProfitSharing()
    {
//        $receiver = json_decode([
//            'type' => $data['type'],
//            'account' => $data['openid'],
//            'relation_type' => $data['relation_type'],
//        ]);
        $config = new WxPayConfig();
        $data = [
            'type' => 'MERCHANT_ID',
            'name' => $config->GetMchName(),
            'account' => $config->GetMerchantId(),
            'relation_type' => 'SERVICE_PROVIDER',
        ];
        $receiver = json_decode($data,256);

        $inputObj = new WxPayProfitSharing($config);
        $inputObj->SetReceiver($receiver);
        $result = WxPayApi::profitsharingaddreceiver($config, $inputObj);

        if ($result['return_code'] != 'SUCCESS') {
            throw new WxPayException($result['return_msg']);
        }

        return $result;
    }
    /**
     * @param $data
     * @return mixed
     * @throws lib\WxPayException
     */
    public static function deleteProfitSharing($data)
    {
        $receiver = json_decode([
            'type' => $data['type'],
            'account' => $data['openid'],
        ]);
        $config = new WxPayConfig();
        $inputObj = new WxPayProfitSharing($config);
        $inputObj->SetReceiver($receiver);
        $result = WxPayApi::profitsharingremovereceiver($config, $inputObj);

        if ($result['return_code'] != 'SUCCESS') {
            throw new WxPayException($result['return_msg']);
        }

        return $result;
    }

    /**
     * @param $amount
     * @param $transaction_id
     * @param $order_id
     * @return mixed
     * @throws WxPayException
     */
    public static function profitSharing($amount, $transaction_id, $order_id)
    {
        $config = new WxPayConfig();
        $out_order_no = createNo('PSO', true);
        //记录分账信息
        $data = [
            'uniacid' => \YunShop::app()->uniacid,
            'order_id' => $order_id,
            'mch_id' => $config->GetMerchantId(),
            'sub_mch_id' => $config->GetSubMerchantId(),
            'appid' => $config->GetAppId(),
            'sub_appid' => $config->GetSubAppId(),
            'transaction_id' => $transaction_id,
            'out_order_no' => $out_order_no,
            'status' => 0,
        ];
        $receiver = [
            [
                'type' => 'MERCHANT_ID',
                'account' => $config->GetMerchantId(),
                'amount' => (int)strval($amount * 100),
                'description' => '门店分账给服务商',
            ]
        ];
        $inputObj = new WxPayProfitSharing($config);
        $inputObj->SetTransaction_id($transaction_id);
        $inputObj->SetOut_order_no($out_order_no);
        $inputObj->SetReceivers(json_encode($receiver,256));
        $result = WxPayApi::profitsharing($config, $inputObj);

        if ($result['return_code'] != 'SUCCESS') {
            $data['status'] = -1;
            $data['message'] = $result['return_msg'] ?: $result['return_code'];
        } elseif ($result['result_code'] != 'SUCCESS') {
            $data['status'] = -1;
            $data['message'] = $result['err_code_des'] ?: $result['err_code'];
        } else {
            $data['status'] = 1;
            $data['message'] = 'SUCCESS';
        }

        $create_data = array_merge($data,$receiver[0]);
        $create_data['type'] = 0;
        WechatProfitSharingLog::create($create_data);
        return $result;
    }
    /**
     * @param $transaction_id
     * @return mixed
     * @throws WxPayException
     */
    public static function profitSharingFinish($transaction_id)
    {
        $config = new WxPayConfig();
        $out_order_no = createNo('PSF', true);
        //记录分账信息
        $data = [
            'uniacid' => \YunShop::app()->uniacid,
            'mch_id' => $config->GetMerchantId(),
            'sub_mch_id' => $config->GetSubMerchantId(),
            'appid' => $config->GetAppId(),
            'sub_appid' => $config->GetSubAppId(),
            'transaction_id' => $transaction_id,
            'out_order_no' => $out_order_no,
            'status' => 0,
            'account' => $config->GetMerchantId(),
            'description' => '完结分账',
        ];
        $inputObj = new WxPayProfitSharing($config);
        $inputObj->SetTransaction_id($transaction_id);
        $inputObj->SetOut_order_no($out_order_no);
        $inputObj->SetDescription($data['description']);
        $result = WxPayApi::profitsharingfinish($config, $inputObj);

        if ($result['return_code'] != 'SUCCESS') {
            $data['status'] = -1;
            $data['message'] = $result['return_msg'] ?: $result['return_code'];
        } elseif ($result['result_code'] != 'SUCCESS') {
            $data['status'] = -1;
            $data['message'] = $result['err_code_des'] ?: $result['err_code'];
        } else {
            $data['status'] = 1;
            $data['message'] = 'SUCCESS';
        }
        $data['type'] = 1;
        WechatProfitSharingLog::updateOrCreate(['transaction_id' => $data['transaction_id']], $data);
        return $result;
    }

    /**
     * @param $out_order_no
     * @param $transaction_id
     * @throws WxPayException
     */
    public static function profitSharingQuery($out_order_no, $transaction_id)
    {
        $config = new WxPayConfig();
        $inputObj = new WxPayProfitSharing($config);
        $inputObj->SetTransaction_id($transaction_id);
        $inputObj->SetOut_order_no($out_order_no);
        $result = WxPayApi::profitsharingquery($config, $inputObj);

        if ($result['return_code'] =! 'SUCCESS') {
            $data['status'] = -1;
            $data['message'] = $result['return_msg'] ?: $result['return_code'];
        } elseif ($result['result_code'] != 'SUCCESS') {
            $data['status'] = -1;
            $data['message'] = $result['err_code_des'] ?: $result['err_code'];
        } else {
            $data['status'] = 1;
            $data['message'] = $result['result_code'];
        }
    }

}