<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/17
 * Time: 下午4:21
 */

namespace Yunshop\Micro\frontend\controllers\MicroShopBonusLog;

use app\common\components\ApiController;
use Yunshop\Micro\common\models\MicroShopBonusLog;

class ListController extends ApiController
{
    public function index()
    {
        $list = MicroShopBonusLog::getBonusLogByMemberId(\YunShop::app()->getMemberId())->orderBy('id', 'desc')->paginate(10);

        return $this->successJson('成功', [
            'status'    => 1,
            'list'      => $list
        ]);
    }

    public function apply()
    {
        $status = \YunShop::request()->apply_status;
        $time_type = \YunShop::request()->time_type;
        $list = MicroShopBonusLog::getBonusLogByMemberId(\YunShop::app()->getMemberId())->applyStatus($status)->byTime($time_type)->orderBy('id', 'desc')->paginate(10);

        return $this->successJson('成功', [
            'status'    => 1,
            'list'      => $list
        ]);
    }
}