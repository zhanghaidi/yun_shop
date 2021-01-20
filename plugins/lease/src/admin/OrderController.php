<?php

namespace Yunshop\LeaseToy\admin;

use app\common\exceptions\ShopException;
use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use Yunshop\LeaseToy\models\Order;
use app\common\services\ExportService;
use app\backend\modules\member\models\Member;
use app\common\services\DivFromService;
use Yunshop\LeaseToy\models\OrderGoods;


/**
* Author: 芸众商城 www.yunzshop.com
* Date: 2018/3/15
* Time: 10:24
*/
class OrderController extends BaseController
{
    const LIST_VIEW = 'Yunshop\LeaseToy::admin.order.list';

    protected $page_size = 20;

    protected $orderModel;

    function __construct()
    {
        parent::__construct();

        $search = request()->search;

        $this->orderModel = Order::getLeaseOrderList($search);

    }
    public function index()
    {

        $this->export($this->orderModel);
        // dd($this->getData());
        return view(self::LIST_VIEW, $this->getData())->render();
    }

    //待支付
    public function waitPay()
    {
        $this->orderModel->waitPay();
        $this->export($this->orderModel->waitPay());
        return view(self::LIST_VIEW, $this->getData())->render();
    }

    //待发货
    public function waitSend()
    {
        $this->orderModel->waitSend();
        $this->export($this->orderModel->waitSend());
        return view(self::LIST_VIEW, $this->getData())->render();
    }

    //待收货
    public function waitReceive()
    {
        $this->orderModel->waitReceive();
        $this->export($this->orderModel->waitReceive());
        return view(self::LIST_VIEW, $this->getData())->render();
    }

    //完成
    public function completed()
    {
        $this->orderModel->completed();
        $this->export($this->orderModel->completed());
        return view(self::LIST_VIEW, $this->getData())->render();
    }

    //取消
    public function cancelled()
    {
        $this->orderModel->cancelled();
        $this->export($this->orderModel->cancelled());
        return view(self::LIST_VIEW, $this->getData())->render();
    }

    public function detail()
    {
        $orderId = request()->id;
        $order = Order::getOrderDetailById($orderId);
         if (!$order) {
            throw new ShopException('未找到该订单');
        }
          if (!empty($order->express)) {
            $express = $order->express->getExpress($order->express->express_code, $order->express->express_sn);
            //dd($express);
            //exit;
            $dispatch['express_sn'] = $order->express->express_sn;
            $dispatch['company_name'] = $order->express->express_company_name;
            $dispatch['data'] = $express['data'];
            $dispatch['thumb'] = $order->hasManyOrderGoods[0]->thumb;
            $dispatch['tel'] = '95533';
            $dispatch['status_name'] = $express['status_name'];
        }
        $order = $this->getLeaseOrderGoods($order);
// dd($order->toArray());
        return view('Yunshop\LeaseToy::admin.order.detail', [
            'order' => $order ? $order->toArray() : [],
            'dispatch' => $dispatch,
            // 'div_from' => $this->getDivFrom($order),
            'var' => \YunShop::app()->get(),
            'ops' => 'Yunshop\LeaseToy::admin.order.ops',
            //'ops' => 'order.ops',
            'edit_goods' => 'goods.goods.edit'
        ])->render();
    }
    private function getLeaseOrderGoods($order)
    {
        $order->hasManyOrderGoods->map(function ($orderGoods) {
            return $orderGoods->hasOneLeaseOrderGoods;
        });

        return $order;
    }
    
    // private function getDivFrom($order)
    // {
    //     if (!$order || !$order->hasManyOrderGoods) {
    //         return ['status' => false];
    //     }
    //     $goods_ids = [];
    //     foreach ($order->hasManyOrderGoods as $key => $goods) {
    //         $goods_ids[] = $goods['goods_id'];
    //     }
    //     $memberInfo = Member::select('realname', 'idcard')->where('uid', $order->uid)->first();

    //     $result['status'] = DivFromService::isDisplay($goods_ids);
    //     $result['member_name'] = $memberInfo->realname;
    //     $result['member_card'] = $memberInfo->idcard;

    //     return $result;
    // }

