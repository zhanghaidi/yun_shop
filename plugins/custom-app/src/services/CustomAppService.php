<?php

namespace Yunshop\CustomApp\services;

class CustomAppService
{
    public $pluginLabel = 'custom_app';

    public $pluginName = 'APP自定义内容';

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
