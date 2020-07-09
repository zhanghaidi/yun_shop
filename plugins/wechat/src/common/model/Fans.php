<?php

namespace Yunshop\Wechat\common\model;

use app\common\models\BaseModel;

class Fans extends BaseModel
{
    public $table = 'mc_mapping_fans';
    protected $guarded = [];
    protected $hidden = ['tag'];
    protected $primaryKey = 'fanid';
    public $timestamps = false;
    /**
     * 字段规则
     *
     * @return array
     */
    public function rules()
    {
        return [
            'uniacid' => 'required|numeric',
            'uid' => 'numeric|required',
            'openid' => 'required',
            //'unionid' => 'required',
        ];
    }

    /**
     * 定义字段名
     *
     * @return array
     */
    public function atributeNames()
    {
        return [
            'uniacid' => '公众号id',
            'uid' => '会员id',
            'openid' => 'openid',
        ];
    }

}
