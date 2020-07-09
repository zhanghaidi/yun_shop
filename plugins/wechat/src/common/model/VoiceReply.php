<?php

namespace Yunshop\Wechat\common\model;

use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class VoiceReply extends BaseModel
{
    //public $table = 'voice_reply';
    //public $timestamps = false;

    public $table = 'yz_wechat_voice_reply';
    protected $guarded = [''];
    use SoftDeletes;

    public function rules()
    {
        return [
            'rid' => 'required|numeric',
            //'title' => 'required',
            'mediaid' => 'required',
            'createtime' => 'required',
        ];
    }

    public function atributeNames()
    {
        return [
            'rid' => '回复ID',
            'title' => '标题',
            'mediaid' => '素材ID',
            'createtime' => '创建时间',
        ];
    }

    public function hasOneAttachment()
    {
        return $this->hasOne(\Yunshop\Wechat\common\model\WechatAttachment::class,'media_id','mediaid');
    }

    // 保存和修改
    public static function saveVoiceReply($reply)
    {
        if (empty($reply['id'])) {
            $voice = new self();
        } else {
            $voice = static::find($reply['id']);
            if (empty($voice)) {
                return ['status' => 0, 'message' => '语音ID不存在:'.$reply['id'], 'data' => []];
            }
        }
        // 填充
        $voice->fill($reply);
        // 验证数据
        $validate = $voice->validator();
        if ($validate->fails()) {
            return ['status' => 0, 'message' => $validate->messages(), 'data' => []];
        }
        if ($voice->save()) {
            return ['status' => 1, 'message' => '语音保存成功!', 'data' => []];
        }
        return ['status' => 0, 'message' => '语音保存失败!', 'data' => []];
    }

    // 通过rid获取多个回复id
    public static function getVoiceReplyIdsByRid($rid)
    {
        return static::select('id')->where('rid',$rid)->orderBy('id','desc')->get();
    }
}
