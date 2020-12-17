<?php

namespace app\common\models\live;


use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class CloudLiveRoomSubscription extends BaseModel
{
    use SoftDeletes;

    public $table = 'yz_cloud_live_room_subscription';

    public $timestamps = false;

    protected $guarded = [''];


}