<?php
namespace app\backend\modules\tracking\controllers;

use app\common\components\BaseController;
use app\backend\modules\tracking\models\GoodsTrackingModel;
use app\common\helpers\PaginationHelper;
use app\backend\modules\tracking\models\ChartChartuser;
use app\backend\modules\tracking\models\GoodsTrackingStatistics;
use Illuminate\Support\Facades\DB;
use app\backend\modules\tracking\models\MemberCart;
use app\common\services\ExportService;


/**
 * Class GoodsTrackingController
 * @package app\backend\modules\tracking\controllers
 */
class GoodsTrackingController extends BaseController
{

    //商品埋点数据
    public function index()
    {
        $records = GoodsTrackingModel::records();

        $search = \YunShop::request()->search;
        if ($search) {

            $records = $records->search($search);

        }

        $recordList = $records->orderBy('create_time', 'desc')->paginate();


        $pager = PaginationHelper::show($recordList->total(), $recordList->currentPage(), $recordList->perPage());

        $this->export($records->orderBy('create_time', 'desc')->get());

        return view('tracking.goodsTracking.index', [
            'pageList'    => $recordList,
            'page'          => $pager,
            'search'        => $search

        ])->render();
    }

    //埋点数据分析报告
    public function report()
    {
        $records = GoodsTrackingModel::records()->groupBy('goods_id');

        $search = \YunShop::request()->search;
        if ($search) {

            $records = $records->search($search);

        }

        $recordList =  $records->orderBy('create_time', 'desc')->paginate();

        $pager = PaginationHelper::show($recordList->total(), $recordList->currentPage(), $recordList->perPage());

        return view('tracking.goodsTracking.report',[

            'pageList'    => $recordList,
            'page'          => $pager,
            'search'        => $search

        ])->render();
    }

    //用户购物车数据
    public function cart()
    {
        $records = MemberCart::records();
        $search = \YunShop::request()->search;
        if ($search) {

            $records = $records->search($search);

        }

        $recordList = $records->orderBy('id', 'desc')->paginate();

        //dd($recordList);
        $pager = PaginationHelper::show($recordList->total(), $recordList->currentPage(), $recordList->perPage());

        return view('tracking.goodsTracking.cart', [
            'pageList'    => $recordList,
            'page'          => $pager,
            'search'        => $search

        ])->render();
    }

    private function getColumns()
    {
        return ["id","小程序主体", "小程序版本", "上级页面", "来源类型", "所属资源", "商品信息", '操作用户', '操作动作', '动作变量', "订单号", "时间"];
    }

    //导出埋点excel数据 20201031
    public function export($recordList)
    {

        if (\YunShop::request()->export == 1) {
            $export_page = request()->export_page ? request()->export_page : 1;
            //清除之前没有导出的文件
            if ($export_page == 1){
                $fileNameArr = file_tree(storage_path('exports'));
                foreach ($fileNameArr as $val ) {
                    if(file_exists(storage_path('exports/' . basename($val)))){
                        unlink(storage_path('exports/') . basename($val)); // 路径+文件名称
                    }
                }
            }

            var_dump($recordList);die;

            $export_model = new ExportService($recordList, $export_page);

            if (!$export_model->builder_model->isEmpty()) {
                $file_name = date('Ymdhis', time()) . '埋点数据导出';//返现记录导出
                $export_data[0] = $this->getColumns();
                foreach ($export_model->builder_model->toArray() as $key => $item) {

                    array_push($export_data[$key + 1],
                        $item['id'],
                        $item['to_type_id']
                    );
                }

                $export_model->export($file_name, $export_data, 'order.list.index');
            }
        }
    }


}
