<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/6
 * Time: 11:08
 */

namespace app\backend\modules\goods\services;
use app\backend\modules\goods\models\Dispatch;
use app\backend\modules\member\models\MemberLevel;
use app\backend\modules\coupon\models\Coupon;
use app\common\facades\Setting;

class GoodsPriceService
{
    public $params;
    public $request;
    public $coupon_list;
    public $member_discount;
    public $error = null;

    public function __construct($request)
    {
        $this->request = $request;
    }

    /*
     * 计算分销商品价格 fixby-zlt-calcgoodsprice 2020-09-21 18:15
     */

    public function calculation(){
        $goods_data = $this->request->goods;
        if(!isset($goods_data['price'])){
            $this->error = '商品价格不能为空！';
            return;
        }
        $goods_price_arr = $this->getGoodsPrice($this->request,$goods_data['price'],$goods_data['weight']);
        $commission_set = Setting::get('plugin.commission');  //分销设置
        $this->coupon_list = Coupon::uniacid()->pluginId()->orderBy('display_order', 'desc')->get()->toArray(); //优惠券
        $this->member_discount = array_column(MemberLevel::getMemberLevelList(),'discount','id');  //获取会员等级折扣
        foreach ($goods_price_arr as $key => $val){
            if (!empty($this->request->widgets['sale']['max_point_deduct']) && $this->request->widgets['sale']['max_point_deduct'] > $val['price']) {
                return ['status' => -1, 'msg' => '积分最大抵扣金额大于商品现价'];
            }
            if (!empty($this->request->widgets['sale']['min_point_deduct']) && $this->request->widgets['sale']['min_point_deduct'] > $val['price']) {
                return ['status' => -1, 'msg' => '积分最少抵扣金额大于商品现价'];
            }

            $val['reduction'] = $val['point_deduct'] = 0;
            if($this->request->widgets['sale']['ed_reduction'] > 0 && $val['price'] >= $this->request->widgets['sale']['ed_full'] ){ //计算单品满减
                $val['reduction'] = $this->request->widgets['sale']['ed_reduction'];
                $val['calc_price'] = round($val['calc_price'] - $this->request->widgets['sale']['ed_reduction'],2);
            }
            $val['coupon_discount'] = $this->calCouponDiscount($this->request,$val['calc_price']);
            if($val['coupon_discount'] > 0)
                $val['calc_price'] = round($val['calc_price'] - $val['coupon_discount'], 2);

            if(!empty($this->request->widgets['sale']['max_point_deduct'])){  //计算积分抵扣
                $val['point_deduct'] = $this->request->widgets['sale']['max_point_deduct'];
            }elseif(!empty($this->request->widgets['sale']['min_point_deduct'])){
                $val['point_deduct'] = $this->request->widgets['sale']['min_point_deduct'];
            }

            if($val['point_deduct'] > 0){
                $val['calc_price'] = round($val['calc_price'] - $val['point_deduct'],2);
                if($val['calc_price'] <= 0){
                    return ['status' => -1, 'msg' => '积分抵扣金额过多'];
                }
            }

            //计算折扣
            $discount = 0;
            if(!empty($this->request->widgets['discount']) && !empty($this->request->widgets['discount']['discount_value'])){
                if($this->request->widgets['discount']['discount_method'] == 1){   //折扣
                    $min_rate = 10;
                    foreach ($this->request->widgets['discount']['discount_value'] as $d_key => $d_val){
                        if(!empty($d_val)){
                            $min_rate = $d_val < $min_rate ? $d_val : $min_rate;
                        }elseif(!empty($this->member_discount[$d_key])){
                            $min_rate = $this->member_discount[$d_key] < $min_rate ? $this->member_discount[$d_key] : $min_rate;
                        }
                    }
                    if($min_rate > 0 && $min_rate < 10){
                        $discount = round($val['calc_price'] * (10-$min_rate) / 10,2);
                    }
                }elseif($this->request->widgets['discount']['discount_method'] == 2){  //固定金额
                    foreach ($this->request->widgets['discount']['discount_value'] as $v){
                        if(!empty($v) && $v > $discount)
                            $discount = $v;
                    }
                }
                if($discount > 0)
                    $val['calc_price'] = round($val['calc_price'] - $discount,2);
            }
            $val['discount'] = $discount;
            $val['final_price'] = $val['calc_price'];

            //计算赠送余额
            $val['award_balance'] = 0;
            if(!empty($this->request->widgets['sale']['award_balance'])){
                $award_balance = $this->request->widgets['sale']['award_balance'];
                if(is_numeric($award_balance)){
                    $val['award_balance'] = $award_balance;
                }else if(preg_match('/(\d+)%/',$award_balance,$match)){
                    if($match[1] >= 100){
                        return ['status' => -1, 'msg' => '赠送余额比列超过100%'];;
                    }
                    $val['award_balance'] = round($val['calc_price'] * $match[1] /100,2);
                }

                $val['final_price'] = round($val['final_price'] - $val['award_balance'],2);

                if($val['final_price'] <= 0){
                    return ['status' => -1, 'msg' => '赠送余额金额过多'];
                }
            }

            //计算赠送积分
            $val['point_amount'] = 0;
            if(!empty($this->request->widgets['sale']['point'])){
                $point = $this->request->widgets['sale']['point'];
                if(is_numeric($point)){
                    $val['point_amount'] = round($point * 0.01,2);
                }else if(preg_match('/(\d+)%/',$point,$match)){
                    $point_give = round($val['calc_price'] * $match[1] /100);
                    if($point_give > 0)
                        $val['point_amount'] = round($point_give * 0.01,2);
                }
                if($val['point_amount'])
                    $val['final_price'] = round($val['final_price'] - $val['point_amount'], 2);
            }

            if($val['final_price'] <= 0){
                return ['status' => -1, 'msg' => '赠送积分金额过多'];
            }else{
                $val['final_price'] = round($val['final_price'],2);
            }

            //是否全额积分抵扣
            if(!empty($this->request->widgets['sale']['has_all_point_deduct'])){
                if($this->request->widgets['sale']['all_point_deduct'] <= 0)
                    return ['status' => -1, 'msg' => '积分全额抵扣需要积分不能为0'];
                if($val['final_price'] > $this->request->widgets['sale']['all_point_deduct'] * 0.01){
                    return ['status' => -1, 'msg' => '积分全额抵扣需要积分过少'];
                }
            }

            //分销计算
            $commission_amount = 0;
            if(!empty($this->request->widgets['commission']['is_commission'])){
                if($this->request->widgets['commission']['has_commission']){
                    foreach ($this->request->widgets['commission']['rule'] as $cv){
                        $temp = 0;
                        if($cv['first_level_rate'] > 0){
                            $temp += $val['calc_price']  * $cv['first_level_rate'] / 100;
                        }elseif($cv['first_level_pay'] > 0){
                            $temp += $cv['first_level_pay'];
                        }
                        if($cv['second_level_rate'] > 0){
                            $temp += $val['calc_price']  * $cv['second_level_rate'] / 100;
                        }elseif($cv['second_level_pay'] > 0){
                            $temp += $cv['second_level_pay'];
                        }
                        if($temp > $commission_amount)
                            $commission_amount = $temp;
                    }
                }else{
                    if($commission_set['is_commission'] == 1){
                        if($commission_set['first_level'] > 0){
                            $commission_amount += $val['calc_price']  * $commission_set['first_level'] / 100;
                        }
                        if($commission_set['second_level'] > 0){
                            $commission_amount += $val['calc_price']  * $commission_set['second_level'] / 100;
                        }
                    }
                }
            }
            $val['commission_amount'] = round($commission_amount, 2);
            if($val['commission_amount'] > 0){
                $val['final_price'] = round($val['final_price'] - $val['commission_amount'],2);
            }

            /*  邮费计算暂时不需要
            if((!empty($this->request->widgets['sale']['ed_num']) && $this->request->widgets['sale']['ed_num'] == 1) || (!empty($this->request->widgets['sale']['ed_money']) && $this->request->widgets['sale']['ed_money'] <= $val['price'])){  //包邮
                $dispatch_fee = 0;
            }else{
                if($this->request->widgets['dispatch']['dispatch_type'] == 1){  //统一邮费计算
                    $dispatch_fee = $this->request->widgets['dispatch']['dispatch_price'];
                }else{
                    if($this->request->widgets['dispatch']['dispatch_id'] > 0){  //自选模板邮费
                        $dispatchModel = Dispatch::find($this->request->widgets['dispatch']['dispatch_id']);
                    } else {  //默认模板邮费
                        $dispatchModel = Dispatch::getOneByDefault();
                    }
                    if($dispatchModel){
                        if($dispatchModel->calculate_type == 1){ //按件计费
                            $dispatch_fee = $dispatchModel->first_piece_price;
                        } else {
                            if($val['weight'] <= $dispatchModel->first_weight){ //按重量计费
                                $dispatch_fee = $dispatchModel->first_weight_price;
                            } else {
                                $dispatch_fee = round($dispatchModel->first_weight_price + ceil(($val['weight']-$dispatchModel->first_weight) / $dispatchModel->another_weight) * $dispatchModel->another_weight_price,2);
                            }
                        }
                    } else {
                        $dispatch_fee = 0;
                    }
                }
            }
            $val['dispatch_fee'] = $dispatch_fee;
            */

            $val['html'] = "<div style='padding-top: 10px;'>{$val['goods_title']}：</div><div style='padding-top: 8px;'>商品初始价格为：{$val['price']}元</div>
<div style='padding-top: 8px;'>单品满额立减金额：{$val['reduction']}元</div><div style='padding-top: 8px;'>折扣金额：{$val['discount']}元</div><div style='padding-top: 8px;'>优惠券优惠金额：{$val['coupon_discount']}元</div><div style='padding-top: 8px;'>赠送余额金额：{$val['award_balance']}元</div><div style='padding-top: 8px;'>赠送积分金额：{$val['point_amount']}元</div><div style='padding-top: 8px;'>积分抵扣金额：{$val['point_deduct']}元</div><div style='padding-top: 8px;'>分销金额：{$val['commission_amount']}元</div><div style='padding-top: 8px;'>商品计算出最终价格为：{$val['final_price']}元</div>";
            $goods_price_arr[$key] = $val;
        }

        $html = '';
        foreach ($goods_price_arr as $v){
            $html .= $v['html'];
        }

        return ['status'=>1,'data'=>['data_arr'=>$goods_price_arr,'html'=>$html]];
    }

