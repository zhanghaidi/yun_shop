<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/17
 * Time: 下午7:46
 */

namespace Yunshop\Micro\frontend\controllers\MicroShop;


use app\common\components\ApiController;
use Yunshop\Micro\common\models\MicroShop;
use Yunshop\Micro\common\models\MicroShopGoods;

class SetGoodsController extends ApiController
{
    public function index()
    {
        $micro_shop = MicroShop::getMicroShopByMemberId(\YunShop::app()->getMemberId());
        if (!isset($micro_shop)) {
            return $this->errorJson('您还不是微店！', [
                'status'    => -1
            ]);
        }
        $goods_ids = \YunShop::request()->goods_ids;
        if (!$goods_ids) {
            return $this->errorJson('请选择商品', [
                'status'    => -1
            ]);
        }
        $goods_ids = substr($goods_ids, 0, strlen($goods_ids) - 1);
        $goods_ids = explode(',', $goods_ids);
        $goods_data = [
            'uniacid'   => \YunShop::app()->uniacid,
            'shop_id'   => $micro_shop->id,
            'member_id' => $micro_shop->member_id
        ];
        foreach ($goods_ids as $goods_id) {
            $goods_data['goods_id'] = $goods_id;
            $result = MicroShopGoods::getGoods($micro_shop->id, $goods_id);
            if ($result) {
                continue;
            }
            $result = MicroShopGoods::create($goods_data);
            if (!$result) {
                return $this->errorJson('选取商品失败', [
                    'status'    => -1
                ]);
            }
        }
        return $this->successJson('选取商品成功', [
            'status'    => 1
        ]);
    }
}