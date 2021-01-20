<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/30
 * Time: 13:51
 */

namespace Yunshop\HelpUserBuying\store\controller;

use app\frontend\modules\member\services\MemberCartService;
use app\frontend\modules\memberCart\MemberCartCollection;

class GoodsBuyController extends \Yunshop\StoreCashier\frontend\store\GoodsBuyController
{
    protected $publicAction = ['index'];
    protected $ignoreAction = ['index', 'validateParam', 'getMemberCarts', 'getShopOrder', 'getPluginOrders'];

    public function __construct()
    {

        parent::__construct();
    }

    protected function getMemberCarts()
    {

        $goods_params = json_decode(request()->input('goods'),true);


        $result = collect($goods_params)->map(function ($memberCart) {
            return MemberCartService::newMemberCart($memberCart);
        });
        $memberCarts = new MemberCartCollection($result);
        $memberCarts->loadRelations();
        return $memberCarts;

    }

    protected function validateParam()
    {
        $this->validate([
            'store_id' => 'required|integer|min:0',
        ]);
    }
}