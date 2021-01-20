<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/23
 * Time: 下午4:56
 */

namespace Yunshop\Micro\frontend\controllers\MicroShop;

use app\common\components\ApiController;
use Yunshop\Micro\common\models\MicroShop;
use Yunshop\Micro\common\models\MicroShopGoods;
use Setting;
use app\frontend\modules\goods\models\Category;

class CategoryByShareController extends ApiController
{
    private function getCaregoryData()
    {
        $shop_id = \YunShop::request()->shop_id;
        if (!$shop_id) {
            return $this->errorJson('shop_id参数不能为空', [
                'status'    => -1
            ]);
        }
        $micro_shop = MicroShop::getMicroShopById($shop_id);
        if (!$micro_shop) {
            return $this->errorJson('未找到此微店', [
                'status'    => -1
            ]);
        }
        $goods_list = MicroShopGoods::getGoodsByMemberId($micro_shop->member_id);
        if ($goods_list->isEmpty()) {
            return $this->errorJson('未获取到分类', [
                'status'    => -1
            ]);
        }
        $category_ids = $goods_list->map(function ($item){
            if ($item->hasOneGoods) {
                $cayegorys = $item->hasOneGoods->belongsToCategorys;
                if (!$cayegorys->isEmpty()) {
                    return $item->hasOneGoods->belongsToCategorys->first()->category_ids;
                }
            }
        });
        if ($category_ids->isEmpty()) {
            return $this->errorJson('未获取到分类', [
                'status'    => -1
            ]);
        }
        $parent_ids = '';
        $child_ids = '';
        foreach ($category_ids->toArray() as $id) {
            $ids = explode(',', $id);
            foreach ($ids as $key => $v) {
                if ($key == 0) {
                    if (empty($parent_ids)) {
                        $parent_ids .= $v;
                    } else {
                        $parent_ids .= ',' . $v;
                    }
                } else {
                    if (empty($child_ids)) {
                        $child_ids .= $v;
                    } else {
                        $child_ids .= ',' . $v;
                    }
                }
            }
        }
        $parent_ids = array_unique(explode(',', $parent_ids));
        $child_ids = array_unique(explode(',', $child_ids));
        return [
            'parent_ids'    => $parent_ids,
            'child_ids'     => $child_ids
        ];
    }

    public function getCategory()
    {
        $caretory_data = $this->getCaregoryData();

        $set = Setting::get('shop.category');
        $pageSize = 10;
        $parent_id = \YunShop::request()->parent_id ? \YunShop::request()->parent_id : '0';
        $list = Category::select()->where('parent_id', $parent_id)->where('enabled', 1)->uniacid()->pluginId()->whereIn('id', $caretory_data['parent_ids'])->paginate($pageSize);
        if ($list->isEmpty()) {
            return $this->errorJson('未获取到分类', [
                'status'    => -1
            ]);
        }
        $list = $list->toArray();
        foreach ($list['data'] as &$item) {
            $item['thumb'] = replace_yunshop(yz_tomedia($item['thumb']));
            $item['adv_img'] = replace_yunshop(yz_tomedia($item['adv_img']));
        }
        $set['cat_adv_img'] = replace_yunshop(yz_tomedia($set['cat_adv_img']));
        $list['set'] = $set;
        if($list['data']){
            return $this->successJson('获取分类数据成功!', $list);
        }
        return $this->errorJson('未检测到分类数据!',$list);
    }

    public function getChildrenCategory()
    {
        $caretory_data = $this->getCaregoryData();

        $pageSize = 10;
        $set = Setting::get('shop.category');
        $parent_id = intval(\YunShop::request()->parent_id);
        if (!in_array($parent_id, $caretory_data['parent_ids'])) {
            return $this->errorJson('参数错误', [
                'status'    => -1
            ]);
        }
        $list = \Yunshop\Micro\common\models\Category::getChildrenCategorysByIds($parent_id,$set, $caretory_data['child_ids'])->whereIn('id', $caretory_data['child_ids'])->paginate($pageSize)->toArray();
        foreach ($list['data'] as &$item) {
            $item['thumb'] = replace_yunshop(yz_tomedia($item['thumb']));
            $item['adv_img'] = replace_yunshop(yz_tomedia($item['adv_img']));
            foreach ($item['has_many_children'] as &$has_many_child) {
                $has_many_child['thumb'] = replace_yunshop(yz_tomedia($has_many_child['thumb']));
                $has_many_child['adv_img'] = replace_yunshop(yz_tomedia($has_many_child['adv_img']));
            }
        }
        $set['cat_adv_img'] = replace_yunshop(yz_tomedia($set['cat_adv_img']));
        $list['set'] = $set;
        if($list){
            return $this->successJson('获取子分类数据成功!', $list);
        }
        return $this->errorJson('未检测到子分类数据!',$list);
    }
}