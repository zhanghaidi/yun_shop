<?php

namespace Yunshop\MinappContent\admin;

use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use Yunshop\MinappContent\models\PostModel;
use Yunshop\MinappContent\services\MinappContentService;

class PostController extends BaseController
{
    private $pageSize = 30;

    private $category;
    private $acupoint;
    private $goods;

    public function preAction()
    {
        parent::preAction();

        $this->category = ArticleCategoryModel::select('id', 'name')
            ->where([
                'uniacid' => \YunShop::app()->uniacid,
                'status' => 1,
            ])->get()->toArray();

        $this->acupoint = AcupointModel::where([
            'uniacid' => \YunShop::app()->uniacid,
            'status' => 1,
        ])->get()->toArray();

        // 芸众商品列表(去掉内购商品 category_id = 25)
        $this->goods = Goods::select('yz_goods.id', 'title', 'thumb', 'price')
            ->join('yz_goods_category', 'yz_goods.id', '=', 'yz_goods_category.goods_id')
            ->where('yz_goods_category.category_id', '<>', 25)
            ->where('status', 1)
            ->orderBy('display_order', 'desc')->get();
    }

    public function index()
    {
        $searchData = \YunShop::request()->search;

        $list = PostModel::select(
            'diagnostic_service_post.*', 'diagnostic_service_user.avatarurl',
            'diagnostic_service_user.nickname', 'diagnostic_service_sns_board.name'
        )->leftJoin('diagnostic_service_user', 'diagnostic_service_user.ajy_uid', '=', 'diagnostic_service_post.user_id')
            ->leftJoin('diagnostic_service_sns_board', 'diagnostic_service_sns_board.id', '=', 'diagnostic_service_post.board_id')
            ->where('diagnostic_service_post.uniacid', \YunShop::app()->uniacid);
        if (isset($searchData['datelimit']['start']) && isset($searchData['datelimit']['end']) &&
            strtotime($searchData['datelimit']['start']) !== false && strtotime($searchData['datelimit']['end']) !== false
        ) {
            $list = $list->where('diagnostic_service_post.create_time', '>', strtotime($searchData['datelimit']['start']))
                ->where('diagnostic_service_post.create_time', '<', strtotime($searchData['datelimit']['end']) + 86400);
        }
        if (isset($searchData['board_id']) && intval($searchData['board_id']) > 0) {
            $list = $list->where('diagnostic_service_post.board_id', intval($searchData['board_id']));
        }
        if (isset($searchData['is_recommend']) && $searchData['is_recommend'] != '') {
            $searchData['is_recommend'] = intval($searchData['is_recommend']);
            $list = $list->where('diagnostic_service_post.is_recommend', $searchData['is_recommend']);
        }
        if (isset($searchData['is_hot']) && $searchData['is_hot'] != '') {
            $searchData['is_hot'] = intval($searchData['is_hot']);
            $list = $list->where('diagnostic_service_post.is_hot', $searchData['is_hot']);
        }
        if (isset($searchData['keywords']) && trim($searchData['keywords']) != '') {
            $searchData['keywords'] = trim($searchData['keywords']);
            $list = $list->where(function ($query) use ($searchData) {
                $query->where('diagnostic_service_post.content', 'like', '%' . $searchData['keywords'] . '%')
                    ->orWhere('diagnostic_service_post.title', 'like', '%' . $searchData['keywords'] . '%')
                    ->orWhere('diagnostic_service_post.id', 'like', '%' . $searchData['keywords'] . '%')
                    ->orWhere('diagnostic_service_user.nickname', 'like', '%' . $searchData['keywords'] . '%')
                    ->orWhere('diagnostic_service_sns_board.name', 'like', '%' . $searchData['keywords'] . '%')
                    ->orWhere('diagnostic_service_post.user_id', 'like', '%' . $searchData['keywords'] . '%');
            });
        }

        $list = $list->orderBy('id', 'desc')
            ->paginate($this->pageSize)->toArray();

        foreach ($list['data'] as &$v) {
            $v['images'] = json_decode($v['images'], true);
        }

        $pager = PaginationHelper::show($list['total'], $list['current_page'], $this->pageSize);

        return view('Yunshop\MinappContent::admin.article.list', [
            'pluginName' => MinappContentService::get('name'),
            'search' => $searchData,
            'data' => $list['data'],
            'pager' => $pager,
            'category' => $this->category,
        ]);
    }
}
