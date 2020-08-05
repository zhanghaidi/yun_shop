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
        return view('tracking.goods_tracking.index', [
            //'list' => $list,
            //'pager' => $pager,
        ])->render();
    }
}
