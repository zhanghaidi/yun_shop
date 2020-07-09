<?php

namespace Yunshop\Poster\services;

use EasyWeChat\Foundation\Application;

class Message
{
    //发送模板消息
    public static function sendTemplateMessage($openid, $templateid, $data)
    {
        $options = ResponseService::wechatConfig();
        $app = new Application($options);

        $notice = $app->notice;
        $notice->uses($templateid)->andData($data)->andReceiver($openid)->send();
    }

    //发送客服消息
    /*
     * $notice可以是微信文本回复或者微信图文回复
     * 文本: $message = new Text(['content' => 'Hello']);
     * 图文:
     * $message = new News([
                    'title' => 'your_title',
                    'image' => 'your_image',
                    'description' => 'your_description',
                    'url' => 'your_url',
                ]);
     */
    public static function sendNotice($openid, $notice)
    {
        $options = ResponseService::wechatConfig();
        $app = new Application($options);
        $app->staff->message($notice)->to($openid)->send();
    }
}