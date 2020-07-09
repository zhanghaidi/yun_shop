<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/20
 * Time: 上午10:22
 */

namespace Yunshop\Supplier\admin\controllers\order;


use app\common\components\BaseController;
use app\common\exceptions\ShopException;
use app\common\helpers\PaginationHelper;
use app\common\services\ExportService;
use Yunshop\Supplier\admin\models\Supplier;
use Yunshop\Supplier\admin\models\SupplierOrderJoinOrder;
use Yunshop\Supplier\common\services\SupplierExportService;

class SupplierOrderController extends BaseController
{
    const PAGE_SIZE = 10;

    /**
     * @name 全部订单
     * @author yangyang
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $params = \YunShop::request()->get('search');
        $list = SupplierOrderJoinOrder::getSupplierOrderList($params)->orderBy('yz_order.id', 'desc')->paginate(self::PAGE_SIZE)->toArray();
        $list['total_price'] = SupplierOrderJoinOrder::getSupplierOrderList($params)->sum('price');
        $list['plugin_id'] = Supplier::PLUGIN_ID;
//        dd($list);
        $this->export(SupplierOrderJoinOrder::getSupplierOrderList($params));

        return view('Yunshop\Supplier::admin.order.supplier_list', $this->getData($list, 'index'));
    }

    /**
     * @name 等待付款
     * @author yangyang
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function waitPay()
    {
        $params = \YunShop::request()->get('search');
        $list = SupplierOrderJoinOrder::getSupplierOrderList($params)->status(0)->orderBy('yz_order.id', 'desc')->paginate(self::PAGE_SIZE)->toArray();
        $list['total_price'] = SupplierOrderJoinOrder::getSupplierOrderList($params)->status(0)->sum('price');
        $this->export(SupplierOrderJoinOrder::getSupplierOrderList($params)->status(0));

        return view('Yunshop\Supplier::admin.order.supplier_list', $this->getData($list, 'wait-pay'));
    }

    /**
     * @name 等待发货
     * @author yangyang
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function waitSend()
    {
        $params = \YunShop::request()->get('search');
        $list = SupplierOrderJoinOrder::getSupplierOrderList($params)->status(1)->orderBy('yz_order.id', 'desc')->paginate(self::PAGE_SIZE)->toArray();
        $list['total_price'] = SupplierOrderJoinOrder::getSupplierOrderList($params)->status(1)->sum('price');
        $this->export(SupplierOrderJoinOrder::getSupplierOrderList($params)->status(1));

        return view('Yunshop\Supplier::admin.order.supplier_list', $this->getData($list, 'wait-send'));
    }

    /**
     * @name 等待收货
     * @author yangyang
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function waitReceive()
    {
        $params = \YunShop::request()->get('search');
        $list = SupplierOrderJoinOrder::getSupplierOrderList($params)->status(2)->orderBy('yz_order.id', 'desc')->paginate(self::PAGE_SIZE)->toArray();
        $list['total_price'] = SupplierOrderJoinOrder::getSupplierOrderList($params)->status(2)->sum('price');
        $this->export(SupplierOrderJoinOrder::getSupplierOrderList($params)->status(2));

        return view('Yunshop\Supplier::admin.order.supplier_list', $this->getData($list, 'wait-receive'));
    }

    /**
     * @name 完成订单
     * @author yangyang
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function completed()
    {
        $params = \YunShop::request()->get('search');
        $list = SupplierOrderJoinOrder::getSupplierOrderList($params)->status(3)->orderBy('yz_order.id', 'desc')->paginate(self::PAGE_SIZE)->toArray();
        $list['total_price'] = SupplierOrderJoinOrder::getSupplierOrderList($params)->status(3)->sum('price');
        $this->export(SupplierOrderJoinOrder::getSupplierOrderList($params)->status(3));

        return view('Yunshop\Supplier::admin.order.supplier_list', $this->getData($list, 'completed'));
    }

    /**
     * @name 关闭订单
     * @author yangyang
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function cancelled()
    {
        $params = \YunShop::request()->get('search');
        $list = SupplierOrderJoinOrder::getSupplierOrderList($params)->status(-1)->orderBy('yz_order.id', 'desc')->paginate(self::PAGE_SIZE)->toArray();
        $list['total_price'] = SupplierOrderJoinOrder::getSupplierOrderList($params)->status(-1)->sum('price');
        $this->export(SupplierOrderJoinOrder::getSupplierOrderList($params)->status(-1));

        return view('Yunshop\Supplier::admin.order.supplier_list', $this->getData($list, 'cancelled'));
    }

    /**
     * @name 退换货订单
     * @author yangyang
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function refund()
    {
        $params = \YunShop::request()->get('search');
        $list = SupplierOrderJoinOrder::getSupplierOrderList($params)->orderBy('yz_order.id', 'desc')->refund()->paginate(self::PAGE_SIZE)->toArray();
        $list['total_price'] = SupplierOrderJoinOrder::getSupplierOrderList($params)->refund()->sum('price');
        $this->export(SupplierOrderJoinOrder::getSupplierOrderList($params)->refund());

        return view('Yunshop\Supplier::admin.order.supplier_list', $this->getData($list, 'refund'));
    }

    public function refunded()
    {
        $params = \YunShop::request()->get('search');
        $list = SupplierOrderJoinOrder::getSupplierOrderList($params)->orderBy('yz_order.id', 'desc')->refunded()->paginate(self::PAGE_SIZE)->toArray();
        $list['total_price'] = SupplierOrderJoinOrder::getSupplierOrderList($params)->refunded()->sum('price');
        $this->export(SupplierOrderJoinOrder::getSupplierOrderList($params)->refunded());

        return view('Yunshop\Supplier::admin.order.supplier_list', $this->getData($list, 'refunded'));
    }

    private function getData($list, $route)
    {
//        dd($list);
        $params = \YunShop::request()->get('search');
        $params['plugin'] = 'fund';
        $pager = PaginationHelper::show($list['total'], $list['current_page'], $list['per_page']);
        return [
            'list'  => $list,
            'pager' => $pager,
            'requestSearch' => $params,
            'var'   => \YunShop::app()->get(),
            'total_price' => $list['total_price'],
            'url' => 'plugin.supplier.admin.controllers.order.supplier-order.' . $route,
            'all_supplier' => Supplier::getSupplierList(null, 1)->get()->toArray(),
            'include_ops'  => 'Yunshop\Supplier::admin.order.ops',
            'include_modals'  => 'Yunshop\Supplier::admin.order.modals',
            'detail_url'   => 'plugin.supplier.admin.controllers.order.supplier-order-detail',
            'plugin_class' => app('plugins')
        ];
    }

    public function export1($builder)
    {
        if (\YunShop::request()->export == 1) {
            $params = \YunShop::request()->search;
            $builder = $builder->with(['discounts'])->orderBy('yz_order.id', 'desc');
            $list = $builder->get();
            if ($list) {
                $list = $list->toArray();
                $export_class = new SupplierExportService();
                $export_class->setColumns();
                $export_class->export($list);
            }
        }
    }

    public function export($build)
    {
        if (request()->export == 1) {
            $build = $build->with(['discounts', 'deductions'])->orderBy('yz_order.id', 'desc');
            $file_name = date('Ymdhis', time()) . '供应商订单';

            $export_page = request()->export_page ? request()->export_page : 1;
            $export_model = new ExportService($build, $export_page);

            $export_data[0] = ['供应商', '订单成本', '省', '市', '区', '抵扣金额', '优惠券优惠', '全场满减优惠', '单品满减优惠', '订单编号', '支付单号', '粉丝ID', '粉丝昵称', '会员姓名', '联系电话', '收货地址', '商品名称', '商品编码', '商品数量', '支付方式', '商品小计', '运费', '应收款', '状态', '下单时间', '付款时间', '发货时间', '完成时间', '快递公司', '快递单号', '订单备注', '用户备注'];

            if ($export_model->builder_model->isEmpty()) {
                throw new ShopException('导出数据为空');
            }

            foreach ($export_model->builder_model as $key => $item) {
                $address = explode(' ', $item->address->address);
                $item->province = !empty($address[0])?$address[0]:'';
                $item->city = !empty($address[1])?$address[1]:'';
                $item->district = !empty($address[2])?$address[2]:'';
                $export_data[$key + 1] = [
                    $item->beLongsToSupplier->username,
                    $item->supplier_profit,
                    $item->province,
                    $item->city,
                    $item->district,
                    $this->getExportDiscount($item, 'deduction'),
                    $this->getExportDiscount($item, 'coupon'),
                    $this->getExportDiscount($item, 'enoughReduce'),
                    $this->getExportDiscount($item, 'singleEnoughReduce'),
                    $item->order_sn,
                    $item->hasOneOrderPay->pay_sn,
                    $item->belongsToMember->uid,
                    $item->belongsToMember->nickname,
                    $item->address->realname,
                    $item->address->mobile,
                    $item->address->address,
                    $this->getGoods($item, 'goods_title'),
                    $this->getGoods($item, 'goods_sn'),
                    $this->getGoods($item, 'total'),
                    $item->pay_type_name,
                    $item->goods_price,
                    $item->dispatch_price,
                    $item->price,
                    $item->status_name,
                    $item->create_time->toDateTimeString(),
                    $item->pay_time->toDateTimeString(),
                    $item->send_time->toDateTimeString(),
                    $item->finish_time->toDateTimeString(),
                    $item->express->express_company_name,
                    $item->express->express_sn,
                    $item->hasOneOrderRemark->remark,
                    $item->note
                ];
            }
            $export_model->export($file_name, $export_data, \Request::query('route'));
        }
    }

    protected function getGoods($order, $key)
    {
        $goods_title = '';
        $goods_sn = '';
        $total = '';
        foreach ($order->hasManyOrderGoods as $goods) {
            $row_goods_title = $goods->title;
            if ($goods->goods_option_title) {
                $row_goods_title .= '['. $goods->goods_option_title .']';
            }

            $goods_title .= '【' . $row_goods_title . '*' . $goods->total . '】';
            $goods_sn .= $goods->goods_sn.'/';
            $total .= $goods->total.'/';
        }
        return $$key;
    }

    protected function getExportDiscount($order, $key)
    {
        $export_discount = [
            'deduction' => 0,    //抵扣金额
            'coupon'    => 0,    //优惠券优惠
            'enoughReduce' => 0,  //全场满减优惠
            'singleEnoughReduce' => 0,    //单品满减优惠
        ];

        foreach ($order['discounts'] as $discount) {

            if ($discount['discount_code'] == $key) {
                $export_discount[$key] = $discount['amount'];
            }
        }

        if (!$export_discount['deduction']) {

            foreach ($order['deductions'] as $k => $v) {
                
                $export_discount['deduction'] += $v['amount'];
            }
        }
        
        return $export_discount[$key];
    }
}