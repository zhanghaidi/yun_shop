<?php

namespace Yunshop\Wechat\common\model;

use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class VideoReply extends BaseModel
{
    //public $table = 'video_reply';
    //public $timestamps = false;

    public $table = 'yz_wechat_video_reply';
    protected $guarded = [''];
    use SoftDeletes;

    public function rules()
    {
        return [
            'rid' => 'required|numeric',
            //'title' => 'required',
            //'description' => 'required',
            //'content' => 'required',
            'mediaid' => 'required',
            'createtime' => 'required',
        ];
    }

    public function atributeNames()
    {
        return [
            'rid' => '规则ID',
            'title' => '标题',
            'description' => '详情',
            'content' => '内容',
            'mediaid' => '素材ID',
            'createtime' => '创建时间',
        ];
    }

    public function hasOneAttachment()
    {
        return $this->hasOne(\Yunshop\Wechat\common\model\WechatAttachment::class,'media_id','mediaid');
    }

    // 保存和修改
    public static function saveVideoReply($reply)
    {
        if (empty($reply['id'])) {
            $video = new self();
        } else {
            $video = static::find($reply['id']);
            if (empty($video)) {
                return ['status' => 0, 'message' => '视频ID不存在:'.$reply['id'], 'data' => []];
            }
        }
        // 填充
        $video->fill($reply);
        // 验证数据
        $validate = $video->validator();
        if ($validate->fails()) {
            return ['status' => 0, 'message' => $validate->messages(), 'data' => []];
        }
        if ($video->save()) {
            return ['status' => 1, 'message' => '视频保存成功!', 'data' => []];
        }
        return ['status' => 0, 'message' => '视频保存失败!', 'data' => []];
    }

    // 通过rid获取多个回复id
    public static function getVideoReplyIdsByRid($rid)
    {
        return static::select('id')->where('rid',$rid)->orderBy('id','desc')->get();
    }
}
