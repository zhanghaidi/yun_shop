<?php

namespace Yunshop\EnterpriseWechat\api;

use app\common\components\ApiController;
use app\common\facades\Setting;
use Yunshop\EnterpriseWechat\Common\services\QyWeiBanService;

/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/6/16
 * Time: 下午2:29
 */
class OrderTrackController extends ApiController
{
    public function sendOrderTrack(){
        $member_id = \YunShop::app()->getMemberId();
        $access_token = (new QyWeiBanService)->getAccessToken();
        var_dump($access_token);die;

    }

}