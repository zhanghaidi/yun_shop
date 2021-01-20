<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/9/12
 * Time: 下午5:34
 */

namespace Yunshop\Mryt\store\admin;


use app\common\components\BaseController;
use app\common\exceptions\ShopException;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use app\common\models\Brand;
use Yunshop\Mryt\store\common\controller\CommonController;
use Yunshop\Mryt\store\models\Store;
use Yunshop\Mryt\store\services\CategoryService;
//use Yunshop\Mryt\store\service\EditGoodsService;
use Yunshop\Mryt\store\common\models\Goods;

class GoodsController extends CommonController
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
        $requestSearch = request()->search;
        $store_id = request()->store_id;

        if ($requestSearch) {
            $requestSearch = array_filter($requestSearch, function ($item) {
                return $item !== '';
            });

            $categorySearch = array_filter(\YunShop::request()->category, function ($item) {
                return !empty($item[0]);
            });

            if ($categorySearch) {
                $requestSearch['category'] = $categorySearch;
            }
        }
        $catetory_menus = CategoryService::getCategoryMenu(
            [
                'catlevel' => 2,
                'ids'   => isset($categorySearch) ? array_values($categorySearch) : [],
            ]
        );

        $store_ids = Store::select('id')->whereIn('uid', $this->child_ids)->get()->toArray();
        $store_ids = array_column($store_ids, 'id');
        if ($store_id) {
            if (in_array($store_id,$store_ids)) {
                $store_ids = [$store_id];
            } else {
                $this->error('无该门店ID');
            }
        }
        // $list = Goods::getGoodsList($requestSearch,  request()->store_id)->orderBy('display_order', 'desc')->orderBy('yz_goods.id', 'desc')->paginate(20)->toArray();
        $list = Goods::getGoodsList($requestSearch, $store_ids)
                       ->with(['hasOneStoreGoods' => function ($query) {
                            $query->with(['hasOneStore']);
                       }])
                       ->orderBy('display_order', 'desc')
                       ->orderBy('yz_goods.id', 'desc')
                       ->paginate(20);

        $list = $this->getMap($list)->toArray();

        $pager = PaginationHelper::show($list['total'], $list['current_page'], $list['per_page']);

        return view('Yunshop\Mryt::store.goods.list', [
            'list' => $list['data'],
            'pager' => $pager,
            'store_id' => $store_id,
            'brands' => $this->getBrands(),
            'requestSearch' => $requestSearch,
            'var' => \YunShop::app()->get(),
            'catetory_menus' => $catetory_menus,
            'lang' => $this->lang,
            'product_attr_list' => $this->getProductAttrList(),
            'product_attr'  => $requestSearch['product_attr'],
            'store_id' => request()->store_id
        ])->render();
    }

    public function edit()
    {
        $selectGoods = Goods::select()->where('id', request()->id)->first();
        if (!$selectGoods) {
            throw new ShopException('未找到商品');
        }
        $goods_service = new EditGoodsService(request()->id, \YunShop::request());
        if (!$goods_service->goods) {
            return $this->message('未找到商品或已经被删除', '', 'error');
        }
        $result = $goods_service->edit();
        if ($result['status'] == 1) {
            \app\common\models\Goods::where('id', request()->id)->update(['plugin_id' => Store::PLUGIN_ID]);
            return $this->message('商品修改成功', Url::absoluteWeb('plugin.store-cashier.admin.goods.index'));
        } else if ($result['status'] == -1){
            if (isset($result['msg'])) {
                $this->error($result['msg']);
            }
            !session()->has('flash_notification.message') && $this->error('商品修改失败');
        }

        return view('Yunshop\Mryt::admin.goods.goods', [
            'goods' => $goods_service->goods_model,
            'lang' => $this->lang,
            'params' => $goods_service->goods_model->hasManyParams->toArray(),
            'allspecs' => $goods_service->goods_model->hasManySpecs->toArray(),
            'html' => $goods_service->optionsHtml,
            'var' => \YunShop::app()->get(),
            'brands' => $goods_service->brands,
            'catetory_menus' => $goods_service->catetory_menus,
            'virtual_types' => [],
            'shopset' => \Setting::get('shop.category'),
            'store_id' => $selectGoods->hasOneStoreGoods->store_id
        ])->render();
    }

    public function setProperty()
    {
        $id = request()->id;
        $field = request()->type;
        $data = (request()->data == 1 ? '0' : '1');
        $goods = Goods::find($id);
        $goods->$field = $data;
        //dd($goods);
        $goods->save();
        \app\common\models\Goods::where('id', $id)->update(['plugin_id' => Store::PLUGIN_ID]);
        echo json_encode(["data" => $data, "result" => 1]);
    }

    public function change()
    {
        $id = request()->id;
        $field = request()->type;
        $goods = Goods::find($id);
        $goods->$field = request()->value;
        $goods->save();
        \app\common\models\Goods::where('id', $id)->update(['plugin_id' => Store::PLUGIN_ID]);
    }

    public  function SearchOrder(){//获取商品名称
        $keyword = request()->keyword;
        $goods= Goods::getSearchOrder($keyword);
        return view('goods.query', [
            'goods' => $goods->toArray(),
        ])->render();
    }

    private function getMap($list)
    {
        $list->map(function ($row){
            // $item['hasOneStoreGoods']['store_id']
            // \Log::info('--store_id-----store_uid--', [$row->hasOneStoreGoods->hasOneStore->uid, $row->hasOneStoreGoods->store_id]);
            $result = Store::getGoodsQrCodeUrl($row->id, $row->hasOneStoreGoods->store_id, $row->hasOneStoreGoods->hasOneStore->uid);
            $row->download_url = $result['url'];
            $row->img_name = $result['name'];
            $row->store_uid = $row->hasOneStoreGoods->hasOneStore->uid;
            $row->store_id = $row->hasOneStoreGoods->store_id;
        });

        return $list;
    }

    // plugin.store-cashier.admin.goods.getSearchGoods
    public function getSearchGoods()
    {
        $keyword = \YunShop::request()->keyword;
        $goods = \Yunshop\Mryt\common\models\Goods::getGoodsByName($keyword);
        if (!$goods->isEmpty()) {
            $goods = set_medias($goods->toArray(), array('thumb', 'share_icon'));
        }
        return view('Yunshop\Mryt::admin.goods.query', [
            'goods' => $goods
        ])->render();

    }

    public function getBrands()
    {
        return Brand::getBrands()->get()->toArray();
    }

    public function getProductAttrList()
    {
        return [
            'is_new' => '新品',
            'is_hot' => '热卖',
            'is_recommand' => '推荐',
            'is_discount' => '促销',
        ];
    }
}