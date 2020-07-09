<?php

namespace Yunshop\Diyform\models;

use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Author: 芸众商城 www.yunzshop.com
 * 会员可通过二维码或链接直接填写表单数据，这时候需要表单，表单数据，和会员三者关联
 * 一张表单，每个会员只能填写一次
 * 一张表单，多个会员可填写
 * 该模型用于表单与会员和表单数据关联
 */
class DiyformTypeMemberDataModel extends BaseModel
{
    use SoftDeletes;
    public $table = 'yz_diyform_type_member_data';
    public $timestamps = true;
    protected $guarded = [''];

    public function hasOneDiyformData()
    {
        return $this->hasOne('Yunshop\Diyform\models\DiyformDataModel', 'id', 'form_data_id');
    }
}