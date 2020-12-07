<?php

namespace Yunshop\FaceAnalysis\services;

use app\common\facades\Setting;
use Yunshop\FaceAnalysis\models\FaceAnalysisLogModel;

class RankingService
{
    public function getJoinable(int $serviceId, int $userId)
    {
        $userRs = FaceAnalysisLogModel::select('id', 'gender', 'age')->where([
            'member_id' => $userId,
            'uniacid' => $serviceId,
        ])->orderBy('id', 'desc')->first();
        if (!isset($userRs->id)) {
            return [];
        }

        $return = [];
        $label = (new FaceAnalysisService())->get('label');
        $sexRs = Setting::get($label . '.sex_ranking');
        if ($sexRs != 0) {
            if ($userRs->gender == 1) {
                $return[] = 1;
            } elseif ($userRs->gender == 2) {
                $return[] = 2;
            }
        }

        $ageRs = Setting::get($label . '.age_ranking');
        if (isset($ageRs['start'][0]) && isset($ageRs['end'][0])) {
            foreach ($ageRs['start'] as $k => $v) {
                if (!isset($ageRs['end'][$k])) {
                    continue;
                }
                if ($ageRs['end'][$k] <= $v) {
                    continue;
                }

                if ($userRs->age < $v) {
                    continue;
                }
                if ($ageRs['end'][$k] < $userRs->age) {
                    continue;
                }

                if ($userRs->gender == 1) {
                    $return[] = $k * 2 + 10;
                } elseif ($userRs->gender == 2) {
                    if ($ageRs['sex'] == 1) {
                        $return[] = $k * 2 + 10 + 1;
                    } else {
                        $return[] = $k * 2 + 10;
                    }
                }

                $return[] = $k + 5;
            }
        }

        return $return;
    }

    public function getAllTypeAndName()
    {
        $return = [];
        $label = (new FaceAnalysisService())->get('label');
        $sexRs = Setting::get($label . '.sex_ranking');
        $ageRs = Setting::get($label . '.age_ranking');
        if ($sexRs != 0) {
            $return[] = ['type'=>1,'name'=>'女神排行榜'];
            $return[] = ['type'=>2,'name'=>'男神排行榜'];
        }

        if (!isset($ageRs['start'][0]) || !isset($ageRs['end'][0])) {
            return $return;
        }

        foreach ($ageRs['start'] as $k => $v) {
            if (!isset($ageRs['end'][$k])) {
                continue;
            }
            $type = $k * 2 + 10;
            if ($ageRs['sex'] == 1) {
                $return[] = ['type'=>$type,'name'=>$v . ' - ' . $ageRs['end'][$k] . '岁女神榜'];
                $return[] = ['type'=>$type+1,'name'=>$v . ' - ' . $ageRs['end'][$k] . '岁男神榜'];
            } else {
                $return[] = ['type'=>$type,'name'=>$v . ' - ' . $ageRs['end'][$k] . '岁榜'];
            }
        }
        return $return;
    }
}
