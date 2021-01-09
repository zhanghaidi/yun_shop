<?php

namespace Yunshop\ActivityQrcode\models;
use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Qrcode extends BaseModel
{

    use SoftDeletes;
    public $table = 'yz_activity_code_qrcode';
    public $timestamps = true;
    protected $guarded = [''];



    //活码关联二维码
    public function belongsToActivity()
    {
        return $this->belongsTo('Yunshop\ActivityQrcode\models\Activity', 'code_id', 'id');
    }

    //活码关联扫描用户记录
    public function hasManyUser()
    {
        return $this->hasMany('Yunshop\ActivityQrcode\models\ActivityUser', 'qrcode_id', 'id');
    }



}