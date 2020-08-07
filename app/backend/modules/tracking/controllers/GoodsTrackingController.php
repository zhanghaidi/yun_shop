<?php
namespace app\backend\modules\tracking\controllers;

use app\common\components\BaseController;
use app\backend\modules\tracking\models\GoodsTrackingModel;
use app\common\helpers\PaginationHelper;
use app\backend\modules\tracking\models\DiagnosticServiceAcupoint;
use app\backend\modules\tracking\models\DiagnosticServiceArticle;
use app\backend\modules\tracking\models\DiagnosticServiceSomatoType;
use app\backend\modules\tracking\models\DiagnosticServicePost;

/**
 * Class GoodsTrackingController
 * @package app\backend\modules\tracking\controllers
 */
class GoodsTrackingController extends BaseController
{
    public function index(){
        $pageSize = 20;
        $list = GoodsTrackingModel::with(['goods','user','resource'])->paginate($pageSize);
        foreach ($list as $v){
            var_dump($v->resource);
        }
        $pager = PaginationHelper::show($list['total'], $list['current_page'], $list['per_page']);
        /*return view('area.selectcitys',
            'citys' => $citys->toArray()
        ])->render();*/
        /*foreach ($list as $k => $v){
            if($v->to_type_id == 1){
                $list[$k]['res'] = DiagnosticServiceAcupoint::where('id', $v->resource_id)->get();
            }elseif ($v->to_type_id == 3){
                $list[$k]['res'] = DiagnosticServiceArticle::where('id', $v->resource_id)->get();
            }elseif ($v->to_type_id == 4){
                $list[$k]['res'] = DiagnosticServicePost::where('id', $v->resource_id)->get();
            }elseif ($v->to_type_id == 5){
                $list[$k]['res'] = DiagnosticServiceSomatoType::where('id', $v->resource_id)->get();
            }
        }*/

        return view('tracking.goodsTracking.index', [
            'pageList' => $list,
            'pager' => $pager,
        ]);
    }
}
