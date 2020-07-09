<?php
/**
 * Created by PhpStorm.
 * User: king
 * Date: 2018/9/21
 * Time: 下午2:05
 */

namespace Yunshop\Designer\Common\Services;


use app\common\helpers\Cache;
use app\common\models\Goods;
use app\frontend\models\MemberCart;
use app\frontend\modules\member\services\factory\MemberFactory;
use Yunshop\Designer\Common\Models\PageModel;
use Yunshop\Designer\models\GoodsGroupGoods;
use Yunshop\Diyform\admin\DiyformDataController;
use Yunshop\JdSupply\services\JdOrderValidate;
use Yunshop\MemberPrice\models\IndependentGoods;

class IndexPageService
{
    /**
     * 终端类型
     *
     * @var int
     */
    private $page_type;


    public function __construct()
    {
        $this->page_type = $this->getPageType();
    }


    public function getIndexPage()
    {
        return $this->getDesignerModel();
    }


    /**
     * @return PageModel
     */
    private function getDesignerModel()
    {
        if (!Cache::has("designer_page_type_{$this->page_type}")) {
            $designerModel = $this->_getDesignerModel();
            Cache::put("designer_page_type_{$this->page_type}", $designerModel, 4200);
            return $designerModel;
        }

        return Cache::get("designer_page_type_{$this->page_type}");
    }

    /**
     * @return PageModel
     */
    private function _getDesignerModel()
    {
        $designerModel = PageModel::uniacid()//查出商品样式
        ->wherePageType($this->page_type)
            ->first();
        if ($designerModel){
            $group_id = GoodsGroupGoods::uniacid()//查询当前页的所有商品组
            ->wherePageType($this->page_type)
                ->DISTINCT(['group_id'])
                ->get(['group_id']);

            $group_goods = [];
            $group['datas'] = json_decode($designerModel['datas'],1);//转数组格式
            $goods_model = \app\common\modules\shop\ShopConfig::current()->get('goods.models.commodity_classification');
            $goods_model = new $goods_model;

            foreach ($group['datas'] as $key=>$itme){//循环门店商品组

                if ($itme['temp'] == 'goods') {
                    if(!isset($itme['params']['lowershelf'])) {
                        $itme['params']['lowershelf'] = 1;
                    }
                }

                foreach ($group_id as $keys=>$itmes){//循环有商品的商品组
                    if ($itme['temp'] == 'assemble'){
                        foreach ($itme['data'] as $i => $goods){
                           $goods_data = $goods_model->find($goods['goods_id']);
                            $group['datas'][$key]['data'][$i]['vip_level_status'] = $goods_data->vip_level_status;
//                            dd($group['datas'][$key]['data'][$i]['vip_level_status']);
                        }
                    }
                    if ($itme['id'] == $itmes['group_id']){
                        $data = [];
                        //$group[$key]['data'] = [];
                        //$group[$key]['Identification'] = 0;
                        $group_goods[$itmes['group_id']] = GoodsGroupGoods::select()//查询当前商品组的商品
                            ->uniacid()
                            ->wherePageType($this->page_type)
                            ->where('group_id',$itmes['group_id']);
                        $da = $group_goods[$itmes['group_id']]->paginate(12)//分页处理
                            ->toArray();
                        foreach ($da['data'] as $ke=>$goods){
                            $kGoods = unserialize($goods['goods']);
                            // 会员价
                            $rGoods = $goods_model->select()
                                ->where('id', $kGoods['goodid'])
                                ->withTrashed()
                                ->with(['hasManyOptions' => function ($query) {
                                    return $query->select('id', 'title', 'thumb', 'product_price', 'market_price','stock');
                                }])
                                ->first();
                            $kGoods['vip_price'] = $rGoods->vip_price;
                            $kGoods['vip_next_price'] = $rGoods->vip_next_price;
                            $kGoods['sales'] = $rGoods->virtual_sales + $rGoods->show_sales;
                            $kGoods['vip_level_status'] = $rGoods->vip_level_status;
                            $kGoods['price_level'] = $rGoods->price_level;

                            $kGoods['stock_status'] = 0;
                            if ($rGoods['stock'] <= 0) {
                                $kGoods['stock_status'] = 1;//库存不足
                            }

                            if ($rGoods['status'] != 1) {
                                $kGoods['stock_status'] = 2; //已下架
                            }

                            if (!empty($rGoods['deleted_at'])) {
                                $kGoods['stock_status'] = 3; //已删除
                            }

                            if ($rGoods['plugin_id'] == 44 && app('plugins')->isEnabled('jd-supply')) {

                                if (!empty($rGoods['has_many_option'])) {

                                    //获取会员默认地址
                                    $member_id = \YunShop::app()->getMemberId();

                                    $memberCart = MemberCart::uniacid()->where("member_id",$member_id)
                                        ->with(["hasManyAddress"=>function($query) use ($member_id){
                                        return $query->where("uid",$member_id)->where("isdefault",1);
                                    }])->with(["hasManyMemberAddress"=>function($query) use ($member_id){
                                            return $query->where("uid",$member_id)->where("isdefault",1);
                                    }])->first();

                                    if (!empty($memberCart)) {
                                        $memberCart['has_many_address'][0]['street'] = "";
                                        $is_street = \Setting::get("shop.trade")['is_street'];
                                        $member_address = ($is_street == 1) ?  $memberCart['has_many_member_address'][0] : $memberCart['has_many_address'][0];
                                        $cart_data = [
                                            "jd_order_goods" => [
                                                "goods_id" => $kGoods['goodid'],
                                                "goods_option_id" => $rGoods['has_many_option']['id'],
                                                "total" => 1
                                            ],
                                            "orderAddress" => $member_address
                                        ];

                                        $jd_res = JdOrderValidate::orderValidate2($cart_data);

                                        if ($jd_res != 1) {
                                            $kGoods['stock_status'] = 4; //不存在
                                        }
                                    }
                                }
                            }

                            $data[$ke] = $kGoods;//反序列化
                        }
                        $group['datas'][$key]['data'] = [];
                        $group['datas'][$key]['data'] = $data;
                    }else{
                        $group['datas'][$key]['data'] =  $group['datas'][$key]['data'];
                    }
                }

                if(is_null($da['total'])){
                    $da['total'] = 0;
                }

                if (is_null($da['data'][0]['Identification'])){
                    $da['data'][0]['Identification'] = 0;
                }

                $group['datas'][$key]['Identification'] = $da['data'][0]['Identification'];
                
                $group['datas'][$key]['total'] = $da['total'];
            }

            $designerModel['datas'] = json_encode($group['datas']);
        }
        if (is_null($designerModel)){
            $designerModel = "";
        }
        return $designerModel;
    }

    /**
     * 终端类型 1 公众号首页， 2 小程序首页，其他………………（9 原生小程序首页）
     *
     * @return int
     */
    private function getPageType()
    {
        $pageType = (int)\YunShop::request()->type;
        $ingress = (string)\YunShop::request()->ingress;

        //如果type==2 && $ingress=='weChatApplet' 原生小程序页面
        if ($pageType == MemberFactory::LOGIN_MINI_APP && $ingress == "weChatApplet") {
            return 9;
        }
        //抖音小程序
        else if ($pageType == MemberFactory::LOGIN_DOUYIN && $ingress == "weChatApplet") {
            return 9;
        }
        return $pageType ?: MemberFactory::LOGIN_OFFICE_ACCOUNT;
    }

}
