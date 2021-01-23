<?php

namespace Yunshop\MinappContent\models;

use app\common\models\BaseModel;

/**
 * Class ComplainTypeModel
 * @package Yunshop\MinappContent\models
 * 投诉类型
 */
class ComplainTypeModel extends BaseModel
{
    public $table = 'diagnostic_service_complain_type';
    public $timestamps = false;
    protected $casts = ['create_time' => 'date'];

    /**
     * 字段规则
     * @return array
     */
    public function rules()
    {
        return [
            'uniacid' => 'required|integer',
            'name' => 'required|string',
            'list_order' => 'integer',
        ];
    }
}
