<?php

namespace Yunshop\ActivityQrcode\services;

use Ixudra\Curl\Facades\Curl;
use app\common\exceptions\AppException;
use Yunshop\RechargeCode\common\services\QrCode;


class ActivityQrcodeService
{
    //获取二维码
    public static function getQrCode($id)
    {
        $url = yzAppFullUrl('plugin.activity-qrcode.api.qrcode.index/' . $id);
        return (new QrCode($url, 'app/public/qr/activity'))->url();
    }

}