<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/6/15
 * Time: 下午4:24
 */

namespace Yunshop\Exhelper\common\models;


use app\common\models\BaseModel;

class SendUser extends BaseModel
{
    public $table = 'yz_exhelper_senduser';
    protected $guarded = [''];
    public $timestamps = false;

    const IS_DEFAULT = 1;
    const NO_DEFAULT = 0;

    public static function getList()
    {
        return self::select()->uniacid();
    }

    public static function getDefault()
    {
        return self::select()->uniacid()->byDefault();
    }

    public function scopeByDefault($query)
    {
        return $query->where('isdefault', 1);
    }

    /**
     * 定义字段名
     *
     * @return array */
    public  function atributeNames() {
        return [
            'sender_name'  => '发件人',
            'sender_tel'  => '联系电话',
            'sender_address'  => '发件地址',
            'sender_code' => '发件地邮编',
            'sender_sign' => '发件人签名',
            'sender_city' => '发件城市'
        ];
    }

    /**
     * 字段规则
     *
     * @return array */
    public  function rules()
    {
        return [
            'sender_name'  => 'required',
            'sender_tel'  => 'required',
            'sender_address'  => 'required',
            'sender_code' => 'required',
            'sender_sign' => 'required',
            'sender_city' => 'required'
        ];
    }
}