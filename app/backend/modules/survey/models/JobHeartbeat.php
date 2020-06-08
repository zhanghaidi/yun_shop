<?php
/**
 * Created by PhpStorm.
 * User: blank
 * Date: 2020/3/27
 * Time: 15:23
 */

namespace app\backend\modules\survey\models;


use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobHeartbeat extends BaseModel
{
    //use SoftDeletes;

    protected $table = 'yz_job_heartbeat';


    public $timestamps = false;

    //protected $dates = ['execution_time'];

    /**
     *  获取队列情况
     * @param $current_time
     * @return array
     */
    public static function getLog($current_time)
    {
        $model = self::select('execution_time')->orderBy('id', 'desc')->first();


        return [
            'queue_status' => self::queueStatus($current_time, $model->execution_time),
            'is_repeat' => self::verifyRepeat($current_time),
        ];
    }

    /**
     * @param $current_time 当前时间
     * @param $execution_time 执行时间
     * @return string 状态
     */
    public static function queueStatus($current_time, $execution_time)
    {
        if (empty($execution_time)) {
            return 'not_open';
        }

        switch ($execution_time) {
            //2分钟以内就是绿灯
            case $current_time < ($execution_time + 120):
                $status = 'green';
                break;
            //4分钟以内就是黄灯
            case $current_time < ($execution_time + 240):
                $status = 'yellow';
                break;
            //4分钟以上就是红灯
            default:
                $status = 'red';

        }
        return $status;
    }

    /**
     * @param int $current_time 指定时间
     * @param int $s 距离指定时间多少秒内
     * @return int
     */
    public static function verifyRepeat($current_time, $s = 60)
    {
        return self::whereBetween('execution_time',[$current_time - $s,$current_time])->count();
    }


}