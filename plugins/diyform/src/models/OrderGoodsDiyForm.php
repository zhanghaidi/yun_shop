<?php


namespace Yunshop\Diyform\models;


use app\common\models\BaseModel;

/**
 * Class OrderGoodsDiyForm
 * @package Yunshop\Diyform\models
 * @property int form_id
 * @property string data
 */
class OrderGoodsDiyForm extends BaseModel
{
    protected $table = 'yz_order_goods_diy_form';
    protected $guarded = ['id'];

    public function diyformData()
    {
        return $this->belongsTo(DiyformDataModel::class, 'diyform_data_id');
    }
}