<?php

namespace Yunshop\ActivityQrcode\models;
use app\common\models\BaseModel;
use app\common\scopes\UniacidScope;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Qrcode extends BaseModel
{

    use SoftDeletes;
    public $table = 'yz_activity_code_qrcode';
    public $timestamps = true;
    protected $guarded = [''];


    public static function boot()
    {
        parent::boot();
        self::addGlobalScope(new UniacidScope());
    }

    /**
     * 字段规则
     * @return array
     */
    public function rules()
    {
        return [
            'uniacid' => 'required|integer',
            'name' => 'required|max:50',
            'qr_img' => 'string',
            'switch_limit' => 'required|integer',
            'sort' => 'required|integer',
            'end_time' => 'required|integer',
        ];
    }

    /**
     * 根据获码id获取活码详情
     * @param $id
     * @return mixed
     */
    public static function getInfo($id)
    {
        return self::withCount([
            'hasManyUser',
            /*'hasManyQrcode as timeout' => function($qrcode){
                return $qrcode->where('end_time', '<', time());
            }*/])
            ->with([
                'belongsToActivity ' => function($activity){
                    return $activity->select('id','title');
                }])
            ->where('id', $id)
            ->first();
    }

    //删除二维码
    public static function deletedQrcode($id)
    {
        return self::where('id', $id)->delete();
    }

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