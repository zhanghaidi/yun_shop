<?php

namespace Yunshop\ActivityQrcode\models;
use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use app\common\scopes\UniacidScope;
use Carbon\Carbon;

class ActivityUser extends BaseModel
{

    use SoftDeletes;
    public $table = 'yz_activity_code_user';
    public $timestamps = true;
    protected $guarded = [''];

    public static function boot()
    {
        parent::boot();
        self::addGlobalScope(new UniacidScope());
    }

    //关联活码
    public function belongsToActivity()
    {
        return $this->belongsTo('Yunshop\ActivityQrcode\models\Activity', 'code_id', 'id');
    }

    //关联二维码
    public function belongsToQrcode()
    {
        return $this->belongsTo('Yunshop\ActivityQrcode\models\Qrcode', 'qrcode_id', 'id');
    }



}