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
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use Yunshop\Supplier\admin\models\Supplier;
use Yunshop\Supplier\common\models\UniAccountUser;
use Yunshop\Supplier\common\models\WeiQingUsers;

class SupplierListController extends BaseController
{
    public function index()
    {
        $set = Setting::get('plugin.supplier');

        $requestSearch = \YunShop::request()->get('search');
        if ($requestSearch) {
            $requestSearch = array_filter($requestSearch, function ($item) {
                return !empty($item);
            });
        }

        $pageSize = 10;
        $list = Supplier::getSupplierList($requestSearch, 1)->paginate($pageSize)->toArray();
        $pager = PaginationHelper::show($list['total'], $list['current_page'], $list['per_page']);

        return view('Yunshop\Supplier::admin.supplier.supplier_list', [
            'set' => $set,
            'list'  => $list,
            'pager' => $pager,
            'requestSearch' => $requestSearch,
            'total' => $list['total'],
            'var'   => \YunShop::app()->get(),
            'exist_diyform' => app('plugins')->isEnabled('diyform'),
        ])->render();
    }

    public function updateSupplier()
    {
        $supplier_list = Supplier::getSupplierList([], 1)->get();
        $supplier_list->map(function ($supplier) {
            WeiQingUsers::updateType($supplier->uid);
            $acount_user = UniAccountUser::select()->whereUid($supplier->uid)->first();
            if ($acount_user) {
                $acount_user->role = 'clerk';
                $acount_user->save();
            }
        });
        dd('更改供应商数据成功');
        exit;
    }

    public function changeOpen()
    {
        $id = (int)request()->id;
        $supplier = Supplier::find($id);
        $supplier->insurance_status = 1;

        if ($supplier->save()) {
            return $this->successJson('开启成功');
        } else {
            return $this->errorJson('开启失败');
        }
    }

    public function changeClose()
    {
        $id = (int)request()->id;
        $supplier = Supplier::find($id);
        $supplier->insurance_status = 0;

        if ($supplier->save()) {
            return $this->successJson('关闭保单');
        } else {
            return $this->errorJson('关闭失败');
        }
    }
}