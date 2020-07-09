<?php

namespace Yunshop\Wechat\common\model;

use app\common\models\BaseModel;

class FansGroups extends BaseModel
{
    public $table = 'mc_fans_groups';
    protected $guarded = [];
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
        ];
    }

}
