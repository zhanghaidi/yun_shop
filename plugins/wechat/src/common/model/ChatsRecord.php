<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/6/6
 * Time: 下午9:09
 */

namespace Yunshop\Wechat\common\model;

use app\common\models\BaseModel;

class ChatsRecord extends BaseModel
{

    const NEWS = 'mpnews';
    const TEXT = 'text';
    const VOICE = 'voice';
    const VIDEO = 'video';
    const IMAGE = 'image';
    const MUSIC = 'music';

    const STAFF = 1;// 客服发送给用户
    const USER = 2;// 用户发给客服

    public $table = 'mc_chats_record';
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
            'openid' => 'required',
            'flag' => 'required|numeric',
            'msgtype' => 'required',
            'content' => 'required',
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
            'uniacid' => '公众号',
            'openid' => 'openid',
            'msgtype' => '消息类型',
            'content' => '内容',
        ];
    }

    public function getContentAttribute($value)
    {
        return unserialize($value);
    }

    public function setContentAttribute($value)
    {
        $this->attributes['content'] = serialize($value);
    }
}