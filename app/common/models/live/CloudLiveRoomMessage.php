<?php

namespace app\common\models\live;

use Carbon\Carbon;
use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;


class CloudLiveRoomMessage extends BaseModel
{
    use SoftDeletes;

    public $table = 'yz_cloud_live_room_message';

    protected $guarded = [''];

}
