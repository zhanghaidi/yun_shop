<?php

namespace Yunshop\MinappContent\admin;

use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use Yunshop\MinappContent\models\PostModel;
use Yunshop\MinappContent\models\SnsBoardModel;
use Yunshop\MinappContent\models\UserModel;
use Yunshop\MinappContent\services\MinappContentService;

class SnsBoardController extends BaseController
{
    private $pageSize = 20;

    public function index()
    {
        $searchData = \YunShop::request()->search;

        $list = SnsBoardModel::where('uniacid', \YunShop::app()->uniacid);
        if (isset($searchData['status']) && in_array($searchData['status'], [0, 1])) {
            $searchData['status'] = intval($searchData['status']);
            $list = $list->where('status', $searchData['status']);
        }
        if (isset($searchData['keywords']) && isset(trim($searchData['keywords'])[0])) {
            $searchData['keywords'] = trim($searchData['keywords']);
            $list = $list->where('name', 'like', '%' . $searchData['keywords'] . '%');
        }
        $list = $list->orderBy('list_order', 'desc')
            ->orderBy('id', 'asc')
            ->paginate($this->pageSize)->toArray();
        $pager = PaginationHelper::show($list['total'], $list['current_page'], $this->pageSize);

        $userIds = $boardIds = [];
        foreach ($list['data'] as $v) {
            $boardIds[] = $v['id'];
            $userIds[] = $v['manager'];
        }

        if (isset($boardIds[0])) {
            $postRs = PostModel::selectRaw('board_id,count(1) as countnum')
                ->whereIn('board_id', $boardIds)
                ->where('uniacid', \YunShop::app()->uniacid)
                ->groupBy('board_id')->get()->toArray();
            foreach ($list['data'] as &$v1) {
                $v1['posts_nums'] = 0;
                foreach ($postRs as $v2) {
                    if ($v1['id'] != $v2['board_id']) {
                        continue;
                    }
                    $v1['posts_nums'] = $v2['countnum'];
                    break;
                }
            }
            unset($v1);
        }
        $userIds = array_values(array_unique(array_filter($userIds)));
        if (isset($userIds[0])) {
            $userRs = UserModel::select('ajy_uid', 'nickname')
                ->whereIn('ajy_uid', $userIds)->get()->toArray();
            foreach ($list['data'] as &$v1) {
                foreach ($userRs as $v2) {
                    if ($v1['manager'] != $v2['ajy_uid']) {
                        continue;
                    }

                    $v1['nickname'] = $v2['nickname'];
                    break;
                }
            }
            unset($v1);
        }

        return view('Yunshop\MinappContent::admin.sns_board.list', [
            'pluginName' => MinappContentService::get('name'),
            'search' => $searchData,
            'data' => $list['data'],
            'pager' => $pager,
        ]);
    }

    public function edit()
    {
        $data = \YunShop::request()->data;
        if ($data) {
            $data = array_filter($data);
            if (!isset($data['name']) || !isset(trim($data['name'])[0])) {
                return $this->message('板块名称不能为空', '', 'danger');
            }
            $data['name'] = trim($data['name']);
            if (!isset($data['manager']) || $data['manager'] <= 0) {
                return $this->message('请选择版主', '', 'danger');
            }

            if (isset($data['id'])) {
                $board = SnsBoardModel::where([
                    'id' => $data['id'],
                    'uniacid' => \YunShop::app()->uniacid,
                ])->first();
                if (!isset($board->id)) {
                    return $this->message('版块ID参数错误', '', 'danger');
                }
            } else {
                $board = new SnsBoardModel;
                $board->uniacid = \YunShop::app()->uniacid;
            }
            $board->name = $data['name'];
            $board->manager = $data['manager'];
            $board->list_order = isset($data['list_order']) ? $data['list_order'] : 0;
            $board->thumb = isset($data['thumb']) ? trim($data['thumb']) : '';
            $board->status = isset($data['status']) ? intval($data['status']) : 0;
            $board->need_check = isset($data['need_check']) ? intval($data['need_check']) : 0;
            $board->need_check_replys = isset($data['need_check_replys']) ? intval($data['need_check_replys']) : 0;
            $board->is_user_publish = isset($data['is_user_publish']) ? intval($data['is_user_publish']) : 0;
            $board->save();
            if (!isset($board->id) || $board->id <= 0) {
                return $this->message('修改失败', '', 'danger');
            }

            return $this->message('修改成功', Url::absoluteWeb('plugin.minapp-content.admin.sns-board.index'));
        }

        $id = (int) \YunShop::request()->id;
        if ($id > 0) {
            $infoRs = SnsBoardModel::where([
                'id' => $id,
                'uniacid' => \YunShop::app()->uniacid,
            ])->first();
        }

        $userRs = UserModel::select('ajy_uid', 'nickname')
            ->where('uniacid', \YunShop::app()->uniacid)
            ->get()->toArray();

        return view('Yunshop\MinappContent::admin.sns_board.edit', [
            'pluginName' => MinappContentService::get('name'),
            'info' => isset($infoRs) ? $infoRs : null,
            'user' => $userRs,
        ]);
    }

    public function status()
    {
        $id = \YunShop::request()->id;
        if ($id <= 0) {
            return $this->message('参数ID错误', '', 'danger');
        }
        $infoRs = SnsBoardModel::where([
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

    public function delete()
    {
        $id = (int) \YunShop::request()->id;
        if ($id <= 0) {
            return $this->message('ID参数错误', '', 'danger');
        }

        SnsBoardModel::where([
            'id' => $id,
            'uniacid' => \YunShop::app()->uniacid,
        ])->delete();

        return $this->message('删除成功');
    }
}
