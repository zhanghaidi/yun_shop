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

    public $timestamps = false;

    const CREATED_AT = 'create_time';
    const UPDATED_AT = 'update_time';

    /**
     *  获取埋点来源类型.
     *
     * @param  string  $value
     * @return string
     * elseif ($value == 2){
        $value = '病例';
        }
     * elseif ($value == 6){
    $value = '灸师';
    }
     *
     */
    public function getToTypeIdAttribute($value)
    {
       /* if($value == 1){
            $value = '穴位';
        }elseif ($value == 3){
            $value = '文章';
        }elseif ($value == 4){
            $value = '话题';
        }elseif ($value == 5){
            $value = '体质';
        }*/
       $map = [
           1 => 'App\backend\modules\tracking\models\DiagnosticServiceAcupoint',
           3 => 'App\backend\modules\tracking\models\DiagnosticServiceArticle',
           4 => 'App\backend\modules\tracking\models\DiagnosticServicePost',
           5 => 'App\backend\modules\tracking\models\DiagnosticServiceSomatoType'
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
        if($value == 1){
            $value = '<span class="label label-default"> <i class="fa fa-eye"></i> 查看</span>';
        }elseif ($value == 2){
            $value = '<span class="label label-info"> <i class="fa fa-star-half-o"></i> 收藏</span>';
        }elseif ($value == 3){
            $value = '<span class="label label-warning"> <i class="fa fa-shopping-cart"></i> 加购</span>';
        }elseif ($value == 4){
            $value = '<span class="label label-primary"> <i class="fa fa-cc-visa"></i> 下单</span>';
        }elseif ($value == 5){
            $value = '<span class="label label-success"> <i class="fa fa-money"></i> 付款</span>';
        }
        return $value;
    }
    /**
     * 获取与上报埋点相关的商品。
     * return $this->hasOne('App\Phone', 'foreign_key', 'local_key');
     */
    public function goods()
    {
        return $this->belongsTo('App\backend\modules\goods\models\Goods');
    }

    /**
     * 获取与上报埋点相关的用户信息。
     * return $this->hasOne('App\Phone', 'foreign_key', 'local_key');
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



}