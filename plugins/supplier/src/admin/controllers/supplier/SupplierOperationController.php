<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/21
 * Time: 下午4:01
 */

namespace Yunshop\Supplier\admin\controller\supplier;


use app\common\components\BaseController;
use Yunshop\Supplier\admin\models\Supplier;
use Yunshop\Supplier\common\services\supplier\SupplierService;

class SupplierOperationController extends BaseController
{
    private function editOrDelete($supplier_id, $type)
    {
        $supplier = SupplierService::verifySupplierIsEmpty(Supplier::getSupplierById($supplier_id, 1));
        if ($type == 1) {
            $supplier->username = \YunShop::request()->username;
            $supplier->save();
            //通知
        } else {
            $supplier->delete();
            //通知
        }
    }

    public function supplierOperation()
    {
        $supplier_id = \YunShop::request()->supplier_id;
        // -1 删除   1 修改
        $type = \YunShop::request()->type;
        $this->editOrDelete($supplier_id, $type);
    }
}