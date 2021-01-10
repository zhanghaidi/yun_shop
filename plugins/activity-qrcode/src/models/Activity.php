<?php

namespace Yunshop\ActivityQrcode\models;
use app\common\models\BaseModel;
use app\common\scopes\UniacidScope;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Activity extends BaseModel
{

    use SoftDeletes;
    public $table = 'yz_activity_code';
    public $timestamps = true;
    protected $guarded = [''];

    public static function boot()
    {
        parent::boot();
        self::addGlobalScope(new UniacidScope());
    }

    //默认值
    public $attributes = [
    ];

    /**
     * 字段规则
     * @return array
     */
    public function rules()
    {
        return [
            'uniacid' => 'required|integer',
            'activity_name' => 'required|max:50',
            'title' => 'required|max:50',
            'description_top' => 'required|max:255',
            'description_bottom' => 'required|max:255',
            'share_title' => 'max:255',
            'share_description' => 'max:255',
            'share_img' => 'string',
            'share_domain' => 'required',
            'status' => 'required|digits_between:0,1',
            'switch_type' => 'required|digits_between:0,1',
            //'type' => 'required|digits_between:0,1',
        ];
    }

    /**
     * 根据获码id获取活码详情
     * @param $id
     * @return mixed
     */
    public static function getActivity($id)
    {
        return self::withCount([
                'hasManyQrcode',
                'hasManyQrcode as timeout' => function($qrcode){
                    return $qrcode->where('end_time', '<', time());
                }
            ])
            /*->with([
                'hasManyQrcode ' => function($qrcode){
                    return $qrcode->with(['hasManyUser']);
                }])*/
            ->where('id', $id)
            ->first();
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeRecords($query)
    {
        return $query->withCount([
            'hasManyQrcode',
            'hasManyQrcode as timeout' => function($qrcode){
                return $qrcode->where('end_time', '<', time());
            }])
            ->with([
                'hasManyQrcode' => function($qrcode){
                    return $qrcode->with(['hasManyUser']);
                }]);
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

    public static function deletedActivity($id)
    {
        return self::where('id', $id)->delete();
    }

    //活码关联二维码
    public function hasManyQrcode()
    {
        return $this->hasMany('Yunshop\ActivityQrcode\models\Qrcode', 'code_id', 'id');
    }

    //活码关联扫描用户记录
    public function hasManyUser()
    {
        return $this->hasMany('Yunshop\ActivityQrcode\models\ActivityUser', 'code_id', 'id');
    }



}