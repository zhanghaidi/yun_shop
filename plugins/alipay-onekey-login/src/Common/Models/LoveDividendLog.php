<?php
/**
 * Created by PhpStorm.
 * User: weifeng
 * Date: 2019-05-14
 * Time: 13:52
 */

namespace Yunshop\Love\Common\Models;


use app\common\models\BaseModel;

class LoveDividendLog extends BaseModel
{
    protected $table = 'yz_love_dividend_log';

    protected $guarded = [''];

    public static function getLog($search)
    {
        $model = self::builder()->whereHas('Member');

        if (!empty($search['log_id'])) {
            $model->where('id', $search['log_id']);
        }

        if (!empty($search['member_id'])) {
            $model->whereHas('Member', function ($q) use($search) {
                $q->where('uid', $search['member_id']);
            });
        }

        if (!empty($search['member'])) {
            $model->whereHas('Member', function ($q) use($search){
                $q->searchLike($search['member']);
            });
        }

        if ($search['search_time'] == 1) {
            $model->whereBetween('created_at', [strtotime($search['time']['start']), strtotime($search['time']['end'])]);
        }

        return $model;
    }

    public function builder()
    {
        return self::uniacid()->with('Member');
    }

    public function Member()
    {
        return $this->belongsTo(Member::class,'member_id','uid');
    }
}