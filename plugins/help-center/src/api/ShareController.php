<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/31 0031
 * Time: 下午 2:59
 */

namespace Yunshop\HelpCenter\api;


use app\common\components\ApiController;

class ShareController extends ApiController
{
    public function index()
    {
        $data_title = \Setting::get('help-center.title');
        $data_icon = \Setting::get('help-center.icon');
        $data_description = \Setting::get('help-center.description');

        $share_data ['icon'] = $data_icon;
        $share_data ['title'] = $data_title;
        $share_data ['description'] = $data_description;

//        dd($share_data);
        return $this->successJson('ok', ['data' => $share_data]);
    }

}