    /*
     * 获取商品所有价格
     */

    protected function getGoodsPrice($request,$goods_price,$goods_weight){
        $res = [];
        if(!empty($request->option_ids)){
            $option_ids = $request->option_ids;
            $len = count($option_ids);
            for ($k = 0; $k < $len; $k++) {
                $ids = $option_ids[$k];
                $option_price = floatVal($request['option_productprice_' . $ids][0]) ?: $goods_price;
                $option_weight = floatVal($request['option_weight_' . $ids][0]) ?: $goods_weight;
                $option_title = $request['option_title_' . $ids][0];
                $res[] = ['price'=>$option_price,'weight'=>$option_weight,'calc_price'=>$option_price,'goods_title'=>'规格-' . $option_title];
            }
        }else{
            $res[] = ['price'=>$goods_price,'weight'=>$goods_weight,'calc_price'=>$goods_price,'goods_title'=>'默认'];
        }
        return $res;
    }

    /*
     * 计算优惠券折扣金额
     */

    protected function calCouponDiscount($request,$price){
        $max_coupon_discount = 0;
        foreach ($this->coupon_list as $v){
            $coupon_discount = 0;
            if(!in_array($v['use_type'],[0,1,2]))
                continue;
            if($v['use_type'] == 1){
                $cat_arr = explode(',',$v['category_ids']);
                $can_use = false;
                if(!empty($request->category->parentid)){
                    foreach ($request->category['parentid'] as $v1){
                        in_array($v1,$cat_arr) && $can_use = true;
                    }
                }
                if(!empty($request->category['childid'])){
                    foreach ($request->category['childid'] as $v2){
                        in_array($v2,$cat_arr) && $can_use = true;
                    }
                }
                if(!$can_use)
                    continue;
            }elseif($v['use_type'] == 2){
                if(empty($request->id) || !in_array($request->id,explode(',',$v['goods_ids'])))
                    continue;
            }

            if($v['is_complex'] == 1){  //多张一起使用 必须限制使用条件才能计算
                if($v['enough'] > 0){
                    $max_num = floor($price / $v['enough']);
                    if($max_num > 0){
                        if($v['coupon_method'] == 1){
                            $coupon_discount = round($v['deduct'] * $max_num, 2);
                        }elseif($v['coupon_method'] == 2){
                            $coupon_discount = round($price * (1 - pow($v['discount'],$max_num)), 2);
                        }
                    }
                }
            }else{
                if($v['enough'] == 0 || ($v['enough'] > 0 && $price >= $v['enough'])){ //满足使用条件
                    if($v['coupon_method'] == 1){
                        $coupon_discount = $v['deduct'];
                    }elseif($v['coupon_method'] == 2){
                        $coupon_discount = round($price * (1 - $v['discount']), 2);
                    }
                }
            }

            if($coupon_discount < $price && $coupon_discount > $max_coupon_discount)
                $max_coupon_discount = $coupon_discount;
        }
        return $max_coupon_discount;
    }

}