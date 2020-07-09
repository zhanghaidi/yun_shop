<?php
/**
 * Created by PhpStorm.
 * User: 芸众网
 * Date: 2019/6/17
 * Time: 9:10
 */

namespace Yunshop\Designer\services;

use Yunshop\Designer\models\DiyMarketSync;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use app\common\exceptions\ShopException;

class SyncDiyMarketService
{
    private $url = '/designer-market/get-diy';

    private $data = array();

    public function __construct()
    {
        $key    = \Setting::get('shop.key')['key'];
        $secret = \Setting::get('shop.key')['secret'];
        $data = \Curl::to(\config::get('auto-update.diyMarket').$this->url)
            ->withHeader(
                "Authorization: Basic " . base64_encode("{$key}:{$secret}")
            )
            ->asJsonResponse(true)
            ->get();
        if($data['isExists']){
             throw new ShopException($data['message']);
        }
        $this->data = $data;
        $this->handle();
    }

    /**
     * 通过远端主键ID 更新本地数据库
     * @return bool
     */
    public function handle()
    {
        $data = $this->data['data'];

        $syncId = array_column($data, 'id');
        $syncIds = DiyMarketSync::select('sync_id')->whereIn('sync_id',$syncId)->get();

        $array_1 = array_flip($syncId);
        //去重
        if($syncIds){
            foreach ($syncIds as $key => $value){
                if (isset($array_1[$value->sync_id])) {
                    unset($array_1[$value->sync_id]);
                }
            }
        }

         //去重
        $resultData = array();
        foreach ($data as $key => $value){
            if(isset($array_1[$value['id']])){
                $resultData[$key] = [
                    'sync_id' => $value['id'],
                    'title' => $value['title'],
                    'category' => $value['category'],
                    'page' => $value['page'],
                    'type' => $value['type'],
                    'thumb_url' => $this->getPicUrl($value['thumb_url']),
                    'data' => 0,
                    'created_at' => $_SERVER['REQUEST_TIME'],
                    'updated_at' => $_SERVER['REQUEST_TIME']
                ];
            }
        }

        DiyMarketSync::whereNotIn('sync_id',$syncId)->delete();
        DiyMarketSync::insert($resultData);
        return true;
    }

    /**
     * 把远端图片写入本地
     * @param $url
     * @return string
     */
    private function getPicUrl($url)
    {
        if($this->fileExists($url) == false){
            return $url;
        }
        $client = new Client(['verify' => false]);  //忽略SSL错误

        //TODO 如果nginx配置了防盗链，返回403，直接返回网络图片
        try {
            $dir = image_put_path().'images/'.\Yunshop::app()->uniacid.'/'.date('Y').'/'.date('m').'/';
            is_dir($dir) OR mkdir($dir, 0777, true);
            $response = $client->get($url, ['save_to' =>image_put_path().'images/'.\Yunshop::app()->uniacid.'/'.date('Y').'/'.date('m').'/'.basename($url)]);  //保存远程url到文件
        } catch (ShopException $e) {

        }

        if (config('app.framework') == 'platform') {
            $attachment = 'static/upload/';
        } else {
            $attachment = 'attachment/';
        }
        $httpType = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
        if($response){
            return $httpType.$_SERVER['HTTP_HOST'].'/'.$attachment.'images/'.\Yunshop::app()->uniacid.'/'.date('Y').'/'.date('m').'/'.basename($url);
        }else{
            return $url;
        }
    }

    /**
     * 递归 对象 转 数组
     *
     * @param object $obj 对象
     * @return array
     */
    private function objectToArray($obj) {
        $obj = (array)$obj;
        foreach ($obj as $k => $v) {
            if (gettype($v) == 'resource') {
                return;
            }
            if (gettype($v) == 'object' || gettype($v) == 'array') {
                $obj[$k] = (array)$this->objectToArray($v);
            }
        }
        return $obj;
    }

    /**
     * @param $url
     * @return bool
     */
    private function fileExists($url)
    {
        $ch = curl_init();
        curl_setopt($ch, curlopt_url,$url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
        curl_setopt($ch, curlopt_nobody, 1); // 不下载
        curl_setopt($ch, curlopt_failonerror, 1);
        curl_setopt($ch, curlopt_returntransfer, 1);
        if(curl_exec($ch)!==false) {
            return true;
        }else {
            return false;
        }
    }

}
