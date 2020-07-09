<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/7/23
 * Time: 15:15
 */

namespace Yunshop\JdSupply\services;


use app\backend\modules\goods\models\GoodsDispatch;
use app\common\models\Brand;
use Yunshop\Diyform\admin\DiyformDataController;
use Yunshop\JdSupply\models\Goods;
use Yunshop\JdSupply\models\GoodsOption;
use Yunshop\JdSupply\models\GoodsSpec;
use Yunshop\JdSupply\models\JdGoods;
use Yunshop\JdSupply\models\JdGoodsControl;
use Yunshop\JdSupply\models\JdGoodsOption;
use Yunshop\JdSupply\models\JdPushMessage;
use Yunshop\JdSupply\services\sdk\JdNewClient;
use Yunshop\JdSupply\services\sdk\JdNewRequest;

class JdGoodsService
{
    static $config;
    const JD_GOODS_PLUGIN_ID = 44;
    static $brand;

    /**
     * 获取商品信息
     * @param $jd_goods_id
     * @return array|bool
     */
    public static function requestJd($jd_goods_id)
    {

        $jdGoodsModel = JdGoods::where('jd_goods_id', $jd_goods_id)->first();
        if (!$jdGoodsModel) {
            return false;
        }

        $set = self::getSet();

        $request = new JdNewRequest($set['app_secret'], $set['app_key']);

        $request->addParam('id', $jd_goods_id);

        $response = JdNewClient::get('/v2/Goods/Detail', $request);

        $data = json_decode($response, true);
        $jd_goods = $data['data'];

        if (empty($data) || $data['code'] != 1 || empty($jd_goods)) {
            return false;
        }


        if (empty($jd_goods['specs']['names']) || empty($jd_goods['specs']['values']) || empty($jd_goods['specs']['options'])) {
            return false;
        }

        return [
            'goods_id' => $jdGoodsModel->goods_id,
            'jd_goods' => $jd_goods,
        ];

    }

    /**
     * 批量获取商品信息
     */
    public static function batchGetGoods($jd_goods_ids)
    {
        $exist_ids = JdGoods::whereIn('jd_goods_id', $jd_goods_ids)->pluck('jd_goods_id','goods_id');
        if ($exist_ids->isEmpty()) {
            return false;
        }
        $set = self::getSet();
        $request = new JdNewRequest($set['app_secret'], $set['app_key']);
        $data['ids'] = $exist_ids->implode(',');
        $request->addBody(json_encode($data));
        $response = JdNewClient::post('/v2/goods/GetBulkGoodDetail', $request);
        $data =  json_decode($response, true);
        return ['data'=>$data['data'],'goods_ids'=>$exist_ids->all()];
    }

    public static function getSet()
    {

        if (!isset(static::$config)) {
            static::$config = \Setting::get('plugin.jd_supply');
        }
        return static::$config;
    }

    /**
     * 商品价格变更
     * @param $data
     * @return bool
     */
    public static function updatePrice($data)
    {
        foreach ($data['goodsIds'] as $goodsId) {
            $goods_data = static::requestJd($goodsId);
            if (!$goods_data) {
                continue;
            }
            static::updateGoods($goods_data['goods_id'], $goods_data['jd_goods']);
            JdPushMessage::create(['uniacid' => \YunShop::app()->uniacid, 'type' => 'goods.price.alter', 'goods_id' => $goods_data['goods_id']]);
        }
        return true;
    }

    /**
     * 商品上下架变更消息
     * @param $data
     * @return bool
     */
    public static function updateOnSale($data)
    {
        foreach ($data['goodsIds'] as $goodsId) {
            $goods_data = static::requestJd($goodsId);
            if (!$goods_data) {
                continue;
            }
            static::updateGoods($goods_data['goods_id'], $goods_data['jd_goods']);
            JdPushMessage::create(['uniacid' => \YunShop::app()->uniacid, 'type' => 'goods.on.sale', 'goods_id' => $goods_data['goods_id']]);
        }
        return true;
    }



    public static function updateUnderCarriage($data)
    {
        foreach ($data['goodsIds'] as $goodsId) {
            $jdGoodsModel = JdGoods::where('jd_goods_id', $goodsId)->first();
            if (!$jdGoodsModel) {
                continue;
            }
            $goods_model = Goods::find($jdGoodsModel->goods_id);
            $goods_model->fill([
                'status' => 0,
            ]);
            $goods_model->save();
            JdPushMessage::create(['uniacid' => \YunShop::app()->uniacid, 'type' => 'goods.undercarriage', 'goods_id' => $jdGoodsModel->goods_id]);
        }
        return true;
    }

