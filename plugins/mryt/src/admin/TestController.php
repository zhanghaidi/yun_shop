<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/12/12
 * Time: 15:04
 */

namespace Yunshop\Mryt\admin;


use app\common\components\BaseController;
use Yunshop\Mryt\services\AutoWithdrawService;

class TestController extends BaseController
{
    public function index()
    {
        (new AutoWithdrawService())->autoWithdraw();
    }

}