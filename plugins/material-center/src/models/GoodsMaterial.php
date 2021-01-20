<?php

namespace Yunshop\MaterialCenter\models;

use Illuminate\Support\Facades\DB;
use app\common\models\BaseModel;

class GoodsMaterial extends BaseModel
{
    public $table = 'yz_goods_material';
    public $guarded = [''];
    // protected $dates = ['created_at'];
    public $hidden = ['updated_at', 'deleted_at', 'uniacid', 'share', 'download' ,'is_show', 'collect'];

    public function getDates()
    {
        return ['created_at'];
    }

    public function scopeSearch($query, $search)
    {
        $query = $query->where('uniacid', \YunShop::app()->uniacid);

        if (!$search) {
            return $query;
        }

        if (isset($search['is_show']) && in_array($search['is_show'], [1, 2])) {
            
            $search['is_show'] = $search['is_show'] == 2 ? 0 : 1;

            $query->where('is_show', $search['is_show']);
        }

        if ($search['is_time'] == 1) {

            $query->where('created_at', '>', strtotime($search['time_range']['start']))->where('created_at', '<', strtotime($search['time_range']['end']));
         }

         if (isset($search['keyword'])) {

              $query->where('title', 'like','%'.$search['keyword'].'%');

//              if (!$material) {
//
//                  $material = $material->whereHas('goods', function ($material) use ($search) {
//
//                       $material = $material->where('title', 'like', '%' . $search['keyword'] . '%');
//
//                  });
//              }
        //     $query->whereHas('goods', function ($goods) use ($search) {

        //         $goods = $goods->where('title', 'like', '%' . $search['keyword'] . '%');

        //     })->orWhere('title', 'like','%'.$search['keyword'].'%');
//
         }

        return $query;
    }

    public function goods()
    {
        return $this->belongsTo(\app\common\models\Goods::class);
    }

    public function atributeNames()
    {
        return [
            'goods_id' => '商品id',
            'content' => '推荐文案',
            'title' => '推荐文案',
            'images' => '图片',
        ];
    }

    public function rules()
    {
        return [
            'goods_id' => 'required',
            'content' => 'required|string|min:10|max:200',
            'title' => 'required|string',
            'images' => 'required',
        ];
    }    
}