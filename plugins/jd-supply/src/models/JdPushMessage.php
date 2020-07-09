<?php


namespace Yunshop\JdSupply\models;


use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class JdPushMessage extends BaseModel
{
    use SoftDeletes;
    public $table = 'yz_jd_supply_push_message';
    protected $guarded = [''];

    public function scopeSearch($query,$search)
    {
        if ($search['type']) {
            $query->where('type',$search['type']);
        }
        if ($search['search_time']) {
            $query->whereBetween('created_at', [strtotime($search['time']['start']), strtotime($search['time']['end'])]);
        }
        return $query;
    }
}