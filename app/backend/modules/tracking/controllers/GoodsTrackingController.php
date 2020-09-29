<?php
namespace app\backend\modules\tracking\controllers;

use app\common\components\BaseController;
use app\backend\modules\tracking\models\GoodsTrackingModel;
use app\common\helpers\PaginationHelper;
use app\backend\modules\tracking\models\ChartChartuser;

/**
 * Class GoodsTrackingController
 * @package app\backend\modules\tracking\controllers
 */
class GoodsTrackingController extends BaseController
{

    public function index()
    {
        $records = GoodsTrackingModel::records();

        $search = \YunShop::request()->search;
        if ($search) {
            /*if ($search['searchtime']) {
                if ($search['time']['start'] != '请选择' && $search['time']['end'] != '请选择') {
                    $data['starttime'] = strtotime($search['time']['start']);
                    $data['endtime'] = strtotime($search['time']['end']);
                }

            }*/
            $records = $records->search($search);

        }

        $recordList = $records->orderBy('create_time', 'desc')->paginate();

        //dd($recordList);
        $pager = PaginationHelper::show($recordList->total(), $recordList->currentPage(), $recordList->perPage());

        return view('tracking.goodsTracking.index', [
            'pageList'    => $recordList,
            'page'          => $pager,
            'search'        => $search

        ])->render();
    }

}
