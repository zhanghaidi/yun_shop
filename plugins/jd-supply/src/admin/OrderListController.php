<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/7/11
 * Time: 9:36
 */

namespace Yunshop\JdSupply\admin;

use app\common\components\BaseController;
use app\common\exceptions\ShopException;
use app\common\helpers\PaginationHelper;
use app\backend\modules\goods\models\GoodsOption;
use app\backend\modules\order\models\OrderGoods;
use app\common\services\ExportService;
use Illuminate\Support\Facades\DB;
use app\backend\modules\member\models\Member;
use app\common\services\DivFromService;
use Yunshop\JdSupply\common\JdSupplyOrderStatus;
use Yunshop\JdSupply\models\JdGoods;
use Yunshop\JdSupply\models\JdSupplyOrder;
use Yunshop\JdSupply\services\CreateOrderService;
use Yunshop\JdSupply\services\JdOrderService;
use Yunshop\TeamDividend\models\TeamDividendLevelModel;
use Yunshop\JdSupply\models\Order;
use Yunshop\JdSupply\services\sdk\JdClient;
use Yunshop\JdSupply\services\sdk\JdRequest;

class OrderListController extends BaseController
{
    /**
     * 页码
     */
    const PAGE_SIZE = 15;


    protected $orderModel;

    public function __construct()
    {
        parent::__construct();
        $params = \YunShop::request()->get();
        $this->orderModel = $this->getOrder()->with('hasOneJdSupplyOrder')->orders($params['search']);
    }

    protected function getOrder()
    {
        return Order::isPlugin()->pluginId();
    }

    public function index()
    {
        $this->export($this->orderModel);

        return view('Yunshop\JdSupply::admin.order.index', $this->getData())->render();
    }

    protected function getData()
    {
        $shopOrderSearch = request()->shop_order_search;
        if ($shopOrderSearch) {
            $shopOrderSearch = array_filter($shopOrderSearch, function ($item) {
                return !empty($item);
            });
        }
        $storeOrderSearch = request()->store_order_search;

        $list['total_price'] = $this->orderModel->sum('price');
        $pages = $this->orderModel->orderBy('id', 'desc')->paginate(self::PAGE_SIZE);
        foreach ($pages as $item){
            $item->canRefund = $item->canRefund();
        }
        $list['plugin_id'] = JdSupplyOrder::PLUGIN_ID;
        $list += $pages->toArray();
        $pager = PaginationHelper::show($list['total'], $list['current_page'], $list['per_page']);

        $data = [
            'list' => $list,
            'total_price' => $list['total_price'],
            'pager' => $pager,
            'shopOrderSearch' => $shopOrderSearch,
            'storeOrderSearch' => $storeOrderSearch,
            'var' => \YunShop::app()->get(),
            'url' => \Request::query('route'),
            'include_ops' => 'Yunshop\JdSupply::admin.order.ops',
            'detail_url' => 'plugin.jd-supply.admin.order-list.detail',
        ];
        return $data;
    }


    public function export($orders)
    {
        if (\YunShop::request()->export == 1) {
            $export_page = request()->export_page ? request()->export_page : 1;
            $orders = $orders->orderBy('id', 'desc')->with(['discounts']);
            $export_model = new ExportService($orders, $export_page);
            if (!$export_model->builder_model->isEmpty()) {
                $file_name = date('Ymdhis', time()) . '订单导出';//返现记录导出
                $export_data[0] = $this->getColumns();
                foreach ($export_model->builder_model->toArray() as $key => $item) {

                    $address = explode(' ', $item['address']['address']);

                    $export_data[$key + 1] = [
                        $item['id'],
                        $item['order_sn'],
                        $item['has_one_order_pay']['pay_sn'],
                        $item['belongs_to_member']['uid'],
                        $this->getNickname($item['belongs_to_member']['nickname']),
                        $item['address']['realname'],
                        $item['address']['mobile'],
                        !empty($address[0]) ? $address[0] : '',
                        !empty($address[1]) ? $address[1] : '',
                        !empty($address[2]) ? $address[2] : '',
                        $item['address']['address'],
                        $this->getGoods($item, 'goods_title'),
                        $this->getGoods($item, 'goods_sn'),
                        $this->getGoods($item, 'total'),
                        $item['pay_type_name'],
                        $this->getExportDiscount($item, 'deduction'),
                        $this->getExportDiscount($item, 'coupon'),
                        $this->getExportDiscount($item, 'enoughReduce'),
                        $this->getExportDiscount($item, 'singleEnoughReduce'),
                        $item['goods_price'],
                        $item['dispatch_price'],
                        $item['price'],
                        $this->getGoods($item, 'cost_price'),
                        $item['status_name'],
                        $item['create_time'],
                        !empty(strtotime($item['pay_time'])) ? $item['pay_time'] : '',
                        !empty(strtotime($item['send_time'])) ? $item['send_time'] : '',
                        !empty(strtotime($item['finish_time'])) ? $item['finish_time'] : '',
                        $item['express']['express_company_name'],
                        '[' . $item['express']['express_sn'] . ']',
                        $item['has_one_order_remark']['remark'],
                    ];
                }
                $export_model->export($file_name, $export_data, 'order.list.index');
            }
        }
    }

