<?php

namespace Yunshop\MinappContent\admin;

use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use Yunshop\MinappContent\models\AnswerModel;
use Yunshop\MinappContent\models\UserModel;
use Yunshop\MinappContent\services\MinappContentService;

class AnswerController extends BaseController
{
    private $pageSize = 30;

    public function index()
    {
        $searchData = \YunShop::request()->search;

        $list = AnswerModel::select(
            'diagnostic_service_answer.*', 'diagnostic_service_somato_type.name'
        )->leftJoin('diagnostic_service_somato_type', 'diagnostic_service_somato_type.id', '=', 'diagnostic_service_answer.ture_somato_type_id')
            ->where('diagnostic_service_answer.uniacid', \YunShop::app()->uniacid);
        if (isset($searchData['datelimit']['start']) && isset($searchData['datelimit']['end']) &&
            strtotime($searchData['datelimit']['start']) !== false && strtotime($searchData['datelimit']['end']) !== false
        ) {
            $list = $list->where('diagnostic_service_answer.create_time', '>', strtotime($searchData['datelimit']['start']))
                ->where('diagnostic_service_answer.create_time', '<', strtotime($searchData['datelimit']['end']) + 86400);
        } else {
            $searchData['datelimit']['start'] = date('Y-m-d H:i:s', strtotime('-1 year'));
            $searchData['datelimit']['end'] = date('Y-m-d H:i:s');
        }
        if (isset($searchData['keywords']) && trim($searchData['keywords']) != '') {
            $searchData['keywords'] = trim($searchData['keywords']);
            $list = $list->where(function ($query) use ($searchData) {
                $query->where('diagnostic_service_answer.user_id', 'like', '%' . $searchData['keywords'] . '%')
                    ->orWhere('diagnostic_service_somato_type.name', 'like', '%' . $searchData['keywords'] . '%');
            });
        }

        $list = $list->orderBy('diagnostic_service_answer.id', 'desc')
            ->paginate($this->pageSize)->toArray();

        $pager = PaginationHelper::show($list['total'], $list['current_page'], $this->pageSize);

        $userIds = [];
        foreach ($list['data'] as $v) {
            $userIds[] = $v['user_id'];
        }
        if (isset($userIds[0])) {
            $userRs = UserModel::select('ajy_uid', 'avatarurl', 'nickname', 'gender')
                ->whereIn('ajy_uid', $userIds)->get()->toArray();
            foreach ($list['data'] as &$v1) {
                foreach ($userRs as $v2) {
                    if ($v1['user_id'] != $v2['ajy_uid']) {
                        continue;
                    }

                    $v1['avatarurl'] = $v2['avatarurl'];
                    $v1['nickname'] = $v2['nickname'];
                    $v1['gender'] = $v2['gender'];
                    break;
                }
            }
            unset($v1);
        }

        return view('Yunshop\MinappContent::admin.answer.list', [
            'pluginName' => MinappContentService::get('name'),
            'data' => $list['data'],
            'search' => $searchData,
            'pager' => $pager,
        ]);
    }

    public function detail()
    {
        $id = (int) \YunShop::request()->id;
        if ($id <= 0) {
            return $this->message('参数ID错误', '', 'danger');
        }
        $infoRs = AnswerModel::where([
            'id' => $id,
            'uniacid' => \YunShop::app()->uniacid,
        ])->first();
        if (!isset($infoRs->id)) {
            return $this->message('不存在或已被删除', '', 'danger');
        }

        return view('Yunshop\MinappContent::admin.answer.detail', [
            'pluginName' => MinappContentService::get('name'),
            'info' => $infoRs,
        ]);
    }

    public function delete()
    {
        $id = (int) \YunShop::request()->id;
        if ($id <= 0) {
            return $this->message('ID参数错误', '', 'danger');
        }

        AnswerModel::where([
            'id' => $id,
            'uniacid' => \YunShop::app()->uniacid,
        ])->delete();

        return $this->message('删除成功');
    }
}
