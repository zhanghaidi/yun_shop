<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/28
 * Time: 上午9:51
 * comment: 商城数据表操作观察者，所有的供应商的方法
 */

namespace Yunshop\Supplier\common\models;


use app\backend\modules\goods\models\Goods as shop_goods;
use app\backend\modules\goods\models\Sale;
use app\common\models\BaseModel;
use app\common\models\goods\Dispatch;
use app\common\models\Member;
use app\common\models\notice\MessageTemp;
use app\common\models\Order;
use app\common\models\PayType;
use app\common\services\MessageService;
use app\common\services\Session;
use EasyWeChat\Foundation\Application;
use Illuminate\Support\Facades\DB;
use Setting;
use Yunshop\Supplier\common\services\notice\NoticeService;

class SupplierObserverMethods extends BaseModel
{
    /**
     * @name 验证是否供应商
     * @author yangyang
     * @return bool
     */
    private static function verifyIsLogin()
    {
        session_start();
        if (!Session::get('supplier')) {
            return false;
        }
        return true;
    }

    /**
     * @name 提现时，插入提现与订单的中间表
     * @author yangyang
     * @param $withdraw_id
     * @param $data
     * @param $operate
     */
    public static function addRelationOrder($withdraw_id, $data, $operate)
    {
        if ($operate == 'insert') {
            $withdraw = SupplierWithdraw::getWithdrawById($withdraw_id);
            $order_ids = explode(',', $withdraw->toArray()['order_ids']);
            foreach ($order_ids as $order_id) {
                /*$result = WithdrawRelationOrder::select()->where('withdraw_id', $withdraw_id)->where('order_id', $order_id)->get();
                if (empty($result)) {*/
                    WithdrawRelationOrder::create(['withdraw_id' => $withdraw_id, 'order_id' => $order_id]);
                //}
            }
            //提现通知
            SupplierObserverMethods::withdrawNotice($withdraw);
        }
    }

    /**
     * @name 供应商添加商品
     * @author yangyang
     * @param $goods_id
     * @param $data
     * @param $operate
     */
    public static function addSupplierGoods($goods_id, $data, $operate)
    {
        if ($operate == 'created' && self::verifyIsLogin()) {
            // 查询商品得引用商城的goods model ，供应商的goods model 默认is_plugin = 1 查不到
            DB::transaction(function () use ($goods_id) {
                $goods = shop_goods::select()->where('id', $goods_id)->first();
                if ($goods->plugin_id != 0) {
                    return;
                }
                shop_goods::where('id', $goods_id)->update(['is_plugin' => 1,'plugin_id'=>\Yunshop\Supplier\common\models\Supplier::PLUGIN_ID]);
                SupplierGoods::create(
                    [
                        'goods_id'      => $goods_id,
                        'supplier_id'   => Session::get('supplier')['id'],
                        'member_id'     => Session::get('supplier')['member_id'],
                    ]
                );

                /*$sale = new Sale();
                $sale->setRawAttributes([
                    'goods_id'  => $goods_id,
                    'max_point_deduct' => '',
                    'min_point_deduct' => '',
                    'max_balance_deduct' => 0,
                    'is_sendfree' => 0,
                    'ed_num' => 0,
                    'ed_money' => 0,
                    'point' => '',
                    'bonus' => 0
                ]);
                $sale->save();*/
            });
        }
    }

    /**
     * @name 供应商删除商品 todo 目前没用到该方法
     * @author yangyang
     * @param $goods_id
     * @param $data
     * @param $operate
     */
    public static function deleteSupplierGoods($goods_id, $data, $operate)
    {
        if ($operate == 'deleted' && self::verifyIsLogin()) {
            SupplierGoods::where('goods_id', $goods_id)->delete();
        }
    }

    /**
     * @name 供应商添加配送模板
     * @author yangyang
     * @param $dispatch_id
     * @param $data
     * @param $operate
     */
    public static function addSupplierDispatch($dispatch_id, $data, $operate)
    {
        if ($operate == 'created' && self::verifyIsLogin()) {
            $dispatch_model = Dispatch::find($dispatch_id);
            $dispatch_model->is_plugin = 1;
            //todo 有的时候save保存不上，原因不明
            if (!$dispatch_model->save()) {
                $result = Dispatch::where('id', $dispatch_id)->update(['is_plugin' => 1,'plugin_id'=>\Yunshop\Supplier\common\models\Supplier::PLUGIN_ID]);
                if ($result) {
                    $is_plugin = 1;
                }
            } else {
                $is_plugin = 1;
            }
            if ($is_plugin == 1) {
                SupplierDispatch::create(
                    [
                        'dispatch_id'   => $dispatch_id,
                        'supplier_id'   => Session::get('supplier')['id'],
                        'member_id'     => Session::get('supplier')['member_id'],
                    ]
                );
            }
        }
    }

