<?php

namespace Yunshop\FaceAnalysis\admin;

use app\common\components\BaseController;
use app\common\helpers\Url;
use app\common\helpers\PaginationHelper;
use app\common\models\Member;
use Yunshop\FaceAnalysis\models\FaceAnalysisLogModel;
use Yunshop\FaceAnalysis\services\FaceAnalysisService;

class FaceAnalysisLogManageController extends BaseController
{
    private $pageSize = 10;

    public function index()
    {
        $faceAnalysis = new FaceAnalysisService();

        $searchData = \YunShop::request()->search;
        !isset($searchData['time_start']) && $searchData['time_start'] = date('Y-m-d H:i:s', strtotime('-1 month'));
        !isset($searchData['time_end']) && $searchData['time_end'] = date('Y-m-d H:i:s');

        $list = FaceAnalysisLogModel::getList()
            ->where('created_at', '>=', strtotime($searchData['time_start']))
            ->where('created_at', '<=', strtotime($searchData['time_end']));

        if (isset($searchData['gender']) && in_array($searchData['gender'], [1, 2])) {
            $list = $list->where('gender', $searchData['gender']);
        }
        if (isset($searchData['age_start']) && $searchData['age_start'] > 0) {
            $list = $list->where('age', '>=', $searchData['age_start']);
        }
        if (isset($searchData['age_end']) && $searchData['age_end'] > 0) {
            $list = $list->where('age', '<=', $searchData['age_end']);
        }
        if (isset($searchData['beauty_start']) && $searchData['beauty_start'] > 0) {
            $list = $list->where('beauty', '>=', $searchData['beauty_start']);
        }
        if (isset($searchData['beauty_end']) && $searchData['beauty_end'] > 0) {
            $list = $list->where('beauty', '<=', $searchData['beauty_end']);
        }
        if (isset($searchData['expression_start']) && $searchData['expression_start'] > 0) {
            $list = $list->where('expression', '>=', $searchData['expression_start']);
        }
        if (isset($searchData['expression_end']) && $searchData['expression_end'] > 0) {
            $list = $list->where('expression', '<=', $searchData['expression_end']);
        }
        if (isset($searchData['hat']) && in_array($searchData['hat'], [9, 1])) {
            if ($searchData['hat'] == 9) {
                $searchData['hat'] = '0';
            }
            $list = $list->where('hat', $searchData['hat']);
        }
        if (isset($searchData['glass']) && in_array($searchData['glass'], [9, 1])) {
            if ($searchData['glass'] == 9) {
                $searchData['glass'] = '0';
            }
            $list = $list->where('glass', $searchData['glass']);
        }
        if (isset($searchData['mask']) && in_array($searchData['mask'], [9, 1])) {
            if ($searchData['mask'] == 9) {
                $searchData['mask'] = '0';
            }
            $list = $list->where('mask', $searchData['mask']);
        }
        if (isset($searchData['hair_length']) && in_array($searchData['hair_length'], [9, 1, 2, 3, 4])) {
            if ($searchData['hair_length'] == 9) {
                $searchData['hair_length'] = '0';
            }
            $list = $list->where('hair_length', $searchData['hair_length']);
        }
        if (isset($searchData['hair_bang']) && in_array($searchData['hair_bang'], [9, 1])) {
            if ($searchData['hair_bang'] == 9) {
                $searchData['hair_bang'] = '0';
            }
            $list = $list->where('hair_bang', $searchData['hair_bang']);
        }
        if (isset($searchData['hair_color']) && in_array($searchData['hair_color'], [9, 1, 2, 3])) {
            if ($searchData['hair_color'] == 9) {
                $searchData['hair_color'] = '0';
            }
            $list = $list->where('hair_color', $searchData['hair_color']);
        }
        $list = $list->orderBy('id', 'desc')->paginate($this->pageSize)->toArray();

        $memberIds = array_column($list['data'], 'member_id');
        if (isset($memberIds[0])) {
            $memberRs = Member::select('uid', 'mobile', 'nickname')
                ->whereIn('uid', $memberIds)->get()->toArray();
            foreach ($list['data'] as $k1 => $v1) {
                foreach ($memberRs as $v2) {
                    if ($v1['member_id'] != $v2['uid']) {
                        continue;
                    }
                    $list['data'][$k1]['mobile'] = $v2['mobile'];
                    $list['data'][$k1]['nickname'] = $v2['nickname'];
                    break;
                }
            }
        }

        $pager = PaginationHelper::show($list['total'], $list['current_page'], $this->pageSize);

        return view('Yunshop\FaceAnalysis::admin.log', [
            'pluginName' => $faceAnalysis->get(),
            'search' => $searchData,
            'data' => $list['data'],
            'pager' => $pager,
        ]);
    }

    public function del()
    {
        $backurl = Url::absoluteWeb('plugin.face-analysis.admin.face-analysis-log-manage.index');
        $managemodel = FaceAnalysisLogModel::find(\YunShop::request()->id);
        if (!$managemodel) {
            return $this->message('此条数据已被删除', '', 'error');
        }

        if ($managemodel->delete()) {
            return $this->message('删除成功', $backurl);
        } else {
            return $this->message('删除失败', '');
        }
    }
}
