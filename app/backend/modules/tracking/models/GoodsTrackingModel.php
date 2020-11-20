<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 17/2/23
 * Time: 下午10:57
 */

namespace app\backend\modules\tracking\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class GoodsTrackingModel extends Model
{
    protected $table = 'diagnostic_service_goods_tracking';
    protected $appends = ['type_id','action_id','action_name','view_num','favorites_num','add_purchase_num','order_payment_num','create_order_num','order_payment_amount'];
    public $timestamps = false;

    const CREATED_AT = 'create_time';
    const UPDATED_AT = 'update_time';

    /**
     *  获取埋点来源类型.
     *
     * @param  string  $value
     * @return string
     */
    public function getToTypeIdAttribute($value)
    {
        $this->type_id = $value;
        $map = [
           1 => 'App\backend\modules\tracking\models\DiagnosticServiceAcupoint', //穴位
           3 => 'App\backend\modules\tracking\models\DiagnosticServiceArticle', //文章
           4 => 'App\backend\modules\tracking\models\DiagnosticServicePost',  //社区
           5 => 'App\backend\modules\tracking\models\DiagnosticServiceSomatoType', //体质
           //6 => 'App\backend\modules\tracking\models\ChartChartuser'
           7 => 'App\backend\modules\tracking\models\AppletsliveReplay', //课时
            8 => 'App\backend\modules\tracking\models\AppletsliveRoom', //直播
            //9 =>'', //商城首页
            10 => 'App\backend\modules\tracking\models\DiagnosticServiceBanner', //活动海报/二维码banner
            11 => 'App\backend\modules\tracking\models\DiagnosticServiceUser', //用户分享
            15 => 'App\backend\modules\coupon\models\Coupon'  //优惠券
        ];
        return $map[$value];
    }

    /**
     *  获取埋点来源操作.
     *
     * @param  string  $value
     * @return string
     */
    public function getActionAttribute($value)
    {
        $this->action_id = $value;
        if($value == 1){
            $this->action_name = '<span class="label label-default"> <i class="fa fa-eye"></i> 查看</span>';
        }elseif ($value == 2){
            $this->action_name = '<span class="label label-info"> <i class="fa fa-star-half-o"></i> 收藏</span>';
        }elseif ($value == 3){
            $this->action_name = '<span class="label label-warning"> <i class="fa fa-shopping-cart"></i> 加购</span>';
        }elseif ($value == 4){
            $this->action_name = '<span class="label label-primary"> <i class="fa fa-cc-visa"></i> 下单</span>';
        }elseif ($value == 5){
            $this->action_name = '<span class="label label-success"> <i class="fa fa-money"></i> 付款</span>';
        }
        $map = [
            4 => 'App\backend\modules\order\models\Order',
            5 => 'App\backend\modules\order\models\OrderPay',
        ];
        return $map[$value];

    }

    //添加商品id对应字段
    public function getGoodsIdAttribute($value)
    {
        $this->view_num = count(self::where(['goods_id' => $value, 'action' => 1])->groupBy('user_id')->get());
        $this->favorites_num = self::where(['goods_id' => $value, 'action' => 2])->count();
        $this->add_purchase_num = self::where(['goods_id' => $value, 'action' => 3])->sum('val');
        $this->create_order_num = self::where(['goods_id' => $value, 'action' => 4])->count();
        $this->order_payment_num = self::where(['goods_id' => $value,'action' => 5])->count();
        $this->order_payment_amount = DB::table('yz_order_goods as og')
            ->join('yz_order as o', 'og.order_id', '=', 'o.id')
            //->select('u.avatarurl', 'p.address')
            ->where('o.status','>', 1)
            ->where('og.goods_id', $value)
            ->sum('og.payment_amount');
        // $favorites_num = DB::table('diagnostic_service_goods_tracking')->where(['goods_id' => $value['id'],'action' => 2])->whereBetween('create_time', $whereBetween)->count();
        // $add_purchase_num = DB::table('diagnostic_service_goods_tracking')->where(['goods_id' => $value['id'],'action' => 3])->whereBetween('create_time', $whereBetween)->sum('val');
        // $create_order_num = DB::table('diagnostic_service_goods_tracking')->where(['goods_id' => $value['id'],'action' => 4])->whereBetween('create_time', $whereBetween)->count();
        // $order_payment_num = DB::table('diagnostic_service_goods_tracking')->where(['goods_id' => $value['id'],'action' => 5])->whereBetween('create_time', $whereBetween)->count();
        return $value;


    }


