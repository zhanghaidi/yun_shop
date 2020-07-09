<?php
/**
 * Created by PhpStorm.
 * User: win 10
 * Date: 2019/7/10
 * Time: 10:06
 */

namespace Yunshop\Designer\models;


use app\common\models\BaseModel;
use Yunshop\Designer\observers\MemberDesignerObserver;

class MemberDesigner extends BaseModel
{
    protected $table = 'yz_member_designer';

    public $timestamps = true;

    protected $guarded = [''];

    public $widgets =[];

    protected $appends = ['page_type_cast'];

    const PAGE_MEMBER_CENTER = 1;//会员中心页面

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

    public static function boot()
    {
        parent::boot();
        //注册观察者
        static::observe(new MemberDesignerObserver());
    }


}