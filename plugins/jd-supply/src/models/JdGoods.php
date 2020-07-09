<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/26
 * Time: 14:14
 */

namespace Yunshop\JdSupply\models;


use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class JdGoods extends BaseModel
{
    use SoftDeletes;

    public $table = 'yz_jd_supply_goods';

    protected $guarded = [''];



    public static function relationSave($goodsId, $data, $operate)
    {
        if (!$goodsId) {
            return false;
        }
        if (empty($data)) {
            return false;
        }

        $goodsModel = self::getGoodsModel($goodsId, $operate);
        //判断deleted
        if ($operate == 'deleted') {

            return $goodsModel->delete();

        } else if ($operate == 'created') {
            $JdGoodsData = [
                'goods_id' => $goodsId,
                'uniacid' => \YunShop::app()->uniacid,
                'jd_goods_id' => $data['jd_goods_id'],
                'shop_id' => $data['shop_id'],
                'source'  => $data['source'],
            ];

            $goodsModel->fill($JdGoodsData);

            $res = $goodsModel->save();

            return $res;
        } else {

            $goodsModel->fill($data);
            return  $goodsModel->save();
        }

    }

    public static function getGoodsModel($goodsId, $operate)
    {
        $model = false;
        if ($operate != 'created') {
            $model = JdGoods::where('goods_id', $goodsId)->first();
        }
        !$model && $model = new JdGoods();

        return $model;
    }

    public function hasOneGoods()
    {
        return $this->hasOne(\app\common\models\Goods::class, 'id', 'goods_id');
    }

    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(function ($builder) {
            $builder->uniacid();
        });
    }


}