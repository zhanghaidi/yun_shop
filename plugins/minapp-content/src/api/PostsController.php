<?php

namespace Yunshop\MinappContent\api;

use app\common\components\ApiController;
use Illuminate\Support\Facades\Redis;
use Yunshop\Appletslive\common\services\BaseService as AppletsliveBaseService;
use Yunshop\MinappContent\models\PostModel;
use Yunshop\MinappContent\models\SnsBoardModel;
use Yunshop\MinappContent\models\UserModel;

class PostsController extends ApiController
{
    protected $publicAction = ['hotPosts', 'snsBoard'];
    protected $ignoreAction = ['hotPosts', 'snsBoard'];

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
}
