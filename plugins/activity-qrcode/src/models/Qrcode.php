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
    protected $appends=['status'];

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
            'qr_img' => 'required|string',
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
        return self::withCount('hasManyUser')
            ->where('id', $id)
            ->first();
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeRecords($query)
    {
        return $query->withCount('hasManyUser')
            ->with([
                'hasManyUser'
            ]);
    }

    public function getQrImgAttribute($value){
        return yz_tomedia($value);
    }

    //搜索条件
    public function scopeSearch($query, array $search)
    {
        //根据用户筛选
        if ($search['name']) {
            $query = $query->where('title', 'like', '%' . $search['name'] . '%')->orWhere('activity_name', 'like', '%' . $search['name'] . '%');
        }
        //根据商品筛选
        if ($search['type']) {
            $query = $query->where('type', $search['type']);
        }
        //根据时间筛选
        if ($search['search_time'] == 1) {
            $query = $query->whereBetween('created_at', [strtotime($search['time']['start']),strtotime($search['time']['end'])]);
        }
        return $query;
    }

    //
    public function getStatusAttribute()
    {
        if($this->attributes['end_time'] < time() ){
            $status = -1;
        }else{
            if($this->attributes['is_full'] == 1){
                $status = 2;
            }else{
                $status = 1;
            }
        }
        return $status;
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