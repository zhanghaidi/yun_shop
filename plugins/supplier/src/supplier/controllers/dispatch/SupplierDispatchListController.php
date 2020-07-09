<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/29
 * Time: 下午8:03
 */

namespace Yunshop\Supplier\supplier\controllers\dispatch;

use app\common\helpers\PaginationHelper;
use app\common\services\Session;
use Setting;
use Yunshop\Supplier\common\controllers\SupplierCommonController;
use Yunshop\Supplier\common\models\SupplierDispatch;

class SupplierDispatchListController extends SupplierCommonController
{
    public function index()
    {
        $shopset = Setting::get('shop');
        $pageSize = 10;
        $list = SupplierDispatch::getList(Session::get('supplier')['id'])->paginate($pageSize)->toArray();
        $list['data'] = collect($list['data'])->sortByDesc('has_one_dispatch.display_order');
        $pager = PaginationHelper::show($list['total'], $list['current_page'], $list['per_page']);

        return view('Yunshop\Supplier::supplier.dispatch.supplier_dispatch_list', [
            'list' => $list,
            'pager' => $pager,
            'shopset' => $shopset
        ])->render();
    }
}