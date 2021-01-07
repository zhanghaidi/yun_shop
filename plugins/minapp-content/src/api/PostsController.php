<?php

namespace Yunshop\MinappContent\api;

use app\common\components\ApiController;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Redis;
use Yunshop\Appletslive\common\services\BaseService as AppletsliveBaseService;
use Yunshop\MinappContent\api\IndexController;
use Yunshop\MinappContent\models\ArticleModel;
use Yunshop\MinappContent\models\PostCommentLikeModel;
use Yunshop\MinappContent\models\PostCommentModel;
use Yunshop\MinappContent\models\PostHistoryModel;
use Yunshop\MinappContent\models\PostLikeModel;
use Yunshop\MinappContent\models\PostModel;
use Yunshop\MinappContent\models\SnsBoardModel;
use Yunshop\MinappContent\models\UserFollowModel;
use Yunshop\MinappContent\models\UserModel;

class PostsController extends ApiController
{
    protected $publicAction = ['hotPosts', 'snsBoard', 'posts', 'recommendPosts', 'commentLists'];
    protected $ignoreAction = ['hotPosts', 'snsBoard', 'posts', 'recommendPosts', 'commentLists'];

    public function hotPosts()
    {
        $pageSize = intval(\YunShop::request()->pageSize);
        if ($pageSize <= 0) {
            $pageSize = 10;
        }
        if ($pageSize >= 100) {
            $pageSize = 10;
        }

        $listRs = PostModel::select(
            'id', 'title', 'content', 'images', 'video', 'video_thumb', 'video_size', 'image_size',
            'view_nums', 'comment_nums', 'like_nums', 'user_id'
        )->where([
            'uniacid' => \YunShop::app()->uniacid,
            'is_hot' => 1,
            'status' => 1,
        ])->orderBy('create_time', 'desc')->paginate($pageSize);
        $userIds = [];
        foreach ($listRs as &$v) {
            $v->images = json_decode($v->images, true);
            $v->video_size = json_decode($v->video_size, true);
            $v->image_size = json_decode($v->image_size, true);
            $v->heat = 10 + ($v->like_nums * 30) + ($v->comment_nums * 50) + ($v->view_nums * 10);
            $userIds[] = $v->user_id;
        }
        $userIds = array_values(array_filter(array_unique($userIds)));
        if (isset($userIds[0])) {
            $userRs = UserModel::select('id', 'ajy_uid', 'nickname', 'avatarurl')
                ->whereIn('ajy_uid', $userIds)->get()->toArray();
            foreach ($listRs as &$v1) {
                foreach ($userRs as $v2) {
                    if ($v1->user_id != $v2['ajy_uid']) {
                        continue;
                    }
                    $v1->nickname = $v2['nickname'];
                    $v1->avatarurl = $v2['avatarurl'];
                    break;
                }
            }
        }
        $return = [
            'total' => $listRs->total(),
            'totalPage' => $listRs->lastPage(),
            'posts' => $listRs->items(),
        ];
        foreach ($return['posts'] as &$v) {
            $v = $v->toArray();
            unset($v['user_id']);
        }

        $heatRs = array_column($return['posts'], 'heat');
        array_multisort($heatRs, SORT_DESC, $return['posts']);

        return $this->successJson('获取首页推荐帖子列表', $return);
    }

    public function snsBoard()
    {
        $type = \YunShop::request()->sns_type;
        if (!in_array($type, ['communityList', 'communityRelease'])) {
            $type = 'communityList';
        }

        $cacheKey = 'AJX:MAC:A:PC:SB:' . \YunShop::app()->uniacid . ':' . $type;
        $result = Redis::get($cacheKey);
        if ($result !== false && $result !== null) {
            return $this->successJson('获取社区版块列表', json_decode($result, true));
        }

        $listRs = SnsBoardModel::select('id', 'name')->where([
            'uniacid' => \YunShop::app()->uniacid,
            'status' => 1,
        ]);
        if ($type == 'communityRelease') {
            $listRs = $listRs->where('is_user_publish', 1);
        }
        $listRs = $listRs->orderBy('list_order', 'desc')->get()->toArray();
        Redis::setex($cacheKey, mt_rand(300, 600), json_encode($listRs));

        return $this->successJson('获取社区版块列表', $listRs);
    }

