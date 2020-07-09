<?php
/**
 * Created by PhpStorm.
 * User: king
 * Date: 2017/9/21
 * Time: 上午11:50
 */

namespace Yunshop\Love\Common\Models;


use app\common\models\BaseModel;
use app\common\traits\CreateOrderSnTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimingLogModel extends BaseModel
{
    use SoftDeletes,CreateOrderSnTrait;

    public $table = 'yz_love_timing_log';

    protected $guarded = [''];

    public $recharge;

    public $noRecharge;

    protected $appends = ['recharge', 'no_recharge'];

    public function getDates()
    {
        return ['created_at'];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function hasOneMember()
    {
        return $this->hasOne('app\common\models\Member', 'uid', 'member_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function yzMember()
    {
        return $this->hasOne('Yunshop\Love\Common\Models\YzMemberModel', 'member_id', 'member_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function hasManyQueue()
    {
        return $this->hasMany('Yunshop\Love\Common\Models\LoveTimingQueueModel', 'recharge_sn', 'recharge_sn');
    }



    public static function getTimingLog($search)
    {
        $model = self::uniacid();

        if (!empty($search['member'])) {
            $model->whereHas('hasOneMember', function ($query) use ($search) {
                return $query->searchLike($search['member']);
            });
        }
        $model->with('hasOneMember');

        if ($search['member_level'] || $search['member_group']) {
            $model->whereHas('yzMember', function ($yzMember) use ($search) {

                if ($search['member_level']) {
                    $yzMember->where('level_id', $search['member_level']);

                }
                if ($search['member_group']) {
                    $yzMember->where('group_id', $search['member_group']);
                }
            });
        }
        if ($search['is_time'] == 1) {
            $model->where('created_at', '>', strtotime($search['time']['start']))->where('created_at', '<', strtotime($search['time']['end']));
        }
        $model->with('yzMember');

        return $model;
    }


    public function getRechargeAttribute()
    {
        if (!isset($this->recharge)) {
            $amount = 0;
            $queues = LoveTimingQueueModel::where('recharge_sn',$this->recharge_sn)->where('status',1)->get();
            foreach ($queues as $queue) {
                $amount += $queue->change_value / 100 * $queue->timing_rate;
            }
        }
        $this->recharge['amount'] = $amount;
        $this->recharge['num'] = count($queues);
        return $this->recharge;
    }

    public function getNoRechargeAttribute()
    {
        if (!isset($this->noRecharge)) {
            $amount = 0;
            $queues = LoveTimingQueueModel::where('recharge_sn',$this->recharge_sn)->where('status',0)->get();
            foreach ($queues as $queue) {
                $amount += $queue->change_value / 100 * $queue->timing_rate;
            }
        }
        $this->noRecharge['amount'] = $amount;
        $this->noRecharge['num'] = count($queues);
        return $this->noRecharge;
    }




}