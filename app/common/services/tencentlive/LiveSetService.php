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

}
