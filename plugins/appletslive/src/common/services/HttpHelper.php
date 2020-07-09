<?php
/**
 * Created by PhpStorm.
 * User: weifeng
 * Date: 2020-02-28
 * Time: 14:09
 *
 *    .--,       .--,
 *   ( (  \.---./  ) )
 *    '.__/o   o\__.'
 *       {=  ^  =}
 *        >  -  <
 *       /       \
 *      //       \\
 *     //|   .   |\\
 *     "'\       /'"_.-~^`'-.
 *        \  _  /--'         `
 *      ___)( )(___
 *     (((__) (__)))     梦之所想,心之所向.
 */

namespace Yunshop\Appletslive\common\services;


class HttpHelper
{
    public static $connectTimeout = 30;//30 second
    public static $readTimeout = 80;//80 second

    public static function doPost($url,$data,$header=null){
        return self::curl($url,'POST',$data,$header);
    }

    public static function doGet($url,$data,$header){
        return self::curl($url,'GET',$data,$header);
    }

    public static function curl($url, $httpMethod = "GET", $postFields = null,$headers = array())
    {
        if(empty($headers)){
            $headers = ["Content-Type:application/json;charset=UTF-8"];
        }
        $headers[] = 'Expect:';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $httpMethod);
        if(ENABLE_HTTP_PROXY) {
            curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_PROXY, HTTP_PROXY_IP);
            curl_setopt($ch, CURLOPT_PROXYPORT, HTTP_PROXY_PORT);
            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $postData = is_array($postFields) ? json_encode($postFields, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : $postFields;
        curl_setopt($ch, CURLOPT_POSTFIELDS,$postData );

        if (self::$readTimeout) {
            curl_setopt($ch, CURLOPT_TIMEOUT, self::$readTimeout);
        }

        if (self::$connectTimeout) {
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::$connectTimeout);
        }

        if(strlen($url) > 5 && strtolower(substr($url,0,5)) == "https" ) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }

        if (is_array($headers) && 0 < count($headers))
        {
            curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
        }

        $curlRes = curl_exec($ch);

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if($httpCode != 200){
            throw new \Exception('curl http error:'.$httpCode);
        }

        return $curlRes;
    }
}