<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/29 0029
 * Time: 上午 11:35
 */
namespace Yunshop\HelpCenter\services;

use app\common\facades\Setting;

class HelpCenterService
{
    public $pluginName = '帮助中心';


    public function get($key = null)
    {
        $value = $this->getPluginName($key);

        return $value;
    }


    public function getPluginName($key)
    {
        if (isset($key)) {
            $set = Setting::get('plugin.help_center');
            $this->pluginName = $set['plugin_name'] ? $set['plugin_name'] : $this->pluginName;
        }
        return $this->pluginName;
    }

    public function getHelpStatusName($key = null)
    {
        switch ($key) {
            case '0':
                return '开启';
                break;
            case '1':
                return '未开启';
                break;
        }
    }


}