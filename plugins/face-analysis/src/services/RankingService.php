<?php

namespace Yunshop\FaceAnalysis\services;

use app\common\facades\Setting;
use Yunshop\FaceAnalysis\models\FaceAnalysisLogModel;
use Yunshop\FaceAnalysis\models\FaceBeautyRankingModel;

class RankingService
{
    public function getJoinable(int $serviceId, int $userId, int $label = 0)
    {
        $return = [];
        $serviceLabel = (new FaceAnalysisService())->get('label');
        if ($label <= 0) {
            $statusRs = Setting::get($serviceLabel . '.ranking_status');
        } else {
            $statusRs = $label;
        }
        if ($statusRs <= 0) {
            return $return;
        }

        $userRs = FaceAnalysisLogModel::select('id', 'gender', 'age')->where([
            'member_id' => $userId,
            'uniacid' => $serviceId,
            'label' => $statusRs,
        ])->orderBy('id', 'desc')->first();
        if (!isset($userRs->id)) {
            return [];
        }

        $sexRs = Setting::get($serviceLabel . '.sex_ranking');
        if ($sexRs != 0) {
            if ($userRs->gender == 1) {
                $return[] = 1;
            } elseif ($userRs->gender == 2) {
                $return[] = 2;
            }
        }

        $ageRs = Setting::get($serviceLabel . '.age_ranking');
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
            $return[] = ['type' => 1, 'name' => '女神排行榜'];
            $return[] = ['type' => 2, 'name' => '男神排行榜'];
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
                $return[] = ['type' => $type, 'name' => $v . ' - ' . $ageRs['end'][$k] . '岁女神榜'];
                $return[] = ['type' => $type + 1, 'name' => $v . ' - ' . $ageRs['end'][$k] . '岁男神榜'];
            } else {
                $return[] = ['type' => $type, 'name' => $v . ' - ' . $ageRs['end'][$k] . '岁榜'];
            }
        }
        return $return;
    }

    public function getUserRanking(int $serviceId, int $userId, int $label, int $beauty = 0)
    {
        $userRs = FaceBeautyRankingModel::select('id', 'type', 'beauty')
            ->where([
                'member_id' => $userId,
                'uniacid' => $serviceId,
                'label' => $label,
                'status' => 1,
            ])->get()->toArray();
        if (!isset($userRs[0])) {
            $rankingRs = $this->getJoinable($serviceId, $userId, $label);
            $logRs = FaceAnalysisLogModel::select('id',  'beauty')->where([
                'member_id' => $userId,
                'uniacid' => $serviceId,
                'label' => $label,
            ])->orderBy('id', 'desc')->first();
            if (isset($logRs->id)) {
                $userRs = [];
                foreach ($rankingRs as $v) {
                    $userRs[] = [
                        'type' => $v,
                        'beauty' => $logRs->beauty,
                    ];
                }
            }
        }
        $userRs[] = [
            'type' => 0,
            'beauty' => $userRs[0]['beauty'],
        ];

        // 如果 魅力 传值，以传值为排行依据
        if ($beauty > 0) {
            foreach ($userRs as $k => $v) {
                $userRs[$k]['beauty'] = $beauty;
            }
        }


        if (!isset($userRs[0])) {
            return [];
        }

        foreach ($userRs as $k => $v) {
            $totalRs = FaceBeautyRankingModel::where([
                'uniacid' => $serviceId,
                'label' => $label,
                'status' => 1,
            ]);
            if ($v['type'] == 0) {
                $totalRs = $totalRs->groupBy('member_id');
            } else {
                $totalRs = $totalRs->where('type', $v['type']);
            }
            $totalRs = $totalRs->count();

            $afterRs = FaceBeautyRankingModel::where([
                'uniacid' => $serviceId,
                'label' => $label,
                'status' => 1,
            ]);
            if ($v['type'] == 0) {
                $afterRs = $afterRs->groupBy('member_id');
            } else {
                $afterRs = $afterRs->where('type', $v['type']);
            }
            $afterRs = $afterRs->where('beauty', '<=', $v['beauty'])->count();

            $userRs[$k]['ranking'] = $totalRs - $afterRs;
            if ($userRs[$k]['ranking'] <= 0) {
                $userRs[$k]['ranking'] = 1;
            }

            $beautyEqualList = FaceBeautyRankingModel::select('id', 'member_id')->where([
                'uniacid' => $serviceId,
                'label' => $label,
                'status' => 1,
            ]);
            if ($v['type'] == 0) {
                $beautyEqualList = $beautyEqualList->groupBy('member_id');
            } else {
                $beautyEqualList = $beautyEqualList->where('type', $v['type']);
            }
            $beautyEqualList = $beautyEqualList->where('beauty', $v['beauty'])
                ->orderBy('like', 'desc')
                ->orderBy('id', 'desc')->get()->toArray();
            foreach ($beautyEqualList as $info) {
                if ($info['member_id'] != $userId) {
                    $userRs[$k]['ranking'] += 1;
                    continue;
                }

                break;
            }

            $totalRs -= 1;
            if ($totalRs <= 0 || $totalRs == $afterRs) {
                $userRs[$k]['percent'] = 99;
            } elseif ($afterRs == 0) {
                $userRs[$k]['percent'] = 10;
            } else {
                $userRs[$k]['percent'] = ceil($afterRs / $totalRs * 100);
            }
        }
        return $userRs;
    }
}