    /**
     * @name 供应商删除配送模板 todo 目前没用到此方法
     * @author yangyang
     * @param $dispatch_id
     * @param $data
     * @param $operate
     */
    public static function deleteSupplierDispatch($dispatch_id, $data, $operate)
    {
        if ($operate == 'deleted' && self::verifyIsLogin()) {
            SupplierDispatch::where('dispatch_id', $dispatch_id)->delete();
        }
    }

    /**
     * @name 下单通知供应商
     * @author yangyang
     * @param $order_id
     * @param $data
     * @param $operator
     */
    public static function createOrderNotice($supplier_order_id, $data, $operator)
    {
        if ($operator != 'created') {
            return;
        }
        $supplier_order = SupplierOrder::select()->where('id', $supplier_order_id)->first();
        $order_model = Order::find($supplier_order->order_id);
        if ($supplier_order) {
            $temp_id = Setting::get('plugin.supplier')['supplier_order_create'];
            if (!$temp_id) {
                return;
            }
            $goods_title = $order_model->hasManyOrderGoods()->first()->title;
            $goods_title .= $order_model->hasManyOrderGoods()->first()->goods_option_title ?: '';
            $params = [
                ['name' => '商城名称', 'value' => \Setting::get('shop.shop')['name']],
                ['name' => '粉丝昵称', 'value' => $order_model->belongsToMember->nickname],
                ['name' => '订单ID', 'value' => $order_model->id],
                ['name' => '订单号', 'value' => $order_model->order_sn],
                ['name' => '下单时间', 'value' => $order_model['create_time']->toDateTimeString()],
                ['name' => '订单金额', 'value' => $order_model['price']],
                ['name' => '运费', 'value' => $order_model['dispatch_price']],
                ['name' => '商品详情（含规格）', 'value' => $goods_title],
            ];
            $msg = MessageTemp::getSendMsg($temp_id, $params);
            if (!$msg) {
                return;
            }
            MessageService::notice(MessageTemp::$template_id, $msg, $supplier_order->member_id, $order_model->uniacid);
        }
    }

