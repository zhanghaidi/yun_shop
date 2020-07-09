<?php


namespace Yunshop\GoodsAssistant\admin;

use app\backend\modules\goods\services\CategoryService;
use app\common\components\BaseController;
use app\common\facades\Setting;
use Yunshop\GoodsAssistant\services\ImportService;


class ImportYzController extends BaseController
{
    private $shopset;

    public function __construct()
    {
        $this->shopset = Setting::get('shop.category');
    }

    public function yzGoods()
    {

        $catetory_menus = CategoryService::getCategoryMenu(['catlevel' => $this->shopset['cat_level']]);
        return view('Yunshop\GoodsAssistant::admin.import',
            [
                'catetory_menus' => $catetory_menus,
                'function' => __FUNCTION__,
                'shopset' => $this->shopset
            ]
        )->render();
    }

    public function getYzGoods()
    {
        $url = \YunShop::request()->url;
        $parentId = \YunShop::request()->parentId;
        $childId = \YunShop::request()->childId;
        $thirdId = \YunShop::request()->thirdId;
        if ($url) {
            if (is_numeric($url)) {
                $itemId = $url;
            } else {
                $url_set = strstr($url, 'goods');
                $url_set = strstr($url_set, '?', TRUE);
                $url_set = explode('/', $url_set);

                $itemId = $url_set[1];
            }
            if (empty($itemId)) {
                die(json_encode(array('result' => 0, 'error' => '未获取到 商品ID!')));
            }

            $goodsData = ImportService::getYzGoods($itemId, $url, $parentId, $childId, $thirdId);
            die(json_encode($goodsData));
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