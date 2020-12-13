<?php

namespace Yunshop\FaceAnalysis\admin;

use app\common\components\BaseController;
use app\common\facades\Setting;
use app\common\helpers\Url;
use app\common\helpers\PaginationHelper;
use Yunshop\FaceAnalysis\models\FaceAnalysisLogModel;
use Yunshop\FaceAnalysis\models\FaceBeautyRankingModel;
use Yunshop\FaceAnalysis\services\FaceAnalysisService;
use Yunshop\FaceAnalysis\services\RankingService;
use app\common\models\Member;

class FaceBeautyRankingController extends BaseController
{
    private $pageSize = 10;

    public function index()
    {
        $faceAnalysis = new FaceAnalysisService();

        $label = Setting::get($faceAnalysis->get('label') . '.ranking_status');
        if ($label <= 0) {
            $label = FaceBeautyRankingModel::getList()->select('id', 'label')
                ->orderBy('id', 'desc')->first();
            if (isset($label->label)) {
                $label = $label->lable;
            } else {
                $label = 1;
            }
        }

        $rankingRs = (new RankingService)->getAllTypeAndName();
        $typeRs = array_column($rankingRs, 'type');
        $typeRs = array_merge([0], $typeRs);
        if (!isset($typeRs[0])) {
            return $this->message($this->error('没有开启中的排行榜榜单，请设置后再次查看'));
        }

        $searchData = \YunShop::request()->search;
        !isset($searchData['type']) && $searchData['type'] = $typeRs[0];
        if (!in_array($searchData['type'], $typeRs)) {
            $searchData['type'] = $typeRs[0];
        }

        $typeNumRs = FaceBeautyRankingModel::getList()->selectRaw('type, count(1) as countNum')
            ->where('label', $label)
            ->groupBy('type')->get();
        foreach ($rankingRs as $k1 => $v1) {
            $rankingRs[$k1]['count'] = 0;
            foreach ($typeNumRs as $v2) {
                if ($v1['type'] != $v2->type) {
                    continue;
                }
                $rankingRs[$k1]['count'] = $v2->countNum;
                break;
            }
        }

        if ($searchData['type'] == 0) {
            $list = FaceBeautyRankingModel::getList()->where('label', $label)
                ->groupBy('member_id')
                ->orderBy('beauty', 'desc')
                ->orderBy('like', 'desc')
                ->orderBy('id', 'desc')->paginate($this->pageSize)->toArray();
        } else {
            $list = FaceBeautyRankingModel::getList()->where([
                'label' => $label,
                'type' => $searchData['type'],
            ])->orderBy('beauty', 'desc')
                ->orderBy('like', 'desc')
                ->orderBy('id', 'desc')->paginate($this->pageSize)->toArray();
        }


        $memberIds = array_column($list['data'], 'member_id');
        if (isset($memberIds[0])) {
            $memberRs = Member::select('uid', 'mobile', 'nickname')
                ->whereIn('uid', $memberIds)->get()->toArray();

            $logIds = FaceAnalysisLogModel::getList()->selectRaw('max(id) as id')
                ->whereIn('member_id', $memberIds)
                ->where('label', $label)
                ->groupBy('member_id')->get()->toArray();
            $logIds = array_column($logIds, 'id');
            if (isset($logIds[0])) {
                $logRs = FaceAnalysisLogModel::getList()
                    ->select('id', 'member_id', 'url', 'created_at')
                    ->whereIn('id', $logIds)->get();
            }


            foreach ($list['data'] as $k1 => $v1) {
                foreach ($memberRs as $v2) {
                    if ($v1['member_id'] != $v2['uid']) {
                        continue;
                    }
                    $list['data'][$k1]['mobile'] = $v2['mobile'];
                    $list['data'][$k1]['nickname'] = $v2['nickname'];
                    break;
                }

                if (!isset($logRs)) {
                    continue;
                }

                foreach ($logRs as $v3) {
                    if ($v1['member_id'] != $v3->member_id) {
                        continue;
                    }
                    $list['data'][$k1]['url'] = $v3->url;
                    $list['data'][$k1]['created_at'] = $v3->created_at;
                    break;
                }

                if ($v1['status'] != 1) {
                    continue;
                }
                $list['data'][$k1]['ranking'] = ($list['current_page'] - 1) * $this->pageSize + $k1 + 1;
            }
        }

        $pager = PaginationHelper::show($list['total'], $list['current_page'], $this->pageSize);

        return view('Yunshop\FaceAnalysis::admin.ranking', [
            'pluginName' => $faceAnalysis->get(),
            'rank' => $rankingRs,
            'search' => $searchData,
            'data' => $list['data'],
            'pager' => $pager,
        ]);
    }

    public function status()
    {
        $id = \YunShop::request()->id;
        if ($id <= 0) {
            $this->errorJson();
        }

        $rankingRs = FaceBeautyRankingModel::select('id', 'status')->where('id', $id)->first();
        if (!isset($rankingRs->id)) {
            $this->errorJson();
        }
        $updateData = ['status' => 1];
        if ($rankingRs->status == 1) {
            $updateData = ['status' => 2];
        }
        FaceBeautyRankingModel::where('id', $id)->update($updateData);

        return $this->successJson('succ', ['switch' => $updateData['status'] == 2 ? 'show' : 'hide']);
    }
}
