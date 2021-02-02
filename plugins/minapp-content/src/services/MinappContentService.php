<?php

namespace Yunshop\MinappContent\services;

class MinappContentService
{
    public $pluginLabel = 'minapp_content';

    public $pluginName = '小程序内容管理';

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