    /**
     * @name 支付通知收货通知
     * @author yangyang
     * @param $order_model
     */
    public static function opOrderNotice($order_model)
    {
        $supplier_order = SupplierOrder::select()->where('order_id', $order_model->id)->first();
        if ($supplier_order) {
            $goods_title = $order_model->hasManyOrderGoods()->first()->title;
            $goods_title .= $order_model->hasManyOrderGoods()->first()->goods_option_title ?: '';
            if ($order_model->getOriginal('status') == 0 && $order_model->status == 1) {
                $temp_id = Setting::get('plugin.supplier')['supplier_order_pay'];
                if (!$temp_id) {
                    return;
                }
                $pay_type_name = PayType::where("id",$order_model->pay_type_id)->value("name");
                $params = [
                    ['name' => '商城名称', 'value' => \Setting::get('shop.shop')['name']],
                    ['name' => '粉丝昵称', 'value' => $order_model->belongsToMember->nickname],
                    ['name' => '订单ID', 'value' => $order_model->id],
                    ['name' => '订单号', 'value' => $order_model->order_sn],
                    ['name' => '下单时间', 'value' => $order_model['create_time']->toDateTimeString()],
                    ['name' => '订单金额', 'value' => $order_model['price']],
                    ['name' => '运费', 'value' => $order_model['dispatch_price']],
                    ['name' => '商品详情（含规格）', 'value' => $goods_title],
                    ['name' => '支付方式', 'value' => $pay_type_name],
                    ['name' => '支付时间', 'value' => $order_model['pay_time']->toDateTimeString()],
                ];
                $msg = MessageTemp::getSendMsg($temp_id, $params);
                if (!$msg) {
                    return;
                }
                MessageService::notice(MessageTemp::$template_id, $msg, $supplier_order->member_id, $order_model->uniacid);
            } else if ($order_model->getOriginal('status') == 2 && $order_model->status == 3) {
                $temp_id = Setting::get('plugin.supplier')['supplier_order_finish'];
                if (!$temp_id) {
                    return;
                }
                $params = [
                    ['name' => '商城名称', 'value' => \Setting::get('shop.shop')['name']],
                    ['name' => '粉丝昵称', 'value' => $order_model->belongsToMember->nickname],
                    ['name' => '订单ID', 'value' => $order_model->id],
                    ['name' => '订单号', 'value' => $order_model->order_sn],
                    ['name' => '下单时间', 'value' => $order_model['create_time']->toDateTimeString()],
                    ['name' => '订单金额', 'value' => $order_model['price']],
                    ['name' => '运费', 'value' => $order_model['dispatch_price']],
                    ['name' => '商品详情（含规格）', 'value' => $goods_title],
                    ['name' => '确认收货时间', 'value' => $order_model['finish_time']->toDateTimeString()],
                ];
                $msg = MessageTemp::getSendMsg($temp_id, $params);
                if (!$msg) {
                    return;
                }
                MessageService::notice(MessageTemp::$template_id, $msg, $supplier_order->member_id, $order_model->uniacid);
            } else if ($order_model->getOriginal('status') == 1 && $order_model->status == 2) {
                $temp_id = Setting::get('plugin.supplier')['supplier_order_send'];
                if (!$temp_id) {
                    return;
                }
                $params = [
                    ['name' => '商城名称', 'value' => \Setting::get('shop.shop')['name']],
                    ['name' => '粉丝昵称', 'value' => $order_model->belongsToMember->nickname],
                    ['name' => '订单ID', 'value' => $order_model->id],
                    ['name' => '订单号', 'value' => $order_model->order_sn],
                    ['name' => '下单时间', 'value' => date('Y-m-d H:i:s', $order_model['create_time'])],
                    ['name' => '订单金额', 'value' => $order_model['price']],
                    ['name' => '运费', 'value' => $order_model['dispatch_price']],
                    ['name' => '商品详情（含规格）', 'value' => $goods_title],
                    ['name' => '发货时间', 'value' => date('Y-m-d H:i:s', $order_model['send_time'])],
                    ['name' => '快递公司', 'value' => $order_model['express']['express_company_name'] ?: "暂无信息"],
                    ['name' => '快递单号', 'value' => $order_model['express']['express_sn'] ?: "暂无信息"],
                ];
                $msg = MessageTemp::getSendMsg($temp_id, $params);
                if (!$msg) {
                    return;
                }
                MessageService::notice(MessageTemp::$template_id, $msg, $supplier_order->member_id, $order_model->uniacid);
            }
        }
    }

    /**
     * @name 提交提现申请微信通知
     * @author yangyang
     * @param $model
     */
    public static function withdrawNotice($model)
    {
        $temp_id = Setting::get('plugin.supplier')['supplier_withdraw_apply'];
        if (!$temp_id) {
            return;
        }
        $member = Member::getMemberById($model->member_id);
        $params = [
            ['name' => '提现单号', 'value' => $model->apply_sn],
            ['name' => '提现金额', 'value' => $model->money],
            ['name' => '昵称', 'value' => $member->nickname],
            ['name' => '手机号', 'value' => $member->mobile],
            ['name' => '申请时间', 'value' => date('Y-m-d H:i', time())]
        ];
        $msg = MessageTemp::getSendMsg($temp_id, $params);
        if (!$msg) {
            return;
        }
        MessageService::notice(MessageTemp::$template_id, $msg, $model->member_id, $model->uniacid);
    }

