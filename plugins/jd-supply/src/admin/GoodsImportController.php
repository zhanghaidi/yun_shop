<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/25
 * Time: 9:53
 */

namespace Yunshop\JdSupply\admin;

use Yunshop\JdSupply\models\Category;
use app\common\components\BaseController;
use app\backend\modules\goods\services\CategoryService;
use app\common\exceptions\AppException;
use app\common\helpers\Url;
use app\common\models\SearchFiltering;
use Yunshop\JdSupply\models\JdGoods;
use Yunshop\JdSupply\services\GoodsImportService;
use Yunshop\JdSupply\services\sdk\JdClient;
use Yunshop\JdSupply\services\sdk\JdNewClient;
use Yunshop\JdSupply\services\sdk\JdNewRequest;
use Yunshop\JdSupply\services\sdk\JdRequest;

class GoodsImportController extends BaseController
{

    protected function shopCategory()
    {
        $set = \Setting::get('shop.category');

        $list = Category::getCategorys(0)->select('id', 'name', 'enabled')->pluginId()->get();

        $list->map(function($category) use($set) {
            $childrens = Category::getChildrenCategorys($category->id,$set)->select('id', 'name', 'enabled')->get()->toArray();
            foreach ($childrens as $key => &$children) {
                if ($set['cat_level'] == 3 &&  $children['has_many_children']) {
                    $children['childrens'] = $children['has_many_children'];
                } else {
                    $children['childrens'] = [];
                }
            }
            $category->childrens = $childrens;

        });

        return $list;
    }

    public function getFilteringList()
    {
        $filtering = SearchFiltering::where('parent_id', 0)->where('is_show', 0)->get();

        foreach ($filtering as $key => &$value) {
            $value['value'] = SearchFiltering::select('id', 'parent_id', 'name')->where('parent_id', $value->id)->get()->toArray();
        }
        return $filtering->toArray();
    }

    public function index()
    {

        $category_list = $this->shopCategory();

        $filtering_list = $this->getFilteringList();

        $thirdPartyCategory = $this->getChildrenCategory();
        return view('Yunshop\JdSupply::admin.goods-import-v2', [
            'category_level' => \Setting::get('shop.category.cat_level')?:2,
            'filtering_list' => json_encode($filtering_list),
            'category_list' => json_encode($category_list),
            'search_cate_v1' => json_encode($thirdPartyCategory),
        ])->render();
    }

    public function goodsPagination()
    {

        $page = request()->input('page', 1);
        $search = request()->input('search', []);
        $page_size = intval($search['goods_page_size']? :20);
        $set = \Setting::get('plugin.jd_supply');
        $cate = $search['cate_v3']? $search['cate_v3'] : ($search['cate_v2']? $search['cate_v2'] : ($search ['cate_v1']? $search['cate_v1']:''));
        $request = new JdNewRequest($set['app_secret'], $set['app_key']);
        $request->addParam('page', $page);
        $request->addParam('limit', $page_size);
        if ($search['word']) {
            $request->addParam('search_words', $search['word']);
        }
        if (intval($search['source'])) {
            $request->addParam('source', $search['source']);
        } else {
            $request->addParam('source', 2);
        }
        if ($cate) {
            $request->addParam('category_id', $cate);
        }
        if ($search['commission_agent']) {
            $request->addParam('commission_agent', $search['commission_agent']);
        }
        if ($search['range_type']) {
            $request->addParam('range_type', $search['range_type']);
            $request->addParam('range_from', $search['range_from']);
            $request->addParam('range_to', $search['range_to']);
        }
        if (isset($search['shipping']) && $search['shipping'] !== '') {
            $request->addParam('is_free_shipping', $search['shipping']);
        }
        if ($search['commission_agent'] == 1 || !isset($search['commission_agent'])){
            $methodUrl = '/v2/GoodsStorage/Lists';
        } else {
            $methodUrl = '/v2/GoodsStorage/MyLists';
        }
        $response = JdNewClient::get($methodUrl, $request);
        $list =  json_decode($response, true);
        if (empty($list) || $list['code'] != 1) {
            return $this->errorJson('请求失败', $list['msg']);
        }

        $list['data']['list'] = $this->listMap($list['data']['list'],$search['goods_import']);
        //dd($list['data']['list']);
        return $this->successJson('list', [
            'total' => $list['data']['count'],
            'per_page' => $page_size,
            'current_page' => $page,
            'data' => $list['data']['list'],
        ]);
    }

