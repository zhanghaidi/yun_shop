<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/6/20
 * Time: 下午2:26
 */

namespace Yunshop\Exhelper\common\models;


class Order extends \app\backend\modules\order\models\Order
{
    public function scopeByOrderSn($query, $order_sn)
    {
        return $query->where('order_sn', $order_sn);
    }

    public function scopeIsPlugin($query)
    {
        return $query;
    }

    public function hasOnePrint()
    {
        return $this->hasOne(PrintStatus::class, 'order_id', 'id');
    }

    public function scopeWhereInIds($query, $order_ids)
    {
        return $query->whereIn('id', $order_ids);
    }

    public function hasManyOrderGoods()
    {
        return $this->hasMany(OrderGoods::class, 'order_id', 'id');
    }

    public function scopeSearch($order_builder, $params)
    {
        if (!$params) {
            return $order_builder;
        }
        if (isset($params['order_sn']) && $params['order_sn']) {
            $order_builder->where('order_sn', $params['order_sn']);
        }
        if (isset($params['order_status']) && $params['order_status']) {
            $order_builder->where('status', $params['order_status']);
        }

        if (isset($params['create_order_time']) && $params['create_order_time']) {
            $order_builder->whereBetween('create_time', [strtotime($params['time']['start']), strtotime($params['time']['end'])]);
        }

        if (isset($params['member']) && $params['member']) {
            $order_builder->whereHas('belongsToMember', function($member)use($params) {
                $member = $member->select('uid', 'nickname','realname','mobile','avatar')
                     ->where('realname', 'like', '%' . $params['member'] . '%')
                    ->orWhere('mobile', 'like', '%' . $params['member'] . '%')
                    ->orWhere('nickname', 'like', '%' . $params['member'] . '%');
            });
        }
        if (isset($params['express_sn']) && $params['express_sn']) {
            $order_builder->whereHas('express', function($express)use($params){
            
                return $express->select()->where('express_sn', $params['express_sn']);
            });
        }
        if (isset($params['express_print_status']) && $params['express_print_status']) {
            $order_builder->whereHas('hasOnePrint', function($print)use($params){
             
                    return $print->where('express_print_status', $params['express_print_status'] == 'no'? 0 : '>', 0);
            });
        }
        if (isset($params['send_print_status']) && $params['send_print_status']) {
             $order_builder->whereHas('hasOnePrint', function($print)use($params){

                    return $print->where('send_print_status', $params['send_print_status'] == 'no' ? 0 : '>', 0);
            });
        }
        if (isset($params['panel_print_status']) && $params['panel_print_status']) {
            $order_builder->whereHas('hasOnePrint', function($print)use($params){

                    return $print->where('panel_print_status', $params['panel_print_status'] == 'no'? 0 : '>', 0);
            });
        }
        return $order_builder;
    }
}