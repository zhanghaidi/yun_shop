<?php

namespace Yunshop\FaceAnalysis\Listener;

use app\common\facades\Setting;
use Exception;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Yunshop\FaceAnalysis\Events\NewAnalysisSubmit;
use Yunshop\FaceAnalysis\models\FaceAnalysisLogModel;
use Yunshop\FaceAnalysis\models\FaceBeautyRankingModel;
use Yunshop\FaceAnalysis\services\AnalysisService;
use Yunshop\FaceAnalysis\services\FaceAnalysisService;
use Yunshop\FaceAnalysis\services\IntegralService;
use Yunshop\FaceAnalysis\services\RankingService;

// class AnalysisLogRanking implements ShouldQueue
class AnalysisLogRanking
{
    // use InteractsWithQueue;

    public function handle(NewAnalysisSubmit $event)
    {
        $serviceId = $event->serviceId;
        $memberId = $event->memberId;
        $label = $event->label;

        if ($label <= 0) {
            \Log::info('人脸检测与分析，检测数据入排行榜，目前排行榜未开启' . json_encode([
                'serviceId' => $serviceId,
                'memberId' => $memberId,
                'label' => $label,
            ]));
            return true;
        }

        $logRs = FaceAnalysisLogModel::select('id', 'gender', 'age', 'beauty')->where([
            'uniacid' => $serviceId,
            'member_id' => $memberId,
            'label' => $label,
        ])->orderBy('id', 'desc')->first();
        if (!isset($logRs->id)) {
            \Log::info('人脸检测与分析，检测数据入排行榜，参数错误' . json_encode([
                'serviceId' => $serviceId,
                'memberId' => $memberId,
                'label' => $label,
            ]));
            return false;
        }


        $joinedRs = FaceBeautyRankingModel::select('id', 'type', 'like', 'status')->where([
            'member_id' => $memberId,
            'label' => $label,
            'uniacid' => $serviceId
        ])->orderBy('id', 'desc')->get()->toArray();
        if (isset($joinedRs[0])) {
            $maxLike = array_column($joinedRs, 'like');
            $maxLike = max($maxLike);
        } else {
            $maxLike = 0;
        }

        $rankingRs = (new RankingService)->getJoinable($serviceId, $memberId, $label);
        $modifyIds = [];
        foreach ($rankingRs as $newType) {
            $tempId = 0;
            foreach ($joinedRs as $v1) {
                if (!isset($v1['id']) || !isset($v1['type'])) {
                    continue;
                }
                if ($v1['type'] != $newType) {
                    continue;
                }
                $modifyIds[] = $v1['id'];
                $tempId = $v1['id'];
                break;
            }

            if ($tempId == 0) {
                $rank = new FaceBeautyRankingModel;
                $rank->uniacid = $serviceId;
                $rank->label = $label;
                $rank->type = $newType;
                $rank->member_id = $memberId;
                $rank->status = 1;
            } else {
                $rank = FaceBeautyRankingModel::where('id', $tempId)->first();
            }
            $rank->gender = $logRs->gender;
            $rank->age = $logRs->age;
            $rank->beauty = $logRs->beauty;
            $rank->like = $maxLike;
            $rank->save();
            if (!isset($rank->id) || $rank->id <= 0) {
                \Log::info('人脸检测与分析，检测数据入排行榜，数据保存进排行榜表错误' . json_encode([
                    'beauty_ranking' => $rank,
                ]));
                throw new Exception('排行榜保存错误');
            }
        }

        foreach ($joinedRs as $v) {
            if (!isset($v['id'])) {
                continue;
            }
            if (in_array($v['id'], $modifyIds)) {
                continue;
            }

            FaceBeautyRankingModel::where('id', $v['id'])->delete();
        }

        return true;
    }
}
