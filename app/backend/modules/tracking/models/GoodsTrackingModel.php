<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 17/2/23
 * Time: 下午10:57
 */

namespace app\backend\modules\tracking\models;

use Illuminate\Database\Eloquent\Model;

class GoodsTrackingModel extends Model
{
    protected $table = 'diagnostic_service_goods_tracking';
    protected $appends = ['type_id','action_id','action_name'];

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
           1 => 'App\backend\modules\tracking\models\DiagnosticServiceAcupoint',
           3 => 'App\backend\modules\tracking\models\DiagnosticServiceArticle',
           4 => 'App\backend\modules\tracking\models\DiagnosticServicePost',
           5 => 'App\backend\modules\tracking\models\DiagnosticServiceSomatoType',
           6 => 'App\backend\modules\tracking\models\ChartChartuser'
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
            5 => 'App\backend\modules\order\models\Order',
        ];
        return $map[$value];

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
                return $goods->select('id','thumb','title','price');
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
        //根据时间筛选
        /*if ($search['searchtime']) {
            $query = $query->whereBetween('create_time', [strtotime($search['times']['start']),strtotime($search['times']['end'])]);
        }*/
        return $query;
    }


}