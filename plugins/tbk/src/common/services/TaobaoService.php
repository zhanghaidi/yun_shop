<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com yangyu
 * Date: 2018/12/4
 * Time: 下午4:04
 */

namespace Yunshop\Tbk\common\services;

use app\backend\modules\goods\models\Category;
use app\common\models\Goods;
use app\common\models\GoodsCategory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Ixudra\Curl\Facades\Curl;
use TbkDgItemCouponGetRequest;
use TbkItemInfoGetRequest;
use TbkItemRecommendGetRequest;
use TbkTpwdCreateRequest;
use TbkUatmFavoritesItemGetRequest;
use TopClient;
use Yunshop\Tbk\common\models\GoodsItem;
use Yunshop\Tbk\common\models\TbkCoupon;
use Yunshop\Tbk\common\models\TbkGoods;
use Yunshop\Tbk\common\models\TbkMember;

class TaobaoService
{
    private $client;
    private $ad_zone_id;
    private $pageSize = 100;

    public function __construct($appkey, $secret, $ad_zone_id)
    {
        $this->ad_zone_id = $ad_zone_id;
        $this->client = new TopClient($appkey, $secret);
    }


    /**
     * @return mixed
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param mixed $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
    }

    /**
     * @return mixed
     */
    public function getPageNo()
    {
        return $this->pageNo;
    }

    /**
     * @param mixed $pageNo
     */
    public function setPageNo($pageNo)
    {
        $this->pageNo = $pageNo;
    }

    /**
     * @return mixed
     */
    public function getPages()
    {
        return $this->pages;
    }

    /**
     * @param mixed $pages
     */
    public function setPages($pages)
    {
        $this->pages = $pages;
    }



    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param string $error
     */
    public function setError(string $error)
    {
        $this->error = $error;
    }


    public function item($numIid)
    {
        if (empty($numIid)) {
            $this->error = '非法的num_iid';
            return false;
        }
        $req = new TbkItemInfoGetRequest();
        $req->setFields("num_iid,title,pict_url,small_images,zk_final_price,item_url,volume");
        $req->setNumIids($numIid);
        $resp = $this->client->execute($req);
        if (!empty($resp->results->n_tbk_item)) {
            $items = $resp->results->n_tbk_item;
            foreach ($items as $row) {
                return $this->itemToModel($row);
            }
            return null;
        } else {
            if (isset($resp->code)) {
                $this->error = $resp->code;
                return false;
            }
            return null;
        }
    }

    public function tpwd($text, $url)
    {
        $req = new TbkTpwdCreateRequest;
        $req->setText($text);
        $req->setUrl($url);
        //$req->setLogo($logo);
        $req->setExt("{}");

        $resp = $this->client->execute($req);
        if (isset($resp->code)) {
            return '';
        }

        return $resp->data->model;
    }

    public function getGoodsDetail($num_iid = '552855465528')
    {


        //header('Content-Type: text/html; charset=GBK');
        $url = "http://hws.m.taobao.com/cache/wdesc/5.0/?id=" . $num_iid;
        //        $url = "http://hws.m.taobao.com/cache/wdesc/5.0/?id=558691223667";
        $content = Curl::to($url)->get();
        //$content = iconv("GBK", "utf-8", $content);

        $content = mb_convert_encoding($content, 'UTF-8', 'GBK');
        preg_match('/tfsContent : \'(.*)?\',/', $content, $res);
        //echo $res[1];
        //exit;
        return $res[1];
    }

    public function tbkToUrl($member_id, $num_iid) {
        $full_pid = TbkMember::whereMemberId($member_id)->first()->full_pid;

        //$url = 'http://taoke.applinzi.com/taokelink.php?pid=mm_30612465_232550411_68576700461&itemid=580861237321';
        $url = 'http://taoke.applinzi.com/taokelink.php?pid='.$full_pid.'&itemid='.$num_iid;
        $res = Curl::to($url)->asJsonResponse(true)->get();
        return $res;
        //dd($res);
    }

