<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/23
 * Time: 上午10:15
 */

namespace Yunshop\Supplier\supplier\controllers\goods;

use app\backend\modules\goods\services\CopyGoodsService;
use app\backend\modules\goods\services\CreateGoodsService;
use app\backend\modules\goods\services\EditGoodsService;
use app\common\helpers\Url;
use app\common\services\Session;
use Illuminate\Http\Request;
use Setting;
use Yunshop\Supplier\admin\models\SupplierGoods;
use Yunshop\Supplier\common\controllers\SupplierCommonController;

class GoodsOperationController extends SupplierCommonController
{
    public function add(Request $request)
    {
        $goods_service = new CreateGoodsService($request, 1);
//        dd($goods_service);
        $result = $goods_service->create();

        if (isset($goods_service->error)) {
            $this->error($goods_service->error);
        }
        if ($result['status'] == 1) {
            return $this->message('商品创建成功', Url::absoluteWeb('plugin.supplier.supplier.controllers.goods.supplier-goods-list'));
        } else if ($result['status'] == -1) {
            $this->error('商品创建失败');
        }

        return view('Yunshop\Supplier::supplier.goods.supplier_goods_op', [
            'goods' => $goods_service->goods_model,
            'lang' => $this->lang(),
            'params' => collect($goods_service->params)->toArray(),
            'brands' => collect($goods_service->brands)->toArray(),
            'allspecs' => [],
            'html' => '',
            'var' => \YunShop::app()->get(),
            'catetory_menus' => $goods_service->catetory_menus,
            'virtual_types' => [],
            'shopset' => Setting::get('shop.category')
        ])->render();
    }

    public function edit(Request $request)
    {
        $supplier_goods = \Yunshop\Supplier\common\models\SupplierGoods::select()->where('supplier_id', Session::get('supplier')['id'])->where('goods_id', $request->id)->first();
        if (!$supplier_goods) {
            return $this->message('该商品不属于你', Url::absoluteWeb('plugin.supplier.supplier.controllers.goods.supplier-goods-list'), 'error');
        }
        $goods_service = new \Yunshop\Supplier\common\services\goods\EditGoodsService($request->id, \YunShop::request(), 1);
        $result = $goods_service->edit();
        if ($result['status'] == 1) {
            return $this->message('商品修改成功', Url::absoluteWeb('plugin.supplier.supplier.controllers.goods.supplier-goods-list'));
        } else if ($result['status'] == -1){
            $this->error('商品修改失败');
        }

        return view('Yunshop\Supplier::supplier.goods.supplier_goods_op', [
            'goods' => $goods_service->goods_model,
            'lang' => $this->lang(),
            'params' => collect($goods_service->goods_model->hasManyParams)->toArray(),
            'allspecs' => collect($goods_service->goods_model->hasManySpecs)->toArray(),
            'html' => $goods_service->optionsHtml,
            'var' => \YunShop::app()->get(),
            'brands' => collect($goods_service->brands)->toArray(),
            'catetory_menus' => implode('', $goods_service->catetory_menus),
            'virtual_types' => [],
            'shopset' => Setting::get('shop.category'),
            'type' => 'edit'
        ])->render();
    }

    public function copy()
    {
        $id = intval(request()->id);
        if (!$id) {
            $this->error('请传入正确参数.');
        }

        $result = CopyGoodsService::copyGoods($id);
        if (!$result) {
            $this->error('商品不存在.');
        }
        $result->status = 0;
        $result->save();
        \Yunshop\Supplier\common\models\SupplierGoods::create([
            'goods_id'  => $result->id,
            'supplier_id'   => Session::get('supplier')['id'],
            'member_id'     => Session::get('supplier')['member_id']
        ]);
        return $this->message('商品复制成功', Url::absoluteWeb('plugin.supplier.supplier.controllers.goods.supplier-goods-list'));
    }

    public function delete()
    {
        $goods_id = request()->id;
        $goods_model = \app\common\models\Goods::find($goods_id);
        $goods_model->delete();
        $supplierGoods = SupplierGoods::where('goods_id', $goods_id)->delete();
        if (goods_model && $supplierGoods){
            return $this->successJson('删除成功');
        }
        // return $this->message('删除成功', Url::absoluteWeb('plugin.supplier.supplier.controllers.goods.supplier-goods-list'));
    }

    public function batchDelete()
    {
        $ids = request()->ids;
        foreach ($ids as $id) {
            $goods_model = \app\common\models\Goods::find($id);
            $goods = $goods_model->delete();
            \Log::debug('供应商商品删除',$id);
            $supplierGoods = SupplierGoods::where('goods_id', $id)->delete();
        }
        if (goods && $supplierGoods){
            return $this->successJson('删除成功');
        }
    
    }

//    public function sort()
//    {
//        $displayOrders = request()->display_order;
//        foreach($displayOrders as $id => $displayOrder){
//            $goods = \app\common\models\Goods::find($id);
//            $goods->display_order = $displayOrder;
//            $goods->save();
//        }
//        return $this->message('商品排序成功', Url::absoluteWeb('plugin.supplier.supplier.controllers.goods.supplier-goods-list'));
//    }

    public function sort()
    {
        $id = request()->id;
        $value = request()->value;

        $goods = \app\common\models\Goods::find($id);
        $goods->display_order = $value;
        if ($goods->save()){
            return $this->successJson('修改成功');
        }else{
            $this->errorJson('修改失败');
        }
    }

    public function change()
    {
        $id = request()->id;
        $field = request()->type;
        $goods = \app\common\models\Goods::find($id);

//        $goods->$field = \YunShop::request()->value;
//        $goods->status = 0;
//        $goods->save();

        $goods->$field = request()->value;
        if ($goods->save()){
            return $this->successJson('修改成功');
        }else{
            $this->errorJson('修改失败');
        }

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
}