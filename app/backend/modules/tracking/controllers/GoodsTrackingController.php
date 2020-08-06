<?php
namespace app\backend\modules\tracking\controllers;

use app\common\components\BaseController;
use app\backend\modules\tracking\models\GoodsTrackingModel;

/**
 * Class GoodsTrackingController
 * @package app\backend\modules\tracking\controllers
 */
class GoodsTrackingController extends BaseController
{
    public function index(){

        $list = GoodsTrackingModel::get();

        /*return view('area.selectcitys', [
            'citys' => $citys->toArray()
        ])->render();*/

        return view('tracking.goodsTracking.index', [
            'list' => $list->toArray(),
            //'pager' => $pager,
        ]);

        //return view('excelRecharge.page');
    }
}
