<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/6/6
 * Time: 下午9:09
 */

namespace Yunshop\Wechat\admin\menu\model;

class Menu extends \Yunshop\Wechat\common\model\Menu
{
	// 通过id获取数据
    public static function getMenuByMenuid($id,$type)
    {
       return static::uniacid()->where(['type'=>$type])->find($id);
    }  

    // 通过id获取menuid
    public static function getMenuIdsByMenuid($id)
    {
        return static::uniacid()->select('menuid')->where('id',$id)->first();
    }

    // 获取历史菜单
    public static function getDisplayMenu()
    {
        return static::uniacid()->where([
            ['status','=',0],
            ['type','=',1]
        ])->paginate(self::PAGE_SIZE)->toArray();
    }

    // 获取当前普通菜单
    public static function getCurrentMenu()
    {
        return static::uniacid()->where([
            ['status',1],
            ['type',1]
        ])->first();
    }

    // 获取个性化菜单
    public static function getCurrentConditionMenu()
    {
        return static::uniacid()->where('type',3)->paginate(self::PAGE_SIZE)->toArray();
    }

    //将当前公众号普通菜单的status都更新为0
    public static function updataStatus(){
        return static::uniacid()->where([
            ['status',1],
            ['type',1]
        ])->update(['status'=>0]);
    }
    //将当前公众号个性化菜单的status都更新为0
    public static function updataConditionStatus(){
        return static::uniacid()->where([
            ['status',1],
            ['type',3]
        ])->update(['status'=>0]);
    }

    //获取当前公众号的菜单组名
    public static function getMenuGroup(){
        return static::uniacid()->pluck('title');
    }

    // 通过id删除对象
    public static function deleteMenuById($id)
    {
        return static::uniacid()->where('id', $id)->delete();
    }

}