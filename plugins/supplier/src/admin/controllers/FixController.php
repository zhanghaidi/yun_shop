<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/12/19
 * Time: 4:50 PM
 */

namespace Yunshop\Supplier\admin\controllers;


use app\common\components\BaseController;
use Illuminate\Support\Facades\DB;

class FixController extends BaseController
{
    public $transactionActions = ['*'];

    public function test()
    {
        dd(1);
        exit;
        $goods_ret = DB::table('yz_goods')
            ->select([
                'yz_goods.id',
                'yz_goods.uniacid',
                'yz_goods.is_plugin',
                'yz_goods.plugin_id',
                'yz_supplier_goods.supplier_id',
                'yz_supplier_goods.member_id'
            ])
            ->join('yz_supplier_goods', 'yz_supplier_goods.goods_id', '=',
                'yz_goods.id')
            //->where('yz_goods.plugin_id', 0)
            ->get();
        if ($goods_ret->isEmpty()) {
            dd('供应商商品正常');
            exit;
        }
        foreach ($goods_ret as $goods) {
            $order_ret = DB::table('yz_order')
                ->select(['yz_order.id', 'yz_order.plugin_id', 'yz_order.uniacid', 'yz_order.order_sn'])
                ->join('yz_order_goods', 'yz_order_goods.order_id', '=',
                    'yz_order.id')
                ->where('yz_order_goods.goods_id', $goods['id'])
                ->where('yz_order.plugin_id', 0)
                ->get();
            if (!$order_ret->isEmpty()) {
                foreach ($order_ret as $order) {
                    $order_goods_ret = DB::table('yz_order_goods')
                        ->select([
                            'yz_order_goods.goods_id',
                            'yz_order_goods.goods_option_id',
                            'yz_order_goods.goods_cost_price',
                            'yz_order_goods.uniacid'
                        ])
                        ->where('yz_order_goods.order_id', $order['id'])
                        ->get();
                    $profit = 0;
                    $order_goods_information = [];
                    $supplier_order_data = [];
                    $supplier_order_data['order_id'] = $order['id'];
                    $supplier_order_data['supplier_id'] = $goods['supplier_id'];
                    $supplier_order_data['member_id'] = $goods['member_id'];
                    foreach ($order_goods_ret as $key => $order_goods) {
                        $order_goods_information[$key] = [
                            'goods_id'      => $order_goods['goods_id'],
                            'option_id'     => $order_goods['goods_option_id'],
                            'cost_price'    => $order_goods['goods_cost_price']
                        ];
                        $profit += $order_goods['goods_cost_price'];
                    }
                    $supplier_order_data['supplier_profit'] = $profit;
                    $supplier_order_data['apply_status'] = 0;
                    $supplier_order_data['order_goods_information'] = 0;
                    $supplier_order_data['uniacid'] = $order['uniacid'];
                    dump('----------');
                    dump($supplier_order_data);
                    dump('order_sn' . $order['order_sn']);
                    /*DB::table('yz_supplier_order')
                        ->insert($supplier_order_data);
                    DB::table('yz_order')
                        ->where('id', $order['id'])
                        ->update(['plugin_id' => 92, 'is_plugin' => 1]);*/
                }
            }
        }
        dump('ok');
    }
}