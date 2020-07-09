<?php
/**
 * Created by PhpStorm.
 * User: 芸众网
 * Date: 2019/6/12
 * Time: 15:16
 */

namespace Yunshop\Designer\services;


class DiyMarketService
{
    private $url = 'http://gy18465381.imwork.net/designer-market/get-diy';


    static $data;

    private function __construct()
    {
        $key    = \Setting::get('shop.key')['key'];
        $secret = \Setting::get('shop.key')['secret'];
        $data = \Curl::to($this->url)
            ->withHeader(
                "Authorization: Basic " . base64_encode("{$key}:{$secret}")
            )
            ->asJsonResponse(true)
            ->get();
        self::$data = $this->object_to_array($data);

    }

    //单例
   public static function getInstance(){
        if(self::$data){
            return self::$data;
        }else{
            $self = new self();
            return $self::$data;
        }
    }


    /**
     * 递归 对象 转 数组
     *
     * @param object $obj 对象
     * @return array
     */
    private function object_to_array($obj) {
        $obj = (array)$obj;
        foreach ($obj as $k => $v) {
            if (gettype($v) == 'resource') {
                return;
            }
            if (gettype($v) == 'object' || gettype($v) == 'array') {
                $obj[$k] = (array)$this->object_to_array($v);
            }
        }

        return $obj;
    }

    //禁止克隆
    private function __clone()
    {
        // TODO: Implement __clone() method.
    }
}