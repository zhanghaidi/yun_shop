<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 17/2/23
 * Time: 下午10:57
 */
namespace app\backend\modules\tracking\models;

use Illuminate\Database\Eloquent\Model;

class GoodsTrackingStatistics extends Model
{
    public static function boot()
    {
        parent::boot();
        self::addGlobalScope(new UniacidScope);
    }

    protected $table = 'diagnostic_service_goods_tracking_statistics';


    /**
     * 获取与上报埋点相关的商品。
     * return $this->hasOne('App\Goods', 'foreign_key', 'local_key');
     */
    public function goods()
    {
        return $this->belongsTo('App\backend\modules\goods\models\Goods');
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeRecords($query)
    {
        return $query->with(['goods' => function ($goods) {
            return $goods->select('id','title','thumb','price');
        }]);
    }

    //搜索条件
    public function scopeSearch($query, array $search)
    {
        //根据商品筛选
        if ($search['keywords']) {
            $query = $query->whereHas('goods', function($goods)use($search) {
                $goods = $goods->select('id', 'title','thumb','price')
                    ->where('title', 'like', '%' . $search['keywords'] . '%')
                    ->orWhere('id', $search['keywords']);
            });
        }

        return $query;
    }

}
