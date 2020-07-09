<?php
/**
 * WeChat - Applet by BaoJia Li
 *
 * @author      BaoJia Li
 * @User        king/QQ:995265288
 * @Tool        PhpStorm
 * @Date        2019/12/18  2:01 PM
 * @link        https://gitee.com/li-bao-jia
 */

namespace Yunshop\MinApp\Backend\Modules\Manual\Controllers;


use app\common\components\BaseController;

class IndexController extends BaseController
{
    public function index()
    {
        return view('Yunshop\MinApp::manual');
    }

}
