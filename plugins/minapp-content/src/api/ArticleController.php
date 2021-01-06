<?php

namespace Yunshop\MinappContent\api;

use app\common\components\ApiController;
use Carbon\Carbon;
use Yunshop\Appletslive\common\services\BaseService;

class ArticleController extends ApiController
{
    protected $ignoreAction = ['articleCategory', 'articles', 'articleInfo'];

    protected $user_id = 0;
    protected $uniacid = 0;

    /**
     *  constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->uniacid = \YunShop::app()->uniacid;
        $this->user_id = \YunShop::app()->getMemberId();
    }

    /**
     * 文章分类
     * @return mixed
     */
    public function articleCategory()
    {
        $uniacid = $this->uniacid;
        $cache_key = 'ajy_article_categories' . $uniacid;

        $categories = cache_load($cache_key);

        if (!$categories) {
            $categories = pdo_getall('diagnostic_service_article_category', array('uniacid' => $uniacid, 'status' => 1), array('id', 'name', 'image', 'type'), '', 'list_order DESC');
            cache_write($cache_key, $categories);
        }

        return $this->successJson('获取文章分类成功', $categories);
    }

    /**
     * 文章列表
     * @return mixed
     */
    public function articles()
    {
        $uniacid = $this->uniacid;

        $pindex = intval(request()->get('page', 1));
        $psize = intval(request()->get('pageSize', 0));;
        if (!$psize) {
            $psize = 10;
        }
        $category_id = intval(request()->get('category_id', 0));
        if (!$category_id) {
            $where = array('uniacid' => $uniacid, 'status' => 1, 'is_hot' => 1);
        } else {
            $where = array('uniacid' => $uniacid, 'cateid' => $category_id, 'status' => 1);
        }

        $query = load()->object('query');
        $articleList = $query->from('diagnostic_service_article')
            ->select('id', 'title', 'description', 'share_img', 'thumb', 'video', 'content', 'images', 'author', 'create_time', 'read_nums', 'like_nums', 'comment_nums')
            ->where($where)
            ->orderby(array('list_order' => 'DESC', 'id' => 'DESC'))
            ->page($pindex, $psize)
            ->getall();

        $total = intval($query->getLastQueryTotal()); //总条数
        $totalPage = intval(($total + $psize - 1) / $psize); //总页数

        foreach ($articleList as $k => $v) {
            Carbon::setLocale('zh');
            $articleList[$k]['time'] = Carbon::createFromTimestamp($v['create_time'])->diffForHumans();
            $articleList[$k]['content'] = html_entity_decode($v['content']);
            $articleList[$k]['images'] = json_decode($v['images'], true);
        }

        return $this->successJson('获取分类下所有文章', compact('total', 'totalPage', 'articleList'));
    }