    /**
     * 批量更新
     * @param $jd_goods_ids
     */
    public static function batchUpdate($jd_goods_ids)
    {
        $list = self::batchGetGoods($jd_goods_ids);
        foreach ($list['data'] as $goods) {
            $goods_id = array_search($goods['id'],$list['goods_ids']);
            $result = static::updateGoods($goods_id, $goods);
            if ($result) {
                $message[] = [
                    'uniacid' => \YunShop::app()->uniacid,
                    'type' => 'goods.alter',
                    'goods_id' => $goods_id,
                    'created_at' => time(),
                    'updated_at' => time(),
                ];
            }
        }
        if (!empty($message)) {
            JdPushMessage::insert($message);
        }
    }


    /**
     * 商品删除
     * @param $data
     * @return bool
     */
    public static function delGoods($data)
    {
        foreach ($data['goodsIds'] as $goodsId) {
            $jdGoodsModel = JdGoods::where('jd_goods_id', $goodsId)->first();
            if ($jdGoodsModel) {
                Goods::where('id', $jdGoodsModel->goods_id)->delete();
                JdGoodsOption::where('goods_id', $jdGoodsModel->goods_id)
                    ->where('jd_goods_id', $goodsId)->delete();
                $jdGoodsModel->delete();
                JdPushMessage::create(['uniacid' => \YunShop::app()->uniacid, 'type' => 'goods.storage.delete', 'goods_id' => $jdGoodsModel->goods_id]);
            }
        }
        return true;
    }

    /**
     * 商品介绍及规格参数变更消息
     * @param $data
     * @return bool
     */
    public static function updateOption($data)
    {
        foreach ($data['goodsIds'] as $goodsId) {
            $goods_data = static::requestJd($goodsId);
            if (!$goods_data) {
                continue;
            }
            static::updateGoods($goods_data['goods_id'], $goods_data['jd_goods']);
            JdPushMessage::create(['uniacid' => \YunShop::app()->uniacid, 'type' => 'goods.alter', 'goods_id' => $goods_data['goods_id']]);
        }
        return true;
    }

    //更新商品
    public static function updateGoods($goods_id, $jd_goods)
    {
        if (empty($jd_goods['specs']['names']) || empty($jd_goods['specs']['values']) || empty($jd_goods['specs']['options'])) {
            return false;
        }
        $jd_goods['param_title'] = array_column($jd_goods['attributes'], 'name');
        $jd_goods['param_value'] = array_column($jd_goods['attributes'], 'value');
        $goods_model = \app\common\models\Goods::find($goods_id);
        $jd_goods_data = [
            'jd_goods_id' => $jd_goods['id'],
            'shop_id' => $jd_goods['shop_id'],
            'source' => $jd_goods['source'],
        ];
        //导入品牌
        self::$brand = intval(self::importBrand($jd_goods['third_brand_name']));

        $data = self::updateGoodsData($jd_goods);

        //关闭自动更新
        $set = self::getSet();
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

        //挂件单独更新
            //$goods_model->widgets = $jd_goods_data;
        $jd_goods_model = JdGoods::where('goods_id', $goods_id)->first();
        $jd_goods_model->fill($jd_goods_data);
        $jd_goods_model->save();

        //配送方式
        $dispatch_model = GoodsDispatch::where(['goods_id' => $goods_id])->first();
        if (empty($dispatch_model)) {
            $dispatch_model = new GoodsDispatch();
        }
        $dispatch_model->setRawAttributes(['goods_id'=>$goods_id,'dispatch_type_ids'=>'1','dispatch_id'=>0]);
        $dispatch_model->save();


        $bool = $goods_model->save();

        if ($bool) {
            \Yunshop\JdSupply\models\GoodsParam::saveParam($jd_goods, $goods_model->id);
            if ($is_close) {
                $spec = GoodsSpec::saveCloseSpec($goods_model->id, $jd_goods['specs'], \YunShop::app()->uniacid);
                $jd_goods = self::options($spec,$jd_goods);
                GoodsOption::saveCloseOption($jd_goods['source'],$goods_model, $jd_goods['specs']['options'], GoodsSpec::$spec_items, \YunShop::app()->uniacid);
            } else {
                $spec = GoodsSpec::saveSpec($goods_model->id, $jd_goods['specs'], \YunShop::app()->uniacid);
                $jd_goods = self::options($spec,$jd_goods);
                GoodsOption::saveOption($jd_goods['source'],$goods_model, $jd_goods['specs']['options'], GoodsSpec::$spec_items, \YunShop::app()->uniacid);

            }
            return true;
        }

        return false;
    }

