<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019-09-23
 * Time: 21:37
 */

namespace Yunshop\Supplier\supplier\controllers\income;


use app\common\helpers\PaginationHelper;
use Yunshop\Supplier\common\controllers\SupplierCommonController;
use Yunshop\Supplier\common\models\SupplierOrder;

class ListController extends SupplierCommonController
{
    public function index()
    {
        $search = request()->search;
        $list = SupplierOrder::with([
            'order' => function ($order) {
                $order->select('id', 'order_sn', 'price');
            }
        ])->search($search)
            ->where('supplier_id', $this->sid)
            ->orderBy('id', 'desc')
            ->paginate();
        $pager = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());

        return view('Yunshop\Supplier::supplier.income.list', [
            'list' => $list,
            'pager' => $pager,
            'search' => $search
        ])->render();
    }
}