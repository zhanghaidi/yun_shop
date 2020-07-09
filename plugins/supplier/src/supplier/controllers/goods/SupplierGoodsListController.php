<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/23
 * Time: 上午10:15
 */

namespace Yunshop\Supplier\supplier\controllers\goods;


use app\common\helpers\PaginationHelper;
use app\common\services\Session;
use Yunshop\Supplier\common\controllers\SupplierCommonController;
use Yunshop\Supplier\supplier\models\SupplierGoodsJoinGoods;
use app\backend\modules\goods\services\CategoryService;
use Setting;
use app\backend\modules\goods\models\Brand;
use app\backend\modules\goods\models\Category;

class SupplierGoodsListController extends SupplierCommonController
{
    public function index()
    {
        //        $product_attr_list = [
        //            'is_new' => '新品',
        //            'is_hot' => '热卖',
        //            'is_recommand' => '推荐',
        //            'is_discount' => '促销',
        //        ];
        //        $brands = Brand::getBrands()->get()->toArray();
        //        $pageSize = 10;
        //        $requestSearch = \YunShop::request()->search;
        //        if ($requestSearch) {
        //            $requestSearch = array_filter($requestSearch, function ($item) {
        //                return $item !== '';
        //            });
        //            $categorySearch = array_filter(\YunShop::request()->category, function ($item) {
        //                if (is_array($item)) {
        //                    return !empty($item[0]);
        //                }
        //                return !empty($item);
        //            });
        //            if ($categorySearch) {
        //                $requestSearch['category'] = $categorySearch;
        //            }
        //        }
        //
        //        $catetory_menus = CategoryService::getCategoryMultiMenuSearch(
        //            [
        //                'catlevel' => Setting::get('shop.category')['cat_level'],
        //                'ids'   => isset($categorySearch) ? array_values($categorySearch) : [],
        //            ]
        //        );
        //        $requestSearch['supplier'] = Session::get('supplier')['id'];
        //        $list = SupplierGoodsJoinGoods::getSupplierGoodsList($requestSearch)->orderBy('display_order', 'desc')->orderBy('goods_id', 'desc')->paginate($pageSize)->toArray();
        //        $list['data'] = collect($list['data']);
        //        $pager = PaginationHelper::show($list['total'], $list['current_page'], $list['per_page']);

        return view('Yunshop\Supplier::supplier.goods.supplier_goods');
    }

    public function goodsList()
    {
        $product_attr_list = [
            'is_new' => '新品',
            'is_hot' => '热卖',
            'is_recommand' => '推荐',
            'is_discount' => '促销',
        ];
        $brands = Brand::getBrands()->get()->toArray();
        $pageSize = 20;

        //        $catetory_menus = CategoryService::getCategoryMultiMenuSearch(
        ////            [
        ////                'catlevel' => Setting::get('shop.category')['cat_level'],
        ////                'ids'   => isset($categorySearch) ? array_values($categorySearch) : [],
        ////            ]
        ////        );
        $catetory_menus = [
            'catlevel' => Setting::get('shop.category')['cat_level'],
            'ids'   => Category::getAllCategoryGroupArray(),//CategoryFactory::create('shop')

        ];

        $requestSearch['supplier_id'] = Session::get('supplier')['id'];
        $list = SupplierGoodsJoinGoods::getSupplierGoodsList($requestSearch)->orderBy('display_order', 'desc')->orderBy('goods_id', 'desc')->paginate($pageSize);
        foreach ($list as $key => $item) {
            $list[$key]['thumb']  = yz_tomedia($item->thumb);
            $list[$key]['link'] = yzAppFullUrl('goods/' . $item['id']);
            $list[$key]['copy_link'] = yzWebFullUrl('plugin.supplier.supplier.controllers.goods.goods-operation.copy') . '&id=' . $item['id'];
        }
        PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());


        $data = [
            'list'  => $list,
            'lang'  => $this->lang(),
            'product_attr_list' => $this->product(),
            'catetory_menus' => $catetory_menus,
            'var' => \YunShop::app()->get(),
            'yz_url' => 'yzWebUrl',
            'edit_url' => 'plugin.supplier.supplier.controllers.goods.goods-operation.edit',
            'delete_url' => 'plugin.supplier.supplier.controllers.goods.goods-operation.delete',
            'delete_msg' => '确定删除该商品么？',
            'sort_url' => 'plugin.supplier.supplier.controllers.goods.goods-operation.sort',
            'copy_url' => 'plugin.supplier.supplier.controllers.goods.goods-operation.copy',
            'create_url' => 'plugin.supplier.supplier.controllers.goods.goods-operation.add',
            'brands' => $brands,
            'requestSearch' => $requestSearch
        ];

        return $this->successJson('查询成功', $data);
    }


    public function goodsSearch()
    {
        $pageSize = 20;
        $requestSearch = request()->search;
        if ($requestSearch) {
            $requestSearch = array_filter($requestSearch, function ($item) {
                return $item !== '';
            });
            $categorySearch = array_filter(request()->category, function ($item) {
                if (is_array($item)) {
                    return !empty($item);
                }
                return !empty($item);
            });
            if ($categorySearch) {
                $requestSearch['category'] = $categorySearch;
            }
        }
        $requestSearch['supplier_id'] = Session::get('supplier')['id'];
        $list = SupplierGoodsJoinGoods::getSupplierGoodsList($requestSearch)->orderBy('display_order', 'desc')->orderBy('goods_id', 'desc')->paginate($pageSize);
        foreach ($list as $key => $item) {
            $list[$key]['thumb']  = yz_tomedia($item->thumb);
            $list[$key]['link'] = yzAppFullUrl('goods/' . $item['id']);
            $list[$key]['copy_link'] = yzWebFullUrl('plugin.supplier.supplier.controllers.goods.goods-operation.copy') . '&id=' . $item['id'];
        }
        PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());
        return $this->successJson('查询成功', $list);
    }

    public function lang()
    {
        return [
            "shopname" => "商品名称",
            "mainimg" => "商品图片",
            "limittime" => "限时卖时间",
            "shopnumber" => "商品编号",
            "shopprice" => "商品价格",
            "putaway" => "上架",
            "soldout" => "下架",
            "good" => "商品",
            "price" => "价格",
            'yes_stock' => '出售中',
            'no_stock' => '售罄',
            "repertory" => "库存",
            "copyshop" => "复制商品",
            "isputaway" => "是否上架",
            "shopdesc" => "商品描述",
            "shopinfo" => "商品详情",
            'shopoption' => "商品规格",
            'marketprice' => "销售价格",
            'shopsubmit' => "发布商品"
        ];
    }

    public function product()
    {
        return [
            'is_new' => '新品',
            'is_hot' => '热卖',
            'is_recommand' => '推荐',
            'is_discount' => '促销',
        ];
    }
}
