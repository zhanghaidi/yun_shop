<?php

namespace Yunshop\Designer\models;


/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/3/21
 * Time: 上午9:53
 */

use app\common\models\BaseModel;
use Yunshop\Designer\observers\DesignerObserver;

class Designer extends BaseModel
{
    protected $table = 'yz_designer';

    public $timestamps = true;

    protected $guarded = [''];

    public $widgets =[];

    protected $appends = ['page_type_cast'];


    public function getPageTypeCastAttribute()
    {
        return explode(',', $this->attributes['page_type']);
    }

    /*
     * 获取分页列表
     *
     * @parms int $pageSize
     *
     * return object
     * */
    public static function getPageList($pageSize)
    {
        return self::uniacid()->orderBy('created_at','desc')->paginate($pageSize);
    }

    /*
     * 搜索名称获取列表页面
     *
     * @param string $name
     *
     * return object
     * */
    public static function getPageListByName($name, $pageSize)
    {
        return self::uniacid()->where('page_name', 'like', '%'.$name.'%')->orderBy('created_at','desc')->paginate($pageSize);
    }

    /*
     * 通过装修页面ID获取装修页面信息
     *
     * @parms int $pageId
     *
     * @return array
     * */
    public static function getDesignerByPageID($pageId)
    {
        return self::uniacid()->where('id', $pageId)->first();
    }

    /*
     * 获取默认模版信息
     * @return object
     * */
    public static function getDefaultDesigner($page_type = 1)
    {
        return self::uniacid()->where('is_default', '1')->where('page_type', $page_type)->first();
    }

    /*
     * 删除装修页面通过装修页面ID
     *
     * @parms int $pageId
     *
     * @return bool $result
     * */
    public static function destoryDesignerByPageId($pageId)
    {
        return self::uniacid()->where('id', $pageId)->delete();
    }

    /*
     * 移除默认字段值，供修改默认使用
     *
     * @return result
     * */
    public static function removeDefault($page_type)
    {
        return self::uniacid()->where("page_type", $page_type)->update(['is_default' => '0']);
    }

    /**
     *在boot()方法里注册下模型观察类
     * boot()和observe()方法都是从Model类继承来的
     * 主要是observe()来注册模型观察类，可以用TestMember::observe(new TestMemberObserve())
     * 并放在代码逻辑其他地方如路由都行，这里放在这个TestMember Model的boot()方法里自启动。
     */
    public static function boot()
    {
        parent::boot();
        //注册观察者
        static::observe(new DesignerObserver());
    }

}
