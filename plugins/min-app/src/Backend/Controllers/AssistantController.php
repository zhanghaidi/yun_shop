<?php
/**
 * Created by PhpStorm.
 * User: king/QQ:995265288
 * Date: 2019/4/3
 * Time: 2:18 PM
 */

namespace Yunshop\MinApp\Backend\Controllers;


use app\common\components\BaseController;

class AssistantController extends BaseController
{
    public function index()
    {
        return view('Yunshop\MinApp::assistant', [

        ])->render();
    }
//background: transparent url({{ plugin_assets('wechat', 'assets/images/menu_foot.png') }}) no-repeat 0 0;

}
