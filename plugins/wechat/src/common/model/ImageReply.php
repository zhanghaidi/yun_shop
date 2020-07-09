<?php

namespace Yunshop\Wechat\common\model;

use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class ImageReply extends BaseModel
{
    //public $table = 'images_reply';
    //public $timestamps = false;

    public $table = 'yz_wechat_image_reply';
    protected $guarded = [''];
    use SoftDeletes;

    public function rules()
    {
        return [
            'rid' => 'required|numeric',
            //'title' => 'required',
            //'description' => 'required',
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
            'mediaid' => '素材ID',
            'createtime' => '创建时间',
        ];
    }

    public function hasOneAttachment()
    {
        return $this->hasOne(\Yunshop\Wechat\common\model\WechatAttachment::class,'media_id','mediaid');
    }

    // 保存和修改
    public static function saveImageReply($reply)
    {
        if (empty($reply['id'])) {
            $image = new self();
        } else {
            $image = static::find($reply['id']);
            if (empty($image)) {
                return ['status' => 0, 'message' => '图片ID不存在:'.$reply['id'], 'data' => []];
            }
        }
        // 填充
        $image->fill($reply);
        // 验证数据
        $validate = $image->validator();
        if ($validate->fails()) {
            return ['status' => 0, 'message' => $validate->messages(), 'data' => []];
        }
        if ($image->save()) {
            return ['status' => 1, 'message' => '图片保存成功!', 'data' => []];
        }
        return ['status' => 0, 'message' => '图片保存失败!', 'data' => []];
    }

    // 通过rid获取多个回复id
    public static function getImageReplyIdsByRid($rid)
    {
        return static::select('id')->where('rid',$rid)->orderBy('id','desc')->get();
    }


}