    //关联商品
    public function recommend($numIid)
    {
        $req = new TbkItemRecommendGetRequest;
        $req->setFields("num_iid,title,pict_url,small_images,reserve_price,zk_final_price,user_type,provcity,item_url,volume");
        $req->setNumIid($numIid);
        $req->setCount("20");
        $req->setPlatform("1");
        $resp = $this->client->execute($req);

        if (!empty($resp->results->n_tbk_item)) {
            $items = $resp->results->n_tbk_item;
            $list = new Collection();
            foreach ($items as $row) {
                $goods = $this->itemToModel($row);
                $list->add($goods);
            }
            return $list;
        } else {
            if (isset($resp->code)) {
                $this->error = $resp->code;
                return false;
            }
            return null;
        }
    }

    //获取选品库列表
    public function getFavouriteList()
    {
        //$this->getGoodsDetail(11);
        $req = new \TbkUatmFavoritesGetRequest();
        $req->setPageNo("1");
        $req->setPageSize($this->pageSize);
        $req->setFields("favorites_title,favorites_id,type");
        $req->setType("1");
        $resp = $this->client->execute($req);
        //dd($resp);
        return $resp;
    }

    //获取优选库宝贝信息
    public function favourite($favoriteId, $pageNo = 1)
    {
        $req = new TbkUatmFavoritesItemGetRequest();
        $req->setPlatform("1");
        $req->setPageSize($this->pageSize);
        $req->setAdzoneId( $this->ad_zone_id);
        //$req->setAdzoneId( '64711400468');
        $req->setFavoritesId($favoriteId);
        $req->setPageNo($pageNo);
        $req->setFields("num_iid,title,pict_url,small_images,reserve_price,zk_final_price,coupon_click_url,coupon_start_time,item_url,coupon_end_time,coupon_remain_count,coupon_info,click_url,status,volume, tk_rate, ");
        //dd($req);
        $resp = $this->client->execute($req);

        $total_page = $resp->total_results / $this->pageSize;

        $this->saveFav($resp);
        //dd($resp);
        //print_r($resp);
        //echo "<br>++++<br>";

        if ($pageNo < $total_page) {
            $this->favourite($favoriteId, $pageNo + 1);
        }

    }

    protected function addTbkCategory($gModel)
    {
        $cateModel = new Category();
        //dd($gModel);
        $category = Category::where('name', $gModel->cat_name)->first();
        if ($category) {
            $second_category = Category::where('name', $gModel->cat_leaf_name)->first();
            if (!$second_category) {
                $cateModel->name = $gModel->cat_leaf_name;
                $cateModel->parent_id = $category->id;
                $cateModel->uniacid = \YunShop::app()->uniacid;
                $cateModel->save();
                $second_id = $cateModel->id;
            } else {
                $second_id = $second_category->id;
            }
            
            return [
                'cate_id' => $category->id,
                'second_id' => $second_id,
            ];
        }

        $cateModel->name = $gModel->cat_name;
        $cateModel->parent_id = 0;
        $cateModel->uniacid = \YunShop::app()->uniacid;

        $cateModel->save();

        $secondCateModel = new Category();
        $secondCateModel->name = $gModel->cat_leaf_name;
        $secondCateModel->parent_id = $cateModel->id;
        $secondCateModel->uniacid = \YunShop::app()->uniacid;
        $secondCateModel->save();

        return [
            'cate_id' => $cateModel->id,
            'second_id' => $secondCateModel->id,
        ];
    }

    protected function itemToModel($gModel, $item)
    {
        //dd($item);
        $goodsShare = new TbkCoupon();
        $goodsShare->goods_id = $gModel->id;
        //$goodsShare->name = $item->title;
        $goodsShare->title = $item->title;
        $goodsShare->uniacid = \YunShop::app()->uniacid;

        //$goodsShare->price = $item->zk_final_price;
        $goodsShare->num_iid = $item->num_iid;;
        $goodsShare->item_url = $item->item_url;
        //$goodsShare->cover = $item->pict_url;
        $goodsShare->volume = $item->volume;
        $goodsShare->status         =   1;
        if(isset($item->coupon_info)){
            $goodsShare->coupon_info          =   $item->coupon_info;
        }
        if (isset($item->coupon_end_time)) {
            $goodsShare->coupon_end_time = $item->coupon_end_time;
        }
        if (isset($item->coupon_amount)) {
            $goodsShare->coupon_amount = $item->coupon_amount;
        }
        if (isset($item->coupon_start_time)) {
            $goodsShare->coupon_start_time = $item->coupon_start_time;
        }
        if (isset($item->coupon_price)) {
            $goodsShare->coupon_price = $item->coupon_price;
        }
        if (isset($item->coupon_status)) {
            $goodsShare->coupon_status = $item->coupon_status;
        }
        if(isset($item->coupon_click_url)){
            $goodsShare->coupon_click_url = $item->coupon_click_url;
        }
        if(isset($item->coupon_remain_count)){
            $goodsShare->coupon_remain_count = $item->coupon_remain_count;
        }
//        if(isset($item->coupon_start_fee)){
//            $goodsShare->coupon_start_fee = $item->coupon_start_fee;
//        }
//        if(isset($item->click_url)){
//            $goodsShare->click_url          =   $item->click_url;
//        }
//        if($goodsShare->isCoupon()){
//            $goodsShare->coupon_status      =   1;
//        }
        //dd($goodsShare);

        if (!$goodsShare->save()) {
            return false;
        }
        return $goodsShare;
    }

    protected function saveTbkGoods($tbkGoodsMode, $gModel, $num_iid) {
        $goodsData = [
            'uniacid'       => \YunShop::app()->uniacid,
            'num_iid'          => $num_iid,
            'goods_id'        => $gModel->id,
            'title'         => $gModel->title,
        ];

        $tbkGoodsMode->fill($goodsData);

        if (!$tbkGoodsMode->save()) {
            return false;
        }
        //dd($goodsModel);
        return $tbkGoodsMode;
    }

    //保存商品分类
    protected function saveGoodsCategory($goodsModel, $cate) {
        $goodsCategory = new GoodsCategory();
        //dd($goodsCategory);
        $goodsCategory->goods_id = $goodsModel->id;
        $goodsCategory->category_id = $cate['second_id'];
        $goodsCategory->category_ids = $cate['cate_id'] .",". $cate['second_id'];
       /* dd($goodsCategory);
        $goodsCategory->fill([
            'goods_id' => $goodsModel->id,
            'category_id' => $cate->second_id,
            'category_ids' => $cate->cate_id .",". $cate->second_id,
            ]);*/
        $goodsCategory->save();
    }

    protected function itemGoods($goodsModel, $item)
    {
        $itemRequest = new TbkItemInfoGetRequest();
        $itemRequest->setNumIids($item->num_iid);

        $goodsItem = $this->client->execute($itemRequest);

        //todo, 处理分类
        //dd($goodsItem);
        $cate = $this->addTbkCategory($goodsItem->results->n_tbk_item);

        //dd($goodsItem);
        $imgs = json_decode(json_encode($item->small_images), true);
        $small_images = $imgs['string'];
        $small_images = serialize($small_images);
        $goodsData = [
            'uniacid'       => \YunShop::app()->uniacid,
            'type'          => 1,
            'status'        => 1,
            'display_order' => 0,
            'title'         => $item->title,
            'thumb'         => $item->pict_url,
            'sku'           => '个',
            'market_price'  => $goodsItem->results->n_tbk_item->reserve_price,
            'price'         => $item->zk_final_price,
            'thumb_url'     => $small_images,
            'cost_price'    => 1,
            'stock'         => '10000',
            'weight'        => 0,
            'is_plugin'     => 0,
            'brand_id'      => 0,
            'plugin_id'     => 188,

        ];

        $goodsModel->fill($goodsData);

        if (!$goodsModel->save()) {
            return false;
        }

        $this->saveGoodsCategory($goodsModel, $cate);
        //dd($goodsModel);
        return $goodsModel;
    }

    private function catchCoupon()
    {
        //$itemRequest->setPlatform(2);

        $page = request()->page;
        $req = new TbkDgItemCouponGetRequest();
        //$req->setQ($keywords);
        $req->setAdzoneId($this->ad_zone_id);
        $req->setPlatform('2');
        $req->setPageSize(100);
        $req->setPageNo(1);
        // dd($req);
        $resp = $this->client->execute($req);
        return $resp;
    }

    public function saveFav($resp, $perPageSize = 50)
    {
        $result = [];
        if ($resp->total_results <= 0 OR !isset($resp->results)) {
            return false;
        }

        $data = $resp->results->uatm_tbk_item;

        if (!$data) {
            return false;
        }

        $list = new Collection();

        foreach ($data as $k => $v) {
            if (!$v->coupon_info) {
                continue;
            }
            //存入商城商品表
            $goodsModel = new Goods();
            $tbkGoodsModel = new TbkGoods();

            preg_match_all('/\d+/', $v->coupon_info, $matches);

            if ($matches) {
                $v->coupon_amount = $matches[0][1];
            } else {
                $v->coupon_amount = 0;
            }
            $v->zk_final_price = floatval($v->zk_final_price);
            $v->coupon_price = floatval($v->zk_final_price - $v->coupon_amount);
            $v->coupon_status = 1;

            $num_id = $v->num_iid;

            //todo, 验证是否被抓取过，抓取过更新
            $tbkCoupon = TbkCoupon::where('num_iid', $v->num_iid)->first();
            if ($tbkCoupon) {
                dd('抓取过');
                return;
            }
            //dd($tbkGoods);

            //存入商城商品表
            $gModel = $this->itemGoods($goodsModel, $v);

            //存入tbk优惠券表
            $couponModel = $this->itemToModel($gModel, $v);
            $tbk = $this->saveTbkGoods($tbkGoodsModel, $gModel, $v->num_iid);
            //dd($couponModel);
        }

        //dd($num_ids);

    }

    /**
     * @param $keywords
     * @param int $perPageSize
     * @return bool|LengthAwarePaginator
     * 优惠券
     */
    public function searchCoupon($keywords, $perPageSize = 50)
    {

        $coupon = $this->catchCoupon();
        $result = [];
        if ($coupon->total_results <= 0 OR !isset($coupon->results)) {
            return false;
        }

        $data = $coupon->results->tbk_coupon;

        if (!$data) {
            return false;
        }
//        dd($resp);

        $list = new Collection();

        //存入商城商品表
        $goodsModel = new Goods();
        $tbkGoodsMode = new TbkGoods();

        $num_ids = [];
        foreach ($data as $k => $v) {
            preg_match_all('/\d+/', $v->coupon_info, $matches);

            if ($matches) {
                $v->coupon_amount = $matches[0][1];
            } else {
                $v->coupon_amount = 0;
            }
            $v->zk_final_price = floatval($v->zk_final_price);
            $v->coupon_price = floatval($v->zk_final_price - $v->coupon_amount);
            $v->coupon_status = 1;

            $num_ids[] = $v->num_iid;

            // $list->add($this->itemGoods($itemRequest, $v));
            // $list->add($v);

            //todo, 验证是否被抓取过
            $tbkGoods = $tbkGoodsMode->whereIn('num_iid', $num_ids)->get();
            dd($tbkGoods);

            //存入tbk商品表
            $gModel = $this->itemGoods($goodsModel, $v);

            //存入tbk优惠券表
            $couponModel = $this->itemToModel($gModel, $v);
            //dd($couponModel);
        }

        //dd($num_ids);

    }
}