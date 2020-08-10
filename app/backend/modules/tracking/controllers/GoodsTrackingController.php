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
    protected $goodsTrackingModels;

    public function index(){
        //$pageSize = 20;
        $this->goodsTrackingModels = $this->pageList();

        //$list = GoodsTrackingModel::with(['goods','user','resource','order'])->paginate($pageSize);

        return view('tracking.goodsTracking.index', $this->resultData());
    }

    private function resultData()
    {
        return [
            'page'     => $this->page(),
            'pageList' => $this->goodsTrackingModels
        ];
    }

    private function page()
    {
        return PaginationHelper::show($this->goodsTrackingModels->total(), $this->goodsTrackingModels->currentPage(), $this->goodsTrackingModels->perPage());
    }

    /**
     * @return RecordsModel
     */
    private function pageList()
    {
        $records = GoodsTrackingModel::with(['goods','user','resource','order'])->orderBy('created_at', 'desc');

        return $records->paginate('', ['*'], '', $this->pageParam());
    }

    /**
     * @return int
     */
    private function pageParam()
    {
        return (int)request()->page ?: 1;
    }
}
