<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/21
 * Time: 下午3:35
 */

namespace Yunshop\Supplier\admin\controllers\goods;

use app\backend\modules\goods\services\EditGoodsService;
use app\common\components\BaseController;
use Illuminate\Http\Request;
use Setting;
use Yunshop\Supplier\common\models\Goods;

class GoodsOperationController extends BaseController
{
    public function edit($request)
    {
        $goods_model = Goods::find($request->id);
        if (!$goods_model) {
            return $this->message('商品不存在,或不属于供应商商品', '', 'error');
        }
        $goods_service = new \Yunshop\Supplier\common\services\goods\EditGoodsService($request->id, $request);
        $result = $goods_service->edit();
        if ($result['status'] == 1) {
            return $this->message('商品修改成功');
        } else if ($result['status'] == -1){
            $this->error('商品修改失败');
        }

        return view('Yunshop\Supplier::admin.goods.supplier_goods_op', [
            'goods' => $goods_service->goods_model,
            'params' => $goods_service->goods_model->hasManyParams->toArray(),
            'allspecs' => $goods_service->goods_model->hasManySpecs->toArray(),
            'html' => $goods_service->optionsHtml,
            'var' => \YunShop::app()->get(),
            'brands' => $goods_service->brands->toArray(),
            'catetory_menus' => implode('', $goods_service->catetory_menus),
            'virtual_types' => [],
            'shopset' => Setting::get('shop.category'),
            'lang' => $this->lang(),
            'type' => 'edit'
        ])->render();
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