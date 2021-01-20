<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/9/22
 * Time: 上午11:09
 */

namespace Yunshop\GoodsPackage\frontend\package\models;

use Illuminate\Database\Eloquent\SoftDeletes;

class GoodsPackage extends \Yunshop\GoodsPackage\common\models\GoodsPackage
{
    use SoftDeletes;

    // 对图片和时间进行处理
    public static function transform($data)
    {
        $data['thumb'] = yz_tomedia($data['thumb']);
        $data['share_thumb'] = yz_tomedia($data['share_thumb']);
        $data['description_thumb'] = yz_tomedia($data['description_thumb']);
        //$data['start_time'] = date("Y-m-d H:i:s",$data['start_time']);
        //$data['end_time'] = date("Y-m-d H:i:s",$data['end_time']);
        return $data;
    }

    // 组装幻灯片数据
    public static function transformCarousel($package)
    {
        $carousels = $package['has_many_carousel'];
        $tempCarousels = [];
        foreach ($carousels as &$carousel) {
            if ($carousel['carousel_open_status']) {
                $carousel['carousel_thumb'] = yz_tomedia($carousel['carousel_thumb']);
                $tempCarousels[] = $carousel;
            }

        }
        // 排序幻灯片
        $carousels = collect($tempCarousels)->sortBy('carousel_sort')->values();
        $package['carousels'] = $carousels->toArray();
        unset($package['has_many_carousel']);
        return $package;
    }

    // 传入一个套餐，获取它的所有商品总价
    public static function getGoodsPriceSumByPackage($package)
    {
        $goodsListPriceSum = 0.00;
        foreach ($package['has_many_category'] as $category) {
            $arrIds = array_filter(explode(';', $category['category_goods_ids']));
            $goodsListPrice = Goods::getGoodsListPriceSumByGoodsIds($arrIds);
            $goodsListPriceSum = bcadd($goodsListPriceSum, $goodsListPrice, 2);
        }
        return $goodsListPriceSum;
    }

    // 组装其他套餐数据
    public static function transformOtherPackages($package)
    {
        $otherPackages = [];
        if($package['other_package_status'] == 1) {
            $arrIds = array_filter(explode(';', $package['other_package_ids']));
            foreach($arrIds as  $id){
                // 通过id查找套餐，本公众号的，未删除的，并且是开启状态的
                $otherPackage = self::getOpenGoodsPackageById($id);
                if (!empty($otherPackage)) {
                    $temp['id'] = $otherPackage->id;
                    $temp['title'] = $otherPackage->title;
                    $temp['thumb'] = yz_tomedia($otherPackage->thumb);
                    // 统计该套餐下的商品总价
                    $temp['price_sum'] = self::getGoodsPriceSumByPackage($otherPackage->toArray());
                    $otherPackages[] = $temp;
                }
            }
        }
        $package['other_packages'] = $otherPackages;
        return $package;
    }

    // 根据商品id获取商品信息
    public static function getGoodsInfo($stringGoodsIds)
    {
        $goodsList = [];
        $arrayGoodsIds = array_filter(explode(';',$stringGoodsIds));
        foreach ($arrayGoodsIds as $goodsId){
           $goodsInfo = Goods::getGoods($goodsId)->toArray();
           if (!empty($goodsInfo)) {
               $goodsList[] = $goodsInfo;
           }
        }
        return $goodsList;
    }

    // 组装栏目数据
    public static function transformCategories($package)
    {

        $categories = $package['has_many_category'];
        foreach ($categories as &$category) {
            $category['category_names'] = $category['category_name'];
            $category['category_sorts'] = $category['category_sort'];
            // 组装栏目下的商品信息
            $category['goods_list'] = self::getGoodsInfo($category['category_goods_ids']);
        }
        // 排序栏目
        $categories = collect($categories)->sortBy('category_sort')->values();
        $package['categories'] = $categories->toArray();
        unset($package['has_many_category']);
        return $package;
    }
    
    // 去除不要的数据
    public static function unsetNeedlessData($packages){
        unset($packages['other_package_ids']);
        unset($packages['other_package_status']);
        return $packages;
    }

    // 计算套餐总价格，它等于套餐下所有商品价格之和
    public static function getGoodsPackagePriceSum($package)
    {
        $goodsPackagePriceSum = 0.00;
        foreach ($package['categories'] as $category){
            foreach ($category['goods_list'] as $goods) {
                $goodsPackagePriceSum = bcadd($goodsPackagePriceSum,$goods['price'],2);
            }
        }
        $package['price'] = $goodsPackagePriceSum;
        return $package;
    }

    /**
     * 套餐查询,组好接口需要的数据
     * @param int $params 套餐id
     * @return array
     */
    public static function search($package_id){
        $package = self::getOpenGoodsPackageById($package_id);
        if (empty($package)) {
            return ['status'=>0,'message'=>'此套餐活动已结束，谢谢您的关注，请浏览其他套餐或商品！','data'=>''];
        }
        // 转为数组
        $package = $package->toArray();
        // 将json数据转回正常数据
        $package = self::transform($package);
        // 把每个幻灯片重新组装
        $package = self::transformCarousel($package);
        // 把每个其他套餐重新组装
        $package = self::transformOtherPackages($package);
        // 把每个栏目重新组装
        $package = self::transformCategories($package);
        // 计算套餐总价
        $package = self::getGoodsPackagePriceSum($package);
        // 去除前端不要的数据
        //$package = self::unsetNeedlessData($package);
        return ['status'=>1,'message'=>'获取商品套餐成功!','data'=>$package];
    }

}