<?php

namespace Yunshop\FaceAnalysis\api;


use app\common\components\ApiController;
use app\common\facades\Setting;
use app\common\models\Member;
use app\common\services\finance\PointService;
use app\frontend\modules\member\controllers\ServiceController;
use Exception;
use Illuminate\Support\Facades\DB;
use Yunshop\FaceAnalysis\models\FaceAnalysisLogModel;
use Yunshop\FaceAnalysis\models\FaceBeautyRankingModel;
use Yunshop\FaceAnalysis\services\AnalysisService;
use Yunshop\FaceAnalysis\services\FaceAnalysisService;
use Yunshop\FaceAnalysis\services\IntegralService;
use Yunshop\FaceAnalysis\services\RankingService;
use Yunshop\FaceAnalysis\Events\NewAnalysisSubmit;

class AnalysisController extends ApiController
{
    public function submit()
    {
        $url = trim(\YunShop::request()->url);
        if ($url == '') {
            return $this->errorJson('请上传图片', $url);
        }
        $urlRs = parse_url($url);
        if (
            !isset($urlRs['scheme']) || !isset($urlRs['host']) ||
            !isset($urlRs['path']) || strpos($url, 'http') !== 0
        ) {
            return $this->errorJson('图片提交错误', $url);
        }

        $userRs = Member::select('uid', 'mobile', 'credit1')
            ->where('uid', \YunShop::app()->getMemberId())->first();
        if (!isset($userRs->uid)) {
            return $this->errorJson('用户数据获取错误', \YunShop::app()->getMemberId());
        }

        $integralService = new IntegralService;
        $costRs = $integralService->getConsumeAndGain(\YunShop::app()->uniacid, $userRs->uid);
        if ($userRs->credit1 < $costRs['consume']) {
            return $this->errorJson('您的健康金不足', $userRs->credit1);
        }

        $faceAnalysisService = new FaceAnalysisService();
        $label = Setting::get($faceAnalysisService->get('label') . '.ranking_status');

        $needPhone = Setting::get($faceAnalysisService->get('label') . '.need_phone');
        if ($needPhone == 1) {
            if (empty($userRs->mobile)) {
                return $this->errorJson('请先补充完善您的手机号码', $userRs->mobile);
            }
        }

        $frequencySet = Setting::get($faceAnalysisService->get('label') . '.frequency');
        if (
            isset($frequencySet['time']) && $frequencySet['time'] > 0 &&
            isset($frequencySet['number']) && $frequencySet['number'] > 0
        ) {
            $timeLimit = strtotime('-' . $frequencySet['time'] . ' minute');
            $numberLimit = FaceAnalysisLogModel::getList()
                ->where('member_id', $userRs->uid)
                ->where('created_at', '>=', $timeLimit)->count();
            if ($numberLimit >= $frequencySet['number']) {
                return $this->errorJson('检测次数过于频繁，请稍后再试', $numberLimit);
            }
        }

        $repeatRs = FaceAnalysisLogModel::getList()->select('id')->where([
            'member_id' => $userRs->uid,
            'url' => $url,
            'label' => $label,
        ])->first();
        if (isset($repeatRs->id)) {
            return $this->errorJson('换一张图片再测吧', $url);
        }

        $analysisService = new AnalysisService;
        $faceRs = $analysisService->detectFace($url);
        if (
            !isset($faceRs['code']) || $faceRs['code'] != 0 ||
            !isset($faceRs['data'])
        ) {
            return $this->errorJson(isset($faceRs['msg']) ? $faceRs['msg'] : '未知错误', '');
        }
        $faceRs = json_decode($faceRs['data'], true);
        if (!isset($faceRs['FaceInfos']) || !isset($faceRs['FaceInfos'][0])) {
            return $this->errorJson('人脸检测分析数据获取错误', $faceRs);
        }
        $faceRs = $faceRs['FaceInfos'][0];
        if (!isset($faceRs['X']) || !isset($faceRs['FaceAttributesInfo'])) {
            return $this->errorJson('人脸检测分析数据解析错误', $faceRs);
        }

        DB::beginTransaction();
        try {
            $log = new FaceAnalysisLogModel;
            $log->uniacid = \YunShop::app()->uniacid;
            $log->member_id = $userRs->uid;
            $log->url = $url;
            if ($faceRs['FaceAttributesInfo']['Gender'] < 50) {
                $log->gender = 1;
            } elseif ($faceRs['FaceAttributesInfo']['Gender'] > 50) {
                $log->gender = 2;
            } else {
                $log->gender = 0;
            }
            $log->age = $faceRs['FaceAttributesInfo']['Age'];
            $log->beauty = $faceRs['FaceAttributesInfo']['Beauty'];
            $log->expression = $faceRs['FaceAttributesInfo']['Expression'];
            $log->hat = $faceRs['FaceAttributesInfo']['Hat'] == true ? 1 : 0;
            $log->glass = $faceRs['FaceAttributesInfo']['Glass'] == true ? 1 : 0;
            $log->mask = $faceRs['FaceAttributesInfo']['Mask'] == true ? 1 : 0;
            $log->hair_length = $faceRs['FaceAttributesInfo']['Hair']['Length'];
            $log->hair_bang = $faceRs['FaceAttributesInfo']['Hair']['Bang'];
            $log->hair_color = $faceRs['FaceAttributesInfo']['Hair']['Color'];
            $log->attribute = json_encode($faceRs['FaceAttributesInfo']);
            $log->quality = json_encode($faceRs['FaceQualityInfo']);
            $log->cost = 0;
            $log->gain = 0;
            $log->label = $label;
            $log->save();
            if (!isset($log->id) || $log->id <= 0) {
                throw new Exception('检测记录保存错误');
            }

            $costRs = $integralService->getConsumeAndGain($log->uniacid, $log->member_id, $log->beauty, false);
            $log->cost = $costRs['consume'];
            $log->gain = $costRs['gain'];
            $log->save();

            if ($costRs['consume'] > 0) {
                $pointData = [
                    'point_income_type' => PointService::POINT_INCOME_LOSE,
                    'point_mode' => PointService::POINT_MODE_FACE_ANALYSIS_CONSUME,
                    'member_id' => $log->member_id,
                    'point' => $costRs['consume'],
                    'remark' => ''
                ];
                $point = new PointService($pointData);
                $pointRs = $point->changePoint();
                if (!isset($pointRs)) {
                    throw new Exception('扣减积分错误');
                }
            }
            if ($costRs['gain'] > 0) {
                $pointData = [
                    'point_income_type' => PointService::POINT_INCOME_GET,
                    'point_mode' => PointService::POINT_MODE_FACE_ANALYSIS_GAIN,
                    'member_id' => $log->member_id,
                    'point' => $costRs['gain'],
                    'remark' => ''
                ];
                $point = new PointService($pointData);
                $pointRs = $point->changePoint();
                if (!isset($pointRs)) {
                    throw new Exception('奖励积分错误');
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            return $this->errorJson($e->getMessage(), '');
        }

        event(new NewAnalysisSubmit($log->uniacid, $log->member_id, $log->label));

        $rankRs = (new RankingService())->getUserRanking($log->uniacid, $log->member_id, $log->label);

        return $this->successJson('ok', [
            'gender' => $log->gender,
            'age' => $log->age,
            'beauty' => $log->beauty,
            'rank' => $rankRs,
            'consume' => $log->cost,
            'gain' => $log->gain,
        ]);
    }
}
