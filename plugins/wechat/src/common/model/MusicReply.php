<?php

namespace Yunshop\Wechat\common\model;

use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class MusicReply extends BaseModel
{
    //public $table = 'music_reply';
    //public $timestamps = false;

    public $table = 'yz_wechat_music_reply';
    use SoftDeletes;
    protected $guarded = [''];

    public function rules()
    {
        return [
            'rid' => 'required|numeric',
            //'title' => 'required',
            //'description' => 'required',
            'url' => 'required',
            'hqurl' => 'required_without:url',
        ];
    }

    public function atributeNames()
    {
        return [
            'rid' => '规则ID',
            'title' => '标题',
            'description' => '详情',
            'url' => '音乐链接',
            'hqurl' => '高品质链接',
        ];
    }

    // 保存和修改
    public static function saveMusicReply($reply)
    {
        if (empty($reply['id'])) {
            $music = new self();
        } else {
            $music = static::find($reply['id']);
            if (empty($music)) {
                return ['status' => 0, 'message' => '音乐ID不存在:'.$reply['id'], 'data' => []];
            }
        }
        // 填充
        $music->fill($reply);
        // 验证数据
        $validate = $music->validator();
        if ($validate->fails()) {
            return ['status' => 0, 'message' => $validate->messages(), 'data' => []];
        }
        if ($music->save()) {
            return ['status' => 1, 'message' => '音乐保存成功!', 'data' => []];
        }
        return ['status' => 0, 'message' => '音乐保存失败!', 'data' => []];
    }

    // 通过rid获取多个回复id
    public static function getMusicReplyIdsByRid($rid)
    {
        return static::select('id')->where('rid',$rid)->orderBy('id','desc')->get();
    }
}
