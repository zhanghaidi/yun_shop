<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/17
 * Time: 下午7:59
 * content 验证供应商是否登录
 */
namespace Yunshop\Supplier\common\controllers;

use app\common\components\BaseController;
use app\common\helpers\Url;
use app\common\services\Session;

class SupplierCommonController extends BaseController
{
    public $sid;

    public function preAction()
    {
        parent::preAction();

        session_start();
        $res = \YunShop::isRole();
        if ($res) {
            Session::set('supplier', $res, '86400');
            $this->sid = $res['id'];
        } else {
            exit($this->message('您不是供应商！', '', 'error'));
        }
    }

}