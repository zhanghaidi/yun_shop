<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/4/6
 * Time: 下午5:29
 */

namespace Yunshop\Designer\services;



use app\common\models\Goods;
use app\frontend\models\MemberCart;
use Yunshop\Designer\Backend\Modules\Page\Controllers\SearchArticleController;
use Yunshop\Designer\models\DesignerMenu;
use Yunshop\JdSupply\services\JdOrderValidate;
use Yunshop\Sign\Frontend\Services\SignAwardService;
use app\common\models\MemberCoupon;
use app\frontend\modules\coupon\models\Coupon;
use app\common\models\MemberShopInfo;
use Yunshop\Love\Common\Services\SetService;

class DesignerService
{
    private $page;

    public function getMenu($page)
    {
        $this->page = $page;

        //$otherData = $this->getOtherData();

        return array(
            'menus'         => $this->getMenuInfo(),
            'params'        => $this->getMenuParams(),
        );

    }
    public function getPage($page = [])
    {
        $this->page = $page;
        $otherData = $this->getOtherData();
        return array(
            'page'          => $this->page,
            'pageinfo'      => $this->getPageInfo(),
            'data'          => $this->getData(),
            'footertype'    => $otherData['footertype'],
            'footermenu'    => $otherData['footermenu'],
            'system'        => $this->getSystem(),
            'menus'         => $this->getMenuInfo(),
            'params'        => $this->getMenuParams(),
            'share'         => array(
                'title'         => $otherData['title'],
                'desc'          => $otherData['desc'],
                'imgUrl'        => $otherData['imgUrl']
            )
        );
    }

    //由于前端要求pageinfo的数据是对象型数组, 而不能是数组型,
    //而原来的getPage()方法被其它地方引用, 因此重写了方法
    public function getPageForHomePage($page = [])
    {

        $this->page = $page;
        $otherData = $this->getOtherData();

        return array(
            'page'          => $this->page,
            'pageinfo'      => $this->getPageInfoInJson(),
            'data'          => $this->getData(),
            'footertype'    => $otherData['footertype'],
            'footermenu'    => $otherData['footermenu'],
            'menus'         => $this->getMenuInfo(),
            'params'        => $this->getMenuParams(),
            'share'         => array(
                'title'         => $otherData['title'],
                'desc'          => $otherData['desc'],
                'imgUrl'        => $otherData['imgUrl']
            )
        );
    }

    private function getMenuInfo()
    {
        $menu = $this->getPageMenu();
        return json_decode($menu['menus'], true);
    }

    private function getMenuParams()
    {
        $menu = $this->getPageMenu();
        return json_decode($menu['params'], true);
    }

    private function getPageMenu()
    {
        $pageInfo = $this->getPageInfo();
        $menuId = intval($pageInfo[0]['params']['footermenu']);
        return DesignerMenu::getMenuById($menuId);
    }

    //商城设置信息
    private function getSystem()
    {
        $system = \Setting::get('shop');
        $system['shop'] = set_medias($system['shop'], 'logo');

        return json_encode($system);
    }

    //和 getSystem() 方法获取的信息一致, 不过返回的是数组
    public function getSystemInfo()
    {
        $system = \Setting::get('shop');
        $system['logo'] = replace_yunshop(yz_tomedia($system['logo']));
        $system['signimg'] = replace_yunshop(yz_tomedia($system['signimg']));

        $system = collect($system)->map(function ($item, $key) {
             if ($key != 'key' && $key != 'pay' && $key != 'sms') {
                 return $item;
             }
        });

        return $system;
    }

    //转化 page_info 字段为对象JSON, 而不包含数组
    private function getPageInfoInJson()
    {
        $this->page['page_info'] = (rtrim(ltrim($this->page['page_info'], '['), ']'));
        return  $this->processData($this->page['page_info']);
    }

    //
    private function getPageInfo()
    {
        return  $this->processData($this->page['page_info']);
    }
    //重构页面 data 数据
    public function getData()
    {
        $data = $this->processData($this->page['datas']);
        //echo '<pre>'; print_r($data); exit;
        foreach ($data as $key => &$temp)
        {
            $temp = $this->getTempData($temp);
        }
        return $data;
    }

