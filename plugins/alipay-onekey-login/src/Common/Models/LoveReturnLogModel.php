<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/8/1
 * Time: 下午12:03
 */

namespace Yunshop\Love\Common\Models;


use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoveReturnLogModel extends BaseModel
{
    use SoftDeletes;
    public $table = 'yz_love_return_log';
    public $timestamps = true;
    protected $guarded = [''];


    public static function getLoveReturnLog($search)
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

        if ($search['is_time']) {
            if ($search['time']) {
                $range = [strtotime($search['time']['start']), strtotime($search['time']['end'])];
                $model->whereBetween('created_at', $range);
            }
        }
        return $model;
    }

    public function hasOneMember()
    {
        return $this->hasOne('app\common\models\Member', 'uid', 'member_id');
    }
}