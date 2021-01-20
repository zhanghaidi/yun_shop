<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com yangyu
 * Date: 2019/1/11
 * Time: 10:13
 */
namespace Yunshop\Tbk\Frontend\Controllers;

use app\common\components\ApiController;
use app\common\helpers\Cache;
use Yunshop\Tbk\common\models\Goods;
use Yunshop\Tbk\common\models\TbkCoupon;
use Yunshop\Tbk\common\services\TaobaoService;

class CouponGoodsController extends ApiController
{
    /**
     * 获取淘宝客商品列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $couponGoods = TbkCoupon::with("goods")->get();

        /*$couponGoods = Goods::whereHas('tbkCoupon', function($query){
            return $query->where("coupon_remain_count", ">", 1);
        })->get();*/
        return $this->successJson('ok',['couponGoods' => $couponGoods]);

    }

    public function getTbkCouponUrl() {
        $member_id = \Yunshop::app()->getMemberId();
        $num_iid = \YunShop::request()->num_iid;
        $taobao = new TaobaoService();
        $res = $taobao->tbkToUrl($member_id, $num_iid);
        return $this->successJson('ok',['couponGoods' => $res]);

    }

    public function getGoodsDetail()
    {
        $num_iid = \YunShop::request()->num_iid;

        $taobao = new TaobaoService();

        /*$content = Cache::get('Tbk_Goods_'.$num_iid);

        if (!$content) {
            $content = $taobao->getGoodsDetail($num_iid);
            Cache::put('Tbk_Goods_'.$num_iid, $content, 24*60);
        }*/


        $content = $taobao->getGoodsDetail($num_iid);


        return $this->successJson('ok',['content' => $content]);
    }
}