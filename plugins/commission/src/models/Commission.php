<?php
/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/3/14
 * Time: 下午5:30
 */

namespace Yunshop\Commission\models;


use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Commission extends BaseModel
{
    use SoftDeletes;
    public $table = 'yz_goods_commission';
    public $timestamps = true;
    protected $guarded = [''];

    public $attributes = [
        'is_commission' => 1,
        'show_commission_button' => 0,
        'poster_picture' => '',
        'has_commission' => 0,
    ];

    public static function relationSave($goodsId, $data, $operate)
    {
        if (!$goodsId) {
            return false;
        }
        if(!$data)
        {
            return false;
        }

        $commissionModel = self::getModel($goodsId, $operate);
        //判断deleted
        if ($operate == 'deleted') {
            return $commissionModel->delete();
        }

        $data['goods_id'] = $goodsId;
        $data['has_commission'] = empty($data['has_commission']) ? 0 : $data['has_commission'];
        $data['rule'] = serialize($data['rule']);

        $commissionModel->setRawAttributes($data);

        return $commissionModel->save();
    }

    public static function getModel($goodsId, $operate)
    {
        $model = false;
        if ($operate != 'created') {
            $model = static::where(['goods_id' => $goodsId])->first();
        }
        !$model && $model = new static;
        return $model;
    }

    public static function getGoodsSet($goodsId)
    {
        $model = $goodsId ? static::where(['goods_id' => $goodsId])->first() : new static;
        !$model && $model = ['is_commission' => 0] ;
        return $model;
    }

    public static function getGoodsById($goodsId)
    {
        return self::where('goods_id', $goodsId);
    }

    public function getModelByGoodsId($goodsId)
    {
        return static::where('goods_id', $goodsId)->first();
    }
}