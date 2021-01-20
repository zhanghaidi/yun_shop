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
use Yunshop\Mryt\common\models\MemberTeamAward;
use Yunshop\Mryt\services\CommonService;

class TeamController extends BaseController
{
    public function index()
    {
        $search = request()->search;
        $set = CommonService::getSet();
        $list = MemberTeamAward::getList($search)->orderBy('id', 'desc')->paginate();
        $amount_total = MemberTeamAward::getList($search)->sum('amount');
        $pager  = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());

        return view('Yunshop\Mryt::admin.teamlist', [
            'list' => $list,
            'set' => $set,
            'pager' => $pager,
            'search' => $search,
            'amount_total' => $amount_total
        ])->render();
    }
}