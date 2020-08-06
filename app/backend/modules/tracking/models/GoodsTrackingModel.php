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
     * 获取与上报埋点相关的商品。
     * return $this->hasOne('App\Phone', 'foreign_key', 'local_key');
     */
    public function goods()
    {
        return $this->hasOne('App\backend\modules\goods\models\Goods');
    }




}