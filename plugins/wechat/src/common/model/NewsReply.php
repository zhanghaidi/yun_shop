<?php

namespace Yunshop\Wechat\common\model;

use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class NewsReply extends BaseModel
{
    //public $table = 'news_reply';
    //public $timestamps = false;

    public $table = 'yz_wechat_news_reply';
    protected $guarded = [''];
    use SoftDeletes;

    public function rules()
    {
        return [
            'rid' => 'required|numeric',
            //'content' => 'required',
            //'parent_id' => 'required',
            //'title' => 'required',
            //'author' => 'required',
            //'description' => 'required',
            //'thumb' => 'required',
            //'url' => 'required',
            //'displayorder' => 'required',
            //'incontent' => 'required',
            //'createtime' => 'required',
            'media_id' => 'required',
        ];
    }

    public function atributeNames()
    {
        return [
            'rid' => '规则ID',
            'content' => '回复内容',
            'parent_id' => '父ID',
            'title' => '标题',
            'author' => '作者',
            'description' => '详情',
            'thumb' => '缩略图',
            'url' => '路径',
            'displayorder' => '排序',
            'incontent' => '内容',
            'createtime' => '创建时间',
            'media_id' => '素材ID',
        ];
    }

    // 保存和修改
    public static function saveNewsReply($reply)
    {
        if (empty($reply['id'])) {
            $news = new self();
        } else {
            $news = static::find($reply['id']);
            if (empty($news)) {
                return ['status' => 0, 'message' => '图文ID不存在:'.$reply['id'], 'data' => []];
            }
        }
        // 填充
        $news->fill($reply);
        // 验证数据
        $validate = $news->validator();
        if ($validate->fails()) {
            return ['status' => 0, 'message' => $validate->messages(), 'data' => []];
        }
        if ($news->save()) {
            return ['status' => 1, 'message' => '图文保存成功!', 'data' => []];
        }
        return ['status' => 0, 'message' => '图文保存失败!', 'data' => []];
    }

    // 通过rid获取多个回复id
    public static function getNewsReplyIdsByRid($rid)
    {
        return static::select('id')->where('rid',$rid)->orderBy('id','desc')->get();
    }

}
