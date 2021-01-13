<?php

namespace Yunshop\WechatComplaint\admin;

use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use Yunshop\WechatComplaint\models\ComplaintLogModel;
use Yunshop\WechatComplaint\models\ComplaintProjectModel;
use Yunshop\WechatComplaint\services\WechatComplaintService;

class ProjectController extends BaseController
{
    private $pageSize = 20;

    public function index()
    {
        $searchData = \YunShop::request()->search;

        $list = ComplaintProjectModel::where('uniacid', \YunShop::app()->uniacid);
        if (isset($searchData['name'])) {
            $list = $list->where('name', 'like', '%' . $searchData['name'] . '%');
        }
        $list = $list->orderBy('id', 'desc')
            ->paginate($this->pageSize)->toArray();
        $projectIds = array_column($list['data'], 'id');
        if (isset($projectIds[0])) {
            $numberRs = ComplaintLogModel::selectRaw('project_id,count(1) as countnum')
                ->where('uniacid', \YunShop::app()->uniacid)
                ->whereIn('project_id', $projectIds)
                ->groupBy('project_id')->get()->toArray();

            $peopleRs = ComplaintLogModel::selectRaw('member_id,project_id,count(1) as countnum')
                ->where('uniacid', \YunShop::app()->uniacid)
                ->whereIn('project_id', $projectIds)
                ->groupBy('project_id', 'member_id')->get()->toArray();

            foreach ($list['data'] as &$v1) {
                $v1['total_num'] = 0;
                foreach ($numberRs as $v2) {
                    if ($v1['id'] != $v2['project_id']) {
                        continue;
                    }
                    $v1['total_num'] = $v2['countnum'];
                    break;
                }

                $v1['total_people'] = 0;
                $tempPeople = [];
                foreach ($peopleRs as $v3) {
                    if ($v1['id'] != $v3['project_id']) {
                        continue;
                    }
                    $tempPeople[] = $v3['member_id'];
                }
                $v1['total_people'] = count($tempPeople);
            }
            unset($v1);
        }

        $pager = PaginationHelper::show($list['total'], $list['current_page'], $this->pageSize);

        return view('Yunshop\WechatComplaint::admin.project.index', [
            'pluginName' => WechatComplaintService::get('name'),
            'data' => $list['data'],
            'search' => $searchData,
            'pager' => $pager,
        ]);
    }

    public function edit()
    {
        $data = \YunShop::request()->data;
        if ($data) {
            if (isset($data['id']) && $data['id'] > 0) {
                $project = ComplaintProjectModel::where([
                    'id' => $data['id'],
                    'uniacid' => \YunShop::app()->uniacid,
                ])->first();
                if (!isset($project->id)) {
                    return $this->message('数据ID不存在', '', 'danger');
                }
            } else {
                $project = new ComplaintProjectModel;
                $project->uniacid = \YunShop::app()->uniacid;
            }
            $project->name = (isset($data['name']) && !empty($data['name'])) ? $data['name'] : '来源项目 - ' . date('Y年m月d日H:i');
            $project->save();
            if (!isset($project->id) || $project->id <= 0) {
                return $this->message('来源保存错误', '', 'danger');
            }

            return $this->message('保存成功', Url::absoluteWeb('plugin.wechat-complaint.admin.project.index'));
        }

        $id = (int) \YunShop::request()->id;
        $project = [];
        if ($id > 0) {
            $project = ComplaintProjectModel::where([
                'id' => $id,
                'uniacid' => \YunShop::app()->uniacid,
            ])->first();
        }

        return view('Yunshop\WechatComplaint::admin.project.edit', [
            'pluginName' => WechatComplaintService::get('name'),
            'data' => $project,
        ]);
    }

    public function delete()
    {
        $id = (int) \YunShop::request()->id;
        $projectRs = ComplaintProjectModel::select('id')->where([
            'id' => $id,
            'uniacid' => \YunShop::app()->uniacid,
        ])->first();
        if (!isset($projectRs->id)) {
            return $this->message('来源未找到', '', 'danger');
        }

        ComplaintProjectModel::where([
            'id' => $id,
            'uniacid' => \YunShop::app()->uniacid,
        ])->delete();

        return $this->message('删除成功');
    }
}
