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
            'logo' => 'required|string',
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
                'hasManyUser',
                'hasManyQrcode',
                'hasManyQrcode as timeout' => function($qrcode){
                    return $qrcode->where('end_time', '<', time());
                },
                'hasManyQrcode as full' => function($qrcode){
                    return $qrcode->where('is_full', 1);
                }
            ])
            ->with([
                'hasQrcode' => function($qr){
                    return $qr->select('id','sort','code_id','qr_img','qr_path','end_time','switch_limit','is_full')->orderBy('sort')->first();
                }
            ])
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
            'hasManyUser',
            'hasManyUser as todayUser' => function($user){
                return $user->whereBetween('created_at', [Carbon::now()->startOfDay()->timestamp, Carbon::now()->endOfDay()->timestamp]);
            },
            'hasManyQrcode',
            'hasManyQrcode as timeout' => function($qrcode){
                return $qrcode->where('end_time', '<', time());
            },
            'hasManyQrcode as full' => function($qrcode){
                return $qrcode->where('is_full', 1);
            }
            ])
            ->with([
                'hasQrcode' => function($qr){
                    return $qr->select('id','sort','code_id','qr_img','qr_path','end_time','switch_limit','is_full')->orderBy('sort')->first();
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

    public function hasQrcode()
    {
        return $this->hasMany('Yunshop\ActivityQrcode\models\Qrcode', 'code_id', 'id')->where('is_full', 0)->where('end_time' ,'>', time());
    }



}