    protected function listMap($data,$goods_import)
    {
        $collection = collect($data)->map(function ($item) use ($goods_import) {
            $item['promotion_rate'] = number_format(($item['guide_price'] - $item['agreement_price']) /$item['agreement_price'] *100,1);
            $item['guide_price'] = number_format($item['guide_price']/100,2);
            $item['agreement_price'] = number_format($item['agreement_price']/100,2);
            $item['market_price'] = number_format($item['market_price']/100,2);
            $item['activity_price'] = number_format($item['activity_price']/100,2);
            if (is_null(JdGoods::where('jd_goods_id', $item['id'])->first())) {
                $item['is_presence'] = 0;
                if ($goods_import == 1) {
                    return $item;
                }
            } else {
                $item['is_presence'] = 1;
                if ($goods_import == 2) {
                    return $item;
                }
            }
            if (empty($goods_import)) {
                return $item;
            }
        })->toArray();

        return array_merge(array_filter($collection));
    }

    public function select()
    {
        $categorySearch = array_filter(request()->category, function ($item) {
            if (is_array($item)) {
                return !empty($item[0]);
            } else {
                return !empty($item);
            }
        });

        $goodsIds = request()->goods_ids;
        $extra['f_value_id'] = intval(request()->f_value_id);

        if (!$categorySearch) {
            throw new AppException('请选择要导入的分类');
        }
        if (!$categorySearch['childid']) {
            throw new AppException('请选择二级分类');
        }

        if (!$goodsIds) {
            throw new AppException('请选择至少一件商品');
        }
        if (request()->commission_agent == 1) {
            $set = \Setting::get('plugin.jd_supply');
            //加入选品
            $request = new JdNewRequest($set['app_secret'], $set['app_key']);

            $request->addParam('goods_ids', implode(',', $goodsIds));
            $response = JdNewClient::get('/v2/GoodsStorage/Add', $request);

        }
        $errorGoodsIds = [];
        foreach ($goodsIds as $goodsId) {
            $result = (new GoodsImportService())->requestJd($categorySearch, $goodsId,$extra); //todo 查询导入 同步 京东商品数据 到 商城商品表
            if (!$result) {
                $errorGoodsIds[] = $goodsId;
                continue;
            }
        }

        if ($errorGoodsIds) {
            $string_error = implode(',', $errorGoodsIds);
            return $this->errorJson('商品导入失败ID:'.$string_error, []);
        }

        return $this->successJson('商品导入成功', []);
    }

    /**
     * @param $array array 要分页的数组
     * @param int $pageSize int 每页条数
     * @param int $page int 当前页数
     * @param null $key string 返回指定键的数值
     * @return array
     */
    public function page_array($array, $pageSize = 1000, $page = 1, $key = null)
    {
        $pagedata = array();

        $start = ($page - 1) * $pageSize; #计算每次分页的开始位置

        $pagedata = array_slice($array,$start,$pageSize);

        if (is_null($key)) {
            return $pagedata;
        }
        return array_pluck($pagedata, $key);  #返回查询数据
    }


    /**
     * 获取第三方分类列表
     * @return array
     */
    public function getThirdPartyCategory()
    {
        $set = \Setting::get('plugin.jd_supply');

        $request = new JdNewRequest($set['app_secret'], $set['app_key']);
        $source = request()->input('source', 2);
        $page = request()->input('page', 1);
        $request->addParam('page',$page);
        $request->addParam('source',$source);
        $request->addParam('limit',200);
        $response = JdNewClient::get('v2/Category/Lists', $request);

        $data =  json_decode($response, true);

        if (empty($data) || $data['code'] != 1) {
            return [];
        }
        if (request()->ajax()) {
            return $this->successJson('', $data['data']['data']);
        }
        return $data['data'];
    }

    /**
     * 获取子分类
     */
    public function getChildrenCategory()
    {
        $set = \Setting::get('plugin.jd_supply');

        $request = new JdNewRequest($set['app_secret'], $set['app_key']);
        $parent = request()->input('parent_id', 0);
        if (intval(request()->input('source'))) {
            $request->addParam('source', request()->input('source'));
        } else {
            $request->addParam('source', 2);
        }
        $request->addParam('parent_id',$parent);

        $response = JdNewClient::get('/v2/Category/GetCategory', $request);

        $data =  json_decode($response, true);
        if (empty($data) || $data['code'] != 1) {
            return [];
        }
        if (request()->ajax()) {
            return $this->successJson('', $data['data']);
        }
        return $data['data'];
    }

}