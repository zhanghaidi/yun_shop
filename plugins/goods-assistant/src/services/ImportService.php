<?php

namespace Yunshop\GoodsAssistant\services;

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/23
 * Time: 下午10:42
 */

use app\backend\modules\goods\models\Share;
use Yunshop\GoodsAssistant\models\Import;
use Ixudra\Curl\Facades\Curl;
use app\common\facades\Setting;
use DOMDocument;

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

class ImportService
{
    public function get_item_tmall($itemid = '', $taobaourl = '', $parentId = 0, $childId = 0, $thirdId = 0)
    {
        error_reporting(0);//关闭报错

        $item = array(
            'goods' => array(),
            'category' => array(),
            'option' => array(),
            'param' => array(),
            'plugin' => array(),
            'spec' => array(),
        );

        $url = self::get_tmall_page_url($itemid);
        $response = ihttp_get($url);
        //判断是否获取到商品
        $length = strval($response['headers']['Content-Length']);
        if ($length != NULL) {
            return array('result' => '0', 'error' => '未从淘宝获取到商品信息!');
        }
        $content = $response['content'];
        //转换utf-8
        if (function_exists('mb_convert_encoding')) {
            $content = mb_convert_encoding($content, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5');
        }

        $dom = new DOMDocument();
        $dom->loadHTML('<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>' . $content);
        $xml = simplexml_import_dom($dom);

        //获取商品规格(再做)
//        $datil = $xml->xpath('//*[@id="J_DetailMeta"]/div[1]/div[1]/div/div[4]/div/div/dl');
//        dd($datil);


        //获取商品标题
        $prodectNameContent = $xml->xpath('//*[@id="J_DetailMeta"]/div[1]/div[1]/div/div[1]');
        $prodectName = trim(strval($prodectNameContent[0]->h1));
        if (empty($prodectName)) {
            $prodectName = trim(strval($prodectNameContent[0]->h1->a));
        }
        $item['goods']['title'] = $prodectName;

        //获取商品图片
        $imgs = array();
        $i = 1;
        while ($i < 6) {
            $img = $xml->xpath('//*[@id="J_UlThumb"]/li[' . $i . ']/a/img');
            if (!(empty($img))) {
                $img = strval($img[0]->attributes()->src);
				$img = mb_substr($img, 0, strpos($img, '_60x60q90.jpg'));
				$img = 'http:' . $img;
				$imgs[] = $img;
			}
            ++$i;
        }
        $item['goods']['thumb'] = $imgs[0];
        $item['goods']['thumb_url'] = serialize(
            array_map(function ($item) {
                return tomedia($item);
            }, $imgs)
        );

        //获取商品信息
        $paramsContent = $xml->xpath('//*[@id="J_AttrList"]');
        $paramsContent = $paramsContent[0]->ul->li;
        $paramsContent = (array) $paramsContent;

        if (!(empty($paramsContent['@attributes']))) {
            unset($paramsContent['@attributes']);
        }
        $params = array();

        foreach ($paramsContent as $paramitem ) {
            $paramitem = strval($paramitem);
            if (!(empty($paramitem))) {
                $paramitem = trim(str_replace('：', ':', $paramitem));
                $p1 = mb_strpos($paramitem, ':');
                $ptitle = mb_substr($paramitem, 0, $p1);
                $pvalue = mb_substr($paramitem, $p1 + 1, mb_strlen($paramitem));
                $param = array('title' => $ptitle, 'value' => $pvalue);
                $params[] = $param;
            }
        }
        $item['param'] = $params;

        //分类
        $shopset = Setting::get('shop.category');
        $category_id = $shopset['cat_level'] == 3 ? $thirdId : $childId;
        $category_ids = $shopset['cat_level'] == 3 ? $parentId . ',' . $childId . ',' . $thirdId : $parentId . ',' . $childId;
        $item['category']['category_id'] = $category_id;
        $item['category']['category_ids'] = $category_ids;

        $item['plugin']['itemid'] = $itemid;
        $item['plugin']['source'] = 'taobao';
        $item['plugin']['url'] = $taobaourl;

//        获取商品详情图片
        $url = self::get_taobao_detail_url($itemid);
        $response = ihttp_get($url);
        $response = self::contentpasswh($response);
        $content = $response['content'];
        if (function_exists('mb_convert_encoding')) {
            $content = mb_convert_encoding($content, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5');
        }
        preg_match_all('/<img.*?src=[\\\\\'| \\"](.*?(?:[\\.gif|\\.jpg]?))[\\\\\'|\\"].*?[\\/]?>/', $content, $imgs);
        if (isset($imgs[1])) {
            foreach ($imgs[1] as $img) {
                $taobaoimg = $img;
                if (substr($taobaoimg, 0, 2) == "//") {
                    $img = "http://" . substr($img, 2);
                }
//                    if (strpos($img, 'imglazyload') !== false) {
//                        continue;
//                    }
                $im = array(
                    "taobao" => $taobaoimg,
                    //"system" => $this->save_image($img, $config)
                );
                $images[] = $im;
            }
        }
        preg_match("/tfsContent : '(.*)'/", $content, $html);
        if ($html[1]) {
            $html = $html[1];
        } else {
            preg_match_all("/tfsContent : '(.*)'/", $content, $html);
            $html=$html[1][0];
        }
        $item['goods']['content'] = $html;  //需要另行处理
        $item['goods']['sku'] = '件';
        $item['goods']['price'] = 0;

        return self::save_goods($item);
    }
    public function get_item_taobao($itemid = '', $taobaourl = '', $parentId = 0, $childId = 0, $thirdId = 0)
    {
        global $_W;
        error_reporting(0);
        $item = array(
            'goods' => array(),
            'category' => array(),
            'option' => array(),
            'param' => array(),
            'plugin' => array(),
            'spec' => array(),
        );
        $url = self::get_tmall_page_url($itemid);
        $response = ihttp_get($url);

        $length = strval($response['headers']['Content-Length']);
        if ($length != NULL) {
            return array('result' => '0', 'error' => '未从淘宝获取到商品信息!');
        }

        $content = $response['content'];
        if (function_exists('mb_convert_encoding')) {
            $content = mb_convert_encoding($content, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5');
        }
        if (strexists($response['content'], 'ERRCODE_QUERY_DETAIL_FAIL')) {
            return array('result' => '0', 'error' => '宝贝不存在!');
        }

        $dom = new DOMDocument();
        $dom->loadHTML('<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>' . $content);
        $xml = simplexml_import_dom($dom);

        preg_match('/var g_config\\s*=(.*);/isU', $content, $match);
        $matchOne = str_replace(array(' ', "\r", "\n", "\t"), array(''), $match[1]);
        $erdr = substr($matchOne, stripos($matchOne, 'sibUrl'));
        $erdr2 = substr($erdr, 0, stripos($erdr, 'descUrl'));
        $two = substr(explode(':', $erdr2)[1], 1);
		$threeUrl = substr($two, 0, -2);
		$detailskip = ihttp_request('https:' . $threeUrl, '', array('referer' => 'https://item.taobao.com?id=' . $itemid, 'accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8', 'accept-encoding' => '', 'accept-language' => 'zh-CN,zh;q=0.9,en;q=0.8', 'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36', 'CURLOPT_USERAGENT' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36'));
		$detailskip = json_decode($detailskip['content'], true);
		$stockArray = array();
		if (($detailskip['code']['code'] == 0) && ($detailskip['code']['message'] == 'SUCCESS')) {
            $stockArray = $detailskip['data']['dynStock']['sku'];
        }

		$specifications = $xml->xpath('//*[@id="J_isku"]/div/dl/dd/ul');
		$specificationsArray = array();
		$guigeArr = array();

		foreach ($specifications as $key => $specificationsInfo ) {
            $sizeArray = (array) $specificationsInfo;
            list($specificationsArray[$key]['title']) = explode(':', $sizeArray['@attributes']['data-property']);
            $sizeLiArray = $sizeArray['li'];

            if (!(is_object($sizeLiArray))) {
                $specificationsArray[$key]['itemsCount'] = count($sizeLiArray);

                foreach ($sizeLiArray as $j => $sizeLiInfo ) {
                    $sizeLiInfoArray = (array) $sizeLiInfo;
                    $guigeArr[$key][$j][] = ';' . $sizeLiInfoArray['@attributes']['data-value'];
                    list($specificationsArray[$key]['propId']) = explode(':', $sizeLiInfoArray['@attributes']['data-value']);
                    list(, $specificationsArray[$key]['items'][$j]['valueId']) = explode(':', $sizeLiInfoArray['@attributes']['data-value']);
                    $sizeLiInfoA = (array) $sizeLiInfoArray['a'];
                    $specificationsTitle = (array) $sizeLiInfoA['span'];
                    $specificationsArray[$key]['items'][$j]['title'] = $specificationsTitle[0];
                    $guigeArr[$key][$j][] = $specificationsTitle[0];
                    $sizeLiInfoAttr = $sizeLiInfoA['@attributes'];

                    if (!(empty($sizeLiInfoAttr['style']))) {
                        $sizeLiInfoAttrStyle = substr($sizeLiInfoAttr['style'], stripos($sizeLiInfoAttr['style'], '//'));
                        $sizeLiInfoAttrStyleUrl = substr($sizeLiInfoAttrStyle, 0, stripos($sizeLiInfoAttrStyle, ')'));
                        $thumb = mb_substr($sizeLiInfoAttrStyleUrl, 0, strpos($sizeLiInfoAttrStyleUrl, '_30x30.jpg'));
                        $specificationsArray[$key]['items'][$j]['thumb'] = $thumb;
                    }
                    else {
                        $specificationsArray[$key]['items'][$j]['thumb'] = '';
                    }
                }
            }
            else {
                $specificationsArray[$key]['itemsCount'] = count($sizeLiArray);
                $objsctArr = (array) $sizeLiArray;
                list($specificationsArray[$key]['propId']) = explode(':', $objsctArr['@attributes']['data-value']);
                list(, $specificationsArray[$key]['items'][0]['valueId']) = explode(':', $objsctArr['@attributes']['data-value']);
                $sizeLiInfoA = (array) $objsctArr['a'];
                $specificationsTitle = (array) $sizeLiInfoA['span'];
                $specificationsArray[$key]['items'][0]['title'] = $specificationsTitle[0];
                $guigeArr[$key][0][] = ';' . $objsctArr['@attributes']['data-value'];
                $guigeArr[$key][0][] = $specificationsTitle[0];
                $sizeLiInfoAttr = $sizeLiInfoA['@attributes'];

                if (!(empty($sizeLiInfoAttr['style']))) {
                    $sizeLiInfoAttrStyle = substr($sizeLiInfoAttr['style'], stripos($sizeLiInfoAttr['style'], '//'));
                    $sizeLiInfoAttrStyleUrl = substr($sizeLiInfoAttrStyle, 0, stripos($sizeLiInfoAttrStyle, ')'));
                    $thumb = mb_substr($sizeLiInfoAttrStyleUrl, 0, strpos($sizeLiInfoAttrStyleUrl, '_30x30.jpg'));
                    $specificationsArray[$key]['items'][0]['thumb'] = $thumb;
                }
                else {
                    $specificationsArray[$key]['items'][0]['thumb'] = '';
                }
            }
        }

		$item['spec'] = self::my_sort($specificationsArray, 'itemsCount', SORT_ASC, SORT_STRING);
		$count = count($guigeArr);

		if ($count == 1) {
            $i = 0;

            while ($i < count($guigeArr[0])) {
                $value = $guigeArr[0][$i][0];
                $title = $guigeArr[0][$i][1];
                $arr[] = $value . ';|' . $title;
                ++$i;
            }
        }
        else if ($count == 2) {
            $i = 0;

            while ($i < count($guigeArr[0])) {
                $value = $guigeArr[0][$i][0];
                $title = $guigeArr[0][$i][1];
                $j = 0;

                while ($j < count($guigeArr[1])) {
                    $valueTwo = $value . $guigeArr[1][$j][0];
                    $titleTwo = $title . '+' . $guigeArr[1][$j][1];
                    $arr[] = $valueTwo . ';|' . $titleTwo;
                    ++$j;
                }

                ++$i;
            }
        }
        else if ($count == 3) {
            $i = 0;

            while ($i < count($guigeArr[0])) {
                $value = $guigeArr[0][$i][0];
                $title = $guigeArr[0][$i][1];
                $j = 0;

                while ($j < count($guigeArr[1])) {
                    $valueTwo = $value . $guigeArr[1][$j][0];
                    $titleTwo = $title . '+' . $guigeArr[1][$j][1];
                    $g = 0;

                    while ($g < count($guigeArr[2])) {
                        $valueThree = $valueTwo . $guigeArr[2][$g][0];
                        $titleThree = $titleTwo . '+' . $guigeArr[2][$g][1];
                        $arr[] = $valueThree . ';|' . $titleThree;
                        ++$g;
                    }

                    ++$j;
                }

                ++$i;
            }
        }
        else if ($count == 4) {
            $i = 0;

            while ($i < count($guigeArr[0])) {
                $value = $guigeArr[0][$i][0];
                $title = $guigeArr[0][$i][1];
                $j = 0;

                while ($j < count($guigeArr[1])) {
                    $valueTwo = $value . $guigeArr[1][$j][0];
                    $titleTwo = $title . '+' . $guigeArr[1][$j][1];
                    $g = 0;

                    while ($g < count($guigeArr[2])) {
                        $valueThree = $valueTwo . $guigeArr[2][$g][0];
                        $titleThree = $titleTwo . '+' . $guigeArr[2][$g][1];
                        $r = 0;

                        while ($r < count($guigeArr[3])) {
                            $valueFour = $valueThree . $guigeArr[3][$r][0];
                            $titleFour = $titleThree . '+' . $guigeArr[3][$r][1];
                            $arr[] = $valueFour . ';|' . $titleFour;
                            ++$r;
                        }

                        ++$g;
                    }

                    ++$j;
                }

                ++$i;
            }
        }
        else if ($count == 5) {
            $i = 0;

            while ($i < count($guigeArr[0])) {
                $value = $guigeArr[0][$i][0];
                $title = $guigeArr[0][$i][1];
                $j = 0;

                while ($j < count($guigeArr[1])) {
                    $valueTwo = $value . $guigeArr[1][$j][0];
                    $titleTwo = $title . '+' . $guigeArr[1][$j][1];
                    $g = 0;

                    while ($g < count($guigeArr[2])) {
                        $valueThree = $valueTwo . $guigeArr[2][$g][0];
                        $titleThree = $titleTwo . '+' . $guigeArr[2][$g][1];
                        $r = 0;

                        while ($r < count($guigeArr[3])) {
                            $valueFour = $valueThree . $guigeArr[3][$g][0];
                            $titleFour = $titleThree . '+' . $guigeArr[3][$g][1];
                            $t = 0;

                            while ($t < count($guigeArr[4])) {
                                $valueFive = $valueFour . $guigeArr[4][$t][0];
                                $titleFive = $titleFour . '+' . $guigeArr[4][$t][1];
                                $arr[] = $valueFive . ';|' . $titleFive;
                                ++$t;
                            }

                            ++$r;
                        }

                        ++$g;
                    }

                    ++$j;
                }

                ++$i;
            }
        }
        else if ($count == 6) {
            $i = 0;

            while ($i < count($guigeArr[0])) {
                $value = $guigeArr[0][$i][0];
                $title = $guigeArr[0][$i][1];
                $j = 0;

                while ($j < count($guigeArr[1])) {
                    $valueTwo = $value . $guigeArr[1][$j][0];
                    $titleTwo = $title . '+' . $guigeArr[1][$j][1];
                    $g = 0;

                    while ($g < count($guigeArr[2])) {
                        $valueThree = $valueTwo . $guigeArr[2][$g][0];
                        $titleThree = $titleTwo . '+' . $guigeArr[2][$g][1];
                        $r = 0;

                        while ($r < count($guigeArr[3])) {
                            $valueFour = $valueThree . $guigeArr[3][$g][0];
                            $titleFour = $titleThree . '+' . $guigeArr[3][$g][1];
                            $t = 0;

                            while ($t < count($guigeArr[4])) {
                                $valueFive = $valueFour . $guigeArr[4][$t][0];
                                $titleFive = $titleFour . '+' . $guigeArr[4][$t][1];
                                $k = 0;

                                while ($k < count($guigeArr[5])) {
                                    $valueSix = $valueFive . $guigeArr[5][$k][0];
                                    $titleSix = $titleFive . '+' . $guigeArr[5][$k][1];
                                    $arr[] = $valueSix . ';|' . $titleSix;
                                    ++$k;
                                }

                                ++$t;
                            }

                            ++$r;
                        }

                        ++$g;
                    }

                    ++$j;
                }

                ++$i;
            }
        }

		$item['total'] = 0;

		foreach ($arr as $key => $asdInfo ) {
            $asdInfoArrAs = explode('|', $asdInfo);
            $asdInfoArr = explode(';', $asdInfoArrAs[0]);
            $asdInfoArr = array_filter($asdInfoArr);
            $j = 0;

            foreach ($asdInfoArr as $asdInfoArrInfo ) {
                $asdInfoArrInfoArr = explode(':', $asdInfoArrInfo);
                $item['option'][$key]['option_specs'][$j]['propId'] = $asdInfoArrInfoArr[0];
                $item['option'][$key]['option_specs'][$j]['valueId'] = $asdInfoArrInfoArr[1];
                ++$j;
            }

            if (!(empty($stockArray[$asdInfoArrAs[0]]))) {
                $item['option'][$key]['stock'] = $stockArray[$asdInfoArrAs[0]]['stock'];
                $item['total'] = $item['total'] + $stockArray[$asdInfoArrAs[0]]['stock'];
            }
            else {
                $item['option'][$key]['stock'] = 0;
            }

            $item['option'][$key]['title'] = explode('+', $asdInfoArrAs[1]);
            $item['option'][$key]['marketprice'] = $detailskip['data']['price'];
        }

		$prodectNameContent = $xml->xpath('//*[@id="J_Title"]');
		$titleArr = (array) $prodectNameContent[0];
		$item['goods']['title'] = trim(strval($titleArr['h3']));
		$prodectDescContent = $xml->xpath('//div/div/div/div/div/div/div/div/div/div/div[1]');
		$item['subTitle'] = trim(strval($prodectDescContent[1]->p));
		$prodectPrice = $xml->xpath('//*[@id="J_StrPrice"]');
		$prodectPriceArr = (array) $prodectPrice[0];
		$taoBaoPrice = trim(strval($prodectPriceArr['em'][1]));
		list($item['goods']['price']) = explode('-', $taoBaoPrice);
		$imgs = array();
		$i = 1;

		while ($i < 6) {
            $img = $xml->xpath('//*[@id="J_UlThumb"]/li[' . $i . ']');
            if (!(empty($img))) {
                $img = strval($img[0]->div->a->img['data-src']);
                $img = mb_substr($img, 0, strpos($img, '_50x50.jpg'));
                $imgs[] = $img;
            }
            ++$i;
        }
        $item['goods']['thumb'] = $imgs[0];
        $item['goods']['thumb_url'] = serialize(
            array_map(function ($item) {
                return tomedia($item);
            }, $imgs)
        );

		$paramsContent = $xml->xpath('//*[@id="attributes"]');
		$paramsContent = $paramsContent[0]->ul->li;
		$paramsContent = (array) $paramsContent;

		if (!(empty($paramsContent['@attributes']))) {
            unset($paramsContent['@attributes']);
        }

		$params = array();

		foreach ($paramsContent as $paramitem ) {
            $paramitem = strval($paramitem);

            if (!(empty($paramitem))) {
                $paramitem = trim(str_replace('：', ':', $paramitem));
                $p1 = mb_strpos($paramitem, ':');
                $ptitle = mb_substr($paramitem, 0, $p1);
                $pvalue = mb_substr($paramitem, $p1 + 1, mb_strlen($paramitem));
                $param = array('title' => $ptitle, 'value' => $pvalue);
                $params[] = $param;
            }

        }
		$item['param'] = $params;

		//分类
        $shopset = Setting::get('shop.category');
        $category_id = $shopset['cat_level'] == 3 ? $thirdId : $childId;
        $category_ids = $shopset['cat_level'] == 3 ? $parentId . ',' . $childId . ',' . $thirdId : $parentId . ',' . $childId;
        $item['category']['category_id'] = $category_id;
        $item['category']['category_ids'] = $category_ids;

        $item['plugin']['itemid'] = $itemid;
        $item['plugin']['source'] = 'taobao';
        $item['plugin']['url'] = $taobaourl;

        $url = self::get_detail_url($itemid, 'taobao');
        $response = Curl::to($url)
            ->get();
        $content = preg_replace("/<img.+(imglazyload\/spaceball)+[^>]+>/", '', $response);
        preg_match_all("/<img.*?src=[\'| \"](.*?(?:[\.gif|\.jpg]?))[\'|\"].*?[\/]?>/", $content, $imgs);
        if (isset($imgs[1])) {
            foreach ($imgs[1] as $img) {
                $taobaoimg = $img;
                if (substr($taobaoimg, 0, 2) == "//") {
                    $img = "http://" . substr($img, 2);
                }
//                    if (strpos($img, 'imglazyload') !== false) {
//                        continue;
//                    }
                $im = array(
                    "taobao" => $taobaoimg,
                    //"system" => $this->save_image($img, $config)
                );
                $images[] = $im;
            }
        }
        preg_match("/tfsContent : '(.*)'/", $content, $html);
        $html = iconv("GBK", "UTF-8", $html[1]);
        $item['goods']['content'] = $html;  //需要另行处理
        $item['goods']['sku'] = '件';
		return self::save_goods($item);
	}

    public function get_tmall_page_url($itemid)
    {
        $url = 'https://detail.tmall.com/item.htm?id=' . $itemid;
        $url = self::getRealURL($url);
        return $url;
    }
    public function getRealURL($url)
    {
        if (function_exists('stream_context_set_default')) {
            stream_context_set_default(array(
                'http' => array('method' => 'HEAD')
            ));
        }
        $header = get_headers($url, 1);
        if (strpos($header[0], '301') || strpos($header[0], '302')) {
            if (is_array($header['Location'])) {
                return $header['Location'][count($header['Location']) - 1];
            }
            return $header['Location'];
        }
        return $url;
    }
    public function my_sort($arrays, $sort_key, $sort_order = SORT_ASC, $sort_type = SORT_NUMERIC)
    {
        if (is_array($arrays)) {
            foreach ($arrays as $array ) {
                if (is_array($array)) {
                    $key_arrays[] = $array[$sort_key];
                }
                else {
                    return false;
                }
            }
        }
        else {
            return false;
        }

        array_multisort($key_arrays, $sort_order, $sort_type, $arrays);
        return $arrays;
    }
    public function get_taobao_detail_url($itemid)
    {
        $url = 'http://hws.m.taobao.com/cache/wdesc/5.0/?id=' . $itemid;
        $url = self::getRealURL($url);
        return $url;
    }
    public function contentpasswh($content)
    {
        $content = preg_replace('/(?:width)=(\'|").*?\\1/', ' width="100%"', $content);
        $content = preg_replace('/(?:height)=(\'|").*?\\1/', ' ', $content);
        $content = preg_replace('/(?:max-width:\\s*\\d*\\.?\\d*(px|rem|em))/', '', $content);
        $content = preg_replace('/(?:max-height:\\s*\\d*\\.?\\d*(px|rem|em))/', '', $content);
        $content = preg_replace('/(?:min-width:\\s*\\d*\\.?\\d*(px|rem|em))/', ' ', $content);
        $content = preg_replace('/(?:min-height:\\s*\\d*\\.?\\d*(px|rem|em))/', ' ', $content);
        return $content;
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

    public static function get_item_taobao1($itemId = '', $sourceUrl = '', $parentId = 0, $childId = 0, $thirdId = 0)
    {

        $shopset = Setting::get('shop.category');
        $info = Import::getInfo($itemId, 'taobao');
        if ($info) {
            //已导入过该商品,如何处理
        }
        $url = self::get_info_url($itemId, 'taobao');
        $response = Curl::to($url)->get();
        if (!isset($response)) {
            return array(
                "result" => '0',
                "error" => '未从淘宝获取到商品信息!'
            );
        }
        if (strexists($response, "ERRCODE_QUERY_DETAIL_FAIL")) {
            return array(
                "result" => '0',
                "error" => '宝贝不存在!'
            );
        }
        $arr = json_decode($response, true);
        $data = $arr['data'];
        $itemInfoModel = $data['itemInfoModel'];
        $category_id = $shopset['cat_level'] == 3 ? $thirdId : $childId;
        $category_ids = $shopset['cat_level'] == 3 ? $parentId . ',' . $childId . ',' . $thirdId : $parentId . ',' . $childId;
        $item = array(
            'goods' => array(),
            'category' => array(),
            'option' => array(),
            'param' => array(),
            'plugin' => array(),
            'spec' => array(),
        );

        $item['goods']['title'] = $itemInfoModel['title'];
        $item['goods']['thumb'] = $itemInfoModel['picsPath'][0];
        //$item['goods']['thumb_url'] = $itemInfoModel['picsPath'];
        $item['goods']['sku'] = '件';
        $item['goods']['thumb_url'] = serialize(
            array_map(function ($item) {
                return tomedia($item);
            }, $itemInfoModel['picsPath'])
        );

        $item['category']['category_id'] = $category_id;
        $item['category']['category_ids'] = $category_ids;

        $item['plugin']['itemid'] = $itemInfoModel['itemId'];
        $item['plugin']['source'] = 'taobao';
        $item['plugin']['url'] = $sourceUrl;

        $params = array();
        if (isset($data['props'])) {
            $props = $data['props'];
            foreach ($props as $pp) {
                $params[] = array(
                    "title" => $pp['name'],
                    "value" => $pp['value']
                );
            }
        }
        $item['param'] = $params;

        $specs = array();
        $options = array();
        if (isset($data['skuModel'])) {
            $skuModel = $data['skuModel'];
            if (isset($skuModel['skuProps'])) {
                $skuProps = $skuModel['skuProps'];
                foreach ($skuProps as $prop) {
                    $spec_items = array();
                    foreach ($prop['values'] as $spec_item) {
                        $spec_items[] = array(
                            'valueId' => $spec_item['valueId'],
                            'title' => $spec_item['name'],
                            "thumb" => !empty($spec_item['imgUrl']) ? $spec_item['imgUrl'] : ''
                        );
                    }
                    $spec = array(
                        "propId" => $prop['propId'],
                        "title" => $prop['propName'],
                        "items" => $spec_items
                    );
                    $specs[] = $spec;
                }
            }
            if (isset($skuModel['ppathIdmap'])) {
                $ppathIdmap = $skuModel['ppathIdmap'];
                foreach ($ppathIdmap as $key => $skuId) {
                    $option_specs = array();
                    $m = explode(";", $key);
                    foreach ($m as $v) {
                        $mm = explode(":", $v);
                        $option_specs[] = array(
                            "propId" => $mm[0],
                            "valueId" => $mm[1]
                        );
                    }
                    $options[] = array(
                        "option_specs" => $option_specs,
                        "skuId" => $skuId,
                        "stock" => 0,
                        "marketprice" => 0,
                        "specs" => ""
                    );
                }
            }
        }
        $item['spec'] = $specs;


        $stack = $data['apiStack'][0]['value'];
        $value = json_decode($stack, true);
        $item_stack = array();
        $data_stack = $value['data'];
        $itemInfoModel_stack = $data_stack['itemInfoModel'];
        $item['goods']['stock'] = $itemInfoModel_stack['quantity'];
        $item['goods']['show_sales'] = $itemInfoModel_stack['totalSoldQuantity'];
        $item['goods']['real_sales'] = $itemInfoModel_stack['totalSoldQuantity'];
        if (isset($data_stack['skuModel'])) {
            $skuModel_stack = $data_stack['skuModel'];
            if (isset($skuModel_stack['skus'])) {
                $skus = $skuModel_stack['skus'];
                foreach ($skus as $key => $val) {
                    $sku_id = $key;
                    foreach ($options as &$o) {
                        if ($o['skuId'] == $sku_id) {
                            $o['stock'] = $val['quantity'];
                            foreach ($val['priceUnits'] as $p) {
                                $o['marketprice'] = $p['price'];
                                $item['goods']['price'] = $p['price'];
                            }
                            $titles = array();
                            foreach ($o['option_specs'] as $osp) {
                                foreach ($specs as $sp) {
                                    if ($sp['propId'] == $osp['propId']) {
                                        foreach ($sp['items'] as $spitem) {
                                            if ($spitem['valueId'] == $osp['valueId']) {
                                                $titles[] = $spitem['title'];
                                            }
                                        }
                                    }
                                }
                            }
                            $o['title'] = $titles;
                        }
                    }
                    unset($o);
                }

            }
        } else {
            $mprice = 0;
            foreach ($itemInfoModel_stack['priceUnits'] as $p) {
                $mprice = $p['price'];
            }
            $item['goods']['price'] = $mprice;
        }
        $item['option'] = $options;
        $url = self::get_detail_url($itemId, 'taobao');
        $response = Curl::to($url)
            ->get();
        $content = preg_replace("/<img.+(imglazyload\/spaceball)+[^>]+>/", '', $response);
        preg_match_all("/<img.*?src=[\'| \"](.*?(?:[\.gif|\.jpg]?))[\'|\"].*?[\/]?>/", $content, $imgs);
        if (isset($imgs[1])) {
            foreach ($imgs[1] as $img) {
                $taobaoimg = $img;
                if (substr($taobaoimg, 0, 2) == "//") {
                    $img = "http://" . substr($img, 2);
                }
//                    if (strpos($img, 'imglazyload') !== false) {
//                        continue;
//                    }
                $im = array(
                    "taobao" => $taobaoimg,
                    //"system" => $this->save_image($img, $config)
                );
                $images[] = $im;
            }
        }
        preg_match("/tfsContent : '(.*)'/", $content, $html);
        $html = iconv("GBK", "UTF-8", $html[1]);
        $item['goods']['content'] = $html;  //需要另行处理
        //dd($item);
        return self::save_goods($item);
    }

    public static function get_item_jingdong($itemId = '', $sourceUrl = '', $parentId = 0, $childId = 0, $thirdId = 0)
    {
        $shopset = Setting::get('shop.category');
        $info = Import::getInfo($itemId, 'jingdong');
        if ($info) {
            //已导入过该商品,如何处理
        }
        $url = self::get_info_url($itemId, 'jingdong');
        $response = Curl::to($url)->withOption('FOLLOWLOCATION', true)->get();
        //dd($response);
        /*$length = strval($response['headers']['Content-Length']);
        if ($length != null) {
            return array('result' => '0', 'error' => '未从京东获取到商品信息!');
        }*/
        $content = $response;
        $dom = new DOMDocument();
        $dom->loadHTML('<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>' . $content);
        $xml = simplexml_import_dom($dom);
        $prodectNameContent = $xml->xpath('//*[@id="goodName"]');
        $prodectName = strval($prodectNameContent[0]->attributes()->value);
        if ($prodectName == null) {
            return array(
                "result" => '0',
                "error" => '宝贝不存在!'
            );
        }
        $category_id = $shopset['cat_level'] == 3 ? $thirdId : $childId;
        $category_ids = $shopset['cat_level'] == 3 ? $parentId . ',' . $childId . ',' . $thirdId : $parentId . ',' . $childId;
        $item = array(
            'goods' => array(),
            'category' => array(),
            'options' => array(),
            'param' => array(),
            'plugin' => array(),
            'specs' => array(),

        );
        $pics = array();
        $imgurls = $xml->xpath('//*[@id="slide"]/ul');
        foreach ($imgurls[0]->li as $imgurl) {
            if (count($pics) < 4) {
                $pics[] = 'http:' . $imgurl->img->attributes()->src;
            }
        }
        $item['goods']['title'] = $prodectName;
        $item['goods']['thumb'] = $pics[0];
        $item['goods']['sku'] = '件';
        //$item['goods']['thumb_url'] = $pics;
        $item['goods']['thumb_url'] = serialize(
            array_map(function ($item) {
                return tomedia($item);
            }, $pics)
        );


        $item['category']['category_id'] = $category_id;
        $item['category']['category_ids'] = $category_ids;

        $item['plugin']['itemid'] = $itemId;
        $item['plugin']['source'] = 'jingdong';
        $item['plugin']['url'] = $sourceUrl;


        $specs = array();
        $item['goods']['stock'] = 10;
        $item['goods']['show_sales'] = 0;
        $prodectPriceContent = $xml->xpath('//*[@id="jdPrice"]');
        $prodectPrices = strval($prodectPriceContent[0]->attributes()->value);

        $item['goods']['price'] = $prodectPrices;
        $url = self::get_detail_url($itemId, 'jingdong');
        $responseDetail = Curl::to($url)
            ->get();
        //dd($responseDetail);
        $contenteDetail = $responseDetail;
        $details = json_decode($contenteDetail, true);
        $prodectContent = $details[wdis];
        $item['goods']['content'] = strval($prodectContent);
        $params = array();
        $pr = json_decode($details[ware][wi][code]);
        //dd($pr);
        /*preg_match_all('/<td class="tdTitle">(.*?)<\\/td>/i', $pr, $params1);
        preg_match_all('/<td>(.*?)<\\/td>/i', $pr, $params2);
        $paramsTitle = $params1[1];
        $paramsValue = $params2[1];
        if (count($paramsTitle) == count($paramsValue)) {
            $i = 0;
            while ($i < count($paramsTitle)) {
                $params[] = array('title' => $paramsTitle[$i], 'value' => $paramsValue[$i]);
                ++$i;
            }
        }*/
        $item['param'] = $params;

        //dd($item);
        return self::save_goods($item);
    }

    public static function get_item_alibaba($itemId = '', $sourceUrl = '', $parentId = 0, $childId = 0, $thirdId = 0)
    {
        $shopset = Setting::get('shop.category');
        $info = Import::getInfo($itemId, 'alibaba');
        if ($info) {
            //已导入过该商品,如何处理
        }
        $url = self::get_info_url($itemId, 'alibaba');
        //dd($url);
        $response = Curl::to($url)
            ->withOption('FOLLOWLOCATION', true)
            ->withOption('USERAGENT',
                'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36')
            ->get();
        //$response = iconv("GBK", "UTF-8", $response);
        //dd($response);
        /*$length = strval($response['headers']['Content-Length']);
        if ($length != NULL) {
            return array('result' => '0', 'error' => '未从1688获取到商品信息!');
        }*/;
        //dd($content);
        $dom = new DOMDocument();
        $dom->loadHTML('<meta http-equiv="Content-Type" content="text/html; charset=gbk"/>' . $response);
        $xml = simplexml_import_dom($dom);
        $prodectNameContent = $xml->xpath('//*[@id="mod-detail-title"]/h1');
        $prodectName = strval($prodectNameContent[0]);
        if ($prodectName == null) {
            return array(
                "result" => '0',
                "error" => '宝贝不存在!'
            );
        }
        $category_id = $shopset['cat_level'] == 3 ? $thirdId : $childId;
        $category_ids = $shopset['cat_level'] == 3 ? $parentId . ',' . $childId . ',' . $thirdId : $parentId . ',' . $childId;
        $item = array(
            'goods' => array(),
            'category' => array(),
            'options' => array(),
            'param' => array(),
            'plugin' => array(),
            'specs' => array(),

        );
        $pics = array();
        $imgurls = $xml->xpath('//*[@id="dt-tab"]/div/ul');
        foreach ($imgurls[0]->li as $imgurlContent) {
            $imgurlall = $imgurlContent->attributes();
            $img = json_decode($imgurlall[1], true);
            if (count($pics) < 4) {
                $pics[] = $img['original'];
            }
        }
        $item['goods']['title'] = $prodectName;
        $item['goods']['thumb'] = $pics[0];
        $item['goods']['sku'] = '件';
        //$item['goods']['thumb_url'] = $pics;
        $item['goods']['thumb_url'] = serialize(
            array_map(function ($item) {
                return tomedia($item);
            }, $pics)
        );


        $item['category']['category_id'] = $category_id;
        $item['category']['category_ids'] = $category_ids;

        $item['plugin']['itemid'] = $itemId;
        $item['plugin']['source'] = 'alibaba';
        $item['plugin']['url'] = $sourceUrl;

        $item['goods']['stock'] = 10;
        $item['goods']['show_sales'] = 0;
        $prodectPriceContent = $xml->xpath('//*[@property="og:product:price"]');
        $prodectPrices = strval($prodectPriceContent[0]->attributes()->content);
        $item['goods']['price'] = $prodectPrices;
        $prodectContent = $xml->xpath('//*[@id="desc-lazyload-container"]');
        $Contents = $prodectContent[0]->attributes();
        $detail = $Contents['data-tfs-url'];
        $contenteDetail = Curl::to(strval($detail))->get();
        $contenteDetail = iconv('GB2312', 'UTF-8', $contenteDetail);
        if (strpos($contenteDetail, '{')) {
            $contenteDetail = substr($contenteDetail, strpos($contenteDetail, '{'), -1);
            $details = json_decode($contenteDetail, true);
            $prodectContent = $details;
            $prodectContent = preg_replace('/ (?:height|width)=(\'|").*?\\1/', '', $prodectContent);
            $item['goods']['content'] = $prodectContent['content'];
        }
        $params = $xml->xpath('//*[@id="mod-detail-attributes"]/div[1]/table/tbody');
        $paramsTitle = array();
        $paramsValue = array();
        foreach ($params[0]->tr as $tr) {
            foreach ($tr[0]->td as $td) {
                if ($td->attributes()->class == 'de-feature' && 0 < strlen(strval($td))) {
                    $paramsTitle[] = strval($td);
                }
                if ($td->attributes()->class == 'de-value' && 0 < strlen(strval($td))) {
                    $paramsValue[] = strval($td);
                }
            }
        }
        $params = array();
        if (count($paramsTitle) == count($paramsValue)) {
            $i = 0;
            while ($i < count($paramsTitle)) {
                $params[] = array('title' => $paramsTitle[$i], 'value' => $paramsValue[$i]);
                ++$i;
            }
        }
        $item['param'] = $params;
        //dd($item);
        return self::save_goods($item);
    }

    public static function save_goods($item = array())
    {
        //获取模型

        if (!empty($item['goods'])) {
            $goodsModel = new Goods;
            $item['goods']['brand_id'] = 0;
            $item['goods']['type'] = 1;
            $item['goods']['reduce_stock_method'] = 0;
            $item['goods']['status'] = 0;
            $item['goods']['has_option'] = count($item['option']) > 0 ? 1 : 0;
            $item['goods']['uniacid'] = \YunShop::app()->uniacid;
            $item['goods']['created_at'] = time();
        }
        $res = $goodsModel->create($item['goods']);
        $goods_id = $res->id;
        if ($goods_id) {
            if (!empty($item['plugin'])) {
                $pluginModel = new  Import;
                $item['plugin']['goods_id'] = $goods_id;
                $item['plugin']['uniacid'] = \YunShop::app()->uniacid;
                $pluginModel->insert($item['plugin']);
            }
            if (!empty($item['category'])) {
                $categoryModel = new  GoodsCategory;
                $item['category']['goods_id'] = $goods_id;
                $item['category']['created_at'] = time();
                $categoryModel->insert($item['category']);
            }
            if (!empty($item['param'])) {
                $paramModel = new  GoodsParam;
                foreach ($item['param'] as $key => $param) {
                    $data['title'] = $param['title'];
                    $data['value'] = $param['value'];
                    $data['goods_id'] = $goods_id;
                    $data['uniacid'] = \YunShop::app()->uniacid;
                    $data['displayorder'] = $key;
                    $paramModel->insert($data);
                }
            }
            if (!empty($item['spec'])) {
                $specModel = new  GoodsSpec;
                $specItemModel = new  GoodsSpecItem;
                $specids = array();
                $displayorder = 0;
                $newspecs = array();
                foreach ($item['spec'] as $spec) {
                    $data_spec = array(
                        "uniacid" => \YunShop::app()->uniacid,
                        "goods_id" => $goods_id,
                        "title" => $spec['title'],
                        "display_order" => $displayorder,
                        "propId" => $spec['propId']
                    );
                    $spec_id = $specModel->insertGetId($data_spec);
                    $data_spec['id'] = $spec_id;
                    $spec_ids[] = $spec_id;
                    $displayorder++;
                    $spec_items = $spec['items'];
                    $displayorder_item = 0;
                    foreach ($spec_items as $spec_item) {
                        $data_item = array(
                            "uniacid" => \YunShop::app()->uniacid,
                            "specid" => $spec_id,
                            "title" => $spec_item['title'],
                            //"thumb" => $this->save_image($spec_item['thumb'], $config),
                            "valueId" => $spec_item['valueId'],
                            "show" => 1,
                            "display_order" => $displayorder_item
                        );
                        $specItemModel->insert($data_item);
                    }
                }
            }
            if (!empty($item['option'])) {
                $optionModel = new GoodsOption;
                $minprice = 0;
                $options = $item['option'];
                if (count($options) > 0) {
                    $minprice = $options[0]['marketprice'];
                }
                $displayorder = 0;
                foreach ($options as $option) {
                    $option_specs = $option['option_specs'];
                    $ids = array();
                    //$valueIds     = array();
                    foreach ($option_specs as $os) {
                        foreach ($newspecs as $nsp) {
                            foreach ($nsp['items'] as $nspitem) {
                                if ($nspitem['valueId'] == $os['valueId']) {
                                    $ids[] = $nspitem['id'];
                                    //$valueIds[] = $nspitem['valueId'];
                                }
                            }
                        }
                    }
                    if (!is_int($option['marketprice'])) {
                        $patterns = "/\d+/";
                        preg_match_all($patterns,$option['marketprice'],$arr);
                        $marketprice = (int)$arr[0];
                    } else {
                        $marketprice = $option['marketprice'];
                    }

                    $ids = implode("_", $ids);
                    //$valueIds = implode("_", $valueIds);
                    $data_option = array(
                        'uniacid' => \YunShop::app()->uniacid,
                        "display_order" => $displayorder,
                        "goods_id" => $goods_id,
                        "title" => implode('+', $option['title']),
                        "specs" => $ids,
                        "stock" => $option['stock'],
                        "market_price" => $marketprice,
                        "skuId" => $option['skuId']
                    );
                    if ($minprice > $option['marketprice']) {
                        $minprice = $option['marketprice'];
                    }
                    $optionModel->insert($data_option);
                }

            }
            return array(
                "result" => '1',
                "error" => '保存成功!'
            );
        }
    }

    public static function get_info_url($itemId, $source)
    {
        switch ($source) {
            case 'taobao':
                $url = TAOBAOINFO . $itemId;
                break;
            case 'jingdong':
                $url = JDINFO . $itemId;
                break;
            case 'alibaba':
                $url = ALIINFO . $itemId . '.html';
                break;
            case 'yzGoods':
                $url = YZINFO . $itemId;
                break;
            default :
                $url = TAOBAOINFO . $itemId;
        }
        return $url;
    }

    public static function get_detail_url($itemId, $source)
    {
        switch ($source) {
            case 'taobao':
                $url = TAOBAODETAIL . $itemId;
                break;
            case 'jingdong':
                $url = JDDETAIL . $itemId;
                break;
            default :
                $url = TAOBAODETAIL . $itemId;
        }
        return $url;
    }

    public static function getYzGoods($itemId = '', $sourceUrl = '', $parentId = 0, $childId = 0, $thirdId = 0)
    {
        $shopset = Setting::get('shop.category');
        $info = Import::getInfo($itemId, 'yzGoods');
        if ($info) {
            //已导入过该商品,如何处理
        }
        $goodsData = static::getCurlGoodsData($itemId);
        if (!$goodsData['result']) {
            return [
                "result" => '0',
                "error" => '宝贝不存在!'
            ];
        }
        $category['category_id'] = $shopset['cat_level'] == 3 ? $thirdId : $childId;
        $category['category_ids'] = $shopset['cat_level'] == 3 ? $parentId . ',' . $childId . ',' . $thirdId : $parentId . ',' . $childId;

//        $img = static::downloadImage($goodsData['data']['thumb']);
//        $goodsData['thumb_url'] = static::getThumbUrl($goodsData['data']['thumb_url']);
//        static::getContent($goodsData['data']['content']);
        return DB::transaction(function () use ($goodsData, $category) {
            /**
             * todo 添加商品基本信息
             */
            $goodsId = static::addGoodsData($goodsData);
            /**
             * todo 修改分类
             */
            static::editGoodsCategoryData($category, $goodsId);

            /**
             * todo 添加属性
             */
            static::addGoodsParamData($goodsData, $goodsId);

            /**
             * todo 添加商品规格项
             */
            $itemData = static::addGoodsSpecData($goodsData, $goodsId);

            /**
             * todo 添加商品规格
             */
            static::addGoodsOptionData($goodsData, $goodsId, $itemData);


            /**
             * todo 添加商品分享信息
             */
            static::addGoodsShareData($goodsData, $goodsId);

            return [
                "result" => '1',
                "message" => '成功!'
            ];

        });


    }

    /**
     * @param $goodsData
     * @return mixed
     */
    public static function addGoodsData($goodsData)
    {
        $data = static::unsetGoodsData($goodsData['data']);

        $goodsId = Goods::insertGetId($data);

        return $goodsId;
    }

    /**
     * @param $goodsData
     * @return mixed
     */
    public static function unsetGoodsData($goodsData)
    {

        unset($goodsData['id']);
        unset($goodsData['uniacid']);
        unset($goodsData['has_many_specs']);
        unset($goodsData['has_many_options']);
        unset($goodsData['has_one_share']);
        unset($goodsData['has_many_param']);

        $goodsData['uniacid'] = \YunShop::app()->uniacid;
        $goodsData['is_plugin'] = 0;
        $goodsData['plugin_id'] = 0;
        $goodsData['created_at'] = time();
        $goodsData['updated_at'] = time();

        $goodsData['thumb'] = static::downloadImage($goodsData['complete_thumb']);
        $goodsData['thumb_url'] = static::getThumbUrl($goodsData['thumb_url']);
        $goodsData['content'] = static::getContent($goodsData['content']);
        unset($goodsData['complete_thumb']);

        return $goodsData;
    }

    /**
     * @param $thumbUrl
     * @return string
     */
    public static function getThumbUrl($thumbUrl)
    {
        $thumbUrl = unserialize($thumbUrl);
        foreach ($thumbUrl as &$item) {
            $item = static::downloadImage($item);
        }

        return $thumbUrl = serialize($thumbUrl);
    }

    /**
     * @param $content
     * @return mixed|string
     */
    public static function getContent($content)
    {

        $response = $content;

        $content = html_entity_decode($content);
        $content = preg_replace("/<img.+(imglazyload\/spaceball)+[^>]+>/", '', $content);

        preg_match_all("/<img.*?src=[\'| \"](.*?(?:[\.gif|\.jpg]?))[\'|\"].*?[\/]?>/", $content, $imgs);
        if (isset($imgs[1])) {
            foreach ($imgs[1] as $img) {

                $yzImg = $img;
                if (substr($yzImg, 0, 2) == "//") {
                    $img = "http://" . substr($yzImg, 2);
                }

                $im = array(
                    "yz_img" => $yzImg,
                    "system" => static::downloadImage($yzImg)
                );
                $images[] = $im;
            }
        }
        if (isset($images)) {
            foreach ($images as $img) {
                $content = str_replace($img['yz_img'], $img['system'], $content);
            }
        }
        return $content;
    }

    /**
     * @param $category
     * @param $goodsId
     */
    public static function editGoodsCategoryData($category, $goodsId)
    {
        $data = [
            'goods_id' => $goodsId,
            'category_id' => $category['category_id'],
            'category_ids' => $category['category_ids'],
            'created_at' => time(),
            'updated_at' => time(),
        ];
        GoodsCategory::insert($data);
    }

    /**
     * @param $goodsData
     * @param $goodsId
     */
    public static function addGoodsParamData($goodsData, $goodsId)
    {
        $has_many_param = $goodsData['data']['has_many_param'];
        foreach ($has_many_param as &$param) {
            unset($param['id']);
            $param['uniacid'] = \YunShop::app()->uniacid;
            $param['goods_id'] = $goodsId;
            $param['created_at'] = time();
            $param['updated_at'] = time();
        }
        GoodsParam::insert($has_many_param);
    }

    /**
     * @param $goodsData
     * @param $goodsId
     */
    public static function addGoodsShareData($goodsData, $goodsId)
    {
        $has_one_share = $goodsData['data']['has_one_share'];
        unset($has_one_share['id']);
        $has_one_share['goods_id'] = $goodsId;
        $has_one_share['share_thumb'] = static::downloadImage($has_one_share['share_thumb']);
        $has_one_share['created_at'] = time();
        $has_one_share['updated_at'] = time();

        Share::insert($has_one_share);
    }


    public static function addGoodsSpecData($goodsData, $goodsId)
    {
        $itemData = [];
        $has_many_specs = $goodsData['data']['has_many_specs'];
        foreach ($has_many_specs as $specs) {
            $specData = [
                'uniacid' => \YunShop::app()->uniacid,
                'goods_id' => $goodsId,
                'title' => $specs['title'],
                'description' => $specs['description'],
                'display_type' => $specs['display_type'],
                'content' => $specs['content'],
                'display_order' => $specs['display_order'],
                'propId' => $specs['propId'],
                'created_at' => time(),
                'updated_at' => time(),
            ];
            $specid = GoodsSpec::insertGetId($specData);
            $itemData += static::addGoodsSpecItemData($specs, $specid);

        }
        return $itemData;
    }

    public static function addGoodsSpecItemData($specs, $specid)
    {
        $itemData = [];
        foreach ($specs['has_many_specs_item'] as $specItem) {
            $specItemData = [
                'uniacid' => \YunShop::app()->uniacid,
                'specid' => $specid,
                'title' => $specItem['title'],
                'thumb' => static::downloadImage($specItem['thumb']),
                'show' => $specItem['show'],
                'display_order' => $specItem['display_order'],
                'valueId' => $specItem['valueId'],
                'virtual' => $specItem['virtual'],
                'created_at' => time(),
                'updated_at' => time(),
            ];
            $specItemId = GoodsSpecItem::insertGetId($specItemData);
            $itemData += [$specItem['id'] => $specItemId];
        }
        return $itemData;

    }


    public static function addGoodsOptionData($goodsData, $goodsId,$itemData)
    {
        $has_many_options = $goodsData['data']['has_many_options'];
        foreach ($has_many_options as &$option) {
            unset($option['id']);
            $option['uniacid'] = \YunShop::app()->uniacid;
            $option['goods_id'] = $goodsId;
            $option['thumb'] = static::downloadImage($option['thumb']);
            $option['specs'] = static::getSpecsData($option['specs'],$itemData);
            $option['created_at'] = time();
            $option['deleted_at'] = time();

        }
        GoodsOption::insert($has_many_options);
    }

    /**
     * @param $specs
     * @param $itemData
     * @return bool|string
     *
     */
    public static function getSpecsData($specs,$itemData)
    {
        $specsData = explode('_',$specs);
        $specId = '';
        foreach ($specsData as $spec) {
            $specId .= '_'.$itemData[$spec];
        }
        return substr($specId, 1);
    }


    public static function getCurlGoodsData($itemId)
    {
        $url = self::get_info_url($itemId, 'yzGoods');
        $response = Curl::to($url)
            ->withOption('FOLLOWLOCATION', true)
            ->withOption('USERAGENT',
                'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36')
            ->get();
        return json_decode($response, true);
    }


    public static function downloadImage($url, $path = 'static/yunshop/goods/images/')
    {
        if (!$url) {
            return '';
        }

        $path = resource_get($path, 1);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        $file = curl_exec($ch);
        curl_close($ch);

        if (config('app.framework') == 'platform') {
            $_path = $path;
            $path = base_path().$path;
        } else {
            $_path = substr($path, 2);
        }
        $url = substr($url, strpos($url,"images"));
        $filename = static::saveAsImage($url, $file, $path);

        return request()->getSchemeAndHttpHost() . $_path . $filename;
    }

    private static function saveAsImage($url, $file, $path)
    {
        $filename = pathinfo($url, PATHINFO_BASENAME);
        $resource = fopen($path . $filename, 'a');
        fwrite($resource, $file);
        fclose($resource);
        return $filename;
    }


}