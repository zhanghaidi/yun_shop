<?php
namespace app\backend\modules\tracking\controllers;

use app\common\components\BaseController;
use app\backend\modules\tracking\models\GoodsTrackingModel;
use app\backend\modules\tracking\models\GoodsTracking;
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
    /*public function export()
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
                    $this->getTypeName($item['to_type_id']),
                    $item['resource_id'],
                    $this->getGoods($item['goods_id']),
                    $this->getUser($item['user_id']),
                    $this->getActionName($item['action']),
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
    }*/


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

            $recordList = GoodsTracking::orderBy('id');

            $export_model = new ExportService($recordList, $export_page);

            if (!$export_model->builder_model->isEmpty()) {
                $file_name = date('Ymdhis', time()) . '商品埋点数据';//返现记录导出
                $export_data[0] = $this->getColumns();

                foreach ($export_model->builder_model->toArray() as $key => $item) {
                    $export_data[$key+1] = [
                        $item['id'],
                        $item['app_type'],
                        $item['app_version'],
                        $item['parent_page'],
                        $this->getTypeName($item['to_type_id']),
                        $item['resource_id'],
                        $this->getGoods($item['goods_id']),
                        $this->getUser($item['user_id']),
                        $this->getActionName($item['action']),
                        $item['val'],
                        date('Y-m-d H:i:s', $item['create_time'])
                    ];
                }
                $export_model->export($file_name, $export_data, 'tracking.goodsTracking.index');

            }
        }
    }

    //转换来源类型
    public function getTypeName($value){
        if($value == 1){
            //商品推荐来源类型1:穴位 2：病例 3：文章 4：社区话题 5：体质测试 6：灸师推荐
            return '穴位';
        }
        if($value == 3){
            return '文章';
        }
        if($value == 4){
            return '社区话题';
        }
        if($value == 5){
            return '体质测试';
        }
        if($value == 6){
            return '灸师';
        }
        if($value == 7){
            return '课时';
        }
        if($value == 8){
            return '直播商品';
        }
        if($value == 9){
            return '商城首页';
        }
        if($value == 10){
            return '活动海报';
        }
        if($value == 11){
            return '分享';
        }
        if($value == 12){
            return '搜索';
        }
        if($value == 13){
            return '购物车';
        }
        if($value == 14){
            return '我的订单';
        }
        if($value == 15){
            return '优惠券';
        }
        if($value == 16){
            return '我的收藏';
        }
        if($value == 17){
            return '用户足迹';
        }
        if($value == 18){
            return '装修页面';
        }
        if($value == 404){
             return '未知';
        }

    }

    //转换操作类型
    public function getActionName($value){
        if($value == 1){
            //动作类型 1：查看 2、收藏 3、加购 4：下单 5：支付
            return '查看';
        }
        if($value == 2){
            return '收藏';
        }
        if($value == 3){
            return '加购';
        }
        if($value == 4){
            return '下单';
        }
        if($value == 5){
            return '付款';
        }
    }

    //获取商品信息
    public function getGoods($value){
        $goods = DB::table('yz_goods')->select('title')->where('id', $value)->first();
        return $value." 【".$goods['title']."】";
    }

    //获取关联用户
    public function getUser($value){
        $user = DB::table('diagnostic_service_user')->select('nickname')->where('ajy_uid', $value)->first();
        return $value." 【".$user['nickname']."】";
    }

    //获取资源名称
    public function getResourceName($value){
        return '';
    }



}