    public static function updateGoodsData($jd_goods)
    {
        //图片处理
        $jd_goods = self::processImage($jd_goods);
        $price_data = [
            'source'            =>  $jd_goods['source'],
            'guide_price'       =>  $jd_goods['guide_price']/100,
            'agreement_price'   =>  $jd_goods['agreement_price']/100,
            'activity_price'    =>  $jd_goods['activity_price']/100
        ];
        $data = [
            'plugin_id' => self::JD_GOODS_PLUGIN_ID,
            'sku' => $jd_goods['unit'],
            'has_option' => 1, //开启规格
            'status' => $jd_goods['status'],
            'stock' => $jd_goods['stock'], //第三方商品库存  客户购买京东商品时才能判断库存，此处默认设置以免商城内部校验不通过
            'title' => $jd_goods['title'], //第三方商品名称
            'thumb' => $jd_goods['cover'], //第三方商品名称图片
            'thumb_url' => serialize($jd_goods['covers']),
            'price' => self::getGuidePrice($price_data), //第三方商品 指导价格
            'market_price' => $jd_goods['market_price'] / 100, //第三方商品 市场价格
            'cost_price'    =>  self::getCostPrice($price_data), //第三方协议价格
            'virtual_sales' => $jd_goods['sale'] ?: 0,
            'content' => htmlspecialchars($jd_goods['description']), //第三方商品描述
            'brand_id'=>self::$brand
        ];
        return $data;
    }

    public static function processImage($jd_goods)
    {
        $jd_goods['description'] = preg_replace('/\s*/','',$jd_goods['description']);

        $preg = '/(\<img.*?src\=\"(.*?)\".*?\>)/';
        preg_match_all($preg, $jd_goods['description'], $match);
        $preg2 = "/\<link.*?href\=\'.*?\/\/(.*?)\'/";
        preg_match_all($preg2, $jd_goods['description'], $match2);

        $jd_goods['description'] = '';
        foreach ($match[1] as $key => $img) {
            if (strpos($img, 'display: none') === false) {
                $jd_goods['description'] .= '<p><img src="' . $match[2][$key] . '" width="100%" alt="图片破损" style=""/></p>';
            }
        }

        foreach ($match2[1] as $key=>$value) {
            $css = file_get_contents('http://'.$value);
            $preg3 = '/background\-image\:url\((.*?)\)/';
            preg_match_all($preg3, $css, $match3);
            foreach ($match3[1] as $img) {
                $jd_goods['description'] .= '<p><img src="' . $img . '" width="100%" alt="图片破损" style=""/></p>';
            }
        }
        $set = self::getSet();
        if ($set['remove_logo'] == 1) {
            $preg_cover = '/(img13\.360buyimg.com\/n0\/)/';
            $jd_goods['cover'] = preg_replace($preg_cover, 'img13.360buyimg.com/n1/s800x800_', $jd_goods['cover']);
            $jd_goods['covers'] = preg_replace($preg_cover, 'img13.360buyimg.com/n1/s800x800_', $jd_goods['covers']);
        }
        return $jd_goods;
    }

    //销售价 定价策略
    public static function getGuidePrice($price_data)
    {
        $set = self::getSet();
        $price = $price_data['guide_price'];
        $cost_price = $price_data['agreement_price'];
        $activity_price = $price_data['activity_price'];
        switch ($price_data['source']) {
            case 2:
                $price_method = $set['price_method'];
                $price_radio = $set['price_radio']  / 100;
                $cost_price_radio = $set['cost_price_radio']  / 100;
                $market_price_radio = $set['market_price_radio']  / 100;
                break;
            case 6:
                $price_method = $set['ali_price_method'];
                $price_radio = $set['ali_price_radio']  / 100;
                $cost_price_radio = $set['ali_cost_price_radio']  / 100;
                $market_price_radio = $set['ali_market_price_radio']  / 100;
                break;
            case 7:
                $price_method = $set['tm_price_method'];
                $price_radio = $set['tm_price_radio']  / 100;
                $cost_price_radio = $set['tm_cost_price_radio']  / 100;
                $market_price_radio = $set['tm_market_price_radio']  / 100;
                break;
            default:
                return $price;
        }

        if ($price_method == 0 && !empty($price_radio)) {
            return $price * $price_radio;
        } elseif ($price_method == 1 && !empty($cost_price_radio)) {
            return $cost_price * $cost_price_radio;
        } elseif ($price_method == 2 && !empty($market_price_radio)) {
            $activity_price = $activity_price ? :$price;
            return $activity_price * $market_price_radio;
        } else {
            return $price;
        }
    }