    public function addPost()
    {
        $title = \YunShop::request()->title;
        isset($title) && $title = trim($title);
        if (!isset($title[0])) {
            return $this->errorJson('标题不能为空');
        }
        $content = \YunShop::request()->content;
        isset($content) && $content = trim($content);
        if (!isset($content[0])) {
            return $this->errorJson('内容不能为空');
        }
        $boardId = intval(\YunShop::request()->board_id);
        if ($boardId <= 0) {
            return $this->errorJson('请选择发言版块');
        }
        $goodsId = \YunShop::request()->goods_id;
        !isset($goodsId) && $goodsId = '';
        isset($goodsId) && $goodsId = trim($goodsId);
        $images = \YunShop::request()->pictures;
        !isset($images) && $images = '';
        $images = html_entity_decode($images);
        $imageSize = \YunShop::request()->image_size;
        !isset($imageSize) && $imageSize = '';
        $imageSize = html_entity_decode($imageSize);
        $video = \YunShop::request()->video;
        !isset($video) && $video = '';
        $videoSize = \YunShop::request()->video_size;
        !isset($videoSize) && $videoSize = '';
        $videoSize = html_entity_decode($videoSize);

        $memberId = \YunShop::app()->getMemberId();
        $userRs = UserModel::select('id', 'is_black', 'black_end_time')
            ->where([
                // 'uniacid' => \YunShop::app()->uniacid,
                'ajy_uid' => $memberId,
            ])->first();
        if (!isset($userRs->id)) {
            return $this->errorJson('用户数据获取错误');
        }
        if ($userRs->is_black == 1) {
            if ($userRs->black_end_time - time() > 1) {
                return $this->errorJson('您已被系统禁言！截止时间至：' . date('Y-m-d H:i:s', $userRs->black_end_time) . '申诉请联系管理员', ['status' => 301]);
            } else {
                UserModel::where('id', $userRs->id)->limit(1)->update([
                    'is_black' => 0,
                    'black_content' => '时间到期,自然解禁',
                ]);
            }
        }

        $appletsliveBaseService = new AppletsliveBaseService();
        $titlecheck = $appletsliveBaseService->msgSecCheck($title);
        if ($titlecheck !== true) {
            return $this->errorJson('标题内容违规', ['status' => 87014]);
        }

        $contentcheck = $appletsliveBaseService->msgSecCheck($content);
        if ($titlecheck !== true) {
            return $this->errorJson('文字内容违规', ['status' => 87014]);
        }

        $titlecheck = $appletsliveBaseService->textCheck($title);
        $contentcheck = $appletsliveBaseService->textCheck($content);

        $post = new PostModel;
        $post->uniacid = \YunShop::app()->uniacid;
        $post->user_id = $memberId;
        $post->title = $title;
        $post->content = $content;
        $post->images = $images;
        $post->image_size = $imageSize;
        $post->video = $video;
        $post->video_size = $videoSize;
        $post->goods_id = $goodsId;
        $post->board_id = $boardId;
        $post->status = 1;
        $post->type = 2;
        if ($titlecheck !== true || $contentcheck !== true) {
            $post->status = 0;
        }
        $post->save();
        if (!isset($post->id) || $post->id <= 0) {
            return $this->errorJson('发布失败', ['status' => 0]);
        }
        if ($post->status == 0) {
            return $this->errorJson('帖子标题或内容涉及违规敏感词,请等待后台审核', ['status' => 0]);
        }
        return $this->successJson('发布成功', ['status' => 1]);
    }

