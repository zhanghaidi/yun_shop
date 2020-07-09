<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/6/6
 * Time: 下午9:09
 */

namespace Yunshop\Wechat\admin\fans\model;

use app\common\models\BaseModel;

class FansTagMapping extends BaseModel
{

    public $table = 'mc_fans_tag_mapping';
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
            'fanid' => 'required|numeric',
            'tagid' => 'numeric|required',
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
            'fanid' => '粉丝id',
            'tagid' => '标签id',
        ];
    }
}