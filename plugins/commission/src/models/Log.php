<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/9/18
 * Time: 上午11:13
 */

namespace Yunshop\Commission\models;


use app\common\models\BaseModel;

class Log extends BaseModel
{
    public $table = 'yz_commission_log';
    public $timestamps = true;
    protected $guarded = [''];

    public static function addLog($before_level_id, $after_level_id, $agent, $remark)
    {
        if (!$agent) {
            return;
        }
        if ($after_level_id != $before_level_id) {
            $time = date('Y-m-d H:i:s', time());
            $log = new self();
            $log->fill([
                'uniacid' => $agent['uniacid'],
                'agent_id' => $agent['id'],
                'uid' => $agent['member_id'],
                'before_level_id' => $before_level_id,
                'after_level_id' => $after_level_id,
                'remark' => $remark,
                'time' => $time
            ]);
            $log->save();
        }
    }
}