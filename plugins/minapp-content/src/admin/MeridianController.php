<?php

namespace Yunshop\MinappContent\admin;

use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use Yunshop\Appletslive\common\models\Replay;
use Yunshop\Appletslive\common\models\Room;
use Yunshop\MinappContent\models\AcupointMerModel;
use Yunshop\MinappContent\models\MeridianModel;
use Yunshop\MinappContent\services\MinappContentService;

class MeridianController extends BaseController
{
    private $pageSize = 100;

    public function index()
    {
        $list = MeridianModel::where('uniacid', \YunShop::app()->uniacid)
            ->orderBy('list_order', 'desc')
            ->paginate($this->pageSize)->toArray();

        $pager = PaginationHelper::show($list['total'], $list['current_page'], $this->pageSize);

        return view('Yunshop\MinappContent::admin.meridian.list', [
            'pluginName' => MinappContentService::get('name'),
            'data' => $list['data'],
            'pager' => $pager,
        ]);
    }

    public function edit()
    {
        $data = \YunShop::request()->data;
        if ($data) {
            $data = array_filter($data);
            if (!isset($data['name'])) {
                return $this->message('经络名称不能为空', '', 'danger');
            }

            if (isset($data['id'])) {
                $meridian = MeridianModel::where([
                    'id' => $data['id'],
                    'uniacid' => \YunShop::app()->uniacid,
                ])->first();
                if (!isset($meridian->id)) {
                    return $this->message('经络ID参数错误', '', 'danger');
                }
            } else {
                $meridian = new MeridianModel;
                $meridian->uniacid = \YunShop::app()->uniacid;
            }
            $meridian->name = $data['name'];
            $meridian->type_id = isset($data['type_id']) ? $data['type_id'] : 1;
            $meridian->list_order = isset($data['list_order']) ? $data['list_order'] : 0;
            $meridian->start_time = isset($data['start_time']) ? $data['start_time'] : '';
            $meridian->end_time = isset($data['end_time']) ? $data['end_time'] : '';
            $meridian->discription = isset($data['discription']) ? $data['discription'] : '';
            $meridian->notice = isset($data['notice']) ? $data['notice'] : '';
            $meridian->content = isset($data['content']) ? $data['content'] : '';
            $meridian->audio = isset($data['audio']) ? $data['audio'] : '';
            $meridian->image = isset($data['image']) ? $data['image'] : '';
            $meridian->is_hot = isset($data['is_hot']) ? $data['is_hot'] : 0;
            $meridian->status = isset($data['status']) ? $data['status'] : 1;
            $meridian->recommend_course = isset($data['recommend_course']) ? $data['recommend_course'] : '';
            $meridian->save();

            return $this->message('保存成功', Url::absoluteWeb('plugin.minapp-content.admin.meridian.index'));
        }

        $id = (int) \YunShop::request()->id;
        if ($id > 0) {
            $meridianInfo = MeridianModel::where([
                'id' => $id,
                'uniacid' => \YunShop::app()->uniacid,
            ])->first();
            if (isset($meridianInfo->recommend_course[0])) {
                $courseRs = Replay::select('id', 'rid')->where([
                    'id' => $meridianInfo->recommend_course,
                    'uniacid' => \YunShop::app()->uniacid,
                    'type' => 1,
                    'delete_time' => 0,
                ])->first();
                if (isset($courseRs->id)) {
                    // 课程id
                    $meridianInfo->recommend_course = $courseRs->rid;
                    // 课时id
                    $meridianInfo->recommend_course_hour = $courseRs->id;
                }
            }
        }

        $courseRs = Room::select('id', 'name')->where([
            'uniacid' => \YunShop::app()->uniacid,
            'type' => 1,
            'delete_time' => 0,
        ])->orderBy('sort', 'desc')->get();

        return view('Yunshop\MinappContent::admin.meridian.edit', [
            'pluginName' => MinappContentService::get('name'),
            'course' => $courseRs,
            'info' => isset($meridianInfo) ? $meridianInfo : null,
        ]);
    }

    public function delete()
    {
        $id = (int) \YunShop::request()->id;
        if ($id <= 0) {
            return $this->message('ID参数错误', '', 'danger');
        }

        $infoRs = AcupointMerModel::select('id')->where('meridian_id', $id)->first();
        if (isset($infoRs->id)) {
            return $this->message('此经络下面有穴位，无法删除', '', 'danger');
        }

        MeridianModel::where([
            'id' => $id,
            'uniacid' => \YunShop::app()->uniacid,
        ])->delete();
        return $this->message('删除成功');
    }

    public function courseHour()
    {
        $id = (int) \YunShop::request()->id;
        $replay = Replay::select('id', 'title')->where([
            'uniacid' => \YunShop::app()->uniacid,
            'rid' => $id,
            'type' => 1,
            'delete_time' => 0,
        ])->orderBy('sort', 'desc')->get();
        return $this->successJson('成功', $replay);
    }

    public function acupoints()
    {
        $id = (int) \YunShop::request()->id;

        $sortData = \YunShop::request()->sort_data;
        if (isset($sortData[0]['id'])) {
            foreach ($sortData as $v) {
                if (!isset($v['id']) || $v['id'] <= 0 ||
                    !isset($v['sort']) || $v['sort'][0] < 0
                ) {
                    continue;
                }
                $v['sort'] = intval($v['sort']);
                AcupointMerModel::where([
                    'id' => $v['id'],
                    'uniacid' => \YunShop::app()->uniacid,
                ])->limit(1)->update([
                    'sort' => $v['sort'],
                ]);
            }
            return $this->successJson('排序更新成功！');
        }

        $infoRs = MeridianModel::where([
            'id' => $id,
            'uniacid' => \YunShop::app()->uniacid,
        ])->first();
        if (!isset($infoRs->id)) {
            return $this->message('ID参数错误', '', 'danger');
        }

        $list = AcupointMerModel::select(
            'diagnostic_service_mer_acupoint.*', 'diagnostic_service_acupoint.name',
            'diagnostic_service_acupoint.chart', 'diagnostic_service_acupoint.image'
        )->leftJoin('diagnostic_service_acupoint', 'diagnostic_service_mer_acupoint.acupoint_id', '=', 'diagnostic_service_acupoint.id')
            ->where('diagnostic_service_mer_acupoint.meridian_id', $id)
            ->where('diagnostic_service_mer_acupoint.uniacid', \YunShop::app()->uniacid)
            ->orderBy('diagnostic_service_mer_acupoint.sort', 'desc')->get()->toArray();
        return view('Yunshop\MinappContent::admin.meridian.acupoints', [
            'pluginName' => MinappContentService::get('name'),
            'info' => $infoRs,
            'data' => $list,
        ]);
    }
}
