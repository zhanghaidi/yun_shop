<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/3/2
 * Time: 下午3:58
 */

namespace app\LeaseToy\api;


use app\common\components\ApiController;
use Yunshop\LeaseToy\models\MemberCart;

class IndexController extends ApiController
{
    /** 是否开启插件
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function isEnabled()
    {
        $memberId = \YunShop::app()->getMemberId();
        $data = [
            'is_enabled' => 0,
            'cart_goods' => 0
        ];

        if (app('plugins')->isEnabled('lease-toy')) {
            $data['isEnabled'] = 1;
        }

        if ($memberId) {
            $cart = new MemberCart();
            $cart_goods = $cart->carts()->where('member_id', $memberId)->count();

            $data['cart_goods'] = $cart_goods;
        }

        return $this->successJson('租赁插件', $data);
    }
}