<?php

/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/9/4
 * Time: 下午1:46
 */

namespace Yunshop\Supplier\frontend;

use app\backend\modules\goods\services\CreateGoodsService;
use app\common\exceptions\ShopException;
use app\common\models\Category;
use app\common\services\MiniFileLimitService;
use Illuminate\Support\Facades\Input;
use Yunshop\Supplier\admin\models\Supplier;
use Yunshop\Supplier\common\models\SupplierGoods;
use app\common\models\Goods;
use Yunshop\Supplier\common\services\goods\EditGoodsService;

class GoodsController extends \app\frontend\modules\goods\controllers\GoodsController
{
    public function getGoodsList()
    {
        $sid = intval(request()->sid);
        if ($sid == 0) {
            throw new ShopException('供应商SID错误');
        }
        $supplier = Supplier::getSupplierById($sid);
        if (!$supplier) {
            throw new ShopException('此SID不是供应商');
        }

        $member_id = \YunShop::app()->getMemberId();
        if ($supplier->member_id != $member_id) {
            $memberSupplier = Supplier::uniacid()->where('member_id',$member_id)->first();
            if (!empty($memberSupplier)) {
                return $this->errorJson('没有权限,跳转会员中心!', ['url'=> yzAppFullUrl('member')]);
            } else {
                return $this->errorJson('没有权限,跳转供应商申请!', ['url'=> yzAppFullUrl('member/supplier')]);
            }
        }

        $supplier->logo = replace_yunshop(tomedia($supplier->logo));
        $goods_ids = SupplierGoods::getGoodsIdsBySid($sid);
        if ($goods_ids->isEmpty()) {
            throw new ShopException('此供应商没有商品');
        }
        $list = \Yunshop\Supplier\frontend\models\SupplierGoods::selectRaw('*, id as goods_id')
            ->whereIn("id", $goods_ids)
            ->paginate(15)
            ->toArray();

        if (empty($list['data'])) {
            throw new ShopException('没有找到商品');
        }

        return $this->successJson('成功', [
            'list' => $list,
            'supplier' => $supplier
        ]);
    }

    //添加商品
    public function addGoods()
    {
        $shop_set = \Setting::get('shop.category');
        $ingress = request()->ingress;

        $member_id = \YunShop::app()->getMemberId();
        $supplier_model = \Yunshop\Supplier\common\models\Supplier::uniacid()->where('member_id', $member_id)->first();
        // 商品基本信息
        $request_data = [
            'title' => request()->title,
            'thumb' => request()->thumb,
            'cost_price' => request()->cost_price,
            'market_price' => request()->market_price,
            'price' => request()->price,
            'virtual_sales' => request()->virtual_sales,
            'sku' => request()->sku,
            'stock' => request()->stock,
        ];

        if ($ingress) {
            $pass = true;
            foreach ($request_data as $key => $check) {
                if ($key == 'thumb') {
                    continue;
                }

                $check_result = (new MiniFileLimitService())->checkMsg($check);
                if ($check_result['errcode'] != 0) {
                    $pass = false;
                    break;
                }
            }
            if ($pass == false) {
                return $this->errorJson('输入信息含有违法违规内容');
            }
        }

        $content = request()->good_content;
        $thumb_url = request()->thumb_url;
        $request_data['thumb_url'] = serialize($thumb_url);
        // 商品分类

        if ($shop_set['cat_level'] != 3) {
            $category = [
                'parentid' => request()->category_pid,
                'childid' => request()->category_cid,
            ];
        }else{
            $category = [
                'parentid' => request()->category_pid,
                'childid' => request()->category_cid,
                'thirdid' => request()->category_tid
            ];
        }

        if (!$request_data) {
            return $this->errorJson('数据为空');
        }
        if (!$category) {
            return $this->errorJson('请选择商品分类');
        }
        // 默认配送方式
        $widgets = [
            'dispatch' => [
                'dispatch_type' => 1,
                'dispatch_price' => 0
            ],
            'commission' => [
                'is_commission' => 1
            ],
            'area_dividend' => [
                'is_dividend' => 1
            ],
            'team_dividend' => [
                'is_dividend' => 1
            ],
            'merchant' =>[
                'is_open_bonus_center' => 1,
                'is_open_bonus_staff' => 1
            ],
            'sale' =>[
                'max_point_deduct' => '',
                'point' => '',
            ],
        ];

        if (is_array($content)) {
            foreach ($content as $value) {
                $request_data['content'] = $request_data['content'].'<p><img src="' . replace_yunshop(yz_tomedia($value,'image')) . '" width="100%" alt="8.jpg"/></p>';
            }
        }

        // 虚拟销量
        if (!$request_data['virtual_sales']) {
            $request_data['virtual_sales'] = 0;
        }

        //默认商品设置
        $defult_data = [
            'brand_id' => 0,
            'type' => 1,//类型1实体商品2虚拟商品
            'status' => 0,//1上架0下架
            'is_deleted' => 0,
            'comment_num' => 0,
            'is_plugin' => 1,
            'plugin_id'=>\Yunshop\Supplier\common\models\Supplier::PLUGIN_ID,
            'reduce_stock_method' => 0,//减库存方式
        ];
        $goods_data = array_merge($request_data,$defult_data);

        // 填充模型
        // 创建新的商品模型
        $goods_model = new \app\backend\modules\goods\models\Goods();
        $goods_model->setRawAttributes($goods_data);
        // 商品挂件[ps:这里只是配送方式]
        $goods_model->widgets = $widgets;
        $goods_model->uniacid = \YunShop::app()->uniacid;
        // 重量
        $goods_model->weight = $goods_model->weight ? $goods_model->weight : 0;
        // 保存验证
        $validator = $goods_model->validator($goods_model->getAttributes());
        if ($validator->fails()) {
            throw new AppException($validator->messages());
        } else {
            // 保存成功
            if ($goods_model->save()) {
                // 保存商品分类
                    \app\backend\modules\goods\services\GoodsService::saveGoodsMultiNewCategory($goods_model, $category, \Setting::get('shop.category'));

                // 供应商商品记录
                SupplierGoods::create(
                    [
                        'goods_id'      => $goods_model->id,
                        'supplier_id'   => $supplier_model->id,
                        'member_id'     => $member_id,
                    ]
                );
                return $this->successJson('上传商品成功');
            }
            return $this->errorJson('上传商品失败');
        }
    }

