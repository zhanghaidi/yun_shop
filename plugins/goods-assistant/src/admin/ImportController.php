<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/23
 * Time: 下午4:15
 */
namespace Yunshop\GoodsAssistant\admin;

use app\common\components\BaseController;
use app\common\facades\Setting;
use Ixudra\Curl\Facades\Curl;
use app\backend\modules\goods\models\Category;
use app\backend\modules\goods\services\CategoryService;
use Yunshop\GoodsAssistant\services\ImportService;
use app\common\helpers\Url;
use Illuminate\Support\Facades\Storage;
use app\common\exceptions\ShopException;


class ImportController extends BaseController
{
    private $shopset;
    public function __construct()
    {
        $this->shopset = Setting::get('shop.category');
    }

    public function taobao()
    {
        $url = \YunShop::request()->url;
        $parentId = \YunShop::request()->parentId;
        $childId = \YunShop::request()->childId;
        $thirdId = \YunShop::request()->thirdId;
        $goodsType = \YunShop::request()->goodsType;

//        $url = 40296636178;//taobao
//        $url = 565073516098;//tianmao
        if ($url) {
            if (is_numeric($url)) {
                $itemId = $url;
            } else {
                preg_match('/id\=(\d+)/i', $url, $matches);
                if (isset($matches[1])) {
                    $itemId = $matches[1];
                }
            }
            if (empty($itemId)) {
                die(json_encode(array(
                    "result" => 0,
                    "error" => "未获取到 itemid!"
                )));
            }
            if (empty($goodsType)) {
                die(json_encode(array(
                    "result" => 0,
                    "error" => "未选择商品类型"
                )));
            } elseif ($goodsType == 'taobao') {
                $taobao = ImportService::get_item_taobao($itemId, $url, $parentId, $childId, $thirdId);
                die(json_encode($taobao));
            } elseif ($goodsType == 'tmall') {
                $tmall = ImportService::get_item_tmall($itemId, $url, $parentId, $childId, $thirdId);
                die(json_encode($tmall));
            }
        }
        $catetory_menus = CategoryService::getCategoryMenu(['catlevel' => $this->shopset['cat_level']]);
        return view('Yunshop\GoodsAssistant::admin.import',
            [
                'catetory_menus' => $catetory_menus,
                'function' => __FUNCTION__,
                'shopset' => $this->shopset
            ]
        )->render();
    }

    public function jingdong()
    {

        $url = \YunShop::request()->url;
        $parentId = \YunShop::request()->parentId;
        $childId = \YunShop::request()->childId;
        $thirdId = \YunShop::request()->thirdId;

        if ($url) {
            if (is_numeric($url)) {
                $itemId = $url;
            } else {
                preg_match('/(\\d+).html/i', $url, $matches);
                if (isset($matches[1])) {
                    $itemId = $matches[1];
                }
            }
            if (empty($itemId)) {
                die(json_encode(array('result' => 0, 'error' => '未获取到 itemid!')));
            }

            $jingdong = ImportService::get_item_jingdong($itemId, $url, $parentId, $childId, $thirdId);
            die(json_encode($jingdong));
        }
        $catetory_menus = CategoryService::getCategoryMenu(['catlevel' => $this->shopset['cat_level']]);
        return view('Yunshop\GoodsAssistant::admin.import',
            [
                'catetory_menus' => $catetory_menus,
                'function' => __FUNCTION__,
                'shopset' => $this->shopset
            ]
        )->render();
    }

    public function alibaba()
    {
        $url = \YunShop::request()->url;
        $parentId = \YunShop::request()->parentId;
        $childId = \YunShop::request()->childId;
        $thirdId = \YunShop::request()->thirdId;
        if ($url) {
            if (is_numeric($url)) {
                $itemId = $url;
            } else {
                preg_match('/(\\d+).html/i', $url, $matches);
                if (isset($matches[1])) {
                    $itemId = $matches[1];
                }
            }
            if (empty($itemId)) {
                die(json_encode(array('result' => 0, 'error' => '未获取到 itemid!')));
            }
            $alibaba = ImportService::get_item_alibaba($itemId, $url, $parentId, $childId, $thirdId);
            die(json_encode($alibaba));
        }
        $catetory_menus = CategoryService::getCategoryMenu(['catlevel' => $this->shopset['cat_level']]);
        return view('Yunshop\GoodsAssistant::admin.import',
            [
                'catetory_menus' => $catetory_menus,
                'function' => __FUNCTION__,
                'shopset' => $this->shopset
            ]
        )->render();
    }


}