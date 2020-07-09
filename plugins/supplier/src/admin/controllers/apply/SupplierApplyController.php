<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/21
 * Time: 下午4:55
 */

namespace Yunshop\Supplier\admin\controllers\apply;

use app\common\components\BaseController;
use app\common\exceptions\ShopException;
use app\common\helpers\PaginationHelper;
use Yunshop\Supplier\admin\models\Supplier;

class SupplierApplyController extends BaseController
{
    public function index()
    {

        $pageSize = 10;

        $search = \YunShop::request()->search;
        $list = Supplier::getSupplierList($search, 0)->paginate($pageSize)->toArray();
        $pager = PaginationHelper::show($list['total'], $list['current_page'], $list['per_page']);

        return view('Yunshop\Supplier::admin.apply.supplier_apply_list', [
            'list'  => $list,
            'pager' => $pager,
            'total' => $list['total'],
            'var'   => \YunShop::app()->get(),
            'params' => $search,
            'exist_diyform' => app('plugins')->isEnabled('diyform'),
        ])->render();
    }

    public function detail()
    {
        $id = intval(request()->id);
        if (!$id) {
            throw new ShopException('参数错误');
        }
        $apply_supplier = Supplier::getSupplierById($id);
        if (!$apply_supplier) {
            throw new ShopException('未找到申请数据');
        }
        return view('Yunshop\Supplier::admin.apply.detail', [
            'apply'  => $apply_supplier
        ])->render();
    }
}