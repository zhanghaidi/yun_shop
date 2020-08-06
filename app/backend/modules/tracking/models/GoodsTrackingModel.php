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
     */
    public function getToTypeIdAttribute($value)
    {
        if($value == 1){
            $value = '穴位';
        }elseif ($value == 2){
            $value = '病例';
        }elseif ($value == 3){
            $value = '文章';
        }elseif ($value == 4){
            $value = '话题';
        }elseif ($value == 5){
            $value = '体质';
        }elseif ($value == 6){
            $value = '灸师';
        }
        return $value;
    }

    /**
     * 获取与上报埋点相关的商品。
     * return $this->hasOne('App\Phone', 'foreign_key', 'local_key');
     */
    public function goods()
    {
        return $this->hasOne('App\backend\modules\goods\models\Goods');
    }

    /**
     * 获取与上报埋点相关的用户信息。
     * return $this->hasOne('App\Phone', 'foreign_key', 'local_key');
     */
    public function user()
    {
        return $this->hasOne('App\backend\modules\tracking\models\DiagnosticServiceUserModel','user_id','ajy_uid');
    }


}