    public function posts()
    {
        $boardId = intval(\YunShop::request()->board_id);
        if ($boardId <= 0) {
            return $this->errorJson('论坛版块id为空');
        }

        $listRs = PostModel::select(
            'id', 'title', 'content', 'images', 'video', 'video_thumb', 'video_size', 'image_size',
            'view_nums', 'comment_nums', 'like_nums', 'user_id'
        )->where([
            'uniacid' => \YunShop::app()->uniacid,
            'board_id' => $boardId,
            'status' => 1,
        ])->orderBy('create_time', 'desc')->paginate(10);
        $userIds = [];
        foreach ($listRs as &$v) {
            $v->images = json_decode($v->images, true);
            $v->video_size = json_decode($v->video_size, true);
            $v->image_size = json_decode($v->image_size, true);
            $v->heat = 10 + ($v->like_nums * 30) + ($v->comment_nums * 50) + ($v->view_nums * 10);
            $userIds[] = $v->user_id;
        }
        $userIds = array_values(array_filter(array_unique($userIds)));
        if (isset($userIds[0])) {
            $userRs = UserModel::select('id', 'ajy_uid', 'nickname', 'avatarurl')
                ->whereIn('ajy_uid', $userIds)->get()->toArray();
            foreach ($listRs as &$v1) {
                foreach ($userRs as $v2) {
                    if ($v1->user_id != $v2['ajy_uid']) {
                        continue;
                    }
                    $v1->nickname = $v2['nickname'];
                    $v1->avatarurl = $v2['avatarurl'];
                    break;
                }
            }
        }
        $return = [
            'total' => $listRs->total(),
            'totalPage' => $listRs->lastPage(),
            'posts' => $listRs->items(),
        ];
        foreach ($return['posts'] as &$v) {
            $v = $v->toArray();
            unset($v['user_id']);
        }

        return $this->successJson('获取帖子列表', $return);
    }

    public function recommendPosts()
    {
        $listRs = PostModel::select(
            'id', 'title', 'content', 'images', 'video', 'video_thumb', 'video_size', 'image_size',
            'view_nums', 'comment_nums', 'like_nums', 'user_id'
        )->where([
            'uniacid' => \YunShop::app()->uniacid,
            'is_recommend' => 1,
            'status' => 1,
        ])->orderBy('create_time', 'desc')->paginate(10);
        $userIds = [];
        foreach ($listRs as &$v) {
            $v->images = json_decode($v->images, true);
            $v->video_size = json_decode($v->video_size, true);
            $v->image_size = json_decode($v->image_size, true);
            $v->heat = 10 + ($v->like_nums * 30) + ($v->comment_nums * 50) + ($v->view_nums * 10);
            $userIds[] = $v->user_id;
        }
        $userIds = array_values(array_filter(array_unique($userIds)));
        if (isset($userIds[0])) {
            $userRs = UserModel::select('id', 'ajy_uid', 'nickname', 'avatarurl')
                ->whereIn('ajy_uid', $userIds)->get()->toArray();
            foreach ($listRs as &$v1) {
                foreach ($userRs as $v2) {
                    if ($v1->user_id != $v2['ajy_uid']) {
                        continue;
                    }
                    $v1->nickname = $v2['nickname'];
                    $v1->avatarurl = $v2['avatarurl'];
                    break;
                }
            }
        }
        $return = [
            'total' => $listRs->total(),
            'totalPage' => $listRs->lastPage(),
            'posts' => $listRs->items(),
        ];
        foreach ($return['posts'] as &$v) {
            $v = $v->toArray();
            unset($v['user_id']);
        }

        return $this->successJson('获取推荐帖子列表', $return);
    }