    //重构页面 data 数据
    public function getMemberData($datas)
    {
        $data = $this->processData($datas);
        //echo '<pre>'; print_r($data); exit;
        foreach ($data as $key => &$temp)
        {
            $temp = $this->getTempData($temp);
        }
        //处理爱心值自定义名称
        if (app('plugins')->isEnabled('love')){
            $love_basics_set = SetService::getLoveSet();//获取爱心值基础设置
            foreach ($data as $key => $iten) {
                if(in_array($iten['temp'],['goods','flashsale','memberasset'])) {
                    foreach ($iten['data'] as $keys => $loove_name) {
                        foreach ($loove_name as $love_key => $love){
                            if ($love['name'] == 'love'){
                                $data[$key]['data']['part'][$love_key]['title'] = $love_basics_set['name'] ?: '爱心值';
                            }
                        }
                    }
                }
            }
        }

        return $data;
    }


    private function getTempData($temp)
    {
        switch ($temp['temp']) {
            case 'goods':
                return $this->getGoodsTempData($temp);
            case 'sign':
                return $this->getSignTempData($temp);
            case 'richtext':
                return $this->getTextTempData($temp);
            case 'menu':
                return $this->getMenuTempData($temp);
            case 'coupon':
                return $this->getCouponTempData($temp);
            case 'article':
                return $this->getArticleTempData($temp);
            case 'headline':
                return $this->getArticleTempData($temp);
            case 'flashsale':
                return $this->getFlashsaleTempData($temp);
            case 'nearbygoods':
                return $this->getNearTempData($temp);
            default:
                return $temp;
        }
    }


