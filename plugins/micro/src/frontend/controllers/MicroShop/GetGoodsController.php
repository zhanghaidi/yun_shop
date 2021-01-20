<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/19
 * Time: 下午5:07
 */

namespace Yunshop\Micro\frontend\controllers\MicroShop;


use app\common\components\ApiController;
use app\common\models\Goods;
use Yunshop\Micro\common\models\MicroShop;
use Yunshop\Micro\common\models\MicroShopGoods;
use Yunshop\Micro\common\services\TimedTaskService;

class GetGoodsController extends ApiController
{
    public function index()
    {
        $micro_shop = MicroShop::getMicroShopByMemberId(\YunShop::app()->getMemberId());
        if (!$micro_shop) {
            return $this->errorJson('您还不是微店', [
                'status'    => -1
            ]);
        }
        $goods_list = MicroShopGoods::getGoodsByMemberId(\YunShop::app()->getMemberId());
        if ($goods_list->isEmpty()) {
            return $this->errorJson('未找到商品', [
                'status'    => -1
            ]);
        } else {
            return $this->successJson('成功', [
                'status'    => 1,
                'list'      => $this->goodsImage($goods_list),
                'count'     => $goods_list->count()
            ]);
        }
    }

    public function goodsImage($list)
    {
        return $list->map(function($goods){
            if ($goods->hasOneGoods) {
                $goods->hasOneGoods->thumb = yz_tomedia($goods->hasOneGoods->thumb);
            }

            return $goods;
        });
    }
}