<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/17
 * Time: 下午8:19
 */

namespace Yunshop\Supplier\admin\controllers\supplier;


use app\common\components\BaseController;
use app\common\helpers\Url;
use Yunshop\Supplier\admin\models\Supplier;
use Yunshop\Supplier\common\models\WeiQingUsers;
use Yunshop\Supplier\common\services\supplier\VerifyPwdService;

class SupplierEditPwdController extends BaseController
{
    public function index()
    {
        $uid = \YunShop::request()->uid;
        $supplier = Supplier::getSupplierByUid($uid)->first();

        $user = WeiQingUsers::getUserByUid($uid)->first();
        if (!$supplier || !$user) {
            return $this->message('供应商不存在', Url::absoluteWeb('plugin.supplier.admin.controllers.supplier.supplier-list'), 'error');
        }

        $pwd_data = \YunShop::request()->data;
        if ($pwd_data) {
            if (!$pwd_data['new_pwd'] || !$pwd_data['new_pwd_too']) {
                return $this->message('密码不能为空');
            } elseif ($pwd_data['new_pwd'] != $pwd_data['new_pwd_too']) {
                return $this->message('两次密码不一致');
            }
            $password = user_hash($pwd_data['new_pwd'], $user->salt);
            $user->password = $password;
            $user->save();
            $supplier->password = $pwd_data['new_pwd'];
            $supplier->save();
            return $this->message('修改密码成功', Url::absoluteWeb('plugin.supplier.admin.controllers.supplier.supplier-list.index'));
        }

        return view('Yunshop\Supplier::admin.supplier.supplier_edit_pwd', [
            'supplier'  => $supplier,
            'var'   => \YunShop::app()->get()
        ])->render();
    }
}