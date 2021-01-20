<?php
/**
 * Created by PhpStorm.
 * Time: 下午3:55
 */

namespace Yunshop\GoodsPackage\common\discount;

use app\frontend\modules\order\discount\BaseDiscount;
use Yunshop\GoodsPackage\common\models\GoodsPackage;
use Yunshop\GoodsPackage\common\models\Goods;
use app\common\exceptions\AppException;

/**
 * 商品套餐优惠，当购买套餐时，每个栏目至少有一种商品时才可以使用套餐优惠中设置的优惠金额
 * Class PackageDiscount
 * @package app\frontend\modules\order\discount
 */
class PackageDiscount extends BaseDiscount
{
    protected $code = 'package';
    protected $name = '商品套餐优惠';
    /**
     * 获取总金额
     * @return float
     */
    protected function _getAmount()
    {
        $result = 0.00;
        $package_id = request()->input('package_id');

        if (!empty($package_id)) {
            // 获取套餐
            $package = GoodsPackage::getGoodsPackageById(request()->input('package_id'));
            if (!empty($package)) {
                // 如果套餐限时时间开启,并且当前时间在套餐限时时间范围外，则直接返回0.00
                $currentTime = time();
                if ($package['limit_time_status'] && ($currentTime < $package['start_time'] || $currentTime > $package['end_time'])) {
                    return $result;
                }
                // 未开启限时开关，或者在限时时间内，则验证所有栏目是否都有购买商品
                // 获取套餐下每个栏目的商品id，二维数组
                $packageCategoryGoodsIds = [];
                foreach ($package->hasManyCategory as $category) {
                    $packageCategoryGoodsIds[] = array_filter(explode(';',$category['category_goods_ids']));
                }

                // 套餐中商品不可能为空，为空要抛异常
                if (empty($packageCategoryGoodsIds)) {
                    throw new AppException('套餐中未找到商品或已经删除');
                }
                // 获取传入的商品信息
                $requestGoodsList = json_decode(request()->input('goods'),true);

                // 为保证数据的准确性，最好每个请求的goodsid都要去数据库获取，看看是否存在这个商品，存在再对比
                    // 先对请求的商品id去重，一次性查出请求的所有商品，如果数量和去重后的商品id数组不相同，则抛出异常
                    // throw new AppException('(ID:' . $requestGoods['goods_id'] . ')未找到商品或已经删除');
                $tempIds = [];
                foreach ($requestGoodsList as $requestGoods) {
                    $tempIds[] = $requestGoods['goods_id'];
                }
                $tempIds = array_unique($tempIds);
                $goodsIds = Goods::getGoodsListByGoodsIds($tempIds);
                if (count($tempIds) != count($goodsIds)) {
                    throw new AppException('未找到商品或已经删除');
                }
                // 循环校验商品id在不在套餐商品数组中,两层for，使用in_array可以判断，
                // 在，则将套餐商品数组的栏目unset，循环结束后判断套餐商品数组为空则给与优惠价格
                $temp = $packageCategoryGoodsIds;
                foreach ($requestGoodsList as $requestGoods) {
                    foreach ($packageCategoryGoodsIds as $key =>  $categoryGoodsIds) {
                        if (in_array($requestGoods['goods_id'],$categoryGoodsIds)) {
                            unset($temp[$key]);
                        }
                    }
                }
                if (empty($temp)) {
                    $result = $package['on_sale_price'];
                }
            }
        }
        return $result;
    }
}