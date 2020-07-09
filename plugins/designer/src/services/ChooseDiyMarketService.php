<?php
/**
 * Created by PhpStorm.
 * User: 芸众网
 * Date: 2019/6/18
 * Time: 14:00
 */

namespace Yunshop\Designer\services;

use Yunshop\Designer\models\Designer;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Yunshop\Designer\models\DiyMarketSync;

class ChooseDiyMarketService
{
    private $url = '/designer-market/get-designer-data/';
    private $syncId;

    public function __construct($syncId)
    {
        $this->syncId = $syncId;
    }

    public function handle()
    {

        $diyMarketSync = DiyMarketSync::where('sync_id',$this->syncId)->first();
        $result = $this->getCurlData();
        if ($result['data']) {
            $resultData = json_decode($result['data']['designer_data']);
//            $designer = new Designer();
            $data = json_decode($resultData->datas);
            foreach ($data as $key => $value) {
                $data[$key] = $this->object_to_array($value);
            }
            $datas  = $this->urlTransition($data);
            foreach ($datas as $key => $value){
                $datas[$key] = $this->arrayToObject($value);
            }

            $postData = [
                'uniacid' => \Yunshop::app()->uniacid,
                'page_name' => $diyMarketSync->title,
                'page_type' => $resultData->page_type,
                'page_info' => $resultData->page_info,
                'keyword' => $resultData->keyword,
                'is_default' => $resultData->is_default,
                'datas' => json_encode($datas),
                'created_at' => time(),
                'updated_at' => time(),
            ];
            $designerId = Designer::insertGetId($postData);
            if ($designerId) {
                return $designerId;
            } else {
                return '选取失败';
            }
        }
        return true;
    }


    /**
     * @return mixed
     */
    private function getCurlData()
    {
        $key    = \Setting::get('shop.key')['key'];
        $secret = \Setting::get('shop.key')['secret'];
        $data = \Curl::to(\config::get('auto-update.diyMarket').$this->url.$this->syncId)
            ->withHeader(
                "Authorization: Basic " . base64_encode("{$key}:{$secret}")
            )
            ->asJsonResponse(true)
            ->get();

        return $data;
    }

    /**
     * 递归转对象
     * @param $e
     * @return object|void
     */
    public function arrayToObject($e){

        if( gettype($e)!='array' ) return;
        foreach($e as $k=>$v){
            if( gettype($v)=='array' || getType($v)=='object' )
                $e[$k]=(object)$this->arrayToObject($v);
        }
        return (object)$e;
    }
    /**
     * 递归 对象 转 数组
     *
     * @param object $obj 对象
     * @return array
     */
    private function object_to_array($obj)
    {
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

    /**
     * 递归替换文件中域名，文件名
     * @param $data
     * @return mixed
     */
    public function urlTransition(&$data)
    {
        foreach ($data as $key => &$value) {
            if (is_array($value)) {
                $this->urlTransition($value);
            } else {
                if($key == 'uniacid'){
                    $value = \Yunshop::app()->uniacid;
                }
                if(strpos($value,'/addons/yun_shop/') !== false){
                     if(explode('i=',$value)['1']){
                         $value = explode('i=',$value)['0'].\YunShop::app()->uniacid;
                     }
                }
                if(filter_var($value, FILTER_VALIDATE_URL)) {
                    if(get_headers($value)){
                        $value = $this->getPicUrl($value);
                    }
                }
            }
        }
        return $data;
    }

    private function getPicUrl($url)
    {
        $client = new Client(['verify' => false]);  //忽略SSL错误

        //TODO 如果nginx配置了防盗链，返回403，直接返回网络图片
        try {
            $dir = image_put_path().'images/'.\Yunshop::app()->uniacid.'/'.date('Y').'/'.date('m').'/';
            is_dir($dir) OR mkdir($dir, 0777, true);
            $response = $client->get($url, ['save_to' =>image_put_path().'images/'.\Yunshop::app()->uniacid.'/'.date('Y').'/'.date('m').'/'.basename($url)]);  //保存远程url到文件
        } catch (\InvalidArgumentException $e) {

        }


        if (config('app.framework') == 'platform') {
            $attachment = 'static/upload/';
        } else {
            $attachment = 'attachment/';
        }
        $httpType = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
        if($response){
            return  $httpType.$_SERVER['HTTP_HOST'].'/'.$attachment.'images/'.\Yunshop::app()->uniacid.'/'.date('Y').'/'.date('m').'/'.basename($url);
        }else{
            return $url;
        }
    }
}