    public function directExport($orders)
    {
        if (\YunShop::request()->direct_export == 1) {
            if (!app('plugins')->isEnabled('team-dividend')) {
                return $this->error('未开启经销商插件无法导出');
            }
            $export_page = request()->export_page ? request()->export_page : 1;
            $orders = $orders->with([
                'discounts',
                'hasManyParentTeam' => function($q) {
                    $q->whereHas('hasOneTeamDividend')
                        ->with(['hasOneTeamDividend' => function($q) {
                            $q->with(['hasOneLevel']);
                        }])
                        ->with('hasOneMember')
                        ->orderBy('level', 'asc');
                },
            ]);
            $export_model = new ExportService($orders, $export_page);
            $team_list = TeamDividendLevelModel::getList()->get();

            $levelId = [];
            foreach ($team_list as $level) {
                $export_data[0][] = $level->level_name;
                $levelId[] = $level->id;
            }

            if (!$export_model->builder_model->isEmpty()) {
                $file_name = date('Ymdhis', time()) . '订单导出';//返现记录导出
                $export_data[0] = array_merge($export_data[0],$this->getColumns());
                foreach ($export_model->builder_model->toArray() as $key => $item) {

                    $level = $this->getLevel($item, $levelId);

                    $export_data[$key + 1] = $level;

                    $address = explode(' ', $item['address']['address']);

                    array_push($export_data[$key + 1],
                        $item['id'],
                        $item['order_sn'],
                        $item['has_one_order_pay']['pay_sn'],
                        $item['belongs_to_member']['uid'],
                        $this->getNickname($item['belongs_to_member']['nickname']),
                        $item['address']['realname'],
                        $item['address']['mobile'],
                        !empty($address[0]) ? $address[0] : '',
                        !empty($address[1]) ? $address[1] : '',
                        !empty($address[2]) ? $address[2] : '',
                        $item['address']['address'],
                        $this->getGoods($item, 'goods_title'),
                        $this->getGoods($item, 'goods_sn'),
                        $this->getGoods($item, 'total'),
                        $item['pay_type_name'],
                        $this->getExportDiscount($item, 'deduction'),
                        $this->getExportDiscount($item, 'coupon'),
                        $this->getExportDiscount($item, 'enoughReduce'),
                        $this->getExportDiscount($item, 'singleEnoughReduce'),
                        $item['goods_price'],
                        $item['dispatch_price'],
                        $item['price'],
                        $this->getGoods($item, 'cost_price'),
                        $item['status_name'],
                        $item['create_time'],
                        !empty(strtotime($item['pay_time'])) ? $item['pay_time'] : '',
                        !empty(strtotime($item['send_time'])) ? $item['send_time'] : '',
                        !empty(strtotime($item['finish_time'])) ? $item['finish_time'] : '',
                        $item['express']['express_company_name'],
                        '[' . $item['express']['express_sn'] . ']',
                        $item['has_one_order_remark']['remark']
                    );
                }
                $export_model->export($file_name, $export_data, 'order.list.index', 'direct_export');
            }
        }
    }

    public function getLevel($member, $levelId)
    {
        $data = [];
        foreach ($levelId as $k => $value) {
            foreach ($member['has_many_parent_team'] as $key => $parent) {
                if ($parent['has_one_team_dividend']['has_one_level']['id'] == $value) {
                    $data[$k] = $parent['has_one_member']['nickname'].' '.$parent['has_one_member']['realname'].' '.$parent['has_one_member']['mobile'];
                    break;
                }
            }
            $data[$k] = $data[$k] ?: '';
        }

        return $data;
    }

    private function getColumns()
    {
        return ["订单id","订单编号", "支付单号", "会员ID", "粉丝昵称", "会员姓名", "联系电话", '省', '市', '区', "收货地址", "商品名称", "商品编码", "商品数量", "支付方式", '抵扣金额', '优惠券优惠', '全场满减优惠', '单品满减优惠', "商品小计", "运费", "应收款", "成本价", "状态", "下单时间", "付款时间", "发货时间", "完成时间", "快递公司", "快递单号", "订单备注"];
    }

