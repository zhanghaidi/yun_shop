<?php
/**
 * Created by PhpStorm.
 * User: weifeng
 * Date: 2020-05-11
 * Time: 16:18
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

namespace app\common\services;


use app\platform\modules\system\models\SystemSetting;

class QcloudCosService
{
    private $app_id;
    private $region;
    private $secretId;
    private $secretKey;
    private $bucket;
    private $cosClient;

    public function __construct($set = false, $region = '', $secretId = '', $secretKey = '', $bucket = '', $appid = '')
    {
        $remote = SystemSetting::settingLoad('remote', 'system_remote');

        if ($set == true) {
            $this->app_id = $appid;
            $this->region = $region; //地域
            $this->secretId = $secretId;
            $this->secretKey = $secretKey;
            $this->bucket = $bucket . '-' . $appid;
        } else {
            $this->app_id = $remote['cos']['appid'];
            $this->region = $remote['cos']['local']; //地域
            $this->secretId = $remote['cos']['secretid'];
            $this->secretKey = $remote['cos']['secretkey'];
            $this->bucket = $remote['cos']['bucket'] . '-' . $remote['cos']['appid'];
        }

//        $this->region = 'ap-guangzhou'; //地域
//        $this->secretId = 'AKIDoQ1OYWBFyRf5JUB7X9YhQxFH6jMqpVih';
//        $this->secretKey = 'uYy71fR2RfZVDiybz7zY4VffSyGcgaHi';
//        $this->bucket = 'ysm-1251768088';

        $this->cosClient = CosV5Service::init($this->region, $this->secretId, $this->secretKey);
    }

    /**
     * 上传文件流 测试
     * @param $bucket
     * @param $key
     * @param $srcPath
     * @param $cosClient
     */
    public function uploadTest($key = '')
    {
        $file = request()->getSchemeAndHttpHost() . '/static/' . $key;

        try {
            if ($file) {
                $result = $this->cosClient->putObject(array(
                    'Bucket' => $this->bucket,
                    'Key' => $key,
                    'Body' => file_get_contents($file),
                ));
                return $result;
            } else {
                return '文件资源不存在';
            }
        } catch (\Exception $e) {
            \Log::error('qcloud-cos上传文件流报错', $e->getMessage());
            return $e->getMessage();
        }
    }

    /**
     * 上传文件流
     * @param $bucket
     * @param $key
     * @param $srcPath
     * @param $cosClient
     */
    public function upload($key = '')
    {
        if (strexists($key, '/static/upload')) {
            $file = request()->getSchemeAndHttpHost() . $key;
        } else {
            $file = request()->getSchemeAndHttpHost() . '/static/upload/' . $key;
        }

        try {
            if ($file) {
                $result = $this->cosClient->putObject(array(
                    'Bucket' => $this->bucket,
                    'Key' => $key,
                    'Body' => file_get_contents($file),
                ));
                return $result;
            } else {
                return '文件资源不存在';
            }
        } catch (\Exception $e) {
            \Log::error('qcloud-cos上传文件流报错', $e->getMessage());
            return $e->getMessage();
        }
    }

    /**
     * 获取上传的url
     * @param $bucket
     * @param $key
     * @param $cosClient
     * @param $expire
     */
    public function getObjUrl($key = '')
    {
        $expire = 10;

        try {
            $signedUrl = $this->cosClient->getObjectUrl($this->bucket, $key, '+'.$expire.' minutes');
            // 请求成功
            return $signedUrl;
        } catch (\Exception $e) {
            // 请求失败
            \Log::error('qcloud-cos读取文件报错', $e);
            return $e->getMessage();
        }
    }
}