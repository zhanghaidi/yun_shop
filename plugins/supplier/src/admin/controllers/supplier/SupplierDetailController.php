<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/17
 * Time: 下午8:19
 */

namespace Yunshop\Supplier\admin\controllers\supplier;


use app\common\components\BaseController;
use app\common\facades\Setting;
use app\common\helpers\Url;
use Yunshop\Supplier\admin\models\Supplier;
use Yunshop\Supplier\common\models\WeiQingUsers;
use Yunshop\Supplier\common\services\supplier\SupplierService;

class SupplierDetailController extends BaseController
{
    public function index()
    {
        $supplier_id = \YunShop::request()->supplier_id;
        $set = Setting::get('plugin.supplier');

        $supplier =  (new SupplierService)->verifySupplierIsEmpty(Supplier::getSupplierById($supplier_id));
        if (!$supplier) {
            return $this->message('供应商不存在', Url::absoluteWeb('plugin.supplier.admin.controllers.supplier.supplier-list'));
        }
        $data = \YunShop::request()->data;
        if ($data) {
            $data['logo'] = replace_yunshop(tomedia($data['logo']));
            $result = SupplierService::verifyMemberIsRepeat($data['member_id'], $supplier['id']);
            if ($result) {
                $supplier->fill($data);
                $supplier->save();
                return $this->message('修改成功！', Url::absoluteWeb('plugin.supplier.admin.controllers.supplier.supplier-list'));
            } else {
                $this->error('该微信已经绑定其他供应商！');
            }
        }

        return view('Yunshop\Supplier::admin.supplier.supplier_detail', [
            'supplier'  => $supplier->toArray(),
            'var'   => \YunShop::app()->get(),
            'is_add' => 0,
            'set' => $set,
        ])->render();
    }

    public function add()
    {
        $data = \YunShop::request()->data;
        $agentDate=[
            'member_id'=>$data['member_id'],
            'created_at' => time(),
            'username'=>$data['username'],
        ];


        if ($data) {
            $result = WeiQingUsers::getUserByUserName($data['username'])->first();
            if ($result) {
                return $this->message('此用户为系统存在用户，无法添加', Url::absoluteWeb('plugin.supplier.admin.controllers.supplier.supplier-list', $data), 'error');
            }
            $data['uniacid'] = \YunShop::app()->uniacid;
            // todo salt 已经废弃
            $data['salt'] = random(8);
            $data['status'] = 1;
            $supplier_model = new Supplier();
            $supplier_model->fill($data);
            $validator = $supplier_model->validator();
            if ($validator->fails()) {
                $this->error($validator->messages());
            } else {
                $uid = Supplier::addWeiqingTables($data['username'], $data['password']);
                if (is_array($uid) && $uid['errno'] == -1) {
                    return $this->message($uid['message'], Url::absoluteWeb('plugin.supplier.admin.controllers.supplier.supplier-list', $data), 'error');
                } else {
                    if ($supplier_model->save()) {
                        event(new \app\common\events\plugin\SupplierEvent($agentDate));
                        $data['uid'] = $uid;
                        $supplier_model->uid = $data['uid'];
                        $supplier_model->save();
                        return $this->message('添加成功', Url::absoluteWeb('plugin.supplier.admin.controllers.supplier.supplier-list'));
                    }
                }
            }
        }

        return view('Yunshop\Supplier::admin.supplier.supplier_detail', [
            'var'   => \YunShop::app()->get(),
            'supplier'  => $data,
            'is_add' => 1
        ])->render();
    }
}