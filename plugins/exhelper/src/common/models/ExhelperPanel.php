<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2019/1/7
 * Time: 17:13
 */

namespace Yunshop\Exhelper\common\models;


use app\common\models\BaseModel;

class ExhelperPanel extends BaseModel
{
    public $table = 'yz_exhelper_panel';
    public $timestamps = true;
    public $guarded = [''];

    public function scopeByOrderSn($query, $order_sn)
    {
        return $query->where('order_sn', $order_sn);
    }

    public static function getDefault()
    {
        return self::select()->uniacid()->where('isdefault', 1);
    }

    public function rules()
    {
        return [
            'panel_name'  => 'required',
            'panel_no'  => '',
            'panel_pass'  => '',
            'panel_sign' => '',
            'panel_code' => '',
            'panel_style' => '',
            'exhelper_style' => 'required',
            'isself' => '',
            'isdefault' => '',
            'begin_time' => '',
            'end_time' => ''

        ];
    }

    public function atributeNames()
    {
        return [
            'panel_name' => '电子面单名称',
            'panel_no' => '电子面单客户账号',
            'panel_pass' => '电子面单密码',
            'exhelper_style' => '快递类型',
            'panel_style' => '模板样式',
            'panel_sign' => '月结编码',
            'isdefault' => '是否为默认模板',
            'isself' => '是否通知快递员上门揽件',
            'begin_time' => '快递员上门揽件开始时间',
            'end_time' => '快递员上门揽件结束时间'
        ];
    }
}