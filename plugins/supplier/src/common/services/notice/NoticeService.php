<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/24
 * Time: 上午9:17
 */

namespace Yunshop\Supplier\common\services\notice;

use EasyWeChat\Foundation\Application;

class NoticeService
{
    public static function notice($templateId, $data, $openId)
    {
        $app = app('wechat');
        $notice = $app->notice;
        //echo '<pre>';print_r([$templateId,$data,$openId]);exit;
        $notice->uses($templateId)->andData($data)->andReceiver($openId)->send();
    }
}