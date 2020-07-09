<?php
/**
 * Created by PhpStorm.
 * User: king
 * Date: 2018/10/29
 * Time: 4:25 PM
 */

namespace Yunshop\Designer\Backend\Models;


use app\common\scopes\UniacidScope;

class TopMenuModel extends \Yunshop\Designer\Common\Models\TopMenuModel
{
    public static function boot()
    {
        parent::boot();
        self::addGlobalScope(new UniacidScope());
    }

    public function scopeSearch($query, array $search)
    {
        if ($search['menu_name']) {
            $query->where('menu_name', 'like', $search['menu_name'] . '%');
        }
        return $query;
    }

    /**
     * 字段规则
     *
     * @return array
     */
    public  function rules()
    {
        return [
            'uniacid'   => "required",
            'params'    => 'required',
            'menus'     => 'required',
            'menu_name' => 'required|max:45'
        ];
    }

    /**
     * 定义字段名
     *
     * @return array
     */
    public  function atributeNames() {
        return [
            'uniacid'   => "所属公众号",
            'params'    => "菜单参数",
            'menus'     => "菜单数据",
            'menu_name' => "菜单名称"
        ];
    }
}
