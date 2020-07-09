<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/7/18
 * Time: 9:42
 */

namespace Yunshop\JdSupply\models;


use app\common\models\BaseModel;

class JdSupplyError extends BaseModel
{
    public $table = 'yz_jd_supply_error';

    protected $guarded = [''];

    protected $attributes = [];

    protected $casts = [
        'error_data' => 'json',
        'request_data' => 'json',
        'response_data' => 'json',
    ];


    /**
     * @param $desc string 描述
     * @param $error string 错误
     * @param $data  mixed 返回数据
     */
    public static function jdError($desc, $request_data, $response_data,$type = 'error')
    {
        static::create([
            'uniacid' => \YunShop::app()->uniacid,
            'desc' => $desc,
            'request_data' => $request_data,
            'response_data' => $response_data,
            'type' => $type
        ]);
    }


    /**
     * @param $desc string 描述
     * @param $error mixed 请求错误
     * @param $data mixed  请求参数
     * @param string $type 类型
     */
    public static function jdRequest($desc, $data, $type = 'request')
    {
        static::create(['uniacid' => \YunShop::app()->uniacid, 'desc' => $desc, 'request_data' => $data, 'type' => $type]);
    }


    /**
     * @param $desc string 描述
     * @param $data mixed 通知数据
     * @param string $type 类型
     */
    public static function jdResponse($desc, $data, $type = 'response')
    {

        static::create(['uniacid' => \YunShop::app()->uniacid, 'desc' => $desc, 'response_data' => $data, 'type' => $type]);
    }
}