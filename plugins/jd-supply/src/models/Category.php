<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/8/21
 * Time: 10:57
 */

namespace Yunshop\JdSupply\models;


class Category extends \app\common\models\Category
{
    /**
     * @param $parent_id
     * @param $pageSize
     * @return mixed
     */
    public static function getCategorys($parentId)
    {
        return self::uniacid()
            ->where('parent_id', $parentId)
            ->orderBy('display_order', 'asc')
            ->orderBy('id', 'asc');
    }

    /**
     * @param $parentId
     * @param $set
     * @return mixed
     */
    public static function getChildrenCategorys($parentId, $set = [])
    {
        $model = self::uniacid();

        if ($set['cat_level'] == 3) {
            $model->with(['hasManyChildren'=>function($qurey){
                return $qurey->orderBy('display_order', 'asc');
            }]);
        }

        $model->where('parent_id', $parentId);
        $model->orderBy('display_order', 'asc');
        return $model;
    }

    public static function shopCategory()
    {
        $set = \Setting::get('shop.category');

        $list = Category::getCategorys(0)->select('id', 'name', 'enabled')->pluginId()->get();

        $list->map(function($category) use($set) {
            $childrens = Category::getChildrenCategorys($category->id,$set)->select('id', 'name', 'enabled')->get()->toArray();
            foreach ($childrens as $key => &$children) {
                if ($set['cat_level'] == 3 &&  $children['has_many_children']) {
                    $children['childrens'] = $children['has_many_children'];
                } else {
                    $children['childrens'] = [];
                }
            }
            $category->childrens = $childrens;

        });

        return $list;
    }
}