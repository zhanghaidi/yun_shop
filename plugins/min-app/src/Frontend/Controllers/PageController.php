<?php
/****************************************************************
 * Author:  king -- LiBaoJia
 * Date:    2020/5/19 3:17 PM
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * IDE:     PhpStorm
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/


namespace Yunshop\MinApp\Frontend\Controllers;


use app\common\components\ApiController;
use app\common\facades\Setting;

class PageController extends ApiController
{
    protected $ignoreAction = ['index'];


    public function index()
    {
        return $this->successJson('ok', $this->resultData());
    }

    private function resultData()
    {
        $setting = $this->setting();

        $data = [
            'qq_map_web_key'  => 'FVFBZ-QXUWU-WUUVZ-4SB4Z-P2N7F-24FNM',
            'qq_map_web_sign' => 'J8x5YVrrM4Hn6DRG28u6kiYnlzeGauaI'
        ];
        if ($setting['qq_map_web_key'] && $setting['qq_map_web_sign']) {
            $data = [
                'qq_map_web_key'  => $setting['qq_map_web_key'],
                'qq_map_web_sign' => $setting['qq_map_web_sign']
            ];
        }
        return $data;
    }

    private function setting()
    {
        return Setting::get('plugin.min_app');
    }
}
