<?php

namespace Yunshop\MinappContent\api;

use app\common\components\ApiController;
use app\common\models\Goods;
use Illuminate\Support\Facades\Redis;
use Yunshop\Appletslive\common\models\Room;
use Yunshop\MinappContent\models\AcupointModel;
use Yunshop\MinappContent\models\ArticleModel;
use Yunshop\MinappContent\models\BannerModel;
use Yunshop\MinappContent\models\PostModel;
use Yunshop\MinappContent\models\SearchModel;
use Yunshop\MinappContent\models\SystemCategoryModel;

class IndexController extends ApiController
{
    protected $publicAction = ['systemCategory', 'banner'];

    public function systemCategory()
    {
        $cacheKey = 'AJX:MAC:A:IC:SC:' . \YunShop::app()->uniacid;
        $result = Redis::get($cacheKey);
        if ($result !== false && $result !== null) {
            return $this->successJson('获取系统分类成功', json_decode($result, true));
        }

        $listRs = SystemCategoryModel::select('id', 'name', 'image', 'jumpurl', 'appid')->where([
            'uniacid' => \YunShop::app()->uniacid,
            'status' => 1,
        ])->orderBy('list_order', 'desc')->get()->toArray();
        Redis::setex($cacheKey, mt_rand(300, 600), json_encode($listRs));

        return $this->successJson('获取系统分类成功', $listRs);
    }

    public function banner()
    {
        $positionId = intval(\YunShop::request()->position_id);
        if ($positionId <= 0) {
            return $this->errorJson('position_id未发现');
        }

        $cacheKey = 'AJX:MAC:A:IC:B:' . $positionId;
        $result = Redis::get($cacheKey);
        if ($result !== false && $result !== null) {
            return $this->successJson('success', json_decode($result, true));
        }

        $bannerRs = BannerModel::select('id', 'title', 'image', 'jumpurl', 'jumptype', 'appid', 'type')->where([
            'position_id' => $positionId,
            'uniacid' => \YunShop::app()->uniacid,
            'status' => 1,
        ])->orderBy('list_order', 'desc')->get()->toArray();
        foreach ($bannerRs as $k => $v) {
            $bannerRs[$k]['image'] = yz_tomedia($v['image']);
        }
        Redis::setex($cacheKey, mt_rand(300, 600), json_encode($bannerRs));

        return $this->successJson('获取系统分类成功', $bannerRs);
    }

    public function search()
    {
        $pageSize = 10;

        $keywords = \YunShop::request()->keywords;
        $keywords = trim($keywords);
        if (!isset($keywords[0])) {
            return $this->errorJson('请输入搜索关键词');
        }
        preg_match_all('/[\x{4e00}-\x{9fff}]+/u', $keywords, $matches);
        $keyword = trim(join('', $matches[0]));
        unset($keywords, $matches);
        if (!isset($keyword[0])) {
            return $this->errorJson('请检查关键词是否合法');
        }
        if (strlen($keyword) > 18) {
            return $this->errorJson('输入的关键词太长');
        }

        $osName = \YunShop::request()->os_name;
        if ($osName == 'ios') {
            $isIos = true;
        } else {
            $isIos = false;
        }

        $search = [
            0 => [
                'type' => 1,
                'name' => '穴位',
            ],
            // 1 => [
            //     'type' => 2,
            //     'name' => '病例',
            // ],
            1 => [
                'type' => 3,
                'name' => '文章',
            ],
            2 => [
                'type' => 4,
                'name' => '商品',
            ],
            3 => [
                'type' => 5,
                'name' => '课程',
            ],
            4 => [
                'type' => 6,
                'name' => '达人',
            ],
        ];

        // 穴位
        $list1Rs = AcupointModel::select('id', 'name', 'image', 'get_position')->where([
            'uniacid' => \YunShop::app()->uniacid,
            'status' => 1,
        ])->where('name', 'like', '%' . str_replace('穴', '', $keyword) . '%')->paginate($pageSize);
        $search[0]['list'] = $list1Rs->items();
        $search[0]['total'] = $list1Rs->total();
        $search[0]['totalPage'] = $list1Rs->lastPage();

        // 文章
        $list3Rs = ArticleModel::select('id', 'title', 'thumb', 'description')->where([
            'uniacid' => \YunShop::app()->uniacid,
            'status' => 1,
        ])->where('title', 'like', '%' . $keyword . '%')->paginate($pageSize);
        $search[1]['list'] = $list3Rs->items();
        $search[1]['total'] = $list3Rs->total();
        $search[1]['totalPage'] = $list3Rs->lastPage();

        // 商品
        $courseGoods = Room::select('id', 'goods_id')->where([
            'type' => 1,
            'delete_time' => 0,
        ])->where('goods_id', '>', 0)->get()->toArray();
        $goodsIds = array_column($courseGoods, 'goods_id');
        if (!isset($goodsIds[0])) {
            $goodsIds = [0];
        }
        // TODO 商品分类 25
        $list4Rs = Goods::select('id', 'title', 'thumb', 'price', 'market_price')
            ->whereHas('hasManyGoodsCategory', function ($query) {
                $query->where('category_id', '!=', 25);
            })->where(['status' => 1])
            ->whereNotIn('id', $goodsIds)
            ->where('title', 'like', '%' . $keyword . '%')->paginate($pageSize);
        $search[2]['list'] = $list4Rs->items();
        $search[2]['total'] = $list4Rs->total();
        $search[2]['totalPage'] = $list4Rs->lastPage();

        // 课程
        if ($isIos) {
            $iosNotShow = Room::select('id')->where([
                'uniacid' => \YunShop::app()->uniacid,
                'type' => 1,
                'delete_time' => 0,
                'buy_type' => 1,
                'ios_open' => 0,
            ])->where('name', 'like', '%' . $keyword . '%')->get()->toArray();
            $iosNotShow = array_column($iosNotShow, 'id');
        }
        if (!isset($iosNotShow[0])) {
            $iosNotShow = [0];
        }
        $list5Rs = Room::select('id', 'name', 'cover_img', 'subscription_num', 'view_num', 'comment_num')
            ->where([
                'uniacid' => \YunShop::app()->uniacid,
                'type' => 1,
                'delete_time' => 0,
            ])->where('name', 'like', '%' . $keyword . '%')
            ->whereIn('display_type', [1, 2])
            ->whereNotIn('id', $iosNotShow)->paginate($pageSize);
        $search[3]['list'] = $list5Rs->items();
        foreach ($search[3]['list'] as &$v) {
            $v->hot_num = $v->subscription_num + $v->view_num + $v->comment_num;
        }
        unset($v);
        $search[3]['total'] = $list5Rs->total();
        $search[3]['totalPage'] = $list5Rs->lastPage();

        // 达人
        $list6Rs = PostModel::select(
            'id', 'title', 'content', 'images', 'video', 'video_thumb', 'view_nums',
            'comment_nums', 'like_nums', 'video_size', 'image_size'
        )->where([
            'uniacid' => \YunShop::app()->uniacid,
            'status' => 1,
            'type' => 2,
        ])->where('title', 'like', '%' . $keyword . '%')->paginate($pageSize);
        $search[4]['list'] = $list6Rs->items();
        foreach ($search[4]['list'] as &$v) {
            $v->images = json_decode($v->images, true);
            $v->video_size = json_decode($v->video_size, true);
            $v->image_size = json_decode($v->image_size, true);
        }
        unset($v);
        $search[4]['total'] = $list6Rs->total();
        $search[4]['totalPage'] = $list6Rs->lastPage();

        // 判断是否搜索到结果
        if ($search[0]['total'] == 0 && $search[1]['total'] == 0 &&
            $search[2]['total'] == 0 && $search[3]['total'] == 0 &&
            $search[4]['total'] == 0
        ) {
            $isSuccess = 0;
        } else {
            $isSuccess = 1;
        }

        // 搜索成功更新关键词记录表状态
        $memberId = \YunShop::app()->getMemberId();
        $searchRs = SearchModel::where([
            'uniacid' => \YunShop::app()->uniacid,
            'user_id' => $memberId,
            'keywords' => $keyword,
        ])->first();
        if (isset($searchRs->id)) {
            $searchRs->search_nums += 1;
            $searchRs->is_delete = 0;
            $searchRs->add_time = time();
        } else {
            $searchRs = new SearchModel;
            $searchRs->uniacid = \YunShop::app()->uniacid;
            $searchRs->user_id = $memberId;
            $searchRs->keywords = $keyword;
        }
        $searchRs->is_success = $isSuccess;
        $searchRs->save();

        if ($isSuccess == 0) {
            return $this->errorJson('未搜到任何信息');
        }

        return $this->successJson('搜索成功', $search);
    }
}
