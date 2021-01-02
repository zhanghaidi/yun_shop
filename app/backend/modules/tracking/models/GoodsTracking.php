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
use app\common\scopes\UniacidScope;

class GoodsTracking extends Model
{
    public static function boot()
    {
        parent::boot();
        self::addGlobalScope(new UniacidScope());
    }

    protected $table = 'diagnostic_service_goods_tracking';
    public $timestamps = false;

    const CREATED_AT = 'create_time';
    const UPDATED_AT = 'update_time';

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




}