<?php
/**
 * Created by PhpStorm.
 * User: blank
 * Date: 2020/4/23
 * Time: 9:59
 */

namespace app\common\services\utils;


use app\common\exceptions\ShopException;

/**
 * 加解密工具类
 * Class EncryptUtil
 * @package app\common\services\utils
 */
class EncryptUtil
{
    const AES_EBC_MODE = "AES-128-ECB";


    /**
     * AES加密，模式为：AES/ECB/PKCK7Padding
     * @param string $data
     * @param string $secKey
     * @param string $method
     * @return array
     */
    public static function encryptECB($data, $secKey, $method = null)
    {
        if (is_null($method)) {
            $method = self::AES_EBC_MODE;
        }

        $encrypted = openssl_encrypt($data, $method, $secKey, OPENSSL_RAW_DATA);

        if($encrypted === false){
            return self::returnData(false,'aes加密失败');
        }

        return self::returnData(true,'aes加密', base64_encode($encrypted));

    }

    /**
     * AES解密，模式为：AES/ECB/PKCK7Padding
     * @param string $data
     * @param string $secKey
     * @param string $method
     * @return array
     */
    public static function decryptECB($data, $secKey,  $method = null)
    {
        if (is_null($method)) {
            $method = self::AES_EBC_MODE;
        }

        $decrypted = openssl_decrypt(base64_decode($data), $method, $secKey, OPENSSL_RAW_DATA);

        if($decrypted === false){
            return self::returnData(false,'aes解密失败');
        }

        return self::returnData(true,'aes解密', $decrypted);
    }


    /**
     * 使用公钥加密
     * @param string $data
     * @param string $public_content
     * @return array
     */
    public static function encrypt($data, $public_content)
    {
        $res = "-----BEGIN PUBLIC KEY-----\n" .
            wordwrap($public_content, 64, "\n", true) .
            "\n-----END PUBLIC KEY-----";

        $pubKey = openssl_get_publickey($res);

        if($pubKey === false){
            return self::returnData(false,'rsa解密公钥无效');
        }

        $crypted = '';
        $isSuccess = openssl_public_encrypt($data, $crypted, $pubKey);
        openssl_free_key($pubKey);
        if($isSuccess == false){
            return self::returnData(false,'rsa加密失败');
        }
        return self::returnData(true,'rsa加密', base64_encode($crypted));
    }

    /**
     * 使用私钥解密
     * @param string $data
     * @param string $private_content
     * @return array
     */
    public static function decrypt($data, $private_content)
    {
        $res = "-----BEGIN RSA PRIVATE KEY-----\n" .
            wordwrap($private_content, 64, "\n", true) .
            "\n-----END RSA PRIVATE KEY-----";


        $priKey = openssl_get_privatekey($res);
        if($priKey === false){
            return self::returnData(false,'rsa解密私钥无效');
        }

        $decrypted = '';
        $isSuccess = openssl_private_decrypt(base64_decode($data), $decrypted, $priKey);
        openssl_free_key($priKey);
        if(!$isSuccess){
            return self::returnData(false,'rsa解密失败');
        }
        return self::returnData(true,'rsa解密成功', $decrypted);
    }

    /**
     * 使用私钥进行签名
     * @param string $data
     * @param string $private_content
     * @return array
     */
    public static function sign($data, $private_content)
    {

        $res = "-----BEGIN RSA PRIVATE KEY-----\n" .
            wordwrap($private_content, 64, "\n", true) .
            "\n-----END RSA PRIVATE KEY-----";

        $priKey = openssl_get_privatekey($res);

        if($priKey === false){
            return self::returnData(false,'rsa签名私钥无效');
        }

        $binary_signature = '';
        $isSuccess = openssl_sign($data, $binary_signature, $priKey, OPENSSL_ALGO_MD5);
        openssl_free_key($priKey);

        if(!$isSuccess) {
            return self::returnData(false,'rsa签名失败');
        }

        return self::returnData(true,'rsa签名成功',base64_encode($binary_signature));
    }

    /**
     * 使用公钥进行验签
     * @param string $signData 需要验证签名的数据
     * @param string $signParam 签名字符串
     * @param string $public_content
     * @return array
     */
    public static function verify($signData, $signParam, $public_content)
    {

        $res = "-----BEGIN PUBLIC KEY-----\n" .
            wordwrap($public_content, 64, "\n", true) .
            "\n-----END PUBLIC KEY-----";

        $pubKey = openssl_get_publickey($res);

        if($pubKey === false) {
            return self::returnData(false,'rsa验签公钥无效');
        }

        $signParam = base64_decode($signParam);
        $isMatch = openssl_verify($signData, $signParam, $pubKey, OPENSSL_ALGO_MD5) === 1;
        openssl_free_key($pubKey);
        return self::returnData($isMatch,'rsa验签');
    }


    /**
     * @param bool $code 状态 true|false
     * @param string $msg 说明
     * @param string $data 数据
     * @return array
     */
    protected static function returnData($code, $msg = '', $data = '')
    {

        return ['code'=> $code, 'msg'=> $msg, 'data' => $data];
    }

}