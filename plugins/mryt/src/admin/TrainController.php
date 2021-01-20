<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2018/10/23
 * Time: 下午5:18
 */

namespace Yunshop\Mryt\admin;


use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use Yunshop\Mryt\common\models\OrderParentingAward;
use Yunshop\Mryt\services\CommonService;

class TrainController extends BaseController
{
    public function index()
    {
        $search = request()->search;
        $set = CommonService::getSet();
        $list = OrderParentingAward::getList($search)->orderBy('id', 'desc')->paginate();
        $amount_total = OrderParentingAward::getList($search)->sum('amount');
        $pager  = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());

        return view('Yunshop\Mryt::admin.train', [
            'list' => $list,
            'set' => $set,
            'pager' => $pager,
            'search' => $search,
            'amount_total' => $amount_total
        ])->render();
    }
}