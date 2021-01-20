<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/28
 * Time: 上午10:39
 */

namespace Yunshop\GroupPurchase\admin;

use app\common\components\BaseController;
use app\common\exceptions\AppException;
use app\common\modules\memberCart\MemberCartCollection;
use app\frontend\modules\member\services\MemberCartService;
use Yunshop\GroupPurchase\models\GroupPurchase;

class CreateOrderController extends BaseController
{
    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws AppException
     */
    public function index()
    {

        \Log::info('用户下单',request()->input());
        $this->validateParam();
        $trade = $this->getMemberCarts()->getTrade();

        return $this->successJson('成功', ['order_ids' => $trade->orders->pluck('id')->implode(',')]);
    }


    /**
     * @return MemberCartCollection|string
     * @throws AppException
     */
    protected function getMemberCarts()
    {
        $goodsId = GroupPurchase::getGoodsId();
        if(!isset($goodsId)){
            return '请配置拼团设置信息';
        }
        $goodsParams = [
            'goods_id' => $goodsId,
        ];
        $result = new MemberCartCollection();
        $result->push(MemberCartService::newMemberCart($goodsParams));
        return $result;
    }
    protected function validateParam(){

    }
}