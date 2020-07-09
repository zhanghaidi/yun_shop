<?php

namespace Yunshop\Wechat\common\model;

use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends BaseModel
{
//    use SoftDeletes;
    public $timestamps = true;

    public $table = 'yz_wechat_menu';
    protected $guarded = [];

    const PAGE_SIZE = 10;
    /**
     * 字段规则
     *
     * @return array
     */
    public function rules()
    {
        return [
            'uniacid' => 'required|numeric',
            'type' => 'numeric|required',
            'title' => 'required|unique:yz_wechat_menu,uniacid,'.\Yunshop::app()->uniacid,

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
            'uniacid' => '公众号id',
            'type' => '菜单类型',
            'title' => '菜单组不能重复',
        ];
    }

}
