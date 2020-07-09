<?php

namespace Yunshop\Wechat\common\model;

use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class BasicReply extends BaseModel
{
    //public $table = 'basic_reply';
    //public $timestamps = false;

    public $table = 'yz_wechat_basic_reply';
    protected $guarded = [''];
    use SoftDeletes;

    public function rules()
    {
        return [
            'rid' => 'required|numeric',
            'content' => 'required',
        ];
    }

    public function atributeNames()
    {
        return [
            'rid' => '规则id',
            'content' => '回复内容',
        ];
    }

    // 保存和修改
    public static function saveBasicReply($reply)
    {
        if (empty($reply['id'])) {
            $words = new self();
        } else {
            $words = static::find($reply['id']);
            if (empty($words)) {
                return ['status' => 0, 'message' => '文字ID不存在:'.$reply['id'], 'data' => []];
            }
        }
        // 填充
        $words->fill($reply);
        // 验证数据
        $validate = $words->validator();
        if ($validate->fails()) {
            return ['status' => 0, 'message' => $validate->messages(), 'data' => []];
        }
        if ($words->save()) {
            return ['status' => 1, 'message' => '关键字保存成功!', 'data' => []];
        }
        return ['status' => 0, 'message' => '关键字保存失败!', 'data' => []];
    }

    // 通过rid获取多个回复id
    public static function getBasicReplyIdsByRid($rid)
    {
        return static::select('id')->where('rid',$rid)->orderBy('id','desc')->get();
    }




}
