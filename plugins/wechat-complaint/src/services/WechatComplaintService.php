<?php

namespace Yunshop\WechatComplaint\services;

class WechatComplaintService
{
    public $pluginLabel = 'wechat_complaint';

    public $pluginName = '仿微信投诉功能';

    public static function get($key = '')
    {
        $self = new self;
        if ($key == 'name') {
            $value = $self->pluginName;
        } else {
            $value = $self->pluginLabel;
        }
        return $value;
    }
}
