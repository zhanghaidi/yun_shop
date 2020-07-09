<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/3/23
 * Time: 上午8:20
 */

namespace Yunshop\Designer\models;



use app\common\models\BaseModel;

class DesignerMenu extends BaseModel
{
    public $table = 'yz_designer_menu';

    public $timestamps = true;

    protected $guarded = [''];

    /*
     *  获取分页列表
     *
     * @param int $pageSize
     *
     * @return object
     * */
    public static function getPageList($pageSize)
    {
        return self::uniacid()->orderBy('created_at','desc')->paginate($pageSize);
    }

    /*
     *  获取当前公众号所有自定义菜单
     *
     * @param
     *
     * @return object
     * */
    public static function getAllMenuList()
    {
        return self::uniacid()->get();
    }

    /*
     * 通过自定义菜单名称搜索
     *
     * @params mixed $name
     *
     * @return object
     * */
    public static function getMenuByName($name, $pageSize)
    {
        return self::uniacid()->where('menu_name', 'like', '%'.$name.'%')->orderBy('created_at','desc')->paginate($pageSize);
    }

    /*
     * 获取自定义底部菜单信息，通过主键ID
     *
     * @param int $menuId
     *
     * return
     * */
    public static function getMenuById($menuId)
    {
        return self::uniacid()->where('id', $menuId)->first();
    }

    public static function getDefaultMenu()
    {
        return self::uniacid()->where('is_default', '1')->first();
    }



    /*
     * 删除菜单信息，通过主键ID
     *
     * @param int $menuId
     *
     * return result
     * */
    public static function destroyMenuById($menuId)
    {
        return static::uniacid()->where('id', $menuId)->delete();
    }

    /*
     * 移除默认字段值，供修改默认使用
     *
     * @return result
     * */
    public static function removeDefault()
    {
        return self::uniacid()->update(['is_default' => '0']);
    }



}