    //成本价 定价策略
    public static function getCostPrice($price_data)
    {
        $set = self::getSet();
        $price = $price_data['guide_price'];
        $cost_price = $price_data['agreement_price'];
        $activity_price = $price_data['activity_price'];

        switch ($price_data['source']) {
            case 2:
                $price_method = $set['cost_price_method'];
                $cost_price_radio = $set['cost_price_radio_cost']  / 100;
                $market_price_radio = $set['market_price_radio_cost']  / 100;
                break;
            case 6:
                $price_method = $set['ali_cost_price_method'];
                $cost_price_radio = $set['ali_cost_price_radio_cost']  / 100;
                $market_price_radio = $set['ali_market_price_radio_cost']  / 100;
                break;
            case 7:
                $price_method = $set['tm_cost_price_method'];
                $cost_price_radio = $set['tm_cost_price_radio_cost']  / 100;
                $market_price_radio = $set['tm_market_price_radio_cost']  / 100;
                break;
            default:
                return $price;
        }
        if ($price_method == 0 && !empty($cost_price_radio)) {
            return $cost_price * $cost_price_radio;
        } elseif ($price_method == 1 && !empty($market_price_radio)) {
            $activity_price = $activity_price ? :$price;
            return $activity_price * $market_price_radio;
        } else {
            return $cost_price;
        }
    }


    //风控策略
    public static function controlMethod($price, $cost_price , $goods_model)
    {
        $set = self::getSet();
        //判断商品是否属于风控商品
        $goods_ids = JdGoodsControl::pluck('goods_id')->toArray();
        if (in_array($goods_model->id,$goods_ids)) {
            return true;
        }

        //利润率 < 设定利润率
        if ($set['control_method'] == 1 && !empty($set['profit_radio'])) {
            if ($set['profit_radio']/100 > (($price - $cost_price) / $cost_price)) {
                $goods_model->status = 0;
                $goods_model->save();
                JdPushMessage::create(['uniacid' => \YunShop::app()->uniacid, 'type' => 'goods.control', 'goods_id' => $goods_model->id]);
                return false;
            }
        } else {
            if ($cost_price > $price) {
                $goods_model->status = 0;
                $goods_model->save();
                JdPushMessage::create(['uniacid' => \YunShop::app()->uniacid, 'type' => 'goods.control', 'goods_id' => $goods_model->id]);
                return false;
            }
        }
        return true;
    }

    //导入品牌
    public static function importBrand($brand_name)
    {
        $set = self::getSet();
        if ($brand_name && $set['create_brand']) {
            $brand = Brand::where('uniacid',\YunShop::app()->uniacid)->where('name', $brand_name)->first();
            if (empty($brand)) {
                $brand = new Brand();
                $brand->name = $brand_name;
                $brand->uniacid = \YunShop::app()->uniacid;
                $brand->save();
            }
            return $brand->id;
        }
    }


    //空规格处理
    public static function options($spec,$jd_goods)
    {
        $a = self::cartesian($spec);
        $b = array_column($jd_goods['specs']['options'],'spec_value_ids');
        foreach ($a as $k=>$v) {
            if (!in_array($v,$b)) {
                $jd_goods['specs']['options'][] = [
                    'id'    => 0,
                    'third_id'    => 0,
                    'goods_id'    => $jd_goods['id'],
                    'spec_value_ids'    => $v,
                    'spec_value_names'    => '',
                    'market_price'    => $jd_goods['market_price'],
                    'guide_price'    => $jd_goods['guide_price'],
                    'agreement_price' => $jd_goods['agreement_price'],
                    'stock'    => 0,
                ];
            }
        }
        return $jd_goods;
        //dd($jd_goods['specs']['options']);
        //dd($jd_goods['specs']['options'],$a);
    }
    private static function cartesian($arr,$str = array())
    {
        //去除第一个元素
        $first = array_shift($arr);
        //判断是否是第一次进行拼接
        if (count($str) > 1) {
            foreach ($str as $k => $val) {
                foreach ($first as $key => $value) {
                    //最终实现的格式 1,3,76
                    //可根据具体需求进行变更
                    $str2[] = $val . '_' . $value;
                }
            }
        } else {
            foreach ($first as $key => $value) {
                //最终实现的格式 1,3,76
                //可根据具体需求进行变更
                $str2[] = $value;
            }
        }
        //递归进行拼接
        if (count($arr) > 0) {
            $str2 = self::cartesian($arr, $str2);
        }
        //返回最终笛卡尔积
        return $str2;
    }

}