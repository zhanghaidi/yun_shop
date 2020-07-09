<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/11/13
 * Time: 1:49 PM
 */

namespace Yunshop\Commission\admin;


use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use Yunshop\Commission\models\Operation;

class OperationController extends BaseController
{
    public function index()
    {
        $list = Operation::select()
            ->with([
                'hasOneOrder' => function ($order) {
                    $order->select(['id', 'order_sn']);
                },
                'hasOneMember' => function ($member) {
                    $member->select(['uid', 'realname', 'nickname', 'avatar']);
                },
                'hasOneBuyMember' => function ($member) {
                    $member->select(['uid', 'realname', 'nickname', 'avatar']);
                },
            ])
            ->search(request()->search)
            ->orderBy('id', 'desc')
            ->paginate();
        $pager  = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());

        return view('Yunshop\Commission::admin.operation', [
            'list' => $list,
            'pager' => $pager,
            'search' => request()->search
        ])->render();
    }
}