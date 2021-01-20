<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com yangyu
 * Date: 2019/1/8
 * Time: 09:54
 */

namespace Yunshop\Tbk\admin;

use app\backend\modules\goods\models\Brand;
use app\backend\modules\goods\services\CategoryService;
use app\common\components\BaseController;
use app\common\exceptions\ShopException;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use Yunshop\Tbk\common\models\Goods;
use app\backend\modules\goods\services\EditGoodsService;


class GoodsController extends BaseController
{
    private $goods_id = null;
    private $lang = null;

    public function __construct()
    {
        $this->lang = array(
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
        );
        $this->goods_id = (int)request()->id;
    }

    public function index()
    {
        //增加商品属性搜索
        $product_attr_list = [
            'is_new' => '新品',
            'is_hot' => '热卖',
            'is_recommand' => '推荐',
            'is_discount' => '促销',
        ];

        $brands = Brand::getBrands()->get()->toArray();

        $requestSearch = \YunShop::request()->search;
        if ($requestSearch) {
            $requestSearch = array_filter($requestSearch, function ($item) {
                return $item !== '';// && $item !== 0;
            });

            $categorySearch = array_filter(\YunShop::request()->category, function ($item) {
                if (is_array($item)) {
                    return !empty($item[0]);
                }
                return !empty($item);
            });

            if ($categorySearch) {
                $requestSearch['category'] = $categorySearch;
            }
        }
//        $catetory_menus = CategoryService::getCategoryMenu(
//            [
//                'catlevel' => $this->shopset['cat_level'],
//                'ids'   => isset($categorySearch) ? array_values($categorySearch) : [],
//            ]
//        );

        $catetory_menus = CategoryService::getCategoryMultiMenuSearch(
            [
                'catlevel' => $this->shopset['cat_level'],
                'ids'   => isset($categorySearch) ? array_values($categorySearch) : [],
            ]
        );

        $list = Goods::Search($requestSearch)->pluginId()->orderBy('display_order', 'desc')->orderBy('yz_goods.id', 'desc')->paginate(20);
        $pager = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());


        $edit_url = 'plugin.tbk.admin.goods.edit';
        $delete_url = 'goods.goods.destroy';
        $delete_msg = '确认删除此商品？';
        $sort_url = 'goods.goods.displayorder';
        return view('Yunshop\Tbk::admin.goods.index', [
            'list' => $list,
            'pager' => $pager,
            //'status' => $status,
            'brands' => $brands,
            'requestSearch' => $requestSearch,
            'var' => \YunShop::app()->get(),
            'catetory_menus' => $catetory_menus,
            'shopset' => $this->shopset,
            'lang' => $this->lang,
            'product_attr_list' => $product_attr_list,
            'yz_url' => 'yzWebUrl',
            'edit_url' => $edit_url,
            'delete_url' => $delete_url,
            'delete_msg' => $delete_msg,
            'sort_url'  => $sort_url,
            'product_attr'  => $requestSearch['product_attr'],
            'copy_url' => 'goods.goods.copy'
        ])->render();
    }

    public function edit(\Request $request)
    {
        //todo 所有操作去service里进行，供应商共用此方法。
        $goods_service = new EditGoodsService($request->id, \YunShop::request());
        if (!$goods_service->goods) {
            return $this->message('未找到商品或已经被删除', '', 'error');
        }
        $result = $goods_service->edit();
        if ($result['status'] == 1) {
            Cache::flush();
            return $this->message('商品修改成功', Url::absoluteWeb('goods.goods.index'));
        } else if ($result['status'] == -1){
            if (isset($result['msg'])) {
                $this->error($result['msg']);
            }
            !session()->has('flash_notification.message') && $this->error('商品修改失败');
        }

        //dd($this->lang);
        return view('Yunshop\Tbk::admin.goods.goods', [
            'goods' => $goods_service->goods_model,
            'lang' => $this->lang,
            'params' => collect($goods_service->goods_model->hasManyParams)->toArray(),
            'allspecs' => collect($goods_service->goods_model->hasManySpecs)->toArray(),
            'html' => $goods_service->optionsHtml,
            'var' => \YunShop::app()->get(),
            'brands' => $goods_service->brands,
            'catetory_menus' => implode('', $goods_service->catetory_menus),
            'virtual_types' => [],
            'shopset' => $this->shopset,
            'type' => 'edit'
        ])->render();
    }

    public function displayorder()
    {
        $displayOrders = \YunShop::request()->display_order;
        foreach($displayOrders as $id => $displayOrder){
            $goods = \app\common\models\Goods::find($id);
            $goods->display_order = $displayOrder;
            $goods->save();
        }
        return $this->message('商品排序成功', Url::absoluteWeb('goods.goods.index'));
        //$this->error($goods);
    }
}