    //编辑商品
    public function editGoods()
    {
        $shop_set = \Setting::get('shop.category');
        $ingress = request()->ingress;

        $goods_id = intval(request()->goods_id);
        if (!$goods_id) {
            return $this->errorJson('请传入正确参数.');
        }

        if ($shop_set['cat_level'] != 3) {
            $category = [
                'parentid' => request()->category_pid,
                'childid' => request()->category_cid,
            ];
        }else{
            $category = [
                'parentid' => request()->category_pid,
                'childid' => request()->category_cid,
                'thirdid' => request()->category_tid
            ];
        }

        $goods_data = [
            'title' => request()->title,
            'thumb' => request()->thumb,
            'cost_price' => request()->cost_price,
            'market_price' => request()->market_price,
            'price' => request()->price,
            'virtual_sales' => request()->virtual_sales,
            'sku' => request()->sku,
            'stock' => request()->stock,
        ];

        if ($ingress) {
            $pass = true;
            foreach ($goods_data as $key => $check) {
                if ($key == 'thumb') {
                    continue;
                }

                $check_result = (new MiniFileLimitService())->checkMsg($check);
                if ($check_result['errcode'] != 0) {
                    $pass = false;
                    break;
                }
            }
            if ($pass == false) {
                return $this->errorJson('输入信息含有违法违规内容');
            }
        }

        $goods_data['thumb_url'] = serialize(request()->thumb_url);

        $goods_model = \Yunshop\Supplier\common\models\Goods::uniacid()->with('hasManyGoodsCategory')->find($goods_id);
        $goods_data['status'] = 0;
        $category_model = \app\common\models\GoodsCategory::where('goods_id', $goods_id)->first();
        if (!empty($category_model)) {
            $category_model->delete();
        }

        \app\backend\modules\goods\services\GoodsService::saveGoodsMultiNewCategory($goods_model, $category, \Setting::get('shop.category'));

        $goods_model->setRawAttributes($goods_data)->save();


        if ($goods_model) {
            return $this->successJson('修改商品成功', $goods_model);
        }
        return $this->errorJson('请检查商品数据');
    }

