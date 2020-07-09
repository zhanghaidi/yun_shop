<?php

/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/9/4
 * Time: 下午7:46
 */

namespace Yunshop\Supplier\supplier\controllers;

use Setting;
use Yunshop\Supplier\admin\models\Supplier;
use Yunshop\Supplier\common\controllers\SupplierCommonController;
use app\common\services\Session;

class InfoController extends SupplierCommonController
{
    public function index()
    {
        $supplier = Supplier::getSupplierById(Session::get('supplier')['id']);

        $supplier_data = request()->data;
        if ($supplier_data) {
            $supplier->fill($supplier_data);
            $supplier->save();
            return $this->message('修改成功', '');
        }

        $set = Setting::get('plugin.supplier');

        return view('Yunshop\Supplier::supplier.info', [
            'supplier' => $supplier,
            'set' => $set
        ])->render();
    }
}