<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/11/23
 * Time: 3:16 PM
 */

namespace Yunshop\Mryt\admin;


use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use Yunshop\Mryt\common\models\TierAward;
use Yunshop\Mryt\services\CommonService;

class TierController extends BaseController
{
    public function index()
    {
        $search = request()->search;
        $set = CommonService::getSet();
        $list = TierAward::getList($search)->orderBy('id', 'desc')->paginate();
        $amount_total = TierAward::getList($search)->sum('amount');
        $pager  = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());

        return view('Yunshop\Mryt::admin.tier', [
            'list' => $list,
            'set' => $set,
            'pager' => $pager,
            'search' => $search,
            'amount_total' => $amount_total
        ])->render();
    }
}