    /**
     * @name 提现每个操作通知
     * @author yangyang
     * @param $model
     */
    public static function withdrawOpNotice($model)
    {
        $member = Member::getMemberById($model->member_id);
        if ($model->status == 2) {
            if (!\YunShop::notice()->getNotSend('supplier.withdraw_ok_title')) {
                return;
            }
            $temp_id = Setting::get('plugin.supplier')['supplier_withdraw_pass'];
            if (!$temp_id) {
                return;
            }
            $params = [
                ['name' => '提现单号', 'value' => $model->apply_sn],
                ['name' => '提现金额', 'value' => $model->money],
                ['name' => '昵称', 'value' => $member->nickname],
                ['name' => '手机号', 'value' => $member->mobile],
                ['name' => '审核时间', 'value' => date('Y-m-d H:i', time())]
            ];
            $msg = MessageTemp::getSendMsg($temp_id, $params);
            if (!$msg) {
                return;
            }
            MessageService::notice(MessageTemp::$template_id, $msg, $model->member_id, $model->uniacid);
        } else if ($model->status == 3) {
            if (!\YunShop::notice()->getNotSend('supplier.withdraw_pay_title')) {
                return;
            }
            $temp_id = Setting::get('plugin.supplier')['supplier_withdraw_play'];
            if (!$temp_id) {
                return;
            }
            $params = [
                ['name' => '提现单号', 'value' => $model->apply_sn],
                ['name' => '提现金额', 'value' => $model->money],
                ['name' => '昵称', 'value' => $member->nickname],
                ['name' => '手机号', 'value' => $member->mobile],
                ['name' => '打款时间', 'value' => date('Y-m-d H:i', time())]
            ];
            $msg = MessageTemp::getSendMsg($temp_id, $params);
            if (!$msg) {
                return;
            }
            MessageService::notice(MessageTemp::$template_id, $msg, $model->member_id, $model->uniacid);
        } else if ($model->status == -1) {
            if (!\YunShop::notice()->getNotSend('supplier.withdraw_no_title')) {
                return;
            }
            $temp_id = Setting::get('plugin.supplier')['supplier_withdraw_reject'];
            if (!$temp_id) {
                return;
            }
            $params = [
                ['name' => '提现单号', 'value' => $model->apply_sn],
                ['name' => '提现金额', 'value' => $model->money],
                ['name' => '昵称', 'value' => $member->nickname],
                ['name' => '手机号', 'value' => $member->mobile],
                ['name' => '驳回时间', 'value' => date('Y-m-d H:i', time())]
            ];
            $msg = MessageTemp::getSendMsg($temp_id, $params);
            if (!$msg) {
                return;
            }
            MessageService::notice(MessageTemp::$template_id, $msg, $model->member_id, $model->uniacid);
        }
    }

    public static function applyNotice($member_id, $type)
    {
        $supplier_set = Setting::get('plugin.supplier');
        $member = Member::getMemberById($member_id);
        if ($type == 1) {
            $temp_id = $supplier_set['supplier_apply_pass'];
            if (!$temp_id) {
                return;
            }
            $params = [
                ['name' => '昵称', 'value' => $member->nickname],
                ['name' => '时间', 'value' => date('Y-m-d H:i', time())]
            ];
            $msg = MessageTemp::getSendMsg($temp_id, $params);
            if (!$msg) {
                return;
            }
        } else {
            if (!\YunShop::notice()->getNotSend('supplier.apply_reject_title')) {
                return;
            }
            $temp_id = $supplier_set['supplier_apply_reject'];
            if (!$temp_id) {
                return;
            }
            $params = [
                ['name' => '昵称', 'value' => $member->nickname],
                ['name' => '时间', 'value' => date('Y-m-d H:i', time())]
            ];
            $msg = MessageTemp::getSendMsg($temp_id, $params);
            if (!$msg) {
                return;
            }
        }
        MessageService::notice(MessageTemp::$template_id, $msg, $member_id);
    }

    /**
     * @name 修改供应商微信角色同时修改相关数据表
     * @author yangyang
     * @param $supplier
     */
    public static function editSupplier($supplier_id, $data, $operator)
    {
        if ($operator != 'updated') {
            return;
        }
        $supplier = Supplier::find($supplier_id);
        if ($supplier->getOriginal('member_id') != $supplier->member_id) {
            //修改supplier_order
            SupplierOrder::where('member_id', $supplier->getOriginal('member_id'))->update(['member_id' => $supplier->member_id]);
            //修改supplier_goods
            SupplierGoods::where('member_id', $supplier->getOriginal('member_id'))->update(['member_id' => $supplier->member_id]);
            //修改supplier_dispatch
            SupplierDispatch::where('member_id', $supplier->getOriginal('member_id'))->update(['member_id' => $supplier->member_id]);
            //修改supplier_withdraw
            SupplierWithdraw::where('member_id', $supplier->getOriginal('member_id'))->update(['member_id' => $supplier->member_id]);
        }
    }
}