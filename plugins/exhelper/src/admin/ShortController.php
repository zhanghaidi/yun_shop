<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/6/14
 * Time: 下午3:50
 */

namespace Yunshop\Exhelper\admin;


use app\backend\modules\goods\services\CategoryService;
use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use Yunshop\Exhelper\common\models\Goods;
use Yunshop\Exhelper\common\models\Short;

class ShortController extends BaseController
{
    public function index()
    {
        $search = \YunShop::request()->search;
        if ($search) {
            $search = array_filter($search, function ($item) {
                return $item !== '';// && $item !== 0;
            });

            $categorySearch = array_filter(\YunShop::request()->category, function ($item) {
                return !empty($item);
            });

            if ($categorySearch) {
                $search['category'] = $categorySearch;
            }
        }
        $shop_set = \Setting::get('shop.category');
        $list = Goods::Search($search)->isPlugin()->wherePluginId(0)->paginate(20);
        $pager = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());
        $catetory_menus = CategoryService::getCategoryMenu(
            [
                'catlevel' => $shop_set['cat_level'],
                'ids'   => isset($categorySearch) ? array_values($categorySearch) : [],
            ]
        );

        return view('Yunshop\Exhelper::admin.short', [
            'list'  => $list,
            'pager' => $pager,
            'catetory_menus' => $catetory_menus,
            'search' => $search
        ]);
    }

    public function edit()
    {
        $short_title = \YunShop::request()->short_title;
        $short_collect = collect($short_title);
        $short_collect->each(function($item, $goods_id){
            if ($item) {
                $short_model = Short::getShortByGoodsId($goods_id)->first();
                if ($short_model) {
                    $short_model->short_title = $item;
                } else {
                    $short_model = new Short();
                    $short_model->fill(['goods_id' => $goods_id, 'short_title' => $item]);
                }
                $short_model->save();
            }
        });
        return $this->message('保存商品简称成功！', Url::absoluteWeb('plugin.exhelper.admin.short.index'), 'success');
    }
}