<?php

namespace Yunshop\MinappContent\models;

use app\common\models\BaseModel;

/**
 * Class ComplainModel
 * @package Yunshop\MinappContent\models
 * 用户投诉模型
 */
class ComplainModel extends BaseModel
{
    public $table = 'diagnostic_service_complain';
    public $timestamps = false;
    protected $casts = ['create_time' => 'date'];
    protected $guarded = [];

    //关联用户
    public function user()
    {

        return $this->belongsTo('App\backend\modules\tracking\models\DiagnosticServiceUser', 'user_id', 'ajy_uid');
    }

    //关联投诉类型
    public function type()
    {
        return $this->belongsTo('Yunshop\MinappContent\models\ComplainTypeModel', 'type', 'id');
    }

    //读取数组格式图片
    public function getImagesAttribute($value)
    {
        return json_decode($value, true);
    }


    /**
     * 取得埋点对应的来源对象。
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function info()
    {
        return $this->morphTo('resource','to_type_id','info_id');
    }

    /**
     *  获取埋点来源类型.
     *
     * @param  string  $value
     * @return string
     */
    public function getToTypeIdAttribute($value)
    {
        $map = [
            1 => 'App\backend\modules\tracking\models\DiagnosticServiceAcupoint', //穴位
            3 => 'App\backend\modules\tracking\models\DiagnosticServiceArticle', //文章
            4 => 'App\backend\modules\tracking\models\DiagnosticServicePost',  //社区
        ];
        return $map[$value];
    }

    public function setTypeIdAttribute()
    {
        return $this->attributes['to_type_id'];
    }

    public function setInfoAttribute()
    {
        return $this->attributes['info_id'];
    }

    /*public function setFirstNameAttribute($value)
    {
        return $this->attributes['first_name'] ;
    }*/

}
