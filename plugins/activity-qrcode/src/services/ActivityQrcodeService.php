<?php

namespace Yunshop\ActivityQrcode\services;

use Ixudra\Curl\Facades\Curl;
use app\common\exceptions\AppException;
use Yunshop\RechargeCode\common\services\QrCode;


class ActivityQrcodeService
{
    //获取二维码
    public static function getQrCode($url)
    {
        return (new QrCode($url, 'app/public/qr/activity'))->url();
    }

    //解析二维码内容
    public static function parseQrCode($path){
        include __DIR__.'/vendor/autoload.php';
        $qrcode = new \Zxing\QrReader($path);  //图片路径
        $text = $qrcode->text(); //返回识别后的文本
        return $text;
    }


}