    /**
     * 文章详情
     * @return mixed
     */
    public function articleInfo()
    {
        $user_id = $this->user_id;
        $uniacid = $this->uniacid;
        $id = intval(request()->get('id', 0));
        if (!$id) {
            return $this->errorJson('文章id不能为空');
        }

        $articleInfo = pdo_get('diagnostic_service_article', array('id' => $id), array('id', 'title', 'description', 'content', 'share_img', 'thumb', 'video', 'uid', 'author', 'create_time', 'read_nums', 'like_nums', 'comment_nums', 'recommend_goods', 'recommend_acupotion', 'to_type_id'));

        if ($articleInfo) {
            $articleInfo['content'] = html_entity_decode($articleInfo['content']); //把 HTML 实体转换为字符
            Carbon::setLocale('zh');
            $articleInfo['time'] = Carbon::createFromTimestamp($articleInfo['create_time'])->diffForHumans();
            //用户收藏数量
            $collectCount = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('diagnostic_service_collect') . " WHERE uniacid = :uniacid AND to_type_id = 3 AND info_id = :id", array(':uniacid' => $uniacid, ':id' => $id));
            $articleInfo['collect_nums'] = intval($collectCount);
            //用户喜欢状态
            $like = pdo_get('diagnostic_service_article_like', array('uniacid' => $uniacid, 'user_id' => $user_id, 'article_id' => $id)); //当前用户是否点赞
            if ($like) {
                $articleInfo['is_like'] = 2;  //已点赞
            } else {
                $articleInfo['is_like'] = 1;  //未点赞
            }
            $articleInfo['acupotions'] = array();
            $articleInfo['goods'] = array();
            if ($articleInfo['recommend_goods']) {
                $articleInfo['recommend_goods'] = explode(',', $articleInfo['recommend_goods']); //文章关联商品
                foreach ($articleInfo['recommend_goods'] as $v) {
                    $recommend_goods = pdo_get('yz_goods', array('id' => $v), array('id', 'title', 'thumb', 'price', 'status', 'deleted_at'));
                    if ($recommend_goods['status'] == 1 && !$recommend_goods['deleted_at']) {
                        $articleInfo['goods'][] = $recommend_goods;
                    }
                }
            }
            if ($articleInfo['recommend_acupotion']) {
                $articleInfo['recommend_acupotion'] = explode(',', $articleInfo['recommend_acupotion']); //文章穴位管理
                foreach ($articleInfo['recommend_acupotion'] as $v) {
                    $recommend_acupoint = pdo_get('diagnostic_service_acupoint', array('id' => $v), array('id', 'name', 'get_position', 'image'));
                    $articleInfo['acupotions'][] = $recommend_acupoint;
                }
            }

            return $this->successJson('请求成功', $articleInfo);
        } else {
            return $this->errorJson('未查到此文章详情');
        }
    }

    /**
     * 文章阅读数接口
     * @return mixed
     */
    public function articleRead()
    {
        $user_id = $this->user_id;
        $uniacid = $this->uniacid;
        $ip = request()->ip();
        $article_id = intval(request()->get('article_id', 0));
        if (!$article_id) {
            return $this->errorJson('文章id不存在', array('status' => 0));
        }

        $read_history = pdo_get('diagnostic_service_article_brows', array('user_id' => $user_id, 'ip' => $ip, 'article_id' => $article_id));
        if (!$read_history) {
            pdo_insert('diagnostic_service_article_brows', array('article_id' => $article_id, 'user_id' => $user_id, 'uniacid' => $uniacid, 'ip' => $ip, 'create_time' => time(), 'day_time' => date('Y-m-d', time())));
            $res = pdo_update('diagnostic_service_article', array('read_nums +=' => 1), array('id' => $article_id));
            if ($res) {
                return $this->successJson('浏览量更新成功');
            } else {
                return $this->errorJson('浏览量更新失败');
            }
        } else {
            return $this->successJson('已阅');
        }

    }

    /**
     * 文章点赞/取消点赞
     * @return mixed
     */
    public function articleLike()
    {
        $user_id = $this->user_id;
        $uniacid = $this->uniacid;
        $article_id = intval(request()->get('article_id', 0));
        if (!$article_id) {
            return $this->errorJson('文章id不存在', array('status' => 0));
        }
        $article = pdo_get('diagnostic_service_article', array('id' => $article_id));
        if (!$article) {
            return $this->errorJson('文章不存在或已被删除', array('status' => 0));
        }
        $like = pdo_get('diagnostic_service_article_like', array('user_id' => $user_id, 'uniacid' => $uniacid, 'article_id' => $article_id));
        if ($like) {
            //已点赞，取消点赞
            $res = pdo_delete('diagnostic_service_article_like', array('user_id' => $user_id, 'uniacid' => $uniacid, 'article_id' => $article_id));
            if ($res) {
                pdo_update('diagnostic_service_article', array('like_nums -=' => 1), array('id' => $article_id));
                return $this->successJson('取消点赞成功', array('status' => 1, 'is_like' => 1));
            } else {
                return $this->errorJson('取消点赞失败', array('status' => 0, 'is_like' => 2));
            }
        } else {
            $data = [
                'uniacid' => $uniacid,
                'user_id' => $user_id,
                'article_id' => $article_id,
                'create_time' => time()
            ];
            $res = pdo_insert('diagnostic_service_article_like', $data);
            if ($res) {
                pdo_update('diagnostic_service_article', array('like_nums +=' => 1), array('id' => $article_id));
                return $this->successJson('点赞成功', array('status' => 1, 'is_like' => 2));
            } else {
                return $this->errorJson('点赞失败', array('status' => 0, 'is_like' => 1));
            }
        }
    }

    /**
     * 文章评论
     * @return mixed
     */
    public function articleComment()
    {
        $user_id = $this->user_id;
        $uniacid = $this->uniacid;
        $user = pdo_get('diagnostic_service_user', array('ajy_uid' => $user_id));
        if ($user['is_black'] == 1) { //用户禁言
            if ($user['black_end_time'] - TIMESTAMP > 1) {
                response()->json([
                    'result' => 301,
                    'msg' => '您已被系统禁言！截止时间至：' . date('Y-m-d H:i:s', $user->black_end_time) . '申诉请联系管理员',
                    'data' => false,
                ], 200, ['charset' => 'utf-8'])->send();
                exit;
            } else {
                pdo_update('diagnostic_service_user', array('is_black' => 0, 'black_content' => '时间到期,自然解禁'), array('ajy_uid' => $user_id));
            }
        }
        $article_id = intval(request()->get('article_id', 0));
        $content = trim(request()->get('content', ''));
        if (!$content) {
            return $this->errorJson('评论内容不能为空', array('status' => 0));
        }
        $parent_id = intval(request()->get('parent_id', 0));
        if (!$article_id) {
            return $this->errorJson('文章id不存在', array('status' => 0));
        }
        $article = pdo_get('diagnostic_service_article', array('id' => $article_id));
        if (!$article) {
            return $this->errorJson('评论文章不存在或已被删除', array('status' => 0));
        }
        $wxapp_base_service = new BaseService();
        $sensitive_check = $wxapp_base_service->msgSecCheck($content);
        if (!is_bool($sensitive_check) || $sensitive_check === false) {
            return $this->errorJson('评论包含敏感内容', $sensitive_check);
        }
        $content_check = $wxapp_base_service->textCheck($content);
        $data = [
            'user_id' => $user_id,
            'uniacid' => $uniacid,
            'article_id' => $article_id,
            'content' => $content,
            'create_time' => time()
        ];
        if ($content_check) {
            $data['status'] = 1;
        } else {
            $data['status'] = 0;
        }
        if ($parent_id) {
            $data['parent_id'] = $parent_id;
            $data['is_reply'] = 1;
            $par_data = pdo_get('diagnostic_service_article_comment', array('id' => $parent_id));
            if (!empty($par_data)) {
                $data['rele_user_id'] = $par_data['user_id'];
            }
            if ($user_id == $par_data['user_id']) {
                $data['mess_status'] = 1;
            }
        }

        $res = pdo_insert('diagnostic_service_article_comment', $data);
        if (!$res) {
            return $this->errorJson('评论失败', array('status' => 0));
        }
        $comment_id = pdo_insertid();
        $newComment = pdo_get('diagnostic_service_article_comment', array('id' => $comment_id));
        if ($data['status'] == 0) {
            return $this->errorJson('评论内容涉及违规敏感词,请等待后台审核', array('status' => 0));
        }
        pdo_update('diagnostic_service_article', array('comment_nums +=' => 1), array('id' => $article_id));
        return $this->successJson('评论成功', array('status' => 1, 'newComment' => $newComment));
    }
}
