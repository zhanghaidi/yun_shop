<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/23
 * Time: 上午10:19
 */

namespace Yunshop\Supplier\supplier\controllers\withdraw;


use app\common\helpers\PaginationHelper;
use app\common\services\Session;
use Yunshop\Supplier\common\controllers\SupplierCommonController;
use Yunshop\Supplier\supplier\models\SupplierWithdraw;

class SupplierWithdrawLogController extends SupplierCommonController
{
    public function index()
    {
        $pageSize = 10;
        $params = \YunShop::request()->search;
        $list = SupplierWithdraw::getWithdrawList($params)->where('supplier_id', Session::get('supplier')['id'])->paginate($pageSize)->toArray();
        $pager = PaginationHelper::show($list['total'], $list['current_page'], $list['per_page']);

        return view('Yunshop\Supplier::supplier.withdraw.supplier_withdraw_list', [
            'list'      => $list,
            'pager'     => $pager,
            'var'       => \YunShop::app()->get(),
            'params'    => $params
        ])->render();
    }
}