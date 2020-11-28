<?php

namespace app\common\services\tencentlive;

use app\common\facades\Setting;

class LiveSetService
{

    public static function getSetting($key = '')
    {
        if (!empty($key)) {
            $live_set = Setting::get('shop.live');
            if (key_exists($key, $live_set)) {
                return $live_set[$key];
            } else {
                return '';
            }
        } else {
            return Setting::get('shop.live');
        }
    }

    public static function getIMSetting($key = '')
    {
        if (!empty($key)) {
            $im_set = Setting::get('shop.im');
            if (key_exists($key, $im_set)) {
                return $im_set[$key];
            } else {
                return '';
            }
        } else {
            return Setting::get('shop.im');
        }
    }

}
