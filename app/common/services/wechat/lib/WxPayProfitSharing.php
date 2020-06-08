<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2019/10/8
 * Time: 16:41
 */

namespace app\common\services\wechat\lib;


use app\common\services\wechat\lib\WxPayConfig;
use app\common\services\wechat\lib\WxPayDataBase;

class WxPayProfitSharing extends WxPayDataBase
{
    public function __construct(WxPayConfig $config)
    {
        $this->values['appid'] = $config->GetAppId();
//        $this->values['sub_appid'] = $config->GetSubAppId();
        $this->values["mch_id"] = $config->GetMerchantId();
        $this->values["sub_mch_id"] = $config->GetSubMerchantId();
    }

    /**
     * 设置随机字符串，不长于32位。推荐随机数生成算法
     * @param string $value
     **/
    public function SetNonce_str($value)
    {
        $this->values['nonce_str'] = $value;
    }
    /**
     * 获取随机字符串，不长于32位。推荐随机数生成算法的值
     * @return 值
     **/
    public function GetNonce_str()
    {
        return $this->values['nonce_str'];
    }
    /**
     * 判断随机字符串，不长于32位。推荐随机数生成算法是否存在
     * @return true 或 false
     **/
    public function IsNonce_strSet()
    {
        return array_key_exists('nonce_str', $this->values);
    }
    public function SetAppid($value)
    {
        $this->values['appid'] = $value;
    }

    public function GetAppid()
    {
        return $this->values['appid'];
    }
    public function SetMch_id($value)
    {
        $this->values['mch_id'] = $value;
    }

    public function GetMch_id()
    {
        return $this->values['mch_id'];
    }
    public function SetSub_mch_id($value)
    {
        $this->values['sub_mch_id'] = $value;
    }

    public function GetSub_mch_id()
    {
        return $this->values['sub_mch_id'];
    }

    public function GetSubOpenid()
    {
        return $this->values['sub_openid'];
    }

    public function SetOpenid($value)
    {
        $this->values['openid'] = $value;
    }

    public function GetOpenid()
    {
        return $this->values['openid'];
    }

    public function SetSub_openid($value)
    {
        $this->values['sub_openid'] = $value;
    }

    public function SetReceiver($value)
    {
        $this->values['receiver'] = $value;
    }
    public function GetReceiver()
    {
        return $this->values['receiver'];
    }

    public function IsReceiverSet()
    {
        return array_key_exists('receiver', $this->values);
    }
    public function SetReceivers($value)
    {
        $this->values['receivers'] = $value;
    }
    public function GetReceivers()
    {
        return $this->values['receivers'];
    }

    public function IsReceiversSet()
    {
        return array_key_exists('receivers', $this->values);
    }
    public function SetTransaction_id($value)
    {
        $this->values['transaction_id'] = $value;
    }
    public function GetTransaction_id()
    {
        return $this->values['transaction_id'];
    }

    public function IsTransaction_idSet()
    {
        return array_key_exists('transaction_id', $this->values);
    }

    public function SetOut_order_no($value)
    {
        $this->values['out_order_no'] = $value;
    }
    public function GetOut_order_no()
    {
        return $this->values['out_order_no'];
    }

    public function IsOut_order_noSet()
    {
        return array_key_exists('out_order_no', $this->values);
    }

    public function SetDescription($value)
    {
        $this->values['description'] = $value;
    }

    public function GetDescription()
    {
        return $this->values['description'];
    }

    public function IsDescription()
    {
        return array_key_exists('description', $this->values);
    }

}