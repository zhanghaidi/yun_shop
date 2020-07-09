<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/21
 * Time: 下午6:53
 */

namespace Yunshop\Supplier\admin\controllers\withdraw;

use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\services\ExportService;
use Yunshop\Supplier\admin\models\SupplierWithdraw;
use Yunshop\Supplier\common\models\SupplierOrder;
use Yunshop\Supplier\common\services\withdraw\SupplierWithdrawService;

class SupplierWithdrawController extends BaseController
{
    public function index()
    {
        $pageSize = 20;
        $params = \YunShop::request()->search;
        $list = SupplierWithdraw::getWithdrawList($params)->paginate($pageSize)->toArray();
        $pager = PaginationHelper::show($list['total'], $list['current_page'], $list['per_page']);

        return view('Yunshop\Supplier::admin.withdraw.supplier_withdraw_list', [
            'list'      => $list,
            'pager'     => $pager,
            'var'       => \YunShop::app()->get(),
            'params'    => $params
        ])->render();
    }

    public function detail()
    {
        $withdraw_id = \YunShop::request()->withdraw_id;
        $withdraw = SupplierWithdrawService::verifyWithdrawIsEmpty(SupplierWithdraw::getWithdrawById($withdraw_id));
        $withdraw->belongsToManyOrder->map(function($order){
            $order->profit = SupplierOrder::select('supplier_profit')->where('order_id', $order->id)->value('supplier_profit');
        });
        return view('Yunshop\Supplier::admin.withdraw.supplier_withdraw_detail', [
            'withdraw'      => $withdraw,
            'order_count'   => count(explode(',', $withdraw->toArray()['order_ids'])),
            'var'           => \YunShop::app()->get()
        ])->render();
    }

    public function export()
    {
        $search = request()->search;
        $builder = (new SupplierWithdraw())->builder($search)->search($search);
        $export_page = request()->export_page ? request()->export_page : 1;
        $export_model = new ExportService($builder, $export_page);
        $file_name = date('Ymdhis', time()) . '供应商提现申请导出';
        $export_data[0] = ['ID', '供应商账号', '提现单号', '提现金额', '提现方式', '状态', '申请时间', '银行账号', '开户人姓名', '开户行', '开户支行', '企业支付宝账号', '企业支付宝用户名', '支付宝账号', '支付宝用户名', '微信账号'];


        foreach ($export_model->builder_model as $key => $item) {
            $export_data[$key + 1] = [
                $item->id,
                $item->hasOneSupplier->username,
                $item->apply_sn,
                $item->money,
                $item->type_name,
                $item->status_obj['name'],
                $item->created_at,
                " ".$item->hasOneSupplier->company_bank,//加空格防止数字过大变为科学计数法
                $item->hasOneSupplier->bank_username,
                $item->hasOneSupplier->bank_of_accounts,
                $item->hasOneSupplier->opening_branch,
                $item->hasOneSupplier->company_ali,
                $item->hasOneSupplier->company_ali_username,
                $item->hasOneSupplier->ali,
                $item->hasOneSupplier->ali_username,
                $item->hasOneSupplier->wechat,
            ];


        }

        $export_model->export($file_name, $export_data, \Request::query('route'));
    }
}