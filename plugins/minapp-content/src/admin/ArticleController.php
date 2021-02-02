<?php

namespace Yunshop\MinappContent\admin;

use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use app\common\models\Goods;
use Yunshop\MinappContent\models\AcupointModel;
use Yunshop\MinappContent\models\ArticleCategoryModel;
use Yunshop\MinappContent\models\ArticleCommentModel;
use Yunshop\MinappContent\models\ArticleDiscussModel;
use Yunshop\MinappContent\models\ArticleLikeModel;
use Yunshop\MinappContent\models\ArticleModel;
use Yunshop\MinappContent\services\MinappContentService;

class ArticleController extends BaseController
{
    private $pageSize = 20;

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

        $list = ArticleModel::where('uniacid', \YunShop::app()->uniacid);
        if (isset($searchData['datelimit']['start']) && isset($searchData['datelimit']['end']) &&
            strtotime($searchData['datelimit']['start']) !== false && strtotime($searchData['datelimit']['end']) !== false
        ) {
            $list = $list->where('create_time', '>', strtotime($searchData['datelimit']['start']))
                ->where('create_time', '<', strtotime($searchData['datelimit']['end']) + 86400);
        }
        if (isset($searchData['article_id']) && intval($searchData['article_id']) > 0) {
            $list = $list->where('id', intval($searchData['article_id']));
        }
        if (isset($searchData['cateid']) && intval($searchData['cateid']) > 0) {
            $list = $list->where('cateid', intval($searchData['cateid']));
        }
        if (isset($searchData['status']) && $searchData['status'] != '') {
            $searchData['status'] = intval($searchData['status']);
            $list = $list->where('status', $searchData['status']);
        }
        if (isset($searchData['is_hot']) && $searchData['is_hot'] != '') {
            $searchData['is_hot'] = intval($searchData['is_hot']);
            $list = $list->where('is_hot', $searchData['is_hot']);
        }
        if (isset($searchData['is_discuss']) && $searchData['is_discuss'] != '') {
            $searchData['is_discuss'] = intval($searchData['is_discuss']);
            $list = $list->where('is_discuss', $searchData['is_discuss']);
        }
        if (isset($searchData['keywords']) && trim($searchData['keywords']) != '') {
            $searchData['keywords'] = trim($searchData['keywords']);
            $list = $list->where(function ($query) use ($searchData) {
                $query->where('title', 'like', '%' . $searchData['keywords'] . '%')
                    ->orWhere('content', 'like', '%' . $searchData['keywords'] . '%')
                    ->orWhere('author', 'like', '%' . $searchData['keywords'] . '%');
            });
        }

        $list = $list->orderBy('list_order', 'desc')
            ->orderBy('id', 'asc')
            ->paginate($this->pageSize)->toArray();
        $pager = PaginationHelper::show($list['total'], $list['current_page'], $this->pageSize);

        return view('Yunshop\MinappContent::admin.article.list', [
            'pluginName' => MinappContentService::get('name'),
            'search' => $searchData,
            'data' => $list['data'],
            'pager' => $pager,
            'category' => $this->category,
        ]);
    }

    public function edit()
    {
        $data = \YunShop::request()->data;
        if ($data) {
            $data = array_filter($data);
            if (!isset($data['title']) || !isset(trim($data['title'])[0])) {
                return $this->message('文章标题不能为空', '', 'danger');
            }
            $data['title'] = trim($data['title']);
            if (!isset($data['content']) || !isset(trim($data['content'])[0])) {
                return $this->message('文章内容不能为空', '', 'danger');
            }
            $data['content'] = trim($data['content']);
            if (!isset($data['cateid']) || intval($data['cateid']) <= 0) {
                return $this->message('文章分类不能为空', '', 'danger');
            }
            if (!isset($data['thumb']) || !isset(trim($data['thumb'])[0])) {
                return $this->message('请上传文章封面图', '', 'danger');
            }
            $data['thumb'] = trim($data['thumb']);
            if (!isset($data['share_img']) || !isset(trim($data['share_img'])[0])) {
                return $this->message('请上传文章分享封面图', '', 'danger');
            }
            $data['share_img'] = trim($data['share_img']);

            if (isset($data['id'])) {
                $article = ArticleModel::where([
                    'id' => $data['id'],
                    'uniacid' => \YunShop::app()->uniacid,
                ])->first();
                if (!isset($article->id)) {
                    return $this->message('参数ID错误', '', 'danger');
                }
            } else {
                $article = new ArticleModel;
                $article->uniacid = \YunShop::app()->uniacid;
            }
            $article->title = $data['title'];
            $article->uid = \YunShop::app()->getMemberId();
            $article->list_order = isset($data['list_order']) ? $data['list_order'] : 0;
            $article->description = isset($data['description']) ? trim($data['description']) : '';
            $article->cateid = $data['cateid'];
            $article->share_img = $data['share_img'];
            $article->thumb = $data['thumb'];
            if (isset($data['recommend_goods']) && is_array($data['recommend_goods'])) {
                $article->recommend_goods = implode(',', $data['recommend_goods']);
            } else {
                $article->recommend_goods = '';
            }
            if (isset($data['recommend_acupotion']) && is_array($data['recommend_acupotion'])) {
                $article->recommend_acupotion = implode(',', $data['recommend_acupotion']);
            } else {
                $article->recommend_acupotion = '';
            }
            $article->video = isset($data['video']) ? trim($data['video']) : '';
            $article->author = isset($data['author']) ? trim($data['author']) : '';
            $article->avatar = isset($data['avatar']) ? trim($data['avatar']) : '';
            $article->status = isset($data['status']) ? intval($data['status']) : 0;
            $article->content = $data['content'];
            $article->images = json_encode(ArticleModel::getImageFromHtml(html_entity_decode($data['content'])));
            $article->is_hot = isset($data['is_hot']) ? intval($data['is_hot']) : 0;
            $article->is_discuss = 0;
            $article->discuss_title = '';
            $article->ture_option = '';
            $article->discuss_start = '';
            $article->end_time = '';
            $article->save();
            if (!isset($article->id) || $article->id <= 0) {
                return $this->message('修改失败', '', 'danger');
            }

            return $this->message('修改成功', Url::absoluteWeb('plugin.minapp-content.admin.article.index'));
        }

        $id = (int) \YunShop::request()->id;
        if ($id > 0) {
            $infoRs = ArticleModel::where([
                'id' => $id,
                'uniacid' => \YunShop::app()->uniacid,
            ])->first();
            if (!isset($infoRs->id)) {
                return $this->message('文章不存在或已被删除', '', 'danger');
            }

            $infoRs->recommend_goods = explode(',', $infoRs->recommend_goods);
            $infoRs->recommend_acupotion = explode(',', $infoRs->recommend_acupotion);
        }

        return view('Yunshop\MinappContent::admin.article.edit', [
            'pluginName' => MinappContentService::get('name'),
            'info' => isset($infoRs) ? $infoRs : null,
            'category' => $this->category,
            'goods' => $this->goods,
            'acupoint' => $this->acupoint,
        ]);
    }

    public function delete()
    {
        $id = (int) \YunShop::request()->id;
        if ($id <= 0) {
            return $this->message('ID参数错误', '', 'danger');
        }

        ArticleCommentModel::where('article_id', $id)->delete();
        ArticleDiscussModel::where('article_id', $id)->delete();
        ArticleLikeModel::where('article_id', $id)->delete();

        ArticleModel::where([
            'id' => $id,
            'uniacid' => \YunShop::app()->uniacid,
        ])->delete();

        return $this->message('删除成功');
    }

    public function status()
    {
        $id = \YunShop::request()->id;
        if ($id <= 0) {
            return $this->message('参数ID错误', '', 'danger');
        }
        $infoRs = ArticleModel::where([
            'id' => $id,
            'uniacid' => \YunShop::app()->uniacid,
        ])->first();
        if (!isset($infoRs->id)) {
            return $this->message('参数ID数据未找到', '', 'danger');
        }
        $message = '';
        if ($infoRs->status == 1) {
            $infoRs->status = 0;
            $message = '隐藏成功';
        } else {
            $infoRs->status = 1;
            $message = '显示成功';
        }
        $infoRs->save();

        return $this->message($message);
    }
}
