<?php

namespace Yunshop\Diyform\models;

use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/9/23
 * Time: 上午9:59
 */
class DiyformDataModel extends BaseModel
{
    use SoftDeletes;
    public $table = 'yz_diyform_data';
    public $timestamps = true;
    protected $guarded = [''];

    public $formData;
    protected $appends = ['form_data'];

    public static function getDiyFormDataByFormId($formId = '', $formDataId = '', $memberId = '', $formType = '')
    {
        $model = self::uniacid();

        if ($formId) {
            $model->where('form_id', $formId);
        }
        if ($formDataId) {
            $model->where('id', $formDataId);
        }
        if ($memberId) {
            $model->where('member_id', $memberId);
        }
        if ($formType) {
            $model->where('form_type', $formType);
        }

        return $model;
    }

    public function member()
    {
        return $this->hasOne('app\common\models\Member','uid','member_id');
    }


    public function getFormDataAttribute()
    {
        if (!isset($this->formData)) {
            $this->formData = iunserializer($this->data);
        }
        return $this->formData;
    }
    public function diyformType(){
        return $this->belongsTo(DiyformTypeModel::class,'form_id');
    }
}