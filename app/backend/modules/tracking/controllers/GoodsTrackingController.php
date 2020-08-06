<?php
namespace app\backend\modules\tracking\controllers;

use app\common\components\BaseController;
use app\backend\modules\tracking\modules\GoodsTrackingModel;

/**
 * Class GoodsTrackingController
 * @package app\backend\modules\tracking\controllers
 */
class GoodsTrackingController extends BaseController
{
    public function index(){

        //$list = GoodsTrackingModel::get();
        $GoodsTrackingModel = new GoodsTrackingModel();
        dd($GoodsTrackingModel);die;
        /*return view('area.selectcitys', [
            'citys' => $citys->toArray()
        ])->render();*/

        return view('tracking.goodsTracking.index', [
            //'list' => $list->,
            //'pager' => $pager,
        ]);

        //return view('excelRecharge.page');
    }
}