    protected function getExportDiscount($order, $key)
    {
        $export_discount = [
            'deduction' => 0,    //抵扣金额
            'coupon' => 0,    //优惠券优惠
            'enoughReduce' => 0,  //全场满减优惠
            'singleEnoughReduce' => 0,    //单品满减优惠
        ];

        foreach ($order['discounts'] as $discount) {

            if ($discount['discount_code'] == $key) {
                $export_discount[$key] = $discount['amount'];
            }
        }

        return $export_discount[$key];
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

            if ($goods['goods_option_title']) {
                $res_title .= '[' . $goods['goods_option_title'] . ']';
            }
            $order_goods = OrderGoods::find($goods['id']);
            if ($order_goods->goods_option_id) {
                $goods_option = GoodsOption::find($order_goods->goods_option_id);
                if ($goods_option) {
                    $goods_sn .= '【' . $goods_option->goods_sn . '】';
                }
            } else {
                $goods_sn .= '【' . $goods['goods_sn'] . '】';
            }

            $goods_title .= '【' . $res_title . '*' . $goods['total'] . '】';
            $total .= '【' . $goods['total'] . '】';
            $cost_price += $goods['goods_cost_price'];
        }
        $res = [
            'goods_title' => $goods_title,
            'goods_sn' => $goods_sn,
            'total' => $total,
            'cost_price' => $cost_price
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

    public function detail()
    {
        $order = $this->orderModel->with(['deductions', 'coupons', 'discounts', 'orderPays' => function ($query) {
            $query->with('payType');
        }, 'hasOnePayType']);

        if (request()->has('id')) {
            $order = $order->find(intval(request('id')));
        }
        if (request()->has('order_sn')) {
            $order = $order->where('order_sn', request('order_sn'))->first();
        }

        if (!$order) {
            throw new AppException('未找到订单');
        }
        //供货链订单
        $list = JdOrderService::jdExpressInfo($order->order_sn,$order->hasManyJdOrderGoods[0]->jd_option_id);
        if ($list['code'] == 1) {
            $dispatch['express_sn'] = $list['data']['info']['no'];
            $dispatch['company_name'] = $list['data']['info']['name'];
            foreach ($list['data']['list'] as $k => $v) {
                $dispatch['data'][$k]['context'] = $v['message'];
                $dispatch['data'][$k]['time'] = date('Y-m-d H:i:s', $v['time']);
            }
            $dispatch['thumb'] = $order->hasManyOrderGoods[0]->thumb;
        }


        $trade = \Setting::get('shop.trade');

        return view('order.detail', [
            'order' => $order ? $order->toArray() : [],
            'invoice_set'=>$trade['invoice'],
            'dispatch' => $dispatch,
            'div_from' => $this->getDivFrom($order),
            'var' => \YunShop::app()->get(),
            'ops' => 'Yunshop\JdSupply::admin.order.ops',
            'edit_goods' => 'plugin.jd-supply.admin.shop-goods.edit'
        ])->render();
    }

    private function getDivFrom($order)
    {
        if (!$order || !$order->hasManyOrderGoods) {
            return ['status' => false];
        }
        $goods_ids = [];
        foreach ($order->hasManyOrderGoods as $key => $goods) {
            $goods_ids[] = $goods['goods_id'];
        }

        $memberInfo = Member::select('realname', 'idcard')->where('uid', $order->uid)->first();

        $result['status'] = DivFromService::isDisplay($goods_ids);
        $result['member_name'] = $memberInfo->realname;
        $result['member_card'] = $memberInfo->idcard;

        return $result;
    }


    public function createOrder()
    {
        $order_id =  request()->input('order_id');


        $order = $this->getOrder()->with(['hasManyOrderGoods', 'address','hasOneJdSupplyOrder','hasManyJdSupplyOrderGoods'])->find($order_id);

        $data = CreateOrderService::createOrder($order);

        if (!isset($data['code']) || $data['code'] != 1) {
            return $this->errorJson($data['msg']);
        }



        JdSupplyOrderStatus::waitSend($order->id);
        
        return $this->successJson('操作成功');
    }

    public function unlockOrder()
    {
        $order_id =  request()->input('order_id');


        $order = $this->getOrder()->with(['hasManyOrderGoods', 'address','hasOneJdSupplyOrder','hasManyJdSupplyOrderGoods'])->find($order_id);



        JdSupplyOrderStatus::unlockOrder($order);

        return $this->successJson('操作成功');
    }
}