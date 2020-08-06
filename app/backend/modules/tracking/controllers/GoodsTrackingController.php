<?php
namespace app\backend\modules\tracking\controllers;

use app\common\components\BaseController;
use app\backend\modules\tracking\models\GoodsTrackingModel;
use app\common\helpers\PaginationHelper;

/**
 * Class GoodsTrackingController
 * @package app\backend\modules\tracking\controllers
 */
class GoodsTrackingController extends BaseController
{
    public function index(){
        $pageSize = 20;
        $list = GoodsTrackingModel::paginate($pageSize)->toArray();
        $pager = PaginationHelper::show($list['total'], $list['current_page'], $list['per_page']);
        /*return view('area.selectcitys',
            'citys' => $citys->toArray()
        ])->render();*/

        return view('tracking.goodsTracking.index', [
            'list' => $list['data'],
            'pager' => $pager,
            'total' => $list['total']
        ]);

        //return view('excelRecharge.page');
    }
}
