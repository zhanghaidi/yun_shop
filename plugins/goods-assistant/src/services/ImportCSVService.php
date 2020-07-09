<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/4/27
 * Time: 9:30
 */

namespace Yunshop\GoodsAssistant\services;

use app\backend\modules\goods\models\Goods;
use app\backend\modules\goods\models\GoodsOption;
use app\backend\modules\goods\models\GoodsSpecItem;
use app\backend\modules\goods\services\GoodsOptionService;
use app\backend\modules\goods\services\GoodsService;
use app\backend\modules\goods\models\GoodsParam;
use app\backend\modules\goods\models\GoodsSpec;
use app\common\helpers\Url;
use app\common\models\GoodsCategory;
use Illuminate\Support\Facades\DB;
use app\backend\modules\goods\models\Share;
use Yunshop\GoodsAssistant\models\Import;
use Ixudra\Curl\Facades\Curl;
use app\common\facades\Setting;
use DOMDocument;

class ImportCSVService
{
    public function save_taobaocsv_goods($item = [])
    {
        $data = [
            'goods' => [],
            'plugin'=>[],
        ];

        $data['goods'] = [
            'title'                 => $item['title'],
            'thumb'                 => $item['pics'][0],
            'thumb_url'             => serialize($item['pics']),
            'content'               => $item['content'],  //需要另行处理
            'sku'                   => '件',
            'stock'                 => $item['stock'] ? $item['stock'] : 0,
            'price'                 => $item['price'] ? $item['price'] : 0,
            'brand_id'              => 0,
            'type'                  => 1,
            'reduce_stock_method'   => 0,
            'status'                => 0,
            'has_option'            => 0,
            'uniacid'               => \YunShop::app()->uniacid,
            'created_at'            => time(),
        ];
        $goodsModel = new Goods;
        $res = $goodsModel->create($data['goods']);
        $goods_id = $res->id;

        if ($goods_id) {
            $data['plugin'] = [
                'itemid'    => 1,
                'source'    => 'taobaoCSV',
                'url'       => 'taobaoCSV',
                'goods_id'  => $goods_id,
                'uniacid'   => \YunShop::app()->uniacid,
            ];
            $pluginModel = new  Import;
//            $pluginModel->insert($item['plugin']);
            //之前是用$item['plugin']但是发现并没有值，所以改成了$data['plugin']
            $pluginModel->insert($data['plugin']);
            $item['category']['category_id'] = 0;
            $item['category']['category_ids'] = 0;
            $categoryModel = new  GoodsCategory;
            $item['category']['goods_id'] = $goods_id;
            $item['category']['created_at'] = time();
            $categoryModel->insert($item['category']);

            return true;
        } else {
            return false;
        }
    }

    public function save_image($url, $iscontent)
    {
        global $_W;
        $ext = strrchr($url, '.');

        if (($ext != '.jpeg') && ($ext != '.gif') && ($ext != '.jpg') && ($ext != '.png')) {
            return $url;
        }

        if (trim($url) == '') {
            return $url;
        }

        $filename = random(32) . $ext;
        $save_dir = ATTACHMENT_ROOT . 'images/' . $_W['uniacid'] . '/' . date('Y') . '/' . date('m') . '/';

        if (!(file_exists($save_dir)) && !(mkdir($save_dir, 511, true))) {
            return $url;
        }


        $img = ihttp_get($url);

        if (is_error($img)) {
            return '';
        }


        $img = $img['content'];

        if (strlen($img) != 0) {
            file_put_contents($save_dir . $filename, $img);
            $imgdir = 'images/' . $_W['uniacid'] . '/' . date('Y') . '/' . date('m') . '/';
            $saveurl = save_media($imgdir . $filename, true);
            return $saveurl;
        }


        return '';
    }
}