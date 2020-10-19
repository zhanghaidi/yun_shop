<?php
namespace app\common\models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class TemplateMsgLog
 * @package app\common\models
 */
class TemplateMsgLog extends BaseModel
{
    use SoftDeletes;

    public $table = 'yz_template_msg_log';

    public $timestamps = false;

    public $dates = ['deleted_at'];

    protected $guarded = [];

    public $selected;

    protected $hidden = ['uniacid', 'deleted_at'];


    /**
     * 定义字段名
     * @return array
     */
    public function atributeNames()
    { //todo typo
        return [
            'uniacid' => '公众号 ID',
            'member_id' => '会员 ID',
            'template_id' => '模板 ID',
            'openid' => '会员 openid',
            'message' => '模板内容',
            'weapp_appid' => '小程序appid',
            'weapp_pagepath' => '小程序路径',
            'news_link' => '跳转网页链接',
            'respon_code' => '微信返回码',
            'respon_data' => '微信返回数据',
            'remark' => '备注',
        ];
    }

    /*
     * 字段规则
     * @return array
     * */
    public function rules()
    {
        return [
            'uniacid' => 'required|integer',
            'member_id' => 'required|integer',
            'template_id' => 'required|string',
            'openid' => 'required|string',
            'message' => 'required|string',
            'weapp_appid' => 'string',
            'weapp_pagepath' => 'string',
            'news_link' => 'string',
            'weapp_appid' => 'string',
            'respon_code' => 'integer',
            'respon_data' => 'string',
            'remark' => 'string',
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

}
