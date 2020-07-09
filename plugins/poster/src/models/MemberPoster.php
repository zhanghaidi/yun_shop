<?php

namespace Yunshop\Poster\models;

use app\common\models\BaseModel;
use app\common\models\Member;

class MemberPoster extends BaseModel
{
    public $table = 'yz_member_poster';

    public $guarded = [];

    public function member()
    {
        return $this->belongsTo(Member::class,'uid','uid');
    }
}