<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/27
 * Time: 9:30
 */

namespace Yunshop\JdSupply\services\sdk;


class JdRequest
{
    public $app_secret  = '';

    public $app_key = '';

    public $paramMap = array();


    public $serverRoot = 'http://api.jxhh.com';


    public function __construct($app_secret, $app_key)
    {

        if(!empty($serverRoot)){
            $this->serverRoot = $serverRoot;
        }


        if(!empty($app_secret)){
            $this->app_secret = $app_secret;
        }

        if(!empty($app_key)){
            $this->paramMap['appKey'] = $app_key;
        }


        $this->preventAttack();

    }


    //防止同源攻击
    public function preventAttack()
    {
        //加入毫秒时间戳POST参数
        $this->paramMap['timestamp'] = $this->getMillisecond();

        $this->paramMap['onnce'] = $this->setOnnce();

    }


    /**
     * 获取时间戳到毫秒
     * @return bool|string
     */
    public static function getMillisecond()
    {
        list($msec, $sec) = explode(' ', microtime());
        $msectime = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
        return $msectimes = substr($msectime, 0, 13);

    }

    public function setOnnce()
    {
        return md5($this->paramMap['appKey'] + $this->paramMap['timestamp']);
    }


    public function addParam($key,$values)
    {
        $addParam = array($key=>$values);
        $this->paramMap = array_merge($this->paramMap,$addParam);
    }

    public function batchAddParam($param)
    {
        $this->paramMap = array_merge($this->paramMap,$param);
    }

    public function removeParam($key)
    {
        foreach ($this->paramMap as $k => $v){
            if($key == $k){
                unset($this->paramMap[$k]);
            }
        }

    }
    public function getParam($key)
    {
        return $this->paramMap[$key];
    }

    public function getAllParam()
    {
        return $this->paramMap;
    }


    public function getServerRoot()
    {
        return $this->serverRoot;
    }

    public function getAppKey()
    {
        return  $this->paramMap['appKey'];
    }

    public function getAppSecret()
    {
        return $this->app_secret;
    }

    /**
     * 将参数转换成k=v拼接的形式
     */
    public function toQueryString()
    {
        $StrQuery="";
        foreach ($this->paramMap as $k=>$v){
            $StrQuery .= strlen($StrQuery) == 0 ? "" : "&";
            $StrQuery.=$k."=".urlencode($v);
        }
        return $StrQuery;
    }
}