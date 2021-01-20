<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/15
 * Time: 下午6:38
 */

namespace Yunshop\Micro\common\models;


use app\common\models\BaseModel;

class MicroShopCarousel extends BaseModel
{
    protected $table = 'yz_micro_shop_carousel';
    protected $guarded = [''];

    public static function getSlidesIsEnabled()
    {
        return self::uniacid()
            ->where('enabled','1');
    }

    public static function getSlides($type = 0)
    {
        return self::uniacid()->isCarousel($type)
            ->orderBy('display_order', 'decs');
    }

    public static function getSlideByid($id)
    {
        return self::find($id);
    }

    public static function deletedSlide($id)
    {
        return self::where('id', $id)
            ->delete();
    }

    public function scopeIsCarousel($query, $type = 0)
    {
        return $query->where('is_carousel', $type);
    }

    /**
     *  定义字段名
     * 可使
     * @return array */
    public  function atributeNames() {
        return [
            'slide_name'=> '幻灯片名称',
            'display_order'=> '排序',
            'thumb'=> '幻灯片图片',
        ];
    }

    /**
     * 字段规则
     * @return array */
    public  function rules() {
        return [
            'slide_name' => 'required',
            'display_order' => 'required',
            'thumb' => 'required',
        ];
    }
}