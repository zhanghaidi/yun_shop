<?php

namespace Yunshop\MinappContent\admin;

use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use Yunshop\MinappContent\models\AcupointCommentModel;
use Yunshop\MinappContent\models\AcupointModel;
use Yunshop\MinappContent\models\UserModel;
use Yunshop\MinappContent\services\MinappContentService;

class AcupointReplysController extends BaseController
{
    private $pageSize = 30;

    public function index()
    {
        $id = \YunShop::request()->id;
        if ($id <= 0) {
            return $this->message('参数ID错误', '', 'danger');
        }

        $infoRs = AcupointModel::where([
            'id' => $id,
            'uniacid' => \YunShop::app()->uniacid,
        ])->first();
        if (!isset($infoRs->id)) {
            return $this->message('穴位ID不存在', '', 'danger');
        }

        $list = AcupointCommentModel::where([
            'uniacid' => \YunShop::app()->uniacid,
            'acupoint_id' => $id,
            'is_reply' => 0,
        ])->orderBy('display_order', 'desc')
            ->orderBy('create_time', 'desc')
            ->paginate($this->pageSize)->toArray();
        $ids = $userIds = [];
        foreach ($list['data'] as $v) {
            $ids[] = $v['id'];
            $userIds[] = $v['user_id'];
        }
        $userIds = array_values(array_unique(array_filter($userIds)));
        if (isset($ids[0])) {
            $replyRs = AcupointCommentModel::selectRaw('parent_id,count(1) as countNum')->where([
                'uniacid' => \YunShop::app()->uniacid,
                'is_reply' => 1,
            ])->whereIn('parent_id', $ids)
                ->groupBy('parent_id')->get()->toArray();

            foreach ($list['data'] as &$v1) {
                $v1['counts'] = 0;
                foreach ($replyRs as $v2) {
                    if ($v1['id'] != $v2['parent_id']) {
                        continue;
                    }
                    $v1['counts'] = $v2['countNum'];
                    break;
                }
            }
            unset($v1);
        }

        if (isset($userIds[0])) {
            $userRs = UserModel::select('ajy_uid', 'avatarurl', 'nickname', 'province', 'city')
                ->whereIn('ajy_uid', $userIds)->get()->toArray();
            foreach ($list['data'] as &$v1) {
                foreach ($userRs as $v2) {
                    if ($v1['user_id'] != $v2['ajy_uid']) {
                        continue;
                    }

                    $v1['avatarurl'] = $v2['avatarurl'];
                    $v1['nickname'] = $v2['nickname'];
                    $v1['province'] = $v2['province'];
                    $v1['city'] = $v2['city'];
                    break;
                }
            }
            unset($v1);
        }

        $pager = PaginationHelper::show($list['total'], $list['current_page'], $this->pageSize);

        return view('Yunshop\MinappContent::admin.acupoint_reply.list', [
            'pluginName' => MinappContentService::get('name'),
            'info' => $infoRs,
            'data' => $list['data'],
            'pager' => $pager,
        ]);
    }

    public function post()
    {
        $id = \YunShop::request()->id;
        if ($id <= 0) {
            return $this->message('参数ID错误', '', 'danger');
        }

        $infoRs = AcupointCommentModel::where([
            'id' => $id,
            'uniacid' => \YunShop::app()->uniacid,
        ])->first();
        if (!isset($infoRs->id)) {
            return $this->message('主评不存在或已被删除', '', 'danger');
        }

        $list = AcupointCommentModel::where([
            'uniacid' => \YunShop::app()->uniacid,
            'parent_id' => $id,
            'is_reply' => 1,
        ])->orderBy('create_time', 'desc')->get()->toArray();
        $userIds = [];
        foreach ($list as $v) {
            $userIds[] = $v['user_id'];
        }
        $userIds = array_values(array_unique(array_filter($userIds)));
        if (isset($userIds[0])) {
            $userRs = UserModel::select('ajy_uid', 'avatarurl', 'nickname', 'province', 'city')
                ->whereIn('ajy_uid', $userIds)->get()->toArray();
            foreach ($list as &$v1) {
                foreach ($userRs as $v2) {
                    if ($v1['user_id'] != $v2['ajy_uid']) {
                        continue;
                    }

                    $v1['avatarurl'] = $v2['avatarurl'];
                    $v1['nickname'] = $v2['nickname'];
                    $v1['province'] = $v2['province'];
                    $v1['city'] = $v2['city'];
                    break;
                }
            }
            unset($v1);
        }

        return view('Yunshop\MinappContent::admin.acupoint_reply.post', [
            'pluginName' => MinappContentService::get('name'),
            'info' => $infoRs,
            'data' => $list,
        ]);
    }

    public function status()
    {
        $id = \YunShop::request()->id;
        if ($id <= 0) {
            return $this->message('参数ID错误', '', 'danger');
        }
        $flag = \YunShop::request()->flag;
        if (isset($flag) && $flag == 1) {
            $infoRs = AcupointCommentModel::where([
                'id' => $id,
                'uniacid' => \YunShop::app()->uniacid,
                'is_reply' => 0,
            ])->first();
            if (!isset($infoRs->id)) {
                return $this->message('参数ID数据未找到', '', 'danger');
            }
            $message = '';
            if ($infoRs->display_order == 1) {
                $infoRs->display_order = 0;
                $message = '取消置顶成功';
            } else {
                $infoRs->display_order = 1;
                $message = '置顶成功';
            }
            $infoRs->save();

            return $this->message($message);
        }

        $check = \YunShop::request()->check;
        if (in_array($check, [1, -1])) {
            $infoRs = AcupointCommentModel::where([
                'id' => $id,
                'uniacid' => \YunShop::app()->uniacid,
                'is_reply' => 0,
            ])->first();
            if (!isset($infoRs->id)) {
                return $this->message('参数ID数据未找到', '', 'danger');
            }

            if ($check == 1) {
                $infoRs->status = 1;

                AcupointModel::where([
                    'id' => $infoRs->acupoint_id,
                    'uniacid' => \YunShop::app()->uniacid,
                ])->limit(1)->increment('comment_nums');
            } else {
                $infoRs->status = -1;
            }
            $infoRs->save();

            return $this->message('评论审核成功');
        }
        return $this->message('未知参数', '', 'danger');
    }

    public function delete()
    {
        $id = (int) \YunShop::request()->id;
        if ($id <= 0) {
            return $this->message('ID参数错误', '', 'danger');
        }

        $infoRs = AcupointCommentModel::where([
            'id' => $id,
            'uniacid' => \YunShop::app()->uniacid,
        ])->first();
        if (!isset($infoRs->id)) {
            return $this->message('参数ID数据未找到', '', 'danger');
        }

        $replyNums = 0;
        if ($infoRs->is_reply == 0) {
            $numsRs = AcupointCommentModel::where([
                'parent_id' => $id,
                'uniacid' => \YunShop::app()->uniacid,
                'status' => 1,
            ])->count();
            $replyNums = $numsRs;
        }

        if ($infoRs->status == 1) {
            $replyNums += 1;
        }

        AcupointCommentModel::where([
            'parent_id' => $id,
            'uniacid' => \YunShop::app()->uniacid,
        ])->delete();

        AcupointModel::where([
            'id' => $infoRs->acupoint_id,
            'uniacid' => \YunShop::app()->uniacid,
        ])->limit(1)->decrement('comment_nums', $replyNums);

        AcupointCommentModel::where('id', $infoRs->id)->limit(1)->delete();

        return $this->message('删除成功');
    }
}