    public function comment()
    {
        $postId = intval(\YunShop::request()->post_id);
        if ($postId <= 0) {
            return $this->errorJson('帖子id不存在');
        }
        $content = \YunShop::request()->content;
        isset($content) && $content = trim($content);
        if (!isset($content[0])) {
            return $this->errorJson('评论内容不能为空');
        }

        $parentId = intval(\YunShop::request()->parent_id);

        $memberId = \YunShop::app()->getMemberId();
        $userRs = UserModel::select('id', 'is_black', 'black_end_time')
            ->where([
                // 'uniacid' => \YunShop::app()->uniacid,
                'ajy_uid' => $memberId,
            ])->first();
        if (!isset($userRs->id)) {
            return $this->errorJson('用户数据获取错误');
        }
        if ($userRs->is_black == 1) {
            if ($userRs->black_end_time - time() > 1) {
                return $this->errorJson('您已被系统禁言！截止时间至：' . date('Y-m-d H:i:s', $userRs->black_end_time) . '申诉请联系管理员', ['status' => 301]);
            } else {
                UserModel::where('id', $userRs->id)->limit(1)->update([
                    'is_black' => 0,
                    'black_content' => '时间到期,自然解禁',
                ]);
            }
        }

        $appletsliveBaseService = new AppletsliveBaseService();
        $contentcheck = $appletsliveBaseService->msgSecCheck($content);
        if ($titlecheck !== true) {
            return $this->errorJson('文字内容违规', ['status' => 87014]);
        }

        $contentcheck = $appletsliveBaseService->textCheck($content);

        $postRs = PostModel::where([
            'id' => $postId,
            'uniacid' => \YunShop::app()->uniacid,
        ])->first();
        if (!isset($postRs->id)) {
            return $this->errorJson('评论帖子不存在或已被删除');
        }

        $comment = new PostCommentModel;
        $comment->user_id = $memberId;
        $comment->uniacid = \YunShop::app()->uniacid;
        $comment->post_id = $postId;
        $comment->content = $content;
        if ($postRs->user_id != $memberId) {
            $comment->mess_status = 0;
        } else {
            $comment->mess_status = 1;
        }
        $comment->rele_user_id = $postRs->user_id;
        if ($contentcheck !== true) {
            $comment->status = 0;
        } else {
            $comment->status = 1;
        }
        if ($parentId > 0) {
            $comment->parent_id = $parentId;
            $comment->is_reply = 1;

            $commentRs = PostCommentModel::select('id', 'user_id')
                ->where('id', $parentId)->first();
            if (isset($commentRs->id)) {
                $comment->rele_user_id = $commentRs->user_id;
            }
            if ($memberId != $commentRs->user_id) {
                $comment->mess_status = 0;
            }
        }
        $comment->save();
        if (!isset($comment->id) || $comment->id <= 0) {
            return $this->errorJson('评论插入失败');
        }

        if ($postRs->is_recommend != 1) {
            $postRs->is_recommend = 1;
            $postRs->save();
        }

        if ($comment->status == 0) {
            return $this->errorJson('评论内容涉及违规敏感词，请等待后台审核');
        }
        $postRs->comment_nums += 1;
        $postRs->save();

        return $this->successJson('评论成功', [
            'newComment' => $comment,
        ]);
    }

    public function commentLists()
    {
        $postId = intval(\YunShop::request()->post_id);
        if ($postId <= 0) {
            return $this->errorJson('帖子id不能为空');
        }

        $listRs = PostCommentModel::select('id', 'user_id', 'content', 'create_time')->where([
            'uniacid' => \YunShop::app()->uniacid,
            'post_id' => $postId,
            'is_reply' => 0,
            'status' => 1,
        ])->orderBy('display_order', 'desc')
            ->orderBy('create_time', 'desc')->paginate(10);
        $commentIds = $userIds = [];
        foreach ($listRs as &$v) {
            $v->time = $this->dataarticletime($v->create_time->timestamp);
            $commentIds[] = $v->id;
            $userIds[] = $v->user_id;
        }
        unset($v);
        if (isset($commentIds[0])) {
            $memberId = \YunShop::app()->getMemberId();
            if ($memberId > 0) {
                $memberLikeRs = PostCommentLikeModel::select('id', 'comment_id')->where([
                    'uniacid' => \YunShop::app()->uniacid,
                    'user_id' => $memberId,
                ])->whereIn('comment_id', $commentIds)->get()->toArray();
                $memberLikeRs = array_column($memberLikeRs, 'comment_id');
            }

            $likeRs = PostCommentLikeModel::selectRaw('id, comment_id, count(1) as countNum')
                ->where('uniacid', \YunShop::app()->uniacid)
                ->whereIn('comment_id', $commentIds)
                ->groupBy('comment_id')->get()->toArray();

            $replyRs = PostCommentModel::select('id', 'user_id', 'content', 'create_time', 'parent_id')->where([
                'uniacid' => \YunShop::app()->uniacid,
                'is_reply' => 1,
                'status' => 1,
            ])->whereIn('parent_id', $commentIds)
                ->orderBy('create_time', 'desc')->get()->toArray();
            foreach ($replyRs as &$v) {
                $userIds[] = $v['user_id'];
                $v['time'] = $this->dataarticletime($v['create_time']);
            }
            unset($v);
        }

        $userIds = array_values(array_filter(array_unique($userIds)));
        if (isset($userIds[0])) {
            $userRs = UserModel::select('id', 'ajy_uid', 'nickname', 'avatarurl', 'province', 'city')
                ->whereIn('ajy_uid', $userIds)->get()->toArray();
        }

        foreach ($listRs as &$v1) {
            if (isset($userRs)) {
                foreach ($userRs as $v2) {
                    if ($v1->user_id != $v2['ajy_uid']) {
                        continue;
                    }
                    $v1->nickname = $v2['nickname'];
                    $v1->avatarurl = $v2['avatarurl'];
                    $v1->province = $v2['province'];
                    $v1->city = $v2['city'];
                    break;
                }
            }

            $v1->is_like = 1;
            if (isset($memberLikeRs) && in_array($v1->id, $memberLikeRs)) {
                $v1->is_like = 2;
            }

            $v1->like_nums = 0;
            foreach ($likeRs as $v3) {
                if ($v1->id != $v3['comment_id']) {
                    continue;
                }
                $v1->like_nums = $v3['countNum'];
                break;
            }

            $v1->reply = [];
            foreach ($replyRs as $v4) {
                if ($v1->id != $v4['parent_id']) {
                    continue;
                }

                if (isset($userRs)) {
                    foreach ($userRs as $v5) {
                        if ($v4['user_id'] != $v5['ajy_uid']) {
                            continue;
                        }
                        $v4['nickname'] = $v5['nickname'];
                        $v4['avatarurl'] = $v5['avatarurl'];
                        $v4['province'] = $v5['province'];
                        $v4['city'] = $v5['city'];
                        break;
                    }
                }

                $v1->reply[] = $v4;
            }
            $v1->reply_nums = count($v1->reply);

        }
        unset($v1);

        return $this->successJson('获取成功', [
            'postCommentCount' => $listRs->total(),
            'total' => $listRs->total(),
            'total_page' => $listRs->lastPage(),
            'discussComments' => $listRs->items(),
        ]);
    }

    public function dataarticletime($times)
    {
        Carbon::setLocale('zh');
        return Carbon::createFromTimestamp($times)->diffForHumans();
    }

    public function commentLike()
    {
        $commentId = intval(\YunShop::request()->comment_id);
        if ($commentId <= 0) {
            return $this->errorJson('评论id不存在');
        }

        $memberId = \YunShop::app()->getMemberId();

        $like = PostCommentLikeModel::where([
            'uniacid' => \YunShop::app()->uniacid,
            'user_id' => $memberId,
            'comment_id' => $commentId,
        ])->first();
        if (isset($like->id)) {
            $like->delete();
            return $this->successJson('取消点赞成功', [
                'status' => 1,
                'is_like' => 1,
            ]);
        } else {
            $like = new PostCommentLikeModel;
            $like->uniacid = \YunShop::app()->uniacid;
            $like->user_id = $memberId;
            $like->comment_id = $commentId;
            $like->save();
            if (isset($like->id) && $like->id >= 0) {
                return $this->successJson('点赞成功', [
                    'status' => 1,
                    'is_like' => 2,
                ]);
            } else {
                return $this->errorJson('点赞失败', [
                    'status' => 0,
                    'is_like' => 1,
                ]);
            }
        }
    }

    public function postLike()
    {
        $postId = intval(\YunShop::request()->post_id);
        if ($postId <= 0) {
            return $this->errorJson('帖子id不存在');
        }

        $memberId = \YunShop::app()->getMemberId();

        $postRs = PostModel::where([
            'id' => $postId,
            'uniacid' => \YunShop::app()->uniacid,
        ])->first();
        if (!isset($postRs->id)) {
            return $this->errorJson('帖子不存在或已被删除');
        }

        $like = PostLikeModel::where([
            'uniacid' => \YunShop::app()->uniacid,
            'user_id' => $memberId,
            'post_id' => $postId,
        ])->first();
        if (isset($like->id)) {
            $like->delete();

            $postRs->like_nums -= 1;
            $postRs->save();

            return $this->successJson('取消点赞成功', [
                'status' => 1,
                'is_like' => 1,
            ]);
        } else {
            $like = new PostLikeModel;
            $like->uniacid = \YunShop::app()->uniacid;
            $like->user_id = $memberId;
            $like->post_id = $postId;
            $like->rele_user_id = $postRs->user_id;
            $like->save();

            if (isset($like->id) && $like->id >= 0) {
                if ($postRs->is_recommend != 1) {
                    $postRs->is_recommend = 1;
                }
                $postRs->like_nums += 1;
                $postRs->save();

                return $this->successJson('点赞成功', [
                    'status' => 1,
                    'is_like' => 2,
                ]);
            } else {
                return $this->errorJson('点赞失败', [
                    'status' => 0,
                    'is_like' => 1,
                ]);
            }
        }
    }

    public function postRead()
    {
        $postId = intval(\YunShop::request()->post_id);
        if ($postId <= 0) {
            return $this->errorJson('帖子id不存在');
        }

        $memberId = \YunShop::app()->getMemberId();

        $ip = request()->ip();

        $historyRs = PostHistoryModel::select('id')->where([
            'user_id' => $memberId,
            'post_id' => $postId,
            'ip' => $ip,
        ])->first();
        if (isset($historyRs->id)) {
            return $this->successJson('已阅');
        }

        $history = new PostHistoryModel;
        $history->user_id = $memberId;
        $history->post_id = $postId;
        $history->ip = $ip;
        $history->day_time = date('Y-m-d');
        $history->uniacid = \YunShop::app()->uniacid;
        $history->save();
        if (!isset($history->id) || $history->id <= 0) {
            return $this->errorJson('浏览量更新失败');
        }

        PostModel::where('id', $postId)->increment('view_nums');

        return $this->successJson('浏览量更新成功');
    }

    public function userFollow()
    {
        $fansId = intval(\YunShop::request()->fans_id);
        if ($fansId <= 0) {
            return $this->errorJson('被关注者id不存在');
        }

        $memberId = \YunShop::app()->getMemberId();
        if ($fansId == $memberId) {
            return $this->errorJson('自己不能关注自己');
        }

        $fansRs = UserModel::select('id')->where('ajy_uid', $fansId)->first();
        if (!isset($fansRs->id)) {
            return $this->errorJson('关注用户不存在', ['is_follow' => 1]);
        }

        $followRs = UserFollowModel::select('id')->where([
            'user_id' => $memberId,
            'fans_id' => $fansId,
            'uniacid' => \YunShop::app()->uniacid,
        ])->first();
        if (isset($followRs->id)) {
            UserFollowModel::where('id', $followRs->id)->delete();
            return $this->successJson('取消关注成功', ['is_follow' => 1]);
        } else {
            $follow = new UserFollowModel;
            $follow->uniacid = \YunShop::app()->uniacid;
            $follow->user_id = $memberId;
            $follow->fans_id = $fansId;
            $follow->rele_user_id = $fansId;
            $follow->save();

            if (!isset($follow->id) || $follow->id <= 0) {
                return $this->successJson('关注失败', ['is_follow' => 1]);
            } else {
                return $this->successJson('关注成功', ['is_follow' => 2]);
            }
        }
    }

    public function followPosts()
    {
        $followRs = UserFollowModel::select('id', 'fans_id')->where([
            'uniacid' => \YunShop::app()->uniacid,
            'user_id' => \YunShop::app()->getMemberId(),
        ])->get()->toArray();
        $followRs = array_column($followRs, 'fans_id');
        $followRs = array_values(array_unique(array_filter($followRs)));
        if (!isset($followRs[0])) {
            return $this->successJson('获取关注列表', [
                'total' => 0,
                'totalPage' => 1,
                'posts' => [],
            ]);
        }

        $listRs = PostModel::select(
            'id', 'title', 'images', 'video', 'video_thumb', 'video_size', 'image_size',
            'view_nums', 'comment_nums', 'like_nums', 'user_id'
        )->whereIn('user_id', $followRs)
            ->where('status', 1)->orderBy('create_time', 'desc')->paginate(10);
        $userIds = [];
        foreach ($listRs as &$v) {
            $v->images = json_decode($v->images, true);
            $v->video_size = json_decode($v->video_size, true);
            $v->image_size = json_decode($v->image_size, true);
            $v->heat = 10 + ($v->like_nums * 30) + ($v->comment_nums * 50) + ($v->view_nums * 10);
            $userIds[] = $v->user_id;
        }
        $userIds = array_values(array_filter(array_unique($userIds)));
        if (isset($userIds[0])) {
            $userRs = UserModel::select('id', 'ajy_uid', 'nickname', 'avatarurl')
                ->whereIn('ajy_uid', $userIds)->get()->toArray();
            foreach ($listRs as &$v1) {
                foreach ($userRs as $v2) {
                    if ($v1->user_id != $v2['ajy_uid']) {
                        continue;
                    }
                    $v1->nickname = $v2['nickname'];
                    $v1->avatarurl = $v2['avatarurl'];
                    break;
                }
            }
        }
        $return = [
            'total' => $listRs->total(),
            'totalPage' => $listRs->lastPage(),
            'posts' => $listRs->items(),
        ];
        return $this->successJson('获取关注列表', $return);
    }

    public function postDelete()
    {
        $postId = intval(\YunShop::request()->id);
        if ($postId <= 0) {
            return $this->errorJson('请传入id');
        }

        $postRs = PostModel::select('id', 'type', 'user_id', 'article_id')->where([
            'id' => $postId,
            'uniacid' => \YunShop::app()->uniacid,
        ])->first();
        if (!isset($postRs->id)) {
            return $this->errorJson('帖子不存在或已被删除');
        }

        $memberId = \YunShop::app()->getMemberId();
        if ($postRs->user_id != $memberId) {
            return $this->errorJson('不是你的帖子乱删个毛线');
        }

        PostModel::where('id', $postRs->id)->delete();

        if ($postRs->type == 1) {
            ArticleModel::where('id', $postRs->article_id)->decrement('discuss_viewpoint_nums');
        }

        PostCommentModel::where('post_id', $postRs->id)->delete();
        PostLikeModel::where('post_id', $postRs->id)->delete();
        return $this->successJson('删除成功');
    }

    public function share()
    {
        $page = \YunShop::request()->page;
        $scene = \YunShop::request()->scene;
        if (!isset($scene[0])) {
            return $this->errorJson('小程序场景值不存在');
        }
        $postId = intval(\YunShop::request()->post_id);
        if ($postId <= 0) {
            return $this->errorJson('帖子id不能为空');
        }

        $memberId = \YunShop::app()->getMemberId();

        



        try {
            $qrcode = IndexController::qrcodeCreateUnlimit($memberId, $scene, $page, isset(\YunShop::request()->os) ? \YunShop::request()->os : '');
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
        return $this->successJson('二维码生成成功', [
            'id' => $qrcode->id,
            'qrcode' => yz_tomedia($qrcode->qrcode),
        ]);
    }
}
