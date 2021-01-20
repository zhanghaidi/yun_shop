<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2018/10/23
 * Time: 下午4:44
 */

namespace Yunshop\Mryt\admin;


use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use Yunshop\Mryt\common\models\MemberReferralAward;
use Yunshop\Mryt\services\CommonService;

class PlushController extends BaseController
{
    public function index()
    {
        $search = request()->search;
        $set = CommonService::getSet();
        $list = MemberReferralAward::getList($search)->orderBy('id', 'desc')->paginate();
        $amount_total = MemberReferralAward::getList($search)->sum('amount');
        $pager  = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());

        return view('Yunshop\Mryt::admin.plushlist', [
            'list' => $list,
            'pager' => $pager,
            'set' => $set,
            'search' => $search,
            'amount_total' => $amount_total
        ])->render();
    }
}