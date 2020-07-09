<?php

namespace Yunshop\Wechat\common\model;

use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserapiReply extends BaseModel
{
    //public $table = 'userapi_reply';

    public $table = 'yz_wechat_userapi_reply';
    protected $guarded = [''];
    use SoftDeletes;

    public function rules()
    {
        return [
            'rid' => 'required|numeric',
            //'description' => 'required',
            //'apiurl' => 'required',
            //'token' => 'required',
            //'default_text' => 'required',
            //'cachetime' => 'required',
        ];
    }

    public function atributeNames()
    {
        return [
            'rid' => '回复ID',
            'description' => '详情',
            'apiurl' => 'api地址',
            'token' => 'token',
            'default_text' => '默认内容',
            'cachetime' => '缓存时间',
        ];
    }

    // 保存和修改
    public static function saveUserapiReply($reply)
    {
        $userapi = new self();
        // 填充
        $userapi->fill($reply);
        // 验证数据
        $validate = $userapi->validator();
        if ($validate->fails()) {
            return ['status' => 0, 'message' => $validate->messages(), 'data' => []];
        }
        if ($userapi->save()) {
            return ['status' => 1, 'message' => 'userapi保存成功!', 'data' => []];
        }
        return ['status' => 0, 'message' => 'userapi保存失败!', 'data' => []];
    }

    // 通过rid获取多个回复id
    public static function getUserapiReplyIdsByRid($rid)
    {
        return static::select('id')->where('rid',$rid)->orderBy('id','desc')->get();
    }




}
