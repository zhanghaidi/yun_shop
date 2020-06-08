<?php
/**
 * Created by PhpStorm.
 * User: weifeng
 * Date: 2020-05-11
 * Time: 17:53
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


class CosV5Service
{
    /**
     * 初始化
     * @param $region
     * @param $secretId
     * @param $secretKey
     * @return \Qcloud\Cos\Client
     */
    public static function init($region, $secretId, $secretKey)
    {
        return new \Qcloud\Cos\Client(
            array(
                'region' => $region,
                'schema' => 'https', //协议头部，默认为http
                'credentials'=> array(
                    'secretId'  => $secretId,
                    'secretKey' => $secretKey),
            ));
    }

    /**
     * 上传文件流
     * @param $bucket
     * @param $key
     * @param $srcPath
     * @param $cosClient
     */
    public static function uploadStream($bucket, $key, $file, $cosClient)
    {
        try {
            if ($file) {
                $result = $cosClient->putObject(array(
                    'Bucket' => $bucket,
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
    public static function getObjUrl($bucket, $key, $cosClient, $expire)
    {
        try {
            $signedUrl = $cosClient->getObjectUrl($bucket, $key, '+'.$expire.' minutes');
            // 请求成功
            return $signedUrl;
        } catch (\Exception $e) {
            // 请求失败
            \Log::error('qcloud-cos读取文件报错', $e);
            return false;
        }
    }

    /**
     * 获取文件 UrL
     * @param $bucket
     * @param $key
     * @param $cosClient
     * @param $expire
     */
    public static function getObjectUrl($bucket, $key, $cosClient, $expire)
    {
        try {
            $signedUrl = $cosClient->getObjectUrl($bucket, $key, '+'.$expire.' minutes');
            // 请求成功
            return $signedUrl;
        } catch (\Exception $e) {
            // 请求失败
            \Log::error('qcloud-cos读取文件报错', $e);
            return false;
        }
    }
}