    /**
     * 获取与上报埋点相关的商品。
     * return $this->hasOne('App\Goods', 'foreign_key', 'local_key');
     */
    public function goods()
    {
        return $this->belongsTo('App\backend\modules\goods\models\Goods');
    }

    /**
     * 获取与上报埋点相关的用户信息。
     * return $this->hasOne('App\User', 'foreign_key', 'local_key');
     */
    public function user()
    {
        return $this->belongsTo('App\backend\modules\tracking\models\DiagnosticServiceUser','user_id','ajy_uid');
    }

    /**
     * 取得埋点对应的来源对象。
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function resource()
    {
        return $this->morphTo('resource','to_type_id','resource_id');
    }

    /**
     * 取得埋点对应的操作订单。
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function order()
    {
        return $this->morphTo('order','action','val');
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeRecords($query)
    {
        return $query->with(['user' => function ($user) {
                return $user->select('ajy_uid', 'nickname', 'avatarurl');
            }])
            ->with('resource')->with('order')
            ->with(['goods' => function ($goods) {
                return $goods->select('id','title','thumb','price');
            }]);
    }

    //搜索条件
    public function scopeSearch($query, array $search)
    {
        /*if ($search['ordersn']) {
            $query->where('ordersn', 'like', $search['ordersn'] . '%');
        }*/

        //搜索来源类型筛选
        if ($search['type_id']) {
            $query = $query->where('to_type_id', $search['type_id']);
        }

        //操作类型筛选
        if ($search['action_id']) {
            $query = $query->where('action', $search['action_id']);
        }
        //根据用户筛选
        if ($search['realname']) {
            $query = $query->whereHas('user', function($user)use($search) {
                $user = $user->select('ajy_uid', 'nickname','telephone','avatarurl')
                    ->where('nickname', 'like', '%' . $search['realname'] . '%')
                    ->orWhere('telephone', 'like', '%' . $search['realname'] . '%')
                    ->orWhere('ajy_uid', $search['realname']);
            });
        }
        //根据商品筛选
        if ($search['keywords']) {
            $query = $query->whereHas('goods', function($goods)use($search) {
                $goods = $goods->select('id', 'title','thumb','price')
                    ->where('title', 'like', '%' . $search['keywords'] . '%')
                    ->orWhere('id', $search['keywords']);
            });
        }
        //根据类型筛选
        if ($search['type']) {
            $query = $query->where('action', $this->searchNameToActionId($search['type']))->orWhere('to_type_id', $this->searchNameToTypeId($search['type']));
        }
        //根据时间筛选
        if ($search['search_time'] == 1) {
            $query = $query->whereBetween('create_time', [strtotime($search['time']['start']),strtotime($search['time']['end'])]);
        }
        return $query;
    }


    //根据搜索关键词转换成相应的动作类型
    public function searchNameToActionId($value){
        if($value == '查看'){
            $value = 1;
        }elseif($value == '收藏'){
            $value = 2;
        }elseif($value == '加购'){
            $value = 3;
        }elseif($value == '下单'){
            $value = 4;
        }elseif($value == '支付'){
            $value = 5;
        }
        return $value;
    }


    //根据搜索关键词转换成相应的动作类型
    public function searchNameToTypeId($value){
        if($value == '穴位'){
            $value = 1;
        }elseif($value == '文章'){
            $value = 3;
        }elseif($value == '帖子'){
            $value = 4;
        }elseif($value == '灸师'){
            $value = 6;
        }elseif($value == '课时'){
            $value = 7;
        }elseif($value == '直播'){
            $value = 8;
        }elseif($value == '商城'){
            $value = 9;
        }elseif($value == '活动'){
            $value = 10;
        }elseif($value == '分享'){
            $value = 11;
        }elseif($value == '搜索'){
            $value = 12;
        }elseif($value == '购物车'){
            $value = 13;
        }elseif($value == '我的订单'){
            $value = 14;
        }elseif($value == '优惠券'){
            $value = 15;
        }elseif($value == '我的收藏'){
            $value = 16;
        }elseif($value == '我的足记'){
            $value = 17;
        }elseif($value == '店铺装修'){
            $value = 18;
        }elseif($value == '未知'){
            $value = 404;
        }
        return $value;
    }


}