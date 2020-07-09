<?php
/**
 * Created by PhpStorm.
 * User: king/QQ:995265288
 * Date: 2019/4/3
 * Time: 5:17 PM
 */

namespace Yunshop\MinApp\Backend\Controllers;


use app\common\components\BaseController;
use app\common\facades\Setting;

class AuditController extends BaseController
{
    public function index()
    {
        $set = Setting::get('plugin.min_app');
        $set['uniacid'] = \YunShop::app()->uniacid;
        $set['host'] = $_SERVER["HTTP_HOST"];

        if (!$set['switch'] OR !$set['secret'] OR !$set['key']) {
            exit('请先开启小程序插件,并设置好key和秘钥');
        }

        return view('Yunshop\MinApp::audit', [
            'set' => $set
        ])->render();
    }

}
