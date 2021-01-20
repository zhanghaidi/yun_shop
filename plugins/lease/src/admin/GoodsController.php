<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/3/7
 * Time: 14:31
 */

namespace Yunshop\LeaseToy\admin;

use app\api\model\Good;
use app\backend\modules\goods\models\Brand;
use app\backend\modules\goods\models\Category;
use app\backend\modules\goods\models\GoodsOption;
use app\backend\modules\goods\models\GoodsSpecItem;
use app\backend\modules\goods\models\Sale;
use app\backend\modules\goods\services\CopyGoodsService;
use app\backend\modules\goods\services\GoodsOptionService;
use app\backend\modules\goods\services\GoodsService;
use app\common\components\BaseController;
use app\backend\modules\goods\services\CategoryService;
use app\backend\modules\goods\models\GoodsParam;
use app\backend\modules\goods\models\GoodsSpec;
use app\common\components\Widget;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use app\common\models\GoodsCategory;
use app\frontend\modules\coupon\listeners\CouponSend;
use Setting;
use Yunshop\LeaseToy\models\Goods;
use app\backend\modules\goods\services\CreateGoodsService;
use app\backend\modules\goods\services\EditGoodsService;


class GoodsController extends \app\backend\modules\goods\controllers\GoodsController
{

    const MESSAGEJUMP = 'plugin.lease-toy.admin.goods.index';

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
                return !empty($item);
            });

            if ($categorySearch) {
                $requestSearch['category'] = $categorySearch;
            }
        }
        $catetory_menus = CategoryService::getCategoryMenu(
            [
                'catlevel' => $this->shopset['cat_level'],
                'ids'   => isset($categorySearch) ? array_values($categorySearch) : [],
            ]
        );

        $list = Goods::Search($requestSearch)->pluginId()->orderBy('display_order', 'desc')->orderBy('yz_goods.id', 'desc')->paginate(20);

        $pager = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());



        $edit_url = 'plugin.lease-toy.admin.goods.edit';
        $delete_url = 'plugin.lease-toy.admin.goods.destroy';
        $delete_msg = '确认删除此商品？';
        $sort_url = 'plugin.lease-toy.admin.goods.displayorder';
        return view('Yunshop\LeaseToy::admin.goods.index', [
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
            'copy_url' => 'plugin.lease-toy.admin.goods.copy'
        ])->render();
    }

      public function copy()
    {
        $id = intval(\YunShop::request()->id);
        if (!$id) {
            $this->error('请传入正确参数.');
        }

        $result = CopyGoodsService::copyGoods($id);
        if (!$result) {
            $this->error('商品不存在.');
        }
        return $this->message('商品复制成功', Url::absoluteWeb(self::MESSAGEJUMP));
    }

    public function create(\Request $request)
    {
        $goods_service = new CreateGoodsService($request);
        $result = $goods_service->create();

        if (isset($goods_service->error)) {
            $this->error($goods_service->error);
        }
        if ($result['status'] == 1) {
            return $this->message('商品创建成功', Url::absoluteWeb(self::MESSAGEJUMP));
        } else if ($result['status'] == -1) {
            if (isset($result['msg'])) {
                $this->error($result['msg']);
            }

            !session()->has('flash_notification.message') && $this->error('商品修改失败');
        }

        return view('goods.goods', [
            'goods' => $goods_service->goods_model,
            'lang' => $this->lang,
            'params' => $goods_service->params->toArray(),
            'brands' => $goods_service->brands->toArray(),
            'allspecs' => [],
            'html' => '',
            'var' => \YunShop::app()->get(),
            'catetory_menus' => $goods_service->catetory_menus,
            'virtual_types' => [],
            'shopset' => $this->shopset
        ])->render();
    }

       public function edit(\Request $request)
    {

        $goods_service = new EditGoodsService($request->id, \YunShop::request());
        if (!$goods_service->goods) {
            return $this->message('未找到商品或已经被删除', '', 'error');
        }
        $result = $goods_service->edit();
        if ($result['status'] == 1) {
            return $this->message('商品修改成功', Url::absoluteWeb(self::MESSAGEJUMP));
        } else if ($result['status'] == -1){
            if (isset($result['msg'])) {
                $this->error($result['msg']);
            }
            !session()->has('flash_notification.message') && $this->error('商品修改失败');
        }

        //dd($this->lang);
        return view('goods.goods', [
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
        return $this->message('商品排序成功', Url::absoluteWeb(self::MESSAGEJUMP));
        //$this->error($goods);
    }

    public function destroy()
    {
        $id = \YunShop::request()->id;
        $goods = Goods::destroy($id);
        return $this->message('商品删除成功', Url::absoluteWeb(self::MESSAGEJUMP));
    }
}