    protected function getData()
    {
        $requestSearch = \Yunshop::request()->get('search');

        if ($requestSearch) {
            $requestSearch = array_filter($requestSearch, function ($item) {
                return !empty($item);
            });
        }

        $list['total_price'] = $this->orderModel->sum('price');
        $list += $this->orderModel->orderBy('id', 'desc')->paginate($this->page_size)->appends(['builder_model'])->toArray();
        $pager = PaginationHelper::show($list['total'], $list['current_page'], $list['per_page']);
        $data = [
            'list' => $list,
            'total_price' => $list['total_price'],
            'pager' => $pager,
            'requestSearch' => $requestSearch,
            'var' => \YunShop::app()->get(),
            'url' => \Request::query('route'),
            // 'include_ops' => 'Yunshop\LeaseToy::admin.order.ops',
            'include_ops' => 'order.ops',
            'detail_url' => 'plugin.lease-toy.admin.order.detail'
        ];
        return $data;

    }

    public function export($orders)
    {
        if ( request()->export != 1) return;
        $export_page = request()->export_page ? request()->export_page : 1;
        $export_model = new ExportService($orders, $export_page);
        if ($export_model->builder_model->isEmpty()) {
            throw new ShopException('没有可导出订单');
        }
        if (!$export_model->builder_model->isEmpty()) {
            $file_name = date('Ymdhis', time()).'订单导出';
            $export_data[0] = $this->getColumns();
            foreach ($export_model->builder_model->toArray() as $key => $item) {
                    $export_data[$key + 1] = [
                        $item['order_sn'],
                        $item['has_one_order_pay']['pay_sn'],
                        $this->getNickname($item['belongs_to_member']['nickname']),
                        $item['address']['realname'],
                        $item['address']['mobile'],
                        $item['address']['address'],
                        $this->getGoods($item, 'goods_title'),
                        $this->getGoods($item, 'goods_sn'),
                        $this->getGoods($item, 'total'),
                        $item['pay_type_name'],
                        $item['goods_price'],
                        $item['dispatch_price'],
                        $item['lease_toy']['deposit_total'],
                        $item['price'],
                        $item['lease_toy']['days'],
                        $item['status_name'],
                        $item['create_time'],
                        !empty(strtotime($item['pay_time']))?$item['pay_time']:'',
                        !empty(strtotime($item['send_time']))?$item['send_time']:'',
                        !empty(strtotime($item['finish_time']))?$item['finish_time']:'',
                        $item['express']['express_company_name'],
                        '['.$item['express']['express_sn'].']',
                        $item['has_one_order_remark']['remark'],
                    ];
            }
            $export_model->export($file_name, $export_data, 'order.list.index');

        }
    }
    private function getColumns()
    {
        return ["订单编号", "支付单号", "粉丝昵称", "会员姓名", "联系电话", "收货地址", "商品名称", "商品编码", "商品数量", "支付方式", "商品小计", "运费", "押金", "应收款", "租赁天数", "状态", "下单时间", "付款时间", "发货时间", "完成时间", "快递公司", "快递单号", "订单备注"];
    }

    private function getGoods($order, $key)
    {
        $goods_title = '';
        $goods_sn = '';
        $total = '';
        $cost_price = 0;
        foreach ($order['has_many_order_goods'] as $goods) {
            $res_title = $goods['title'];
            $res_title = str_replace('-', '，', $res_title);
            $res_title = str_replace('+', '，', $res_title);
            $res_title = str_replace('/', '，', $res_title);
            $res_title = str_replace('*', '，', $res_title);
            $res_title = str_replace('=', '，', $res_title);

            // if ($goods['goods_option_title']) {
            //     $res_title .= '['. $goods['goods_option_title'] .']';
            // }
            // $order_goods = OrderGoods::find($goods['id']);
            // if ($order_goods->goods_option_id) {
            //     $goods_option = GoodsOption::find($order_goods->goods_option_id);
            //     if ($goods_option) {
            //         $goods_sn .= '【' . $goods_option->goods_sn.'】';
            //     }
            // } else {
            //     $goods_sn .= '【' . $goods['goods_sn'].'】';
            // }

            $goods_sn .= '【' . $goods['goods_sn'].'】';
            $goods_title .= '【' . $res_title . '*' . $goods['total'] . '】';
            $total .= '【' . $goods['total'].'】';
            $cost_price += $goods['goods_cost_price'];
        }
        $res = [
            'goods_title' => $goods_title,
            'goods_sn' => $goods_sn,
            'total' => $total,
        ];
        return $res[$key];
    }

    private function getNickname($nickname)
    {
        if (substr($nickname, 0, strlen('=')) === '=') {
            $nickname = '，' . $nickname;
        }
        return $nickname;
    }
}