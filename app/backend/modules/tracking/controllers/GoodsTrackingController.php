<?php
namespace app\backend\modules\tracking\controllers;

use app\common\components\BaseController;
use app\backend\modules\tracking\models\GoodsTrackingModel;
use app\common\helpers\PaginationHelper;
use app\backend\modules\tracking\models\ChartChartuser;
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
       // $goodsRecords = DB::table('diagnostic_service_goods_tracking')->groupBy('goods_id')->get()->toArray();
        $search = \YunShop::request()->search;
        $goodsRecords = GoodsTrackingModel::records()->groupBy('goods_id');
        foreach ($goodsRecords as $record){
            //用户浏览数量
            $record->user_num = GoodsTrackingModel::where('goods_id', $record->goods_id)->groupBy('user_id')->count();
            //$goodsRecords[$k]['user_num'] = DB::table('diagnostic_service_goods_tracking')->where('goods_id', $v['goods_id'])->groupBy('user_id')->count();
            //商品加购件数
            $record->add_num = GoodsTrackingModel::where(['goods_id', $record->goods_id, 'action' => 3])->groupBy('user_id')->count();
            //$goodsRecords[$k]['add_num'] = DB::table('diagnostic_service_goods_tracking')->where(['goods_id' => $v['goods_id'],'action' => 3])->sum('val');
            //支付数
            $record->buy_num = GoodsTrackingModel::where(['goods_id', $record->goods_id, 'action' => 5])->groupBy('user_id')->count();
            //$goodsRecords[$k]['buy_num'] = DB::table('diagnostic_service_goods_tracking')->where(['goods_id' => $v['goods_id'],'action' => 5])->count();
            //支付金额
            //$goodsRecords[$k]['buy_price'] = DB::table('diagnostic_service_goods_tracking')->where(['goods_id' => $v['goods_id'],'action' => 5])->count();
            //支付转化率
        }
        $recordList = $goodsRecords->paginate();

        $pager = PaginationHelper::show($recordList->total(), $recordList->currentPage(), $recordList->perPage());

        return view('tracking.goodsTracking.report',[

            'pageList'    => $recordList,
            'page'          => $pager,
            'search'        => $search

        ])->render();
    }

}
