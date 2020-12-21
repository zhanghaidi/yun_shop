<?php

namespace Yunshop\Examination\services;

class ExaminationService
{
    public $pluginLabel = 'examination';

    public $pluginName = '养居益考试模块';

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