    //商品详情
    public function goodsDetail()
    {
        $goods_id = intval(request()->goods_id);
        if (!$goods_id) {
            return $this->errorJson('请传入正确参数.');
        }

        $goodsModel = \Yunshop\Supplier\common\models\Goods::uniacid()->where('id',$goods_id)->with('hasManyGoodsCategory')->first();
        $category_ids = explode(',', $goodsModel->hasManyGoodsCategory[0]->category_ids);
        //按大小排序
        array_multisort($category_ids,SORT_ASC, $category_ids);
        $goodsModel['category_ids'] = [
            'parentid' => $category_ids[0],
            'childid' => $category_ids[1],
            'thirdid' => $category_ids[2]
        ];
        if (strexists($goodsModel->thumb, 'image/')) {
            $goodsModel->thumb = replace_yunshop(yz_tomedia($goodsModel->thumb,'image'));
        } else {
            $goodsModel->thumb = replace_yunshop(yz_tomedia($goodsModel->thumb));
        }

        if ($goodsModel->thumb_url) {
            $thumb_url = unserialize($goodsModel->thumb_url);
            foreach ($thumb_url as $key => $url) {
                if (strexists($url, 'image/')) {
                    $thumb_url[$key] = replace_yunshop(yz_tomedia($url,'image'));
                } else {
                    $thumb_url[$key] = replace_yunshop(yz_tomedia($url));
                }
            }
            $goodsModel->thumb_url = $thumb_url;
        };

        return $this->successJson('获取商品详情成功', $goodsModel);
    }

    //获取所有分类
    public function getCategory()
    {
        $shop_set = \Setting::get('shop.category');

        if ($shop_set['cat_level'] != 3) {
            $list = Category::uniacid()->where('plugin_id', 0)->where('parent_id', 0)->get();
            $list->map(function ($category) {
                $category->childrens = Category::uniacid()->where('plugin_id', 0)->where('parent_id', $category->id)->get();
            });
        }else{
            $list = Category::uniacid()->where('plugin_id', 0)->where('parent_id', 0)->get();
            $list->map(function ($category){
                $category->childrens = Category::uniacid()->where('plugin_id', 0)->where('parent_id', $category->id)->get();
                $category->childrens->map(function ($category) {
                    $category->childrens = Category::uniacid()->where('plugin_id', 0)->where('parent_id', $category->id)->get()->toArray();
                });
            });
        }

        if (!empty($list)) {
            return $this->successJson('获取分类成功!', $list);
        }
    }

    //通过商品二级分类ID获取商品列表
    public function getGoodsByCategoryId()
    {
        $category_id = intval(request()->category_id);
        if (!$category_id) {
            return $this->errorJson('参数错误');
        }
        $list = \Yunshop\Supplier\common\models\Goods::getGoodsList(['category' => $category_id])->whereStatus(1)->orderBy('display_order', 'desc')->orderBy('yz_goods.id', 'desc')->get();

        if ($list->isEmpty()) {
            return $this->errorJson('该分类下没有商品');
        }

        $list->map(function($goods_model){
            $goods_model->buyNum = 0;
            if (strexists($goods_model->thumb, 'image/')) {
                $goods_model->thumb = yz_tomedia($goods_model->thumb,'image');
            } else {
                $goods_model->thumb = yz_tomedia($goods_model->thumb);
            }
        });
        if ($list->isEmpty()) {
            return $this->errorJson('该分类下没有商品');
        }


        return $this->successJson('获取商品成功', $list);
    }

    //删除商品
    public function delGoods()
    {
        $success_code = 1;
        $goods_id = \YunShop::request()->get('goods_id');
        if (!$goods_id) {
            return $this->errorJson('请传入正确参数!');
        }
        $goods_model = Goods::find($goods_id);
        $goods_model->delete();
        SupplierGoods::where('goods_id', $goods_id)->delete();
        return $this->successJson('删除商品成功!', $success_code);
    }

    //上传图片
    public function upload()
    {
        $file = request()->file('file');
        if (!$file) {
            return $this->errorJson('请传入正确参数.');
        }
        if ($file->isValid()) {
            // 获取文件相关信息
            $originalName = $file->getClientOriginalName(); // 文件原名
            $realPath = $file->getRealPath();   //临时文件的绝对路径
            $ext = $file->getClientOriginalExtension();

            $defaultImgType = [
                'jpg', 'bmp', 'eps', 'gif', 'mif', 'miff', 'png', 'tif',
                'tiff', 'svg', 'wmf', 'jpe', 'jpeg', 'dib', 'ico', 'tga', 'cut', 'pic'
            ];

            if (!in_array($ext, $defaultImgType)) {
                return $this->errorJson('非规定类型的文件格式');
            }

            $newOriginalName = md5($originalName . str_random(6)) . '.' . $ext;

            \Storage::disk('image')->put($newOriginalName, file_get_contents($realPath));

            return $this->successJson('上传成功', [
                'img'    => \Storage::disk('image')->url($newOriginalName),
            ]);
        }

    }

}
