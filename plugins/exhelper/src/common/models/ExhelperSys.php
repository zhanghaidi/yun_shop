<?php

/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/6/14
 * Time: 上午11:40
 */

namespace Yunshop\Exhelper\common\models;

use app\common\models\BaseModel;

class ExhelperSys extends BaseModel
{
    public $table = 'yz_exhelper_sys';
    protected $guarded = [''];
    public $timestamps = false;

    public static function getOnlyOne()
    {
        return self::select()->uniacid();
    }

    /**
     * 字段规则
     *
     * @return array */
    public  function rules()
    {
        return [
            'ip'  => 'required',
            'port'  => 'required',
            'name' => 'required',
            'apikey' => '',
            'merchant_id' => ''
        ];
    }

    /**
     * 定义字段名
     *
     * @return array */
    public  function atributeNames() {
        return [
            'ip'  => 'IP',
            'port'  => '微信号',
            'name' => '打印机名称',
            'apikey' => '快递鸟 apikey',
            'merchant_id' => '快递鸟商户id'
        ];
    }
}