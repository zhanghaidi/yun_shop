<?php
namespace app\backend\modules\tracking\controllers;

use app\common\components\BaseController;
use app\backend\modules\tracking\models\GoodsTrackingModel;
use app\common\helpers\PaginationHelper;
use app\backend\modules\tracking\models\ChartChartuser;
use app\backend\modules\tracking\models\GoodsTrackingStatistics;
use Illuminate\Support\Facades\DB;


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

    public function report()
    {
        $records = GoodsTrackingStatistics::records();

        $search = \YunShop::request()->search;
        if ($search) {

            $records = $records->search($search);

        }

        $recordList =  $records->orderBy('created_at', 'desc')->paginate();

        $pager = PaginationHelper::show($recordList->total(), $recordList->currentPage(), $recordList->perPage());

        return view('tracking.goodsTracking.report',[

            'pageList'    => $recordList,
            'page'          => $pager,
            'search'        => $search

        ])->render();
    }

}
