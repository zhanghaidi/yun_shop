<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/18
 * Time: 上午10:05
 */

namespace Yunshop\Micro\common\models;


class Category extends \app\common\models\Category
{
    public static function getChildrenCategorysByIds($parentId, $set, $child_ids)
    {
        $model = self::uniacid();

        if ($set['cat_level'] == 3) {
            $model->with(['hasManyChildren'=>function($qurey) use ($child_ids){
                $qurey->where('enabled', 1)->whereIn('id', $child_ids);
            }]);
        }

        $model->where('parent_id', $parentId);
        $model->where('enabled', 1);
        $model->orderBy('id', 'asc');
        return $model;
    }
}