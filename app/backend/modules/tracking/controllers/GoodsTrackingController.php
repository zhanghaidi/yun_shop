<?php
namespace app\backend\modules\tracking\controllers;

use app\common\components\BaseController;

/**
 * Class GoodsTrackingController
 * @package app\backend\modules\tracking\controllers
 */
class GoodsTrackingController extends BaseController
{
    public function index(){
        var_dump(222);die;
        return view('tracking.goodsTracking.index', [
            //'list' => $list,
            //'pager' => $pager,
        ]);
    }
}
