<?php

namespace Yunshop\FaceAnalysis\services;

use app\common\facades\Setting;
use Yunshop\FaceAnalysis\models\FaceAnalysisLogModel;

class IntegralService
{
    /**
     * 获取对当前用户使用人脸分析的花费和赠送积分数值
     * @param $serviceId 服务ID
     * @param $userId 用户ID
     * @param $beauty 颜值
     * @param $isBefore 使用后/使用前
     * 
     * @notice 使用前，如果积分和颜值有关，则返回100(颜值最大值)
     *         使用后，给出确定的积分
     */
    public function getConsumeAndGain(int $serviceId, int $userId, int $beauty, bool $isBefore = true)
    {
        $label = (new FaceAnalysisService())->get('label');
        $settingRs = Setting::get($label);

        $return = [
            'consume' => 0,
            'gain' => 0
        ];

        if (isset($settingRs['consume_status']) && $settingRs['consume_status'] == 1) {
            $userUseNumber = FaceAnalysisLogModel::where([
                'member_id' => $userId,
                'uniacid' => $serviceId,
            ])->count();
            if ($isBefore === true) {
                $userUseNumber -= 1;
            }

            if (!isset($settingRs['consume_frequency'])) {
                $settingRs['consume_frequency'] = 0;
            }
            if ($userUseNumber <= $settingRs['consume_frequency']) {
                $return['consume'] = isset($settingRs['consume_number']) ? $settingRs['consume_number'] : 0;
            } else {
                if (isset($settingRs['consume_type']) && $settingRs['consume_type'] == 2) {
                    $return['consume'] = isset($settingRs['consume_surplus']) ? $settingRs['consume_surplus'] : 0;
                } else {
                    if ($isBefore === false) {
                        $return['consume'] = 100;
                    } else {
                        $return['consume'] = $beauty;
                    }
                }
            }
        }

        if (isset($settingRs['gain_status']) && $settingRs['gain_status'] == 1) {
            if (!isset($userUseNumber)) {
                $userUseNumber = FaceAnalysisLogModel::where([
                    'member_id' => $userId,
                    'uniacid' => $serviceId,
                ])->count();
                if ($isBefore === true) {
                    $userUseNumber -= 1;
                }
            }

            if (!isset($settingRs['gain_frequency'])) {
                $settingRs['gain_frequency'] = 0;
            }
            if ($userUseNumber <= $settingRs['gain_frequency']) {
                if (isset($settingRs['gain_type']) && $settingRs['gain_type'] == 2) {
                    $return['gain'] = isset($settingRs['gain_number']) ? $settingRs['gain_number'] : 0;
                } else {
                    if ($isBefore === false) {
                        $return['gain'] = 100;
                    } else {
                        $return['gain'] = $beauty;
                    }
                }
            } else {
                $return['gain'] = isset($settingRs['gain_surplus']) ? $settingRs['gain_surplus'] : 0;
            }
        }
        if ($return['consume'] < 0) {
            $return['consume'] = 0;
        }
        if ($return['gain'] < 0) {
            $return['gain'] = 0;
        }
        return $return;
    }
}
