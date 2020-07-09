<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/6/6
 * Time: 下午9:09
 */

namespace Yunshop\Wechat\common\model;

use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class WechatNews extends BaseModel
{
    //public $table = 'wechat_news';
    //public $timestamps = false;

    public $table = 'yz_wechat_news';
    protected $guarded = [''];
    use SoftDeletes;

    protected $hidden = [
        'uniacid',
    ];

    public function rules()
    {
        return [
            'uniacid' => 'required|numeric',
            'attach_id' => 'required|numeric',
//            'thumb_media_id' => 'required',
            //'title' => 'required|numeric',
        ];
    }

    public function atributeNames()
    {
        return [
            'uniacid' => '公众号id',
            'attach_id' => '素材id',
            'thumb_media_id' => '图文封面',
            //'title' => '标题',
        ];
    }

}