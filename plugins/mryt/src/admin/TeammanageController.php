<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2018/10/23
 * Time: 下午5:14
 */

namespace Yunshop\Mryt\admin;


use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use Yunshop\Mryt\common\models\OrderTeamAward;

class TeammanageController extends BaseController
{
    public function index()
    {
        $search = request()->search;
        $list = OrderTeamAward::getList($search)->orderBy('id', 'desc')->paginate();
        $amount_total = OrderTeamAward::getList($search)->sum('amount');
        $pager  = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());

        return view('Yunshop\Mryt::admin.teammanage', [
            'list' => $list,
            'pager' => $pager,
            'search' => $search,
            'amount_total' => $amount_total
        ])->render();
    }
}