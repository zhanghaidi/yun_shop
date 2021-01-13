<?php


namespace Yunshop\ActivityQrcode\services;

use Ixudra\Curl\Facades\Curl;
use app\common\exceptions\AppException;
use Yunshop\ActivityQrcode\services\QrCode;
use Zxing\QrReader;

class ActivityQrcodeService
{
    //获取二维码
    public static function getQrCode($url, $size, $margin)
    {
        return (new QrCode($url, 'app/public/qr/activity', $size, $margin))->url();
    }

    //解析二维码内容
    public static function parseQrCode($path){

        return (new QrReader($path))->text();

    }


}