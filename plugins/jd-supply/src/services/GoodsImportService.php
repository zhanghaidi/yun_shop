<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/26
 * Time: 11:55
 */

namespace Yunshop\JdSupply\services;



use app\backend\modules\goods\models\GoodsFiltering;
use app\backend\modules\goods\models\GoodsParam;
use Yunshop\JdSupply\models\Goods;
use Yunshop\JdSupply\models\GoodsOption;
use Yunshop\JdSupply\models\GoodsSpec;
use Yunshop\JdSupply\models\JdGoods;
use Yunshop\JdSupply\models\JdGoodsControl;
use Yunshop\JdSupply\services\sdk\JdClient;
use Yunshop\JdSupply\services\sdk\JdNewClient;
use Yunshop\JdSupply\services\sdk\JdNewRequest;
use Yunshop\JdSupply\services\sdk\JdRequest;

class GoodsImportService
{
    const JD_GOODS_PLUGIN_ID = 44;
    static $brand;


    public function requestJd($category, $goods_id, $extra = [])
    {
        $set = \Setting::get('plugin.jd_supply');

        $request = new JdNewRequest($set['app_secret'], $set['app_key']);

        $request->addParam('id', $goods_id);
        $response = JdNewClient::get('/v2/Goods/Detail', $request);

        $data =  json_decode($response, true);
        $jd_goods = $data['data'];

        if (empty($data) || $data['code'] != 1 || empty($jd_goods)) {
            return false;
        }
        $jdGoodsModel = JdGoods::where('jd_goods_id',  $jd_goods['id'])->first();


        if (empty($jd_goods['specs']['names']) || empty($jd_goods['specs']['values']) || empty($jd_goods['specs']['options'])) {
            return false;
        }
        $jd_goods['param_title'] = array_column($jd_goods['attributes'],'name');
        $jd_goods['param_value'] = array_column($jd_goods['attributes'],'value');
        if ($jdGoodsModel) {
            return $this->updateGoods($jdGoodsModel->goods_id, $category, $jd_goods, $extra);
        }

        return $this->createGoods($category, $jd_goods, $extra);

    }

    public function updateGoods($goods_id, $category, $jd_goods, $extra)
    {
        $goods_model =  \app\common\models\Goods::find($goods_id);
        $jd_goods_data = [
            'jd_goods_id' => $jd_goods['id'],
            'shop_id' => $jd_goods['shop_id'],
            'source' => $jd_goods['source'],
        ];



        self::$brand = intval(JdGoodsService::importBrand($jd_goods['third_brand_name']));

        $data = $this->updateGoodsData($jd_goods);

        $set = \Setting::get('plugin.jd_supply');

        //关闭自动更新
        $is_close = $set['is_close_auto_update'];
        //单品风控商品
        $goods_ids = JdGoodsControl::pluck('goods_id')->toArray();
        if ($is_close || in_array($goods_id,$goods_ids)) {
            $is_close = 1;
            unset($data['price']);
        }

        if ($set['is_close_auto_detail']) {
            unset($data['title']);
            unset($data['content']);
        }


        $goods_model->fill($data);
        //挂件单独更新,防止重置
        //$goods_model->widgets = $jd_goods_data;
        $jd_goods_model = JdGoods::where('goods_id', $goods_id)->first();
        $jd_goods_model->fill($jd_goods_data);
        $jd_goods_model->save();
        $bool = $goods_model->save();

        if ($extra['f_value_id']) {
            $filtering = [$extra['f_value_id']];
            //标签处理
            GoodsFiltering::where('goods_id', $goods_id)->delete();
            $filtering = array_filter($filtering);
            if ($filtering) {
                foreach ($filtering as $key => $value) {
                    GoodsFiltering::insert([
                        'goods_id' => $goods_id,
                        'filtering_id' => $value
                    ]);
                }
            }
        }

        if ($bool) {
            //商品分类
            \app\backend\modules\goods\services\GoodsService::saveGoodsMultiCategory($goods_model, $category, \Setting::get('shop.category'));
            \Yunshop\JdSupply\models\GoodsParam::saveParam($jd_goods,$goods_model->id);
            if ($is_close) {
                $spec = GoodsSpec::saveCloseSpec($goods_model->id, $jd_goods['specs'], \YunShop::app()->uniacid);
                $jd_goods = JdGoodsService::options($spec,$jd_goods);

                GoodsOption::saveCloseOption($jd_goods['source'],$goods_model, $jd_goods['specs']['options'], GoodsSpec::$spec_items, \YunShop::app()->uniacid);
            } else {
                $spec = GoodsSpec::saveSpec($goods_model->id, $jd_goods['specs'], \YunShop::app()->uniacid);
                $jd_goods = JdGoodsService::options($spec,$jd_goods);
                GoodsOption::saveOption($jd_goods['source'],$goods_model, $jd_goods['specs']['options'], GoodsSpec::$spec_items, \YunShop::app()->uniacid);
            }
            return true;
        }

        return false;
    }


    public function createGoods($category, $jd_goods, $extra)
    {
        $goods_model =  new Goods();
        //导入品牌
        self::$brand = intval(JdGoodsService::importBrand($jd_goods['third_brand_name']));
        $goods_model->fill($this->createGoodsData($jd_goods));
        $jd_goods_data = [
            'jd_supply' => [
                'jd_goods_id' => $jd_goods['id'],
                'shop_id' => $jd_goods['shop_id'],
                'source'  => $jd_goods['source'],
            ]
        ];


        if ($extra['f_value_id']) {
            $jd_goods_data = array_merge($jd_goods_data, ['filtering' => [$extra['f_value_id']]]);
        }

        $goods_model->widgets = array_merge(self::getWidgets(), $jd_goods_data);

        $bool = $goods_model->save();
        if ($bool) {
            //商品分类
            \app\backend\modules\goods\services\GoodsService::saveGoodsMultiCategory($goods_model, $category, \Setting::get('shop.category'));

            //商品属性
            \Yunshop\JdSupply\models\GoodsParam::saveParam($jd_goods,$goods_model->id);
            $spec = GoodsSpec::saveSpec($goods_model->id, $jd_goods['specs'], \YunShop::app()->uniacid);
            $jd_goods = JdGoodsService::options($spec,$jd_goods);
            GoodsOption::saveOption($jd_goods['source'],$goods_model, $jd_goods['specs']['options'], GoodsSpec::$spec_items, \YunShop::app()->uniacid);
            return true;
        }
        return false;

    }

    public function updateGoodsData($jd_goods)
    {
        $jd_goods = JdGoodsService::processImage($jd_goods);

        $price_data = [
            'source'            =>  $jd_goods['source'],
            'guide_price'       =>  $jd_goods['guide_price']/100,
            'agreement_price'   =>  $jd_goods['agreement_price']/100,
            'activity_price'    =>  $jd_goods['activity_price']/100
        ];
        $data = [
            'plugin_id'     => self::JD_GOODS_PLUGIN_ID,
            'sku'           => $jd_goods['unit'],
            'has_option'    => 1, //开启规格
            'status'        => $jd_goods['status'],
            'stock'         => $jd_goods['stock'], //第三方商品库存  客户购买京东商品时才能判断库存，此处默认设置以免商城内部校验不通过
            'title'         => $jd_goods['title'], //第三方商品名称
            'thumb'         => $jd_goods['cover'], //第三方商品名称图片
            'thumb_url'     => serialize($jd_goods['covers']),
            'price'         => JdGoodsService::getGuidePrice($price_data), //第三方商品 指导价格(定价策略)
            'market_price'  => $jd_goods['market_price']/100, //第三方商品 市场价格
            'cost_price'    =>  JdGoodsService::getCostPrice($price_data), //第三方协议价格
            'virtual_sales' => $jd_goods['sale']?:0,
            'content'       => htmlspecialchars($jd_goods['description']), //第三方商品描述
            'brand_id'       => self::$brand, //

        ];
        return $data;

    }

    public function createGoodsData($jd_goods)
    {
        $jd_goods = JdGoodsService::processImage($jd_goods);
        $price_data = [
            'source'            =>  $jd_goods['source'],
            'guide_price'       =>  $jd_goods['guide_price']/100,
            'agreement_price'   =>  $jd_goods['agreement_price']/100,
            'activity_price'    =>  $jd_goods['activity_price']/100
        ];
        $data = [
            'uniacid'       => \YunShop::app()->uniacid,
            'type'          => 1,
            'display_order' => 0,
            'weight'        => 0,
            'is_plugin'     => 0,
            'brand_id'      => 0,
            'plugin_id'     => self::JD_GOODS_PLUGIN_ID,
            'sku'           => $jd_goods['unit'],
            'has_option'    => 1, //开启规格
            'status'        => $jd_goods['status'],
            'stock'         => $jd_goods['stock'], //第三方商品库存  客户购买京东商品时才能判断库存，此处默认设置以免商城内部校验不通过
            'title'         => $jd_goods['title'], //第三方商品名称
            'thumb'         => $jd_goods['cover'], //第三方商品名称图片
            'thumb_url'     => serialize($jd_goods['covers']),
            'price'         => JdGoodsService::getGuidePrice($price_data), //定价策略
            'market_price'  => $jd_goods['market_price']/100, //第三方商品 市场价格
            'cost_price'    =>  JdGoodsService::getCostPrice($price_data), //第三方协议价格
            'virtual_sales' => $jd_goods['sale']?:0,
            'content'       => htmlspecialchars($jd_goods['description']), //第三方商品描述
            'brand_id'       => self::$brand, //
        ];

        return $data;

    }


    public static function getWidgets()
    {
        return [
            'sale' => [
                'max_point_deduct' => '',
                'min_point_deduct' => '',
                'point' => ''
            ],
            'dispatch' => [
                'dispatch_id' => 0,
                'dispatch_price' => 0,
                'dispatch_type' => 1,
                'is_cod' => 1,
                'dispatch_type_ids' => [1],
            ],
            'discount' => [
                'discount_method' => 1,
                'discount_value' => []
            ],
            'service' => [
                'is_automatic' => 0,
            ],
            'team_dividend' => [
                'is_dividend' => 1,
                'has_dividend' => 0,
                'has_dividend_rate' => 0
            ],
            'area_dividend' => [
                'is_dividend' => 1,
                'has_dividend' => 0,
                'has_dividend_rate' => 0
            ],
            'single_return' => [
                'is_single_return' => 1,
                'return_rate' => 0
            ],
            'love' => [
                'deduction' => 1,
                'deduction_proportion' => 0,
                'award' => 1,
                'award_proportion' => 0
            ],
            'merchant' => [
                'is_open_bonus_staff' => 1,
                'is_open_bonus_center' => 1,
                'staff_bonus' => 0
            ],
            'commission' => [
                'is_commission' => 1,
                'rule' => [
                    'level_0' => [
                        'first_level_rate' => 0,
                        'second_level_rate' => 0,
                        'third_level_rate' => 0,
                    ]
                ]
            ],
            'room' => [
                'is_dividend' => 1,
                'has_dividend' => 0,
                'has_dividend_price' => 0,
                'has_dividend_rate' => 0,
            ],
        ];
    }

    public function jdImage($content){

        if(preg_match("/\/\/(.*?)\'/", $content,$url)){
            $url = "http://".$url[1];
            if(!preg_match("/jd\.com/", $url)){
                return $content;
            }
            $data = file_get_contents($url);
            if(trim($data) == ""){
                return $content;
            }
            $body = "";
            if(preg_match_all("/\/\/(.*?);/", $data,$imgs)){
                foreach ($imgs[1] as $key=>$img){
                    $img = "http://".$img;
                    $img = str_replace(")","", $img);
                    $body .= '<p><img src="'.$img.'" width="100%" alt="图片破损" style=""/></p>';
                }
            }
            if($body == ""){
                return $content;
            }
            return $body;
        }
        return $content;
    }

    public function contentDealWith($descriptions)
    {
        $html = '';
        foreach ($descriptions as $image) {
            $html .= '<p><img src="'.$image.'" width="100%" alt="图片破损" style=""/></p>';
        }

        return htmlspecialchars($html);
    }
}