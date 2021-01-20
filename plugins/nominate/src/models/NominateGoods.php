<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019/1/18
 * Time: 2:29 PM
 */

namespace Yunshop\Nominate\models;


use app\common\models\BaseModel;

class NominateGoods extends BaseModel
{
    public $table = 'yz_nominate_goods';
    public $timestamps = true;
    protected $guarded = [''];

    public static function relationSave($goodsId, $data, $operate)
    {
        if (!$goodsId || !$data) {
            return;
        }
        $model = self::select()->where('goods_id', $goodsId)->first();
        if (!$model) {
            $data['goods_id'] = $goodsId;
            self::create($data);
        } else {
            $model->fill($data);
            $model->save();
        }
    }
}