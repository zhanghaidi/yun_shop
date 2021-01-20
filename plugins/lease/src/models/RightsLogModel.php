<?php

namespace Yunshop\LeaseToy\models;

use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use app\common\exceptions\AppException;
use Illuminate\Support\Facades\DB;

class RightsLogModel extends BaseModel
{
    use SoftDeletes;

    public $table = 'yz_lease_toy_rights_log';

    protected $guarded = [];
    
    protected $attributes = [
        'sue_rent_free' => 0,
        'sue_deposit_free' => 0,
    ];

    /**
     * 已用权益数
     * @param  [type] $member_id [description]
     * @return [type]            [description]
     */
    static public function getRightsLog($member_id)
    {
        $data = self::uniacid()->where('member_id', $member_id)->first([
            DB::raw('SUM(sue_rent_free) as sue_rent_free'),
            DB::raw('SUM(sue_deposit_free) as sue_deposit_free')
        ])->toArray();

        if ($data['sue_rent_free'] === null && $data['sue_deposit_free'] === null) {
            return false;
        }

        return $data;
    }
}