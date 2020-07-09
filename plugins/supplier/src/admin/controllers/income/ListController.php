<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019-09-23
 * Time: 21:14
 */

namespace Yunshop\Supplier\admin\controllers\income;


use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use Yunshop\Supplier\common\models\SupplierOrder;

class ListController extends BaseController
{
    public function index()
    {
        $search = request()->search;
        $list = SupplierOrder::uniacid()->with([
            'supplier' => function ($supplier) {
                $supplier->select('id', 'username');
            },
            'member' => function ($member) {
                $member->select('uid', 'nickname', 'realname', 'mobile', 'avatar');
            },
            'order' => function ($order) {
                $order->select('id', 'order_sn', 'price');
            }
        ])->search($search)
            ->orderBy('id', 'desc')
            ->paginate();
        $pager = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());

        return view('Yunshop\Supplier::admin.income.list', [
            'list' => $list,
            'pager' => $pager,
            'search' => $search
        ])->render();
    }
}