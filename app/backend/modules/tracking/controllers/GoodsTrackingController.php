<?php
namespace app\backend\modules\tracking\controllers;

use app\common\components\BaseController;
use app\backend\modules\tracking\models\GoodsTrackingModel;
use app\common\helpers\PaginationHelper;
use app\backend\modules\tracking\models\ChartChartuser;
use app\backend\modules\tracking\models\GoodsTrackingStatistics;
use app\common\helpers\Url;
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

        $this->export();

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
        return ["id","小程序主体", "小程序版本", "上级页面", "来源类型", "所属资源", "商品信息", '操作用户', '操作动作', '动作变量', "时间"];
    }

    //导出埋点excel数据 20201031
    public function export()
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
            $recordList = DB::table('diagnostic_service_goods_tracking')->get();

            $file_name = date('Ymdhis', time()) . '埋点数据导出';//返现记录导出
            $export_data[0] = $this->getColumns();
            foreach ($recordList as $key => $item) {
                $export_data[$key+1] = [
                    $item['id'],
                    $item['app_type'],
                    $item['app_version'],
                    $item['parent_page'],
                    $item['to_type_id'],
                    $item['resource_id'],
                    $item['goods_id'],
                    $item['user_id'],
                    $item['action'],
                    $item['val'],
                    date('Y-m-d H:i:s', $item['create_time'])
                ];
            }

            \Excel::create($file_name, function ($excel) use ($export_data) {
                $excel->setTitle('Office 2005 XLSX Document');
                $excel->setCreator('芸众商城商品编号');
                $excel->setLastModifiedBy("芸众商城商品编号");
                $excel->setSubject("Office 2005 XLSX Test Document");
                $excel->setDescription("Test document for Office 2005 XLSX, generated using PHP classes.");
                $excel->setKeywords("office 2005 openxml php");
                $excel->setCategory("report file");
                $excel->sheet('info', function ($sheet) use ($export_data) {
                    $sheet->rows($export_data);
                });
            })->export('xls');

            return $this->message('退货地址修改成功', Url::absoluteWeb('tracking.goodsTracking.index'));
        }
    }


}
