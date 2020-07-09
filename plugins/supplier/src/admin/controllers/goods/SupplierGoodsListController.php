<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/21
 * Time: 下午2:21
 */

namespace Yunshop\Supplier\admin\controllers\goods;


use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use Yunshop\Supplier\admin\models\Supplier;
use Yunshop\Supplier\admin\models\SupplierGoods;
use Yunshop\Supplier\admin\models\SupplierGoodsJoinGoods;
use app\backend\modules\goods\services\CategoryService;
use app\backend\modules\goods\models\Brand;
use Setting;
use app\backend\modules\goods\models\Category;



class SupplierGoodsListController extends BaseController
{
    public function index()
    {
//        $pageSize = 10;
//        $requestSearch = \YunShop::request()->search;
//
//        $brands = Brand::getBrands()->get()->toArray();
//
//        if ($requestSearch) {
//            $requestSearch = array_filter($requestSearch, function ($item) {
//                return $item !== '';
//            });
//            $categorySearch = array_filter(\YunShop::request()->category, function ($item) {
//
//                return !empty($item[0]);
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
//        //dd($requestSearch);->pluginId()
//        $list = SupplierGoodsJoinGoods::getSupplierGoodsList($requestSearch)->orderBy('display_order', 'desc')->orderBy('goods_id', 'desc')->paginate($pageSize)->toArray();
//        $list['data'] = collect($list['data']);
//        //echo '<pre>';print_r($list);exit;
//        $pager = PaginationHelper::show($list['total'], $list['current_page'], $list['per_page']);
//        $all_supplier = Supplier::getSupplierList(null, 1)->get()->toArray();
//
//        $edit_url = 'plugin.supplier.admin.controllers.goods.goods-operation.edit';
//        $delete_url = '';
//        $delete_msg = '您没有权限删除商品';

        return view('Yunshop\Supplier::admin.goods.supplier_goods');
    }

    public function goodsList()
    {
        $pageSize = 20;

        $brands = Brand::getBrands()->get()->toArray();

        $requestSearch = '';
        $categorySearch = array_filter(\YunShop::request()->category, function ($item) {

            return !empty($item[0]);
        });
        if ($categorySearch) {
            $requestSearch['category'] = $categorySearch;
        }

        $catetory_menus = [
                'catlevel' => Setting::get('shop.category')['cat_level'],
                'ids'   => Category::getAllCategoryGroupArray()//CategoryFactory::create('shop')//isset($categorySearch) ? array_values($categorySearch) : [],
            ];
        //dd($requestSearch);->pluginId()
        $list = SupplierGoodsJoinGoods::getSupplierGoodsList($requestSearch)->orderBy('display_order', 'desc')->orderBy('goods_id', 'desc')->paginate($pageSize);
        foreach ($list as $key => $item){
            $list[$key]['thumb']  = yz_tomedia($item->thumb);
            $list[$key]['link'] = yzAppFullUrl('goods/'.$item['id']);
        }
        PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());
//        $list = $list->toArray();
//        $list['data'] = collect($list['data']);
        $all_supplier = Supplier::getSupplierList(null, 1)->get()->toArray();


        $edit_url = 'plugin.supplier.admin.controllers.goods.goods-operation.edit';
        $delete_url = '';
        $delete_msg = '您没有权限删除商品';

       $data = [
            'list'  => $list,
            'brands' => $brands,
            'catetory_menus' => $catetory_menus,
            'var' => \YunShop::app()->get(),
            'all_supplier' => $all_supplier,
            'edit_url' => $edit_url,
            'delete_url' => $delete_url,
            'delete_msg' => $delete_msg,
            'sort_url' => 'plugin.supplier.supplier.controllers.goods.goods-operation.sort',
            'copy_url' => 'goods.goods.copy',
            'yz_url' => 'yzWebUrl',
            'lang'  => $this->lang(),
            'product_attr_list' => $this->product(),
       ];
       return $this->successJson('查询成功',$data);
    }


    public function goodsSearch()
    {
        $pageSize = 20;
        $requestSearch = request()->search;

        $category = request()->category;

        if ($requestSearch) {
            $requestSearch = array_filter($requestSearch, function ($item) {
                return $item !== '';
            });
            $categorySearch = array_filter($category, function ($item) {

                return !empty($item);
            });
            if ($categorySearch) {
                $requestSearch['category'] = $categorySearch;
            }
        }
        $list = SupplierGoodsJoinGoods::getSupplierGoodsList($requestSearch)->orderBy('display_order', 'desc')->orderBy('goods_id', 'desc')->paginate($pageSize);
        foreach ($list as $key => $item){
            $list[$key]['thumb']  = yz_tomedia($item->thumb);
            $list[$key]['link'] = yzAppFullUrl('goods/'.$item['id']);
        }
        PaginationHelper::show($list['total'], $list['current_page'], $list['per_page']);
//        dd($list);
//        $list = $list->toArray();
//        $list['data'] = collect($list['data']);

        
        return $this->successJson('查询成功',$list);
        

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