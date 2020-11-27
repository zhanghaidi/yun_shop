<?php

namespace app\common\models\live;
;

use Carbon\Carbon;
use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class CloudLiveRoom
 * @package app\common\models\live\CloudLiveRoom
 * @property int id
 * @property int uniacid
 * @property string name
 * @property int roomid
 * @property string cover_img
 * @property int live_status
 * @property Carbon start_time
 * @property Carbon end_time
 * @property string anchor_name
 * @property string share_img
 * @property string push_url
 * @property string pull_url
 */
class CloudLiveRoom extends BaseModel
{

    use SoftDeletes;

    public $table = "yz_cloud_live_room";
    public $timestamps = false;
    public $dates = ['deleted_at'];
    protected $guarded = [''];
    protected $mediaFields = ['cover_img', 'share_img'];
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
            'roomid' => '直播房间ID',
            'cover_img' => '封面图片',
            'live_status' => '直播间状态',
            'start_time' => '开播时间',
            'end_time' => '结束时间',
            'anchor_name' => '主播名称',
            'share_img' => '分享图片',
            'push_url' => '推流URL',
            'pull_url' => '拉流URL',
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
            'name' => 'required',
            'roomid' => 'integer',
            'cover_img' => 'required|string',
            'anchor_name' => 'required|string',
            'share_img' => 'required|string',
            'push_url' => 'string',
            'pull_url' => 'string',
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

        if (!empty($data['time']) && $data['time']['start'] != '请选择' && $data['time']['end'] != '请选择') {
            $data['start_time'] = strtotime($data['time']['start']);
            $data['end_time'] = strtotime($data['time']['end']);
        }

        return array_except($data, ['time']);
    }

    public function getRoomById($id)
    {
        return self::uniacid()->where(id, $id);
    }

    public function parseStatus($status)
    {
        return self::$liveStatus[$status];
    }

}
