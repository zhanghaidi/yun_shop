<?php

namespace Yunshop\MinappContent\api;

use app\common\components\ApiController;
use app\common\facades\Setting;
use app\common\models\Goods;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Yunshop\Appletslive\common\models\Room;
use Yunshop\MinappContent\models\AcupointModel;
use Yunshop\MinappContent\models\ArticleModel;
use Yunshop\MinappContent\models\BannerModel;
use Yunshop\MinappContent\models\BannerPositionModel;
use Yunshop\MinappContent\models\HotSpotModel;
use Illuminate\Support\Facades\Cache;
use Yunshop\MinappContent\models\PostModel;
use Yunshop\MinappContent\models\SearchModel;
use Yunshop\MinappContent\models\ShareQrcodeModel;
use Yunshop\MinappContent\models\SystemCategoryModel;
use Yunshop\MinappContent\models\SystemImageModel;
use Yunshop\MinappContent\services\WeixinMiniprogramService;

class IndexController extends ApiController
{
    protected $publicAction = ['systemCategory', 'banner', 'systemSecond', 'systemLoginImage', 'hotSearch', 'hotSpot'];
    protected $ignoreAction = ['systemCategory', 'banner', 'systemSecond', 'systemLoginImage', 'hotSearch', 'hotSpot'];

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
        $label = trim(\YunShop::request()->label);
        if (!isset($label[0])) {
            return $this->errorJson('label未发现');
        }

        $cacheKey = 'AJX:MAC:A:IC:B:' . $label;
        $result = Redis::get($cacheKey);
        if ($result !== false && $result !== null) {
            return $this->successJson('success', json_decode($result, true));
        }

        $positionRs = BannerPositionModel::select('id')->where('label', $label)->first();
        if (!isset($positionRs->id)) {
            return $this->errorJson('position未发现');
        }

        $bannerRs = BannerModel::select('id', 'title', 'image', 'jumpurl', 'jumptype', 'appid', 'type')->where([
            'position_id' => $positionRs->id,
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

            $v->heat = 10 + ($v->like_nums * 30) + ($v->comment_nums * 50) + ($v->view_nums * 10);
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

    public function searchByType()
    {
        $pageSize = 10;

        $type = intval(\YunShop::request()->search_type);
        if (!in_array($type, [1, 3, 4, 5, 6])) {
            return $this->errorJson('类型不存在');
        }

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

        if ($type == 1) {
            // 穴位
            $listRs = AcupointModel::select('id', 'name', 'image', 'get_position')->where([
                'uniacid' => \YunShop::app()->uniacid,
                'status' => 1,
            ])->where('name', 'like', '%' . str_replace('穴', '', $keyword) . '%')->paginate($pageSize);
        } elseif ($type == 3) {
            // 文章
            $listRs = ArticleModel::select('id', 'title', 'thumb', 'description')->where([
                'uniacid' => \YunShop::app()->uniacid,
                'status' => 1,
            ])->where('title', 'like', '%' . $keyword . '%')->paginate($pageSize);
        } elseif ($type == 4) {
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
            $listRs = Goods::select('id', 'title', 'thumb', 'price', 'market_price')
                ->whereHas('hasManyGoodsCategory', function ($query) {
                    $query->where('category_id', '!=', 25);
                })->where(['status' => 1])
                ->whereNotIn('id', $goodsIds)
                ->where('title', 'like', '%' . $keyword . '%')->paginate($pageSize);
        } elseif ($type == 5) {
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
            $listRs = Room::select('id', 'name', 'cover_img', 'subscription_num', 'view_num', 'comment_num')
                ->where([
                    'uniacid' => \YunShop::app()->uniacid,
                    'type' => 1,
                    'delete_time' => 0,
                ])->where('name', 'like', '%' . $keyword . '%')
                ->whereIn('display_type', [1, 2])
                ->whereNotIn('id', $iosNotShow)->paginate($pageSize);
            foreach ($listRs as &$v) {
                $v->hot_num = $v->subscription_num + $v->view_num + $v->comment_num;
            }
            unset($v);
        } elseif ($type == 6) {
            // 达人
            $listRs = PostModel::select(
                'id', 'title', 'content', 'images', 'video', 'video_thumb', 'view_nums',
                'comment_nums', 'like_nums', 'video_size', 'image_size'
            )->where([
                'uniacid' => \YunShop::app()->uniacid,
                'status' => 1,
                'type' => 2,
            ])->where('title', 'like', '%' . $keyword . '%')->paginate($pageSize);
            foreach ($listRs as &$v) {
                $v->images = json_decode($v->images, true);
                $v->video_size = json_decode($v->video_size, true);
                $v->image_size = json_decode($v->image_size, true);

                $v->heat = 10 + ($v->like_nums * 30) + ($v->comment_nums * 50) + ($v->view_nums * 10);
            }
            unset($v);
        }

        // 判断是否搜索到结果
        if ($listRs->total() == 0) {
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

        return $this->successJson('搜索成功', [
            'list' => $listRs->items(),
            'total' => $listRs->total(),
            'totalPage' => $listRs->lastPage(),
        ]);
    }

    public function systemSecond()
    {
        return $this->successJson('获取秒数成功', ['second' => 3]);
    }

    public function systemLoginImage()
    {
        $aid = intval(\YunShop::request()->app_aid);
        if ($aid <= 0) {
            return $this->errorJson('请传入aid');
        }

        $listRs = SystemImageModel::select('name', 'image', 'description', 'jumpurl', 'appid')->where([
            'uniacid' => \YunShop::app()->uniacid,
            'status' => 1,
            'aid' => $aid,
        ])->inRandomOrder()->limit(1)->get();
        foreach ($listRs as &$v) {
            $v->image = yz_tomedia($v->image);
        }
        return $this->successJson('获取图片成功', $listRs);
    }

    public function shareQrcode()
    {
        $page = \YunShop::request()->page;
        $scene = \YunShop::request()->scene;
        if (!isset($scene[0])) {
            return $this->errorJson('小程序场景值不存在');
        }

        $memberId = \YunShop::app()->getMemberId();
        // $localPath = \YunShop::app()->uniacid . '/' . date('Y/m/') . 'qrcode/';

        try {
            $qrcode = self::qrcodeCreateUnlimit($memberId, $scene, $page, isset(\YunShop::request()->os) ? \YunShop::request()->os : '');
            if (!isset($qrcode->id) || !isset($qrcode->qrcode)) {
                throw new Exception('小程序码生成错误');
            }
        } catch (Exception $e) {
            Log::info("生成小程序码失败", [
                'qrcode' => isset($qrcode) ? $qrcode : '',
                'page' => $page,
                'scene' => $scene,
                'msg' => $e->getMessage(),
            ]);
            return $this->errorJson($e->getMessage());
        }

        return $this->successJson('success', [
            'id' => $qrcode->id,
            'qrcode' => yz_tomedia($qrcode->qrcode),
        ]);
    }

    public static function qrcodeCreateUnlimit(int $userId, string $scene, string $page, string $os = '')
    {
        $qrName = md5(\YunShop::app()->uniacid . $userId . time() . random(6)) . '.png';

        try {
            $qrResponse = WeixinMiniprogramService::getCodeUnlimit($scene, $page, 430, [
                'auto_color' => false,
                'line_color' => [
                    'r' => '#ABABAB',
                    'g' => '#ABABAC',
                    'b' => '#ABABAD',
                ],
                'is_hyaline' => true,
            ]);
        } catch (Exception $e) {
            Log::info("生成小程序码失败", [
                'response' => isset($qrResponse) ? $qrResponse : '',
                'page' => $page,
                'scene' => $scene,
                'msg' => $e->getMessage(),
            ]);
            throw new Exception($e->getMessage());
        }

        $fileRs = Storage::disk('image')->put($qrName, $qrResponse);
        if ($fileRs !== true) {
            throw new Exception('二维码写入错误');
        }

        $uploadRs = file_remote_upload_wq($qrName);
        if (isset($uploadRs)) {
            throw new Exception('二维码文件写入失败');
        }

        $qrcode = new ShareQrcodeModel();
        $qrcode->uniacid = \YunShop::app()->uniacid;
        $qrcode->uniacname = isset(Setting::get('plugin.wechat')['name']) ? Setting::get('plugin.wechat')['name'] : '';
        $qrcode->user_id = $userId;
        $qrcode->qrcode = 'image/' . $qrName;
        $qrcode->share_time = date('Y-m-d H:i:s');
        $qrcode->clicknums = 0;
        $qrcode->os = isset(\YunShop::request()->os) ? \YunShop::request()->os : '';
        $qrcode->container = 'wechat';
        $qrcode->type = 1;
        $qrcode->poster = '';
        $qrcode->page = $page;
        $qrcode->scene = $scene;
        $qrcode->save();
        if (!isset($qrcode->id) || $qrcode->id <= 0) {
            throw new Exception('二维码保存失败');
        }
        return $qrcode;
    }

    public function hotSearch()
    {
        $listRs = SearchModel::selectRaw('keywords, count(keywords) as nums')->where([
            'uniacid' => \YunShop::app()->uniacid,
            'is_success' => 1,
        ])->groupBy('keywords')
            ->orderBy('nums', 'desc')->limit(8)->get();
        return $this->successJson('success', $listRs);
    }

    public function yzGoods()
    {
        $listRs = Goods::select('id', 'title', 'thumb', 'price')
            ->whereHas('hasManyGoodsCategory', function ($query) {
                $query->where('category_id', '!=', 25);
            })->where(['status' => 1])
            ->orderBy('display_order', 'desc')
            ->orderBy('id', 'asc')
            ->paginate(10);
        $return = [];
        $return['goods'] = $listRs->items();
        $return['total'] = $listRs->total();
        $return['totalPage'] = $listRs->lastPage();
        return $this->successJson('成功获取商品列表', $return);
    }

    //    首页热区
    public function hotSpot()
    {
        $cache_key = 'hotSpot' . $this->uniacid;
        $hotSpot = Cache::get($cache_key);
        if(!$hotSpot){
            $hotSpot = HotSpotModel::uniacid()
                ->select('id','list_order','title','type')
                ->withCount(['image' => function($image){
                    return $image->where('status', 1);
                }])
                ->with(['image' => function($image){
                    return $image->select('id','spot_id','list_order','image','jumpurl','appid')->where('status', 1)
                        ->orderBy('list_order', 'desc');

                }])
                ->where(['status' => 1])
                ->orderBy('list_order', 'desc')
                ->get();
            Cache::forget($cache_key);
            Cache::add($cache_key, $hotSpot, 7200);
        }
        return $this->successJson('ok',$hotSpot);

    }
}
