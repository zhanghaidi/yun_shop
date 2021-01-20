<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/10
 * Time: 下午6:46
 */

namespace Yunshop\Micro\common\models;


use app\backend\modules\member\models\Member;
use app\common\models\BaseModel;
use app\common\models\Income;

class MicroShopBonusLog extends BaseModel
{
    protected $table = 'yz_micro_shop_bonus_log';
    protected $guarded = [''];
    protected $appends = ['mode_type', 'status_name', 'order_status_name'];
    protected $casts = [
        'pay_time' => 'datetime',
        'complete_time' => 'datetime'
    ];
    protected $dates = ['pay_time', 'complete_time'];
    const IS_LOWER = 1;
    const TODAY = 1;
    const YESTERDAY = 2;
    const WEEK = 3;
    const MONTH = 4;
    const APPLY_STATUS_TRUE = 1;
    private $start_time;
    private $end_time;

    public static function getBonusLogList($params, $apply_status = null)
    {
        return self::builder()->search($params)->applyStatus($apply_status);
    }

    public static function getBonusLogByLogId($log_id)
    {
        return self::builder()->byLogId($log_id)->first();
    }

    public static function getBonusLogByMemberId($member_id)
    {
        return self::builder()->byMemberId($member_id);
    }


    public static function builder()
    {
        return self::with([
            'hasOneMicroShop',
            'hasOneMember',
            'hasOneMicroShopLevel'
        ]);
    }

    public function hasOneMicroShop()
    {
        return $this->hasOne(MicroShop::class, 'id', 'shop_id');
    }

    public function hasOneMember()
    {
        return $this->hasOne(Member::class, 'uid', 'member_id');
    }

    public function hasOneMicroShopLevel()
    {
        return $this->hasOne(MicroShopLevel::class, 'id', 'level_id');
    }

    public function getModeTypeAttribute()
    {
        $mode_type = '';
        if ($this->is_lower == 0) {
            $mode_type = trans('Yunshop\Micro::pack.micro_bonus');
        } else if ($this->is_lower == 1) {
            $mode_type = trans('Yunshop\Micro::pack.lower_micro_bonus');
        }
        return $mode_type;
    }

    public function getStatusNameAttribute()
    {
        $status_name = '';
        if ($this->apply_status == 0) {
            $status_name = '未结算';
        } else if ($this->apply_status == 1) {
            $status_name = '已结算';
        } else if ($this->apply_status == -1) {
            $status_name = '已失效';
        }
        return $status_name;
    }

    public function getOrderStatusNameAttribute()
    {
        $status_name = '';
        if ($this->order_status == 1) {
            $status_name = '已付款';
        } else if ($this->order_status == 3) {
            $status_name = '已完成';
        } else if ($this->order_status == 0) {
            $status_name = '待付款';
        }
        return $status_name;
    }

    public function scopeByLogId($query, $log_id)
    {
        return $query->where('id', $log_id);
    }

    public function scopeApplyStatus($query, $status)
    {
        if (!isset($status) || !in_array($status, ['0', '1', '-1'])) {
            return $query;
        }
        return $query->where('apply_status', $status);
    }

    public function scopeIsLower($query, $type)
    {
        return $query->where('is_lower', $type);
    }

    public function scopeByMemberId($query, $member_id)
    {
        return $query->where('member_id', $member_id);
    }

    public function scopeByShopId($query, $shop_id)
    {
        return $query->where('shop_id', $shop_id);
    }

    public function scopeByOrderStatus($query, $status = null)
    {
        if (!isset($status)) {
            return $query;
        }
        return $query->where('order_status', $status);
    }

    public function scopeByOrderId($query, $order_id)
    {
        return $query->where('order_id', $order_id);
    }

    public function incomes()
    {
        return $this->morphMany(Income::class, 'incometable');
    }

    /**
     * @name 时间区间查询
     * @author 杨洋
     * @param $query
     * @param $type
     * @return mixed
     */
    public function scopeByTime($query, $type)
    {
        if ($type == -1) {
            return $query;
        }
        if ($type == self::TODAY) {
            $this->start_time = mktime(0,0,0,date('m'),date('d'),date('Y'));
            $this->end_time = mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
        } else if ($type == self::YESTERDAY) {
            $this->start_time = mktime(0,0,0,date('m'),date('d')-1,date('Y'));
            $this->end_time = mktime(0,0,0,date('m'),date('d'),date('Y'))-1;
        } else if ($type == self::WEEK) {
            $this->start_time = mktime(0,0,0,date("m"),date("d")-date("w")+1,date("Y"));
            $this->end_time = mktime(23,59,59,date("m"),date("d")-date("w")+7,date("Y"));
        } else if ($type == self::MONTH) {
            $this->start_time = mktime(0,0,0,date('m'),1,date('Y'));
            $this->end_time = mktime(23,59,59,date('m'),date('t'),date('Y'));
        }
        return $query->whereBetween('created_at', [$this->start_time, $this->end_time]);
    }

    public function scopeSearch($query, $params)
    {
        //echo '<pre>';print_r($params);exit;
        $query->uniacid();
        if (!$params) {
            return $query;
        }
        if ($params['shop_name']) {
            $query->whereHas('hasOneMicroShop', function($shop)use($params) {
                $shop = $shop->select()
                    ->where('shop_name', 'like', '%' . $params['shop_name'] . '%');
            });
        }
        if ($params['level_id']) {
            $query->whereHas('hasOneMicroShopLevel', function($level)use($params) {
                $level = $level->select()
                    ->where('level_id', $params['level_id']);
            });
        }
        if ($params['member']) {
            $query->whereHas('hasOneMember', function($member)use($params) {
                $member = $member->select('uid', 'nickname','realname','mobile','avatar')
                    ->where('realname', 'like', '%' . $params['member'] . '%')
                    ->orWhere('mobile', 'like', '%' . $params['member'] . '%')
                    ->orWhere('nickname', 'like', '%' . $params['member'] . '%');
            });
        }
        if ($params['order_sn']) {
            $query->where('order_sn', $params['order_sn']);
        }

        if ($params['is_lower'] == 0 || $params['is_lower'] == 1) {
            $query->where('is_lower', $params['is_lower']);
        }

        if (isset($params['apply_status'])) {
            $query->applyStatus($params['apply_status']);
        }
        return $query;
    }
}