<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/7/26
 * Time: 上午9:53
 */

namespace Yunshop\Love\Common\Models;


use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Yunshop\Love\Common\Services\LoveTradingService;

class LoveTradingModel extends BaseModel
{

    use SoftDeletes;
    public $table = 'yz_love_trading';

    public $StatusService;
    public $TypeService;
    protected $appends = ['status_name', 'type_name'];

    public function getStatusNameAttribute()
    {
        if (!isset($this->StatusService)) {
            $this->StatusService = LoveTradingService::getStatusName($this->status);
        }
        return $this->StatusService;
    }

    public function getTypeNameAttribute()
    {
        if (!isset($this->TypeService)) {
            $this->TypeService = LoveTradingService::getTypeName($this);
        }
        return $this->TypeService;
    }

    public static function getLoveTradingLog($search)
    {
        $model = self::uniacid();
        if (!empty($search['log_id'])) {
            $model->where('id', $search['log_id']);
        }

        if (!empty($search['member_id'])) {
            $model->whereHas('hasOneMember', function ($query) use ($search) {
                return $query->searchLike($search['member_id']);
            });
        }
        $model->with('hasOneMember');

        if (!empty($search['buy_id'])) {
            $model->whereHas('hasOneMemberByBuy', function ($query) use ($search) {
                return $query->searchLike($search['buy_id']);
            });
        }
        $model->with('hasOneMemberByBuy');

        if ($search['is_time']) {
            if ($search['time']) {
                $range = [strtotime($search['time']['start']), strtotime($search['time']['end'])];
                $model->whereBetween('created_at', $range);
            }
        }
        return $model;
    }


    public static function getRecyclLove($recycl)
    {
        $model = self::uniacid();
        $model->where('status', 0);

        $model->where(DB::raw('`created_at` + (' . $recycl . ' * 3600)'), '<=', time());

        return $model;
    }


    public static function updatedRecycl($data, $condition)
    {
        return self::where($condition)
            ->update($data);
    }


    public function hasOneMember()
    {
        return $this->hasOne('app\common\models\Member', 'uid', 'member_id');
    }

    public function hasOneMemberByBuy()
    {
        return $this->hasOne('app\common\models\Member', 'uid', 'buy_id');
    }

}