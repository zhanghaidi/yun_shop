<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/7/31
 * Time: 上午9:36
 */

namespace Yunshop\Love\Backend\Modules\Love\Controllers;


use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use Yunshop\Love\Common\Models\LoveTradingModel;

class TradingLoveController extends BaseController
{
    protected $pageSize;
    protected $loveName;
    
    public function preAction()
    {
        parent::preAction();
        $this->pageSize = 10;
        $this->loveName = \Yunshop\Love\Common\Services\SetService::getLoveName();

    }

    public function index()
    {
        $search = \YunShop::request()->get('search');

        $list = LoveTradingModel::getLoveTradingLog($search)->orderBy('id', 'desc')->paginate($this->pageSize);
        $pager = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());
        if (!$search['time']) {
            $search['time']['start'] = date("Y-m-d H:i:s", time());
            $search['time']['end'] = date("Y-m-d H:i:s", time());
            $search['is_time'] = 0;
        }
        return view('Yunshop\Love::Backend.Love.trading-log', [
            'list' => $list,
            'pager' => $pager,
            'search' => $search,
            'love_name' => $this->loveName,
        ])->render();
    }
    
    
    public function export()
    {
        //导出
    }
}