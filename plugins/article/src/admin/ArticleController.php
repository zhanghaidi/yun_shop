<?php
namespace Yunshop\Article\admin;

use app\backend\modules\member\models\MemberLevel;
use Illuminate\Http\Request;
use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use Yunshop\Article\models\Article;
use Yunshop\Article\models\Category;
use Yunshop\Article\models\Log;
use Yunshop\Article\models\Share;
use Yunshop\Article\models\Report;
use Yunshop\Article\services\ArticleService;

class ArticleController extends BaseController
{
    /*
     * credit 积分
     * credit 余额; bonus 奖金("余额"即"奖金"的一种);
     * award 奖励 (包括"积分"和"余额")
     */

    /**
     * 文章列表
     * @return mixed
     */
    public function index()
    {
        $pageSize = 10;
        $search = [
            'category_id' => \YunShop::request()->search['category_id'] ? \YunShop::request()->search['category_id'] : '',
            'keyword' => \YunShop::request()->search['keyword'] ? \YunShop::request()->search['keyword'] : '',
        ];

        if (!empty($search['category_id']) || !empty($search['keyword'])) {
            $articles = Article::getArticlesBySearch($search['category_id'], $search['keyword'])->orderBy('updated_at', 'desc')->paginate($pageSize)->toArray();
        } else {
            $articles = Article::getArticles()->orderBy('updated_at', 'desc')->paginate($pageSize)->toArray();
        }

        $categorys = Category::getCategorys()->orderBy('id', 'desc')->get()->toArray();
        $pager = PaginationHelper::show($articles['total'], $articles['current_page'], $articles['per_page']);
        return view('Yunshop\Article::admin.list',
            [
                'articles' => $articles,
                'categorys' => $categorys,
                'search' => $search,
                'pager' => $pager,
            ]
        )->render();
    }

    /**
     * 添加文章
     * @return mixed
     */
    public function add()
    {
        $articleModel = new Article();
        $requestArticle = \YunShop::request()->article;
        $categorys = Category::getCategorys()->get()->toArray();

        $levels = MemberLevel::getMemberLevelList();
        if ($requestArticle) {
//            dd($requestArticle);
            $articleModel->fill($requestArticle);
            $articleModel->uniacid = \YunShop::app()->uniacid;
            $articleModel->advs = ArticleService::setJson($articleModel->advs);
            $articleModel->virtual_created_at = strtotime($articleModel->virtual_created_at);
            $articleModel->show_levels = implode(',', $articleModel->show_levels);

            $validator = $articleModel->validator($articleModel->getAttributes());
            if ($validator->fails()) {
                $this->error($validator->messages());
            } else {
                if ($articleModel->save()) {
                    return $this->message('文章创建成功', Url::absoluteWeb('plugin.article.admin.article.index'));
                } else {
                    $this->error('文章创建失败');
                }
            }
        }

        return view('Yunshop\Article::admin.info',
            [
                'article' => $articleModel,
                'categorys' => $categorys,
                'levels' => $levels,
            ]
        )->render();
    }

    /**
     * 文章编辑
     * @return mixed
     */
    public function edit()
    {
        $articleId = \YunShop::request()->id;
        $articleModel = Article::getArticle($articleId);

        $levels = MemberLevel::getMemberLevelList();
        if (!$articleModel) {
            return $this->error('无此记录或已被删除');
        }
        $articleModel->advs = ArticleService::getJson($articleModel->advs); //todo 营销产品具体信息
        $categorys = Category::getCategorys()->get()->toArray();
        $bonusSum = Share::getBonusSum($articleId);
        $articleModel->show_levels = ($articleModel->show_levels == '') ? '' : explode(',', $articleModel->show_levels);

        $requestArticle = \YunShop::request()->article;
        if ($requestArticle) {
            $articleModel->fill($requestArticle);
            $articleModel->advs = ArticleService::setJson($articleModel->advs);
            $articleModel->no_copy_url = $requestArticle['no_copy_url'];
            $articleModel->no_share = $requestArticle['no_share'];
            $articleModel->no_share_to_friend = $requestArticle['no_share_to_friend'];
            $articleModel->show_levels = ($requestArticle['show_levels'] == '') ? '' : implode(',', $requestArticle['show_levels']);
            $articleModel->virtual_created_at = strtotime($articleModel->virtual_created_at);
            $validator = $articleModel->validator();
            if ($validator->fails()) {
                $this->error($validator->messages());
            } else {
                if ($articleModel->save()) {
                    return $this->message('文章修改成功', Url::absoluteWeb('plugin.article.admin.article.index'));
                } else {
                    $this->error('文章修改失败');
                }
            }
        }
        return view('Yunshop\Article::admin.info',
            [
                'article' => $articleModel,
                'categorys' => $categorys,
                'bonus_sum' => $bonusSum,
                'levels' => $levels,
            ]
        )->render();
    }

