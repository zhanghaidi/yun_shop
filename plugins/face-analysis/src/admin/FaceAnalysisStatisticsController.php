<?php

namespace Yunshop\FaceAnalysis\admin;

use app\common\components\BaseController;
use app\common\facades\Setting;
use Yunshop\FaceAnalysis\models\FaceAnalysisLogModel;
use Yunshop\FaceAnalysis\models\FaceBeautyRankingModel;
use Yunshop\FaceAnalysis\services\FaceAnalysisService;
use app\common\models\Member;

class FaceAnalysisStatisticsController extends BaseController
{
    public function province()
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


        $limit = \YunShop::request()->limit;
        if ($limit <= 0) {
            $limit = 100;
        }

        $memberIds = FaceAnalysisLogModel::getList()->select('id', 'member_id')
            ->where('label', $label)
            ->orderBy('id', 'desc')
            ->groupBy('member_id')
            ->limit($limit)->get()->toArray();
        $memberIds = array_column($memberIds, 'member_id');
        if (isset($memberIds[500])) {
            $memberIds = array_chunk($memberIds, 500);
        } elseif (isset($memberIds[0])) {
            $memberIds = [$memberIds];
        } else {
            $memberIds = [];
        }

        $provinceRs = array();
        $otherRs = array();
        foreach ($memberIds as $v) {
            if (!isset($v[0]) || !is_array($v)) {
                continue;
            }
            $tempMember = Member::select('uid', 'nationality', 'resideprovince')
                ->whereIn('uid', $v)->get()->toArray();
            foreach ($tempMember as $v1) {
                if ($v1['nationality'] != '中国') {
                    if (!isset($otherRs[$v1['nationality']])) {
                        $otherRs[$v1['nationality']] = 1;
                    } else {
                        $otherRs[$v1['nationality']] += 1;
                    }
                    continue;
                }

                if (!isset($provinceRs[$v1['resideprovince']])) {
                    $provinceRs[$v1['resideprovince']] = 1;
                } else {
                    $provinceRs[$v1['resideprovince']] += 1;
                }
            }
        }

        $provinceData = [];
        foreach ($provinceRs as $k => $v) {
            $provinceData[] = ['province' => $k, 'num' => $v];
        }
        $otherData = [];
        foreach ($otherRs as $k => $v) {
            $otherData[] = ['nation' => $k, 'num' => $v];
        }

        return view('Yunshop\FaceAnalysis::admin.province', [
            'pluginName' => $faceAnalysis->get(),
            'map' => json_encode($provinceData, 256),
            'data' => $provinceData,
            'other' => $otherData,
            'limit' => $limit
        ]);
    }
}
