<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/11
 * Time: 上午10:20
 */

namespace Yunshop\GoodsPackage\frontend\package;

use app\common\components\ApiController;
use app\frontend\modules\member\services\MemberCartService;
use app\frontend\modules\memberCart\MemberCartCollection;
use Yunshop\GoodsPackage\common\models\GoodsPackage;
use app\common\exceptions\AppException;

class PackageBuyController extends ApiController
{
    /**
     * @return MemberCartCollection
     * @throws \app\common\exceptions\AppException
     */
    protected function getMemberCarts($data)
    {
        $result = new MemberCartCollection();
        foreach ($data as $goods) {
            $goods_params = [
                'goods_id' => $goods['goods_id'],
                'total' => $goods['total'],
                'option_id' => $goods['option_id'],
            ];
            $result->push(MemberCartService::newMemberCart($goods_params));
        }
        return $result;
    }

    protected function validateData($package_id,$goods_list) {

        // 验证套餐必须存在
        $package = GoodsPackage::getOpenGoodsPackageById($package_id);
        if (empty($package)) {
            throw new AppException('参数错误，套餐不存在或未开启');
        }

        // 正则验证，整数规则,第一位必须是1-9，后面的必须是0-9
        $reg = '/^[1-9][0-9]*$/';
        foreach ($goods_list as $item) {
            // 不为空并且是正整数
            if(empty($item['goods_id']) || (!empty($item['goods_id']) && !preg_match($reg,$item['goods_id']))) {
                throw new AppException('参数错误!商品id('.$item['goods_id'].')'.'不为整数');
            }
            // 有值时必须是整数
            if (!empty($item['option_id'] && !preg_match($reg,$item['option_id']))) {
                throw new AppException('参数错误!规格id('.$item['option_id'].')'.'不为整数');
            }
            // 必须整数
            if (empty($item['total'])  || (!empty($item['total'] && !preg_match($reg,$item['total'])))) {
                throw new AppException('参数错误!商品购买数量('.$item['total'].')'.'必须大于1');
            }
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse

     * @throws \app\common\exceptions\ShopException
     */
    public function index()
    {
        $package_id = request()->get('package_id');
        $goods_list = json_decode(request()->get('goods'),true);
        // 2.验证数据，套餐id是否存在，商品数据是否符合
        $this->validateData($package_id,$goods_list);
        // 3.填充立即购买
        $trade = $this->getMemberCarts($goods_list)->getTrade();
        // 4.将套餐id回传
        $trade['package_id'] = $package_id;
        return $this->successJson('成功', $trade);
    }
}