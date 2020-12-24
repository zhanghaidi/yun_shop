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

    public function getTrackInfo($key,$item){
        if($item['to_type_id'] == 1){
            //商品推荐来源类型1:穴位 2：病例 3：文章 4：社区话题 5：体质测试 6：灸师推荐
            $item['to_type_id'] == '穴位';
        }
        if($item['to_type_id'] == 3){
            $item['to_type_id'] == '文章';
        }
        if($item['to_type_id'] == 4){
            $item['to_type_id'] == '社区话题';
        }
        if($item['to_type_id'] == 5){
            $item['to_type_id'] == '体质测试';
        }
        if($item['to_type_id'] == 6){
            $item['to_type_id'] == '灸师';
        }
        if($item['to_type_id'] == 7){
            $item['to_type_id'] == '课时';
        }
        if($item['to_type_id'] == 8){
            $item['to_type_id'] == '直播商品';
        }
        if($item['to_type_id'] == 9){
            $item['to_type_id'] == '商城首页';
        }
        if($item['to_type_id'] == 10){
            $item['to_type_id'] == '活动海报';
        }
        if($item['to_type_id'] == 11){
            $item['to_type_id'] == '分享';
        }
        if($item['to_type_id'] == 12){
            $item['to_type_id'] == '搜索';
        }
        if($item['to_type_id'] == 13){
            $item['to_type_id'] == '购物车';
        }
        if($item['to_type_id'] == 14){
            $item['to_type_id'] == '我的订单';
        }
        if($item['to_type_id'] == 15){
            $item['to_type_id'] == '优惠券';
        }
        if($item['to_type_id'] == 16){
            $item['to_type_id'] == '我的收藏';
        }
        if($item['to_type_id'] == 17){
            $item['to_type_id'] == '用户足迹';
        }
        if($item['to_type_id'] == 18){
            $item['to_type_id'] == '装修页面';
        }
        if($item['to_type_id'] == 404){
            $item['to_type_id'] == '未知';
        }

    }


}
