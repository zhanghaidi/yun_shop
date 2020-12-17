<?php

namespace app\common\models\live;

use Carbon\Carbon;
use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use app\common\models\Goods;
use app\common\models\live\CloudLiveRoomLike;
use app\common\models\live\CloudLiveRoomSubscription;
use app\backend\modules\tracking\models\DiagnosticServiceUser;

/**
 * Class CloudLiveRoom
 * @package app\common\models\live\CloudLiveRoom
 * @property int id
 * @property int uniacid
 * @property string name
 * @property string stream_name
 * @property string cover_img
 * @property int live_status
 * @property Carbon start_time
 * @property Carbon end_time
 * @property string anchor_name
 * @property string header_img
 * @property string goods_ids
 * @property string share_title
 * @property string share_img
 * @property string push_url
 * @property string pull_url
 * @property string group_id
 * @property string group_name
 * @property int sort
 */
class CloudLiveRoom extends BaseModel
{

    use SoftDeletes;

    public $table = "yz_cloud_live_room";
    public $timestamps = false;
    public $dates = ['deleted_at'];
    protected $guarded = [''];
    protected $mediaFields = ['cover_img', 'share_img', 'header_img'];
    protected $casts = ['start_time' => 'date', 'end_time' => 'date', 'updated_at' => 'date', 'created_at' => 'date'];
    protected $appends = ['status_parse'];

    protected static $liveStatus = [0 => '关闭', 101 => '直播中', 102 => '未开始', 103 => '已结束', 104 => '禁播', 105 => '暂停', 106 => '异常', 107 => '已过期'];

    //默认值
    public $attributes = [
        'live_status' => 0,
    ];

    /**
     * 自定义字段名
     * 可使用
     * @return array
     */
    public function atributeNames()
    {
        return [
            'id' => 'ID',
            'uniacid' => '公众号 ID',
            'name' => '直播间名称',
            'stream_name' => '直播流名称',
            'cover_img' => '封面图片',
            'live_status' => '直播间状态',
            'start_time' => '开播时间',
            'end_time' => '结束时间',
            'anchor_name' => '主播名称',
            'header_img' => '主播头像',
            'goods_ids' => '关联商品',
            'share_title' => '分享标题',
            'share_img' => '分享图片',
            'push_url' => '推流URL',
            'pull_url' => '拉流URL',
            'group_id' => '直播群id',
            'group_name' => '直播群名称',
            'sort' => '排序',
            'virtual_people' => '虚拟人数',
            'virtual_num' => '虚拟倍数',
            'created_at' => '创建时间',
        ];
    }

    /**
     * 字段规则
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'integer',
            'uniacid' => 'required|integer',
            'name' => 'required|string',
            'stream_name' => 'string',
            'cover_img' => 'required|string',
            'anchor_name' => 'required|string',
            'header_img' => 'required|string',
            'goods_ids' => 'string',
            'share_title' => 'required|string',
            'share_img' => 'required|string',
            'push_url' => 'string',
            'pull_url' => 'string',
            'sort' => 'integer',
            'virtual_people' => 'integer',
            'virtual_num' => 'integer',
            'start_time' => 'required|integer',
            'end_time' => 'required|integer',
        ];
    }

    public function getStatusParseAttribute()
    {
        return $this->parseStatus($this->live_status);
    }

    public function scopeSearch(Builder $query, $search)
    {
        $model = $query->where('uniacid', \YunShop::app()->uniacid);

        if (!empty($search['name'])) {
            $query->where('name', 'like', '%' . $search['name'] . '%');
        }

        if (!empty($search['id'])) {
            $query->where('id', '=', $search['id']);
        }

        if (!empty($search['anchor_name'])) {
            $query->where('anchor_name', 'like', '%' . $search['anchor_name'] . '%');
        }
        return $model;
    }

    public static function handleArray($data, $id)
    {
        $data['uniacid'] = \YunShop::app()->uniacid;

        if ($id) {
            $data['id'] = $id;
            $data['updated_at'] = time();
        } else {
            $data['created_at'] = time();
        }

        if($data['goods_ids']){
            $data['goods_ids'] = implode(',',$data['goods_ids']);
        }else{
            $data['goods_ids'] = '';
        }

        if (!empty($data['time']) && $data['time']['start'] != '请选择' && $data['time']['end'] != '请选择') {
            $data['start_time'] = strtotime($data['time']['start']);
            $data['end_time'] = strtotime($data['time']['end']);
        }

        return array_except($data, ['time','goods_names']);
    }

    public function goods($need_all = true){
        if(!empty($this->goods_ids)){
            if($need_all){
                return Goods::uniacid()->whereIn('id',explode(',',$this->goods_ids))->get()->toArray();
            }else{
                return Goods::uniacid()->whereIn('id',explode(',',$this->goods_ids))->where('status',1)->orderby('display_order','desc')->get()->toArray();
            }
        }
        return [];
    }

    public function getRoomById($id)
    {
        return self::uniacid()->where(id, $id);
    }

    public function parseStatus($status)
    {
        return self::$liveStatus[$status];
    }

    //关联点赞
    public function hasManyLike()
    {

        return $this->hasMany('app\common\models\live\CloudLiveRoomLike', 'room_id', 'id');
    }

    //关联订阅
    public function hasManySubscription()
    {
        return $this->hasMany('app\common\models\live\CloudLiveRoomSubscription', 'room_id', 'id');
    }

    //获取用户是否订阅
    public function isSubscription()
    {
        return $this->hasOne('app\common\models\live\CloudLiveRoomSubscription', 'room_id', 'id');
    }


}
