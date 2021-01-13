<?php

namespace Yunshop\MinappContent\admin;

use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use app\common\models\Goods;
use Yunshop\MinappContent\models\CosImagesLogsModel;
use Yunshop\MinappContent\models\PostCommentModel;
use Yunshop\MinappContent\models\PostLikeModel;
use Yunshop\MinappContent\models\PostModel;
use Yunshop\MinappContent\models\SnsBoardModel;
use Yunshop\MinappContent\models\UserModel;
use Yunshop\MinappContent\services\MinappContentService;

class PostController extends BaseController
{
    private $pageSize = 30;

    private $board;

    public function preAction()
    {
        parent::preAction();

        $this->board = SnsBoardModel::select('id', 'name')
            ->where([
                'uniacid' => \YunShop::app()->uniacid,
                'status' => 1,
            ])->get()->toArray();

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

        return view('Yunshop\MinappContent::admin.post.list', [
            'pluginName' => MinappContentService::get('name'),
            'search' => $searchData,
            'data' => $list['data'],
            'pager' => $pager,
            'board' => $this->board,
        ]);
    }

    public function edit()
    {

        $data = \YunShop::request()->data;
        if ($data) {
            $data = array_filter($data);
            if (!isset($data['title']) || !isset(trim($data['title'])[0])) {
                return $this->message('标题不能为空', '', 'danger');
            }
            $data['title'] = trim($data['title']);
            if (!isset($data['content']) || !isset(trim($data['content'])[0])) {
                return $this->message('内容不能为空', '', 'danger');
            }
            $data['content'] = trim($data['content']);
            if (!isset($data['board_id']) || intval($data['board_id']) <= 0) {
                return $this->message('请选择话题版块', '', 'danger');
            }
            if (!isset($data['user_id']) || intval($data['user_id']) <= 0) {
                return $this->message('请选择发布用户', '', 'danger');
            }
            if (!isset($data['images']) && !isset($data['video'])) {
                return $this->message('图片或视频至少有一项要上传', '', 'danger');
            }
            if (isset($data['images'])) {
                $data['images'] = array_values(array_filter($data['images']));
                if (!isset($data['images'][0])) {
                    return $this->message('图片上传出现错误', '', 'danger');
                }
            }
            if (isset($data['video'])) {
                $data['video'] = trim($data['video']);
            }

            if (isset($data['id'])) {
                $post = PostModel::where([
                    'id' => $data['id'],
                    'uniacid' => \YunShop::app()->uniacid,
                ])->first();
                if (!isset($post->id)) {
                    return $this->message('参数ID错误', '', 'danger');
                }
            } else {
                $post = new PostModel;
                $post->uniacid = \YunShop::app()->uniacid;
            }
            $post->board_id = intval($data['board_id']);
            $post->type = 2;
            $post->content = $data['content'];
            $post->status = isset($data['status']) ? $data['status'] : 0;
            $post->user_id = intval($data['user_id']);
            $post->title = $data['title'];
            if (isset($data['goods_id']) && is_array($data['goods_id'])) {
                $post->goods_id = implode(',', $data['goods_id']);
            } else {
                $post->goods_id = '';
            }
            if (isset($data['images']) && is_array($data['images'])) {
                $post->images = json_encode($data['images']);
            } else {
                $post->images = '';
            }
            $post->is_recommend = isset($data['is_recommend']) ? intval($data['is_recommend']) : 0;
            $post->is_hot = isset($data['is_hot']) ? intval($data['is_hot']) : 0;
            $post->video = isset($data['video']) ? $data['video'] : '';

            if (isset($data['video'][0])) {
                $thumbRs = CosImagesLogsModel::select('id', 'thumb')
                    ->where('video', $data['video'])->first();
                if (isset($thumbRs->id)) {
                    $post->video_thumb = $thumbRs->thumb;
                } else {
                    $post->video_thumb = '';
                }
            } else {
                $post->video_thumb = '';
            }

            $post->save();
            if (!isset($post->id) || $post->id <= 0) {
                return $this->message('修改失败', '', 'danger');
            }

            return $this->message('修改成功', Url::absoluteWeb('plugin.minapp-content.admin.post.index'));
        }

        $id = (int) \YunShop::request()->id;
        if ($id > 0) {
            $infoRs = PostModel::where([
                'id' => $id,
                'uniacid' => \YunShop::app()->uniacid,
            ])->first();
            if (!isset($infoRs->id)) {
                return $this->message('话题不存在或已被删除', '', 'danger');
            }

            $infoRs->images = json_decode($infoRs->images, true);
            $infoRs->goods_id = explode(',', $infoRs->goods_id);
        }

        $userRs = UserModel::select('ajy_uid', 'nickname')
            ->where('uniacid', \YunShop::app()->uniacid)
            ->orderBy('id', 'asc')->get()->toArray();

        $goodsRs = Goods::select('id', 'title')
            ->where('status', 1)
            ->orderBy('display_order', 'desc')->get()->toArray();

        return view('Yunshop\MinappContent::admin.post.edit', [
            'pluginName' => MinappContentService::get('name'),
            'info' => isset($infoRs) ? $infoRs : null,
            'board' => $this->board,
            'user' => $userRs,
            'goods' => $goodsRs,
        ]);
    }

    public function delete()
    {
        $id = (int) \YunShop::request()->id;
        if ($id <= 0) {
            return $this->message('ID参数错误', '', 'danger');
        }

        PostCommentModel::where('post_id', $id)->delete();
        PostLikeModel::where('post_id', $id)->delete();

        PostModel::where([
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
        $infoRs = PostModel::where([
            'id' => $id,
            'uniacid' => \YunShop::app()->uniacid,
        ])->first();
        if (!isset($infoRs->id)) {
            return $this->message('参数ID数据未找到', '', 'danger');
        }

        $isRecommend = \YunShop::request()->is_recommend;
        if (isset($isRecommend) && $isRecommend == 1) {
            $message = '';
            if ($infoRs->is_recommend == 1) {
                $infoRs->is_recommend = 0;
                $message = '取消推荐成功';
            } else {
                $infoRs->is_recommend = 1;
                $message = '推荐成功';
            }
            $infoRs->save();

            return $this->message($message);
        }

        $isHot = \YunShop::request()->is_hot;
        if (isset($isHot) && $isHot == 1) {
            $message = '';
            if ($infoRs->is_hot == 1) {
                $infoRs->is_hot = 0;
                $message = '取消首页精选成功';
            } else {
                $infoRs->is_hot = 1;
                $message = '首页精选成功';
            }
            $infoRs->save();

            return $this->message($message);
        }

        $check = \YunShop::request()->check;
        if (isset($check) && $check == 1) {
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

        return $this->message('参数错误');
    }
}