    private function getGoodsTempData($temp)
    {
        $goods_ids = [];
        foreach ($temp['data'] as $key => $goods) {
            $goods_ids[] = $goods['goodid'];
        }

        if (count($goods_ids) > 0) {
            $goodsInfo = $this->getGoodsModelByIds($goods_ids);
            $goodsInfo = set_medias($goodsInfo,'thumb');

            foreach ($temp['data'] as $keyTwo => &$valueTwo) {
                foreach ($goodsInfo as $key => $goodsModel) {
                    if ($valueTwo['goodid'] == $goodsModel['id']) {

                        $valueTwo['name']     = $goodsModel['title'];
                        $valueTwo['priceold'] = $goodsModel['market_price'];
                        $valueTwo['pricenow'] = $goodsModel['price'];
                        $valueTwo['img']      = yz_tomedia($goodsModel['thumb']);
                        //$valueTwo['sales']    = $goodsModel->show_sales;
                        //$valueTwo['unit']     = $goodsModel->stock;

                        $valueTwo['stock_status'] = 0;

                        if ($goodsModel['stock'] <= 0) {
                            $valueTwo['stock_status'] = 1;//库存不足
                        }

                        if ($goodsModel['status'] != 1) {
                            $valueTwo['stock_status'] = 2; //已下架
                        }

                        if (!empty($goodsModel['deleted_at'])) {
                            $valueTwo['stock_status'] = 3; //已删除
                        }

                        if ($goodsModel['plugin_id'] == 44 && app('plugins')->isEnabled('jd-supply')) {

                            if (!empty($goodsModel['has_many_option'])) {

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
                                            "goods_id" => $valueTwo['goodid'],
                                            "goods_option_id" => $goodsModel['has_many_option']['id'],
                                            "total" => 1
                                        ],
                                        "orderAddress" => $member_address
                                    ];

                                    $jd_res = JdOrderValidate::orderValidate2($cart_data);

                                    if ($jd_res != 1) {
                                        $valueTwo['stock_status'] = 4; //不存在
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        //sort($temp['data']);
        return $temp;
    }

    private function getGoodsModelByIds($goods_ids)
    {
        $goodsModels = Goods::select('id', 'title', 'market_price', 'price', 'thumb','stock','status','deleted_at','plugin_id')
            ->uniacid()
            ->withTrashed()
            ->with(['hasManyOptions' => function ($query) {
                return $query->select('id', 'title', 'thumb', 'product_price', 'market_price','stock');
            }])
            ->whereIn('id', $goods_ids)
            ->get();

        return $goodsModels->isEmpty() ? [] : $goodsModels->toArray();
    }

    private function getFlashsaleTempData($temp)
    {
        $goods_ids = [];
        foreach ($temp['data'] as $key => $goods) {
            $goods_ids[] = $goods['goodid'];
        }
        if (count($goods_ids) > 0) {
            $goodsInfo = $this->getLimitBuyGoodsModelByIds($goods_ids);
            $goodsInfo = set_medias($goodsInfo,'thumb');

            foreach ($temp['data'] as $keyTwo => &$valueTwo) {
                foreach ($goodsInfo as $key => $goods) {
                    if ($valueTwo['goodid'] == $goods['id']) {
                        if ($goods['has_one_goods_limit_buy']['end_time'] < time()) {
                            unset($temp['data'][$keyTwo]);
                        } else {
                            $valueTwo['name'] = $goods['title'];
                            $valueTwo['priceold'] = $goods['market_price'];
                            $valueTwo['pricenow'] = $goods['price'];
                            $valueTwo['img'] = $goods['thumb'];
                            $valueTwo['sales'] = $goods['real_sales'];
                            $valueTwo['unit'] = $goods['unit'];
                            $valueTwo['stock'] = $goods['stock'];
                            $valueTwo['end_time'] = date('Y/m/d H:i:s',$goods['has_one_goods_limit_buy']['end_time']);
                            $valueTwo['start_time'] = date('Y/m/d H:i:s',$goods['has_one_goods_limit_buy']['start_time']);
                        }
                    }
                }
            }
        }
        sort($temp['data']);
        return $temp;
    }


    private function getLimitBuyGoodsModelByIds($goods_ids)
    {
        $goodsModels = Goods::select('id', 'title', 'market_price', 'price', 'thumb', 'real_sales', 'sku', 'stock')
            ->with(['hasOneGoodsLimitBuy' => function ($query) {
                return $query->select('goods_id', 'start_time', 'end_time');
            }])
            ->whereIn('id', $goods_ids)
            ->uniacid()
            ->get();

        return $goodsModels->isEmpty() ? [] : $goodsModels->toArray();
    }

    private function getCouponTempData($temp)
    {
        //判断会员可以领取的优惠券
        $uid = \YunShop::app()->getMemberId();
        if (!$uid) {
            return $temp;
        }
        $member = MemberShopInfo::getMemberShopInfo($uid);
        $memberLevel = $member->level_id;
        $now = strtotime('now');
        $coupons = Coupon::getCouponsForMember($uid, $memberLevel, null, $now)
            ->orderBy('display_order','desc')
            ->orderBy('updated_at','desc');
        if($coupons->get()->isEmpty()){
            return $temp;
        }
        $couponsData = self::getCouponData($coupons->get()->toArray());
        foreach ($couponsData as $value) {
            $coupons_id[] = $value['id'];
        }
        if (count($couponsData) > 0) {
            foreach ($temp['data'] as $keyTwo => &$valueTwo) {
                if (in_array($valueTwo['coupon_id'], $coupons_id)) {
                    foreach ($couponsData as $coupons) {
                        if ($valueTwo['coupon_id'] == $coupons['id']) {
                            $gettotal = MemberCoupon::uniacid()->where("coupon_id", $coupons['coupon_id'])->count();
                            if ($coupons['total'] == -1) {
                                $lasttotal = '无限数量';
                            } else {
                                $lasttotal = $coupons['total'] - $gettotal;
                                $lasttotal = ($lasttotal > 0) ? $lasttotal : 0; //考虑到可领取总数修改成比之前的设置小, 则会变成负数
                            }
                            $valueTwo['coupon_id'] = $coupons['id'];
                            $valueTwo['lasttotal'] = $lasttotal;
                            $valueTwo['name'] = $coupons['name'];
                            $valueTwo['enough'] = $coupons['enough'];
                            $valueTwo['coupon_method'] = $coupons['coupon_method'];
                            $valueTwo['deduct'] = floatval($coupons['deduct']);
                            $valueTwo['api_availability'] = $coupons['api_availability'];
                            $valueTwo['time_end'] = $coupons['time_end'];
                            $valueTwo['discount'] = floatval($coupons['discount']);
                            $valueTwo['enough'] = floatval($coupons['enough']);
                        }
                    }
                } else {
                    unset($temp['data'][$keyTwo]);
                }
            }
            if ($temp['alldata']) {
                foreach ($temp['alldata'] as $keyTwo => &$valueTwo) {
                    if (in_array($valueTwo['coupon_id'], $coupons_id)) {
                        foreach ($couponsData as $coupons) {
                            if ($valueTwo['coupon_id'] == $coupons['id']) {
                                $gettotal = MemberCoupon::uniacid()->where("coupon_id", $coupons['coupon_id'])->count();
                                if ($coupons['total'] == -1) {
                                    $lasttotal = '无限数量';
                                } else {
                                    $lasttotal = $coupons['total'] - $gettotal;
                                    $lasttotal = ($lasttotal > 0) ? $lasttotal : 0; //考虑到可领取总数修改成比之前的设置小, 则会变成负数
                                }
                                $valueTwo['coupon_id'] = $coupons['id'];
                                $valueTwo['lasttotal'] = $lasttotal;
                                $valueTwo['name'] = $coupons['name'];
                                $valueTwo['enough'] = $coupons['enough'];
                                $valueTwo['coupon_method'] = $coupons['coupon_method'];
                                $valueTwo['deduct'] = floatval($coupons['deduct']);
                                $valueTwo['api_availability'] = $coupons['api_availability'];
                                $valueTwo['time_end'] = $coupons['time_end'];
                                $valueTwo['discount'] = floatval($coupons['discount']);
                                $valueTwo['enough'] = floatval($coupons['enough']);
                            }
                        }
                    } else {
                        unset($temp['alldata'][$keyTwo]);
                    }
                }
            }
        }
        return $temp;
    }

    //添加"是否可领取" & "是否已抢光" & "是否已领取"的标识
    public static function getCouponData($coupons)
    {
        foreach($coupons as $k=>$v){
            if (($v['total'] != -1) && ($v['has_many_member_coupon_count'] >= $v['total'])){
                $coupons[$k]['api_availability'] = 3;
            } elseif($v['member_got_count'] > 0){
                $coupons[$k]['api_availability'] = 2;
            } else{
                $coupons[$k]['api_availability'] = 1;
            }

            //增加属性 - 对于该优惠券,用户可领取的数量
            if($v['get_max'] != -1){
                $coupons[$k]['api_remaining'] = $v['get_max'] - $v['member_got_count'];
                if ($coupons[$k]['api_remaining'] < 0){ //考虑到优惠券设置会变更,比如原来允许领取6张,之后修改为3张,那么可领取张数可能会变成负数
                    $coupons[$k]['api_remaining'] = 0;
                }
            } elseif($v['get_max'] == -1){
                $coupons[$k]['api_remaining'] = -1;
            }

            //添加优惠券使用范围描述
            switch($v['use_type']){
                case Coupon::COUPON_SHOP_USE:
                    $coupons[$k]['api_limit'] = '商城通用';
                    break;
                case Coupon::COUPON_CATEGORY_USE:
                    $coupons[$k]['api_limit'] = '适用于下列分类: ';
                    $coupons[$k]['api_limit'] = implode(',', $v['categorynames']);
                    break;
                case Coupon::COUPON_GOODS_USE:
                    $coupons[$k]['api_limit'] = '适用于下列商品: ';
                    $coupons[$k]['api_limit'] = implode(',', $v['goods_names']);
                    break;
            }
        }
        return $coupons;
    }

    private function getArticleTempData($temp)
    {
        if (!app('plugins')->isEnabled('article')) {
            return $temp;
        }

        $member_id = \YunShop::app()->getMemberId();

        $article_ids = [];

        if($temp['params']['addmethod'] == 0)
        {
            $res = (new SearchArticleController)->designerResult();

            $temp['alldata'] = [];
            if($temp['params']['shownum'] > 0)
            {
                $num = 0;
                foreach ($res as $re)
                {
                   // $article_pay_money = ($re['has_one_article_pay'] === null)  ? 0 : $re['has_one_article_pay']['money'];
                  //  $article_pay_record = ($re['has_one_record'] === null) ? 0 : $re['has_one_record']['pay_status'];
                    $son = [
                        'title' =>  $re['title'] ,
                        'articleid' =>  $re['id'] ,
                        'category' =>  $re['belongs_to_category']['name'] ,
                        'time' =>  $re['created_at'] ,
                        'hrefurl' =>  $re['link'] ,
                        'thumb' =>  $re['thumb'] ,
                        'read_num' =>  $re['read_num'] ,
                        'author' =>  $re['author'] ,
                        'has_one_article_pay' => $re['has_one_article_pay'],
                        'has_one_record' => $re['has_one_record']
                    ];
                    $temp['alldata'][] = $son;
                    $num += 1;
                    if($num >= $temp['params']['shownum'])
                    {
                        break;
                    }
                } 
            }
        }
        if ($temp['data']) {
            foreach ($temp['data'] as $key => $article) {
                $article_ids[] = $article['articleid'];
            }
        } elseif ($temp['alldata']) {
            foreach ($temp['alldata'] as $key => $article) {
                $article_ids[] = $article['articleid'];
            }
        }

        if (count($article_ids) > 0) {
            $articleInfo = ArticleService::getArticleByIds($article_ids,$member_id);
            $articleInfo = set_medias($articleInfo,'thumb');

            foreach ($temp['data'] as $keyTwo => &$valueTwo) {
                foreach ($articleInfo as $articles) {
                    if ($valueTwo['articleid'] == $articles['id']) {
                        //$article_pay_money = ($articles['has_one_article_pay'] === null)  ? 0 : $articles['has_one_article_pay']['money'];
                        //$article_pay_record = ($articles['has_one_record'] === null) ? 0 : $articles['has_one_record']['pay_status'];
                        $valueTwo['category'] = $articles['belongs_to_category']['name'];
                        $valueTwo['time'] = $articles['created_at'];
                        $valueTwo['title'] = $articles['title'];
                        $valueTwo['thumb'] = $articles['thumb'];
                        $valueTwo['hrefurl'] = $articles['link'];
                        $valueTwo['read_num'] = $articles['read_num'];
                        $valueTwo['has_one_article_pay'] = $articles['has_one_article_pay'];
                        $valueTwo['has_one_record'] = $articles['has_one_record'];
                    }
                    if (!ArticleService::getArticleById($valueTwo['articleid'],$member_id)) {
                        unset($temp['data'][$keyTwo]);
                    }
                }
            }
        }
        return $temp;
    }

    private function getSignTempData($temp)
    {
        $temp['params']['sign_status'] = false;
        $temp['params']['button_hint'] = "连续0天";
        $temp['params']['award_content'] = "可获得积分：+0.00；优惠券：（0）张";
        if (\YunShop::plugin()->get('sign') && !\YunShop::isWeb()) {
            $signAward = new SignAwardService();

            $button_hint = $signAward->getCumulativeNumber();
            $button_hint = $button_hint ? $button_hint -1 : 0;
            $award_content = $signAward->getSignAwardContent();

            $temp['params']['text'] = $signAward->signModel->sign_status ? "已".trans('Yunshop\Sign::sign.plugin_name') : "未".trans('Yunshop\Sign::sign.plugin_name');
            $temp['params']['sign_name']  = trans('Yunshop\Sign::sign.plugin_name');
            $temp['params']['sign_status'] = $signAward->signModel->sign_status;
            $temp['params']['button_hint'] = "连续" . $button_hint . "天";
            $temp['params']['award_content'] = "可获得" . $award_content;
        }
        return $temp;
    }


    private function getTextTempData($temp)
    {
        $temp['content'] = $this->unescape($temp['content']);

        return $temp;
    }


    private function getMenuTempData($temp)
    {
        if ($temp['params']['num'] == '25%' && count($temp['data']) == 5) {
            array_pop($temp['data']);
        }

        return $temp;
    }

    private function getNearTempData($temp)
    {
        if (!$temp['params']['distance']) {
            $temp['params']['distance'] = 0;
        }
        return $temp;
    }

    private function getOtherData()
    {
        $pageInfo = $this->getPageInfo();
        return array(
            'page_title'    => $pageInfo[0]['params']['title'] ?: "未设置页面标题",
            'page_desc'     => $pageInfo[0]['params']['desc'] ?: "未设置页面简介",
            'page_img'      => $pageInfo[0]['params']['img'] ?: '',
            'page_keyword'  => $pageInfo[0]['params']['kw'] ?: '',
            'footertype'    => intval($pageInfo[0]['params']['footer']),
            'footermenu'    => intval($pageInfo[0]['params']['footermenu']),
        );
    }

    private function processData($data)
    {
        return json_decode(html_entity_decode($data), true);
    }

    //解码字符串，处理文本数据
    public function unescape($str)
    {
        $ret = '';
        $len = strlen($str);
        for ($i = 0; $i < $len; $i++) {
            if ($str[$i] == '%' && $str[$i + 1] == 'u') {
                $val = hexdec(substr($str, $i + 2, 4));
                if ($val < 0x7f)
                    $ret .= chr($val);
                else if ($val < 0x800)
                    $ret .= chr(0xc0 | ($val >> 6)) . chr(0x80 | ($val & 0x3f));
                else
                    $ret .= chr(0xe0 | ($val >> 12)) . chr(0x80 | (($val >> 6) & 0x3f)) . chr(0x80 | ($val & 0x3f));
                $i += 5;
            } else if ($str[$i] == '%') {
                $ret .= urldecode(substr($str, $i, 3));
                $i += 2;
            } else
                $ret .= $str[$i];
        }
        return $ret;
    }




}
