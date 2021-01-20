<?php

namespace Yunshop\LeaseToy\models;

use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use app\backend\modules\member\models\Member;
use Yunshop\LeaseToy\models\DepositRecordModel;

/**
* Author: 芸众商城 www.yunzshop.com
* Date: 2018/3/2
* Time: 15:22
*/
class LeaseMemberModel extends BaseModel
{
    use SoftDeletes;

    public $table = 'yz_lease_toy_member';

    protected $guarded = [''];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public $attributes = [];

    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
    }


    public static function searchRecord($search)
    {
        $model = self::uniacid();

        if (!empty($search['realname']) || !empty($search['level'])) {
            $model->whereHas('belongsToMember', function ($query) use($search) {
                if (!empty($search['level'])) {
                    $query->whereHas('yzMember', function ($query2) use ($search) {
                        $query2->where('level_id', $search['level']);
                    });
                }
                if (!empty($search['realname'])) {
                    $query->where('nickname', 'like', '%' . $search['realname'] . '%')
                    ->orWhere('mobile', 'like', $search['realname'] . '%')
                    ->orWhere('realname', 'like', '%' . $search['realname'] . '%');
                }
            });
        }

        if (!empty($search['min_deposit'])) {
            $model->where('total_deposit', '>', $search['min_deposit']);
        }

        if (!empty($search['max_deposit'])) {
            $model->where('total_deposit', '<', $search['max_deposit']);

        }

        $model->with(['belongsToMember' => function ($query) use ($search) {
            $member = $query->select('uid', 'nickname', 'avatar', 'realname', 'mobile')
            ->with(['yzMember' => function ($query3) {
                return $query3->select('member_id','level_id');
            }]);

            return $member;
        }]);

        //$model->get();
        //dd(DB::getQueryLog());

        return $model;
    }


    public function belongsToMember()
    {
        return $this->belongsTo('app\backend\modules\member\models\Member', 'member_id', 'uid');
    }

    public function hasManyRecord()
    {
        return $this->hasMany('Yunshop\LeaseToy\models\DepositRecordModel', 'lease_member_id', 'member_id');
    }
}
