<?php

namespace app\common\models\live;

use Carbon\Carbon;
use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;


class CloudLiveRoomLike extends BaseModel
{
    use SoftDeletes;

    public $table = 'yz_cloud_live_room_like';

    protected $guarded = [''];

}
