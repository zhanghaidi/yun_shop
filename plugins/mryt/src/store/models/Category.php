<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/9/4
 * Time: 下午7:17
 */

namespace Yunshop\Mryt\store\models;

use Illuminate\Database\Eloquent\Builder;
use Yunshop\Mryt\store\models\Store;


class Category extends \app\common\models\Category
{
    public static function getCategoryByLevelAndByParentId($search, $level, $parent_id)
    {
        return self::select('id', 'name', 'thumb', 'adv_img', 'adv_url', 'enabled')->byLevel($level)->byParentId($parent_id)->hasStoreGoodsCategory($search);
    }

    public static function getCategoryByLevelAndByParentIds($search, $level, $parent_id)
    {
        return self::select('id', 'name', 'thumb', 'adv_img', 'adv_url', 'enabled')->where('enabled', 1)->byLevel($level)->byParentId($parent_id)->hasStoreGoodsCategory($search);
    }

    public static function getAllCategory($search)
    {
        return self::select()->hasStoreGoodsCategory($search);
    }

    public static function getAllCategoryGroup($params)
    {
        $categorys = self::getAllCategory($params)->get();

        $categoryMenus['parent'] = $categoryMenus['children'] = [];

        foreach ($categorys as $category) {
            !empty($category['parent_id']) ?
                $categoryMenus['children'][$category['parent_id']][] = $category :
                $categoryMenus['parent'][$category['id']] = $category;
        }

        return $categoryMenus;
    }

    public static function getCategory($id)
    {
        return self::find($id);
    }

    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    public function scopeByParentId($query, $parent_id)
    {
        return $query->where('parent_id', $parent_id);
    }

    public function scopeHasStoreGoodsCategory($query, $search)
    {
        return $query->whereHas('hasOneStoreGoodsCategory', function($storeGoodsCategory)use($search){
            if ($search['store_id']) {
                $storeGoodsCategory = $storeGoodsCategory->byStoreId($search['store_id']);
            }
            if ($search['parent_id'] || $search['parent_id'] === '0') {
                $storeGoodsCategory->byParentId($search['parent_id']);
            }
        });
    }

    public function scopePluginId($query)
    {
        return $query->where('plugin_id', Store::PLUGIN_ID);
    }

    public function hasOneStoreGoodsCategory()
    {
        return $this->hasOne(StoreGoodsCategory::class, 'category_id', 'id');
    }

    public static function boot()
    {
        parent::boot();

        static::addGlobalScope(function (Builder $builder) {
            $builder->pluginId()->uniacid();
        });
    }
}