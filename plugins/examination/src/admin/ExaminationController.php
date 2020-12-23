<?php

namespace Yunshop\Examination\admin;

use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use Yunshop\Examination\models\ExaminationContentModel;
use Yunshop\Examination\models\ExaminationModel;
use Yunshop\Examination\models\PaperModel;
use Yunshop\Examination\services\ExaminationService;

class ExaminationController extends BaseController
{
    private $pageSize = 20;

    public function index()
    {
        $id = (int) \YunShop::request()->id;
        $isAjax = (int) \YunShop::request()->is_ajax;

        $listRs = ExaminationModel::where('uniacid', \YunShop::app()->uniacid)
            ->orderBy('id', 'desc')
            ->paginate($this->pageSize);
        $pager = PaginationHelper::show($listRs->total(), $listRs->currentPage(), $this->pageSize);

        return view('Yunshop\Examination::admin.examination.index', [
            'pluginName' => ExaminationService::get('name'),
            'data' => $listRs->items(),
            'pager' => $pager,
        ]);
    }

    public function edit()
    {
        $paperRs = PaperModel::select('id', 'name', 'question', 'score')
            ->where('uniacid', \YunShop::app()->uniacid)
            ->orderBy('id', 'asc')->get()->toArray();

        $data = \YunShop::request()->data;
        if ($data) {
            if (!isset($data['paper_id']) || !in_array($data['paper_id'], array_column($paperRs, 'id'))) {
                return $this->message('请选择试卷', '', 'error');
            }

            if (isset($data['time_range']['start']) && isset($data['time_range']['end'])) {
                $data['time_range']['start'] = strtotime($data['time_range']['start']);
                $data['time_range']['end'] = strtotime($data['time_range']['end']);
                if (isset($data['time_status']) && $data['time_status'] == 1) {
                    if ($data['time_range']['start'] === false || $data['time_range']['end'] === false) {
                        return $this->message('参与考试时间范围选择错误', '', 'error');
                    }

                    if ($data['time_range']['start'] >= $data['time_range']['end']) {
                        return $this->message('参与考试时间范围选择错误。', '', 'error');
                    }
                }

                $data['time_range']['start'] = date('Y-m-d H:i:s', $data['time_range']['start']);
                $data['time_range']['end'] = date('Y-m-d H:i:s', $data['time_range']['end']);
            }

            if (isset($data['id']) && $data['id'] > 0) {
                $examination = ExaminationModel::where([
                    'id' => $data['id'],
                    'uniacid' => \YunShop::app()->uniacid,
                ])->first();
                if (!isset($examination->id)) {
                    return $this->message('考试ID未找到', '', 'error');
                }
            } else {
                $examination = new ExaminationModel;
                $examination->uniacid = \YunShop::app()->uniacid;
            }
            $examination->name = $data['name'];
            $examination->url = $data['url'];
            $examination->start = $data;
            $examination->end = $data;
            $examination->duration = $data;
            $examination->frequency = $data;
            $examination->interval = $data;
            $examination->is_question_score = $data;
            $examination->is_score = $data;
            $examination->is_question = $data;
            $examination->is_answer = $data;
            $examination->status = 1;
            $examination->paper_id = $data['paper_id'];
            $examination->save();
            if (!isset($examination->id) || $examination->id <= 0) {
                return $this->message('考试数据保存错误', '', 'error');
            }

            if (isset($data['id']) && $data['id'] > 0) {
                $content = ExaminationContentModel::where('examination_id', $data['id'])->first();
                if (!isset($content->id)) {
                    return $this->message('考试详情未找到', '', 'error');
                }
            } else {
                $content = new ExaminationContentModel;
                $content->examination_id = $examination->id;
            }
            if (isset($data['content'])) {
                $data['content'] = strip_tags($data['content']);
            } else {
                $data['content'] = '';
            }
            $content->content = $data['content'];
            $content->save();
            if (!isset($content->id) || $content->id <= 0) {
                return $this->message('考试详情保存错误', '', 'error');
            }

            return $this->message('保存成功', Url::absoluteWeb('plugin.examination.admin.examination.index'));
        }

        $id = (int) \YunShop::request()->id;
        if ($id > 0) {
            $infoRs = ExaminationModel::where([
                'id' => $id,
                'uniacid' => \YunShop::app()->uniacid,
            ])->with('content')->first();
            if (!isset($infoRs->id)) {
                return $this->message('考试数据错误，请联系开发或删除分类', '', 'error');
            }
        } else {
            $infoRs = null;
        }

        return view('Yunshop\Examination::admin.examination.edit', [
            'pluginName' => ExaminationService::get('name'),
            'info' => $infoRs,
            'paper' => $paperRs,
        ]);
    }
}
