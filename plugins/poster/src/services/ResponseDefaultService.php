<?php

namespace Yunshop\Poster\services;

//对微信的默认回复
class ResponseDefaultService
{
    public static function index()
    {
        $notice = array(
            'type' => 'text',
            'content' => '感谢您的关注!',
        );
        return $notice;
    }
}
