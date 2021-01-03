<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 17/2/23
 * Time: 下午10:57
 */

namespace app\backend\modules\tracking\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use app\common\scopes\UniacidScope;

class MemberCart extends Model
{

    use SoftDeletes;
    protected $guarded = ['id'];
    protected $table = 'yz_member_cart';

    public static function boot()
    {
        parent::boot();
        self::addGlobalScope(new UniacidScope());
    }

    /**
     * 获取与购物车相关的商品。
     * return $this->hasOne('App\Goods', 'foreign_key', 'local_key');
     */
    public function goods()
    {
        return $this->belongsTo('App\backend\modules\goods\models\Goods');
    }

    /**
     * 获取与购物车相关的用户信息。
     * return $this->hasOne('App\User', 'foreign_key', 'local_key');
     */
    public function user()
    {
        return $this->belongsTo('App\backend\modules\tracking\models\DiagnosticServiceUser','member_id','ajy_uid');
    }


    /**
     * 取得购物车相关的规格。
     *
     * @return $this->hasOne('App\User', 'foreign_key', 'local_key');
     */
    public function option()
    {
        return $this->belongsTo('App\backend\modules\goods\models\GoodsOption','option_id','id');
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
            ->with(['option' => function($option) {
                return $option->select('id','goods_id','title','thumb','goods_sn','product_price');
            }])
            ->with(['goods' => function ($goods) {
                return $goods->select('id','title','thumb','price');
            }]);
    }

    //搜索条件
    public function scopeSearch($query, array $search)
    {
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
        if ($search['search_time'] == 1) {
            $query = $query->whereBetween('created_at', [strtotime($search['time']['start']),strtotime($search['time']['end'])]);
        }
        return $query;
    }


}