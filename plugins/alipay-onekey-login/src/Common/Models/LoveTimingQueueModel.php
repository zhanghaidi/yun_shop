<?php
/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/9/21
 * Time: 上午11:50
 */

namespace Yunshop\Love\Common\Models;


use app\common\models\BaseModel;
use app\common\traits\CreateOrderSnTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class LoveTimingQueueModel extends BaseModel
{
    use SoftDeletes, CreateOrderSnTrait;
    public $table = 'yz_love_timing_queue';
    public $timestamps = true;
    protected $guarded = [''];

    public static function getRechargeQueue()
    {
        $model = self::uniacid();


        $model->where(DB::raw('`created_at` + `timing_days` * 86400'), '<=', time());


        $model->where('status', '0');

        return $model;
    }


    /**
     * 定义字段名
     *
     * @return array
     */
    public function atributeNames()
    {
        return [
            'uniacid'       => "公众号ID",
            'member_id'     => "会员ID",
            'change_value'  => '充值数',
            'timing_days'   => '规则天数',
            'timing_rate'   => '规则比例',
            'recharge_sn'   => '充值单号',
        ];
    }

    /**
     * 字段规则
     *
     * @return array
     */
    public function rules()
    {
        return [
            'uniacid'       => "required|integer",
            'member_id'     => "required|integer",
            'change_value'  => 'numeric|regex:/^[\-\+]?\d+(?:\.\d{1,2})?$/|max:9999999999',
            'timing_days'   => 'required|integer',
            'timing_rate'   => 'required|integer',
            'recharge_sn'   => 'required',
        ];
    }

}