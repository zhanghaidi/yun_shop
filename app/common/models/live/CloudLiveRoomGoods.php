<?php

namespace app\common\models\live;

use Carbon\Carbon;
use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use app\common\models\Goods;

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
class CloudLiveRoomGoods extends BaseModel
{

    use SoftDeletes;

    public $table = "yz_cloud_live_room_goods";

    protected $guarded = [''];

    /**
     * 字段规则
     * @return array
     */
    public function rules()
    {
        return [
            'uniacid' => 'required|integer',
            'title' => 'required|string',
            'description' => 'string',
            'thumb' => 'required|string',
            'price' => 'required',
            'pagepath' => 'required',
        ];
    }

}