    /**
     * 文章删除
     * @return mixed
     */
    public function deleted()
    {
        $id = \YunShop::request()->id;

        if (!Article::getArticle($id)) {
            return $this->error('没有此文章或已删除');
        }

        if (Article::deletedArticle($id)) {
            return $this->message('删除文章成功', Url::absoluteWeb('plugin.article.admin.article.index'));
        }
    }

    /**
     * 文章采集
     * @return mixed
     */
    public function collect()
    {
        $url = \YunShop::request()->url;
        $category_id = \YunShop::request()->category_id;
        if ($url && $category_id) {
            $content = ArticleService::getContentByCollect($url);
            if (!$content) {
                echo 0;exit;
            }
            $articleModel = new Article;
            $articleModel->uniacid = \YunShop::app()->uniacid;
            $articleModel->category_id = $category_id;
            $articleModel->title = $content['title'];
            $articleModel->content = htmlspecialchars_decode($content['contents']);
            $articleModel->keyword = '文章采集';
            $articleModel->report_enabled = 0;
            $articleModel->state = 0;
            $articleModel->desc = $content['desc'];
            $articleModel->thumb = $content['thumb'];
            $articleModel->state_wechat = 0;
            $articleModel->virtual_created_at = time();
            $articleModel->save();
            echo 1;exit;
        }

        $categorys = Category::getCategorys()->get()->toArray();
        return view('Yunshop\Article::admin.collect',
            [
                'categorys' => $categorys,
            ]
        )->render();
    }

    /**
     * "阅读 & 点赞"的记录
     * @return mixed
     */
    public function log()
    {
        $pageSize = 10;
        $id = \YunShop::request()->id;
        if(!$id){
            return $this->error('没有提供文章参数!');
        }

        $articleModel = Article::getArticle($id);
        if(!$id){
            return $this->error('文章不存在!');
        }
        $article = $articleModel->toArray();

        $bonusSum = Share::getBonusSum($id); //累计奖励金额
        $pointSum = Share::getPointSum($id); //累计奖励积分

        //默认阅读/点赞 数量
        $logs = Log::getLogsById($id)->paginate($pageSize)->toArray();

        $pager = PaginationHelper::show($logs['total'], $logs['current_page'], $logs['per_page']);
        return view('Yunshop\Article::admin.log',
            [
                'id' => $id,
                'article' => $article,
                'logs' => $logs,
                'pager' => $pager,
                'bonus_sum' => $bonusSum,
                'point_sum' => $pointSum,

            ])->render();
    }

    /**
     * 分享的记录
     * @return mixed
     */
    public function share()
    {
        $pageSize = 10;
        $id = \YunShop::request()->id;
        if(!$id){
            return $this->error('没有提供文章参数!');
        }

        $articleModel = Article::getArticle($id);
        if(!$id){
            return $this->error('文章不存在!');
        }
        $article = $articleModel->toArray();

        $bonusSum = Share::getBonusSum($id); //累计奖励金额
        $pointSum = Share::getPointSum($id); //累计奖励积分

        $shares = Share::getSharesById($id)->paginate($pageSize)->toArray();
        $pager = PaginationHelper::show($shares['total'], $shares['current_page'], $shares['per_page']);
        return view('Yunshop\Article::admin.share',
            [
                'id' => $id,
                'article' => $article,
                'shares' => $shares,
                'pager' => $pager,
                'bonus_sum' => $bonusSum,
                'point_sum' => $pointSum,

            ])->render();
    }

    /**
     * 举报记录
     * @return mixed
     */
    public function report()
    {
        $pageSize = 10;
        $id = \YunShop::request()->id;
        $reports = Report::getReports()->paginate($pageSize)->toArray();
        $pager = PaginationHelper::show($reports['total'], $reports['current_page'], $reports['per_page']);
        return view('Yunshop\Article::admin.report',
            [
                'id' => $id,
                'reports' => $reports,
                'pager' => $pager

            ])->render();
    }


}
