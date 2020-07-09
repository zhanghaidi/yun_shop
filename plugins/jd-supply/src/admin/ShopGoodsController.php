<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/27
 * Time: 17:53
 */

namespace Yunshop\JdSupply\admin;

use app\backend\modules\goods\controllers\GoodsController;
use app\backend\modules\goods\models\Brand;
use app\common\exceptions\ShopException;
use app\common\helpers\Url;
use Yunshop\JdSupply\models\Category;
use Yunshop\JdSupply\models\Goods;
use Yunshop\JdSupply\models\JdGoods;
use Yunshop\JdSupply\services\JdGoodsService;

class ShopGoodsController  extends GoodsController
{
    protected $success_url = 'plugin.jd-supply.admin.shop-goods.index';

    public function index()
    {


/*        //增加商品属性搜索
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


        $add_url = 'plugin.jd-supply.admin.shop-goods.add';
        $edit_url = 'plugin.jd-supply.admin.shop-goods.edit';
        $delete_url = 'plugin.jd-supply.admin.shop-goods.destroy';
        $delete_msg = '确认删除此商品？';
        $sort_url = 'plugin.jd-supply.admin.shop-goods.displayorder';
        return view('Yunshop\JdSupply::admin.goods.index', [
            'list' => $list,
            'pager' => $pager,
            //课程商品id
            'courseGoods_ids' => [],
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
            'copy_url' => 'plugin.jd-supply.admin.shop-goods.copy',
            'add_url' => $add_url,
        ])->render();*/

        return view('Yunshop\JdSupply::admin.goods.index')->render();
    }


    public function goodsSearch()
    {
        $requestSearch = request()->search;
        $page = request()->page;
        if ($requestSearch) {
            $requestSearch = array_filter($requestSearch, function ($item) {
                return $item !== '';// && $item !== 0;
            });

            $categorySearch = array_filter(request()->category, function ($item) {
                if (is_array($item)) {
                    return !empty($item[0]);
                }
                return !empty($item);
            });

            if ($categorySearch) {
                $requestSearch['category'] = $categorySearch;
            }
        }
        $page_size = $requestSearch['page_size']?:20;
        $list = Goods::Search($requestSearch)->pluginId()->orderBy('display_order', 'desc')->orderBy('yz_goods.id', 'desc')->paginate($page_size);
        foreach ($list as $key => $item){
            $list[$key]['thumb']  = yz_tomedia($item->thumb);

            $list[$key]['link'] = yzAppFullUrl('goods/'.$item['id']);
        }

        if($list){
            return $this->successJson('成功',$list);
        }else{
            return $this->errorJson('找不到数据');
        }

    }


    /**
     * 商品列表页数据
     */
    public function goodsList()
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

        $catetory_menus = [
            'catlevel' => $this->shopset['cat_level'],
            'ids'   => Category::shopCategory()//CategoryFactory::create('shop'),
        ];
        $list = Goods::Search($requestSearch)->pluginId()->orderBy('display_order', 'desc')->orderBy('yz_goods.id', 'desc')->paginate(20);
        foreach ($list as $key => $item){
            $list[$key]['thumb']  = yz_tomedia($item->thumb);
            $list[$key]['link'] = yzAppFullUrl('goods/'.$item['id']);

        }

        $edit_url = 'plugin.jd-supply.admin.shop-goods.edit';
        $delete_url = 'plugin.jd-supply.admin.shop-goods.delete';
        $delete_msg = '确认删除此商品？';
        $sort_url = 'plugin.jd-supply.admin.shop-goods.displayorder';

        $data = [
            'list' => $list,
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
            'copy_url' => 'plugin.jd-supply.admin.shop-goods.copy',
        ];
        return $this->successJson('成功',$data);
    }


    public function updateJdGoods()
    {
        $id =  intval(request()->input('id'));

        if (empty($id)) {
            throw new ShopException('参数错误');
        }

        $goods_model =  Goods::select('id','uniacid','status')->with('hasOneJdGoods')->find($id);

        if (!$goods_model && !$goods_model->hasOneJdGoods) {
            throw new ShopException('商品不存在或已被删除');
        }

        $jd_goods  =  JdGoodsService::requestJd($goods_model->hasOneJdGoods->jd_goods_id);
        if ($jd_goods === false) {
            throw new ShopException('获取商品信息失败');
        }
        $bool = JdGoodsService::updateGoods($jd_goods['goods_id'],$jd_goods['jd_goods']);

        if ($bool) {
            return $this->message('更新成功', Url::absoluteWeb($this->success_url));
        }

        return $this->message('更新失败', Url::absoluteWeb($this->success_url), 'error');
    }


    public function delete()
    {
        $goods_id = request()->id;
        $goods_model = \app\common\models\Goods::find($goods_id);
        $goods_model->delete();
        $goods = JdGoods::where('goods_id', $goods_id)->delete();
        if ($goods_model && $goods){
            return $this->successJson('删除成功');
        }
    }

    public function batchDelete()
    {
        $ids = request()->ids;
        foreach ($ids as $id) {
            $goods_model = \app\common\models\Goods::find($id);
            $goods = $goods_model->delete();
            \Log::debug('聚合供应链商品删除',$id);
            $jdGoods = JdGoods::where('goods_id', $id)->delete();
        }
        if ($goods && $jdGoods){
            return $this->successJson('删除成功');
        }

    }

    public function batchUpdate()
    {
        $ids = request()->ids;
        $success = 0;
        $fail = 0;
        foreach ($ids as $id) {
            $result = $this->update($id);
            if ($result) {
                $success += 1;
            } else {
                $fail += 1;
            }
        }
        if ($result){
            return $this->successJson("更新成功：{$success} 个,更新失败：{$fail} 个",Url::absoluteWeb($this->success_url));
        }

    }

    public function update($id)
    {
        if (empty($id)) {
            return false;
        }

        $goods_model =  Goods::select('id','uniacid','status')->with('hasOneJdGoods')->find($id);

        if (!$goods_model && !$goods_model->hasOneJdGoods) {
            return false;
        }

        $jd_goods  =  JdGoodsService::requestJd($goods_model->hasOneJdGoods->jd_goods_id);
        if ($jd_goods === false) {
            return false;
        }
        $bool = JdGoodsService::updateGoods($jd_goods['goods_id'],$jd_goods['jd_goods']);
        return true;
    }
}