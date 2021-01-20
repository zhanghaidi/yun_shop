<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/6/28
 * Time: 上午10:26
 */

namespace Yunshop\Micro\frontend\controllers\MicroShop;


use app\common\components\ApiController;
use Yunshop\Micro\common\models\MicroShopGoods;

class DelGoodsController extends ApiController
{
    public function index()
    {
        $id = \YunShop::request()->id;
        if (!isset($id) || empty($id)) {
            return $this->errorJson('参数错误', []);
        }
        $goods_model = MicroShopGoods::select()->whereId($id)->whereMemberId(\YunShop::app()->getMemberId())->first();
        if (!$goods_model) {
            return $this->errorJson('未找到商品', []);
        }
        $ret = $goods_model->delete();
        if ($ret) {
            return $this->successJson('删除成功', []);
        } else {
            return $this->errorJson('删除失败', []);
        }
    }
}