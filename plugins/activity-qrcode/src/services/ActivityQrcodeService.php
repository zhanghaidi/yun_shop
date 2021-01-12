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

        $QRCodeReader = new Libern\QRCodeReader\QRCodeReader();
        $qrcode_text = $QRCodeReader->decode($path);
        echo $qrcode_text;
    }


}