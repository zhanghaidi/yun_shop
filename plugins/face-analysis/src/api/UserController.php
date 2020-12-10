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

class UserController extends ApiController
{
    public function info()
    {
        $memberId = \YunShop::app()->getMemberId();
        $logRs = FaceAnalysisLogModel::getList()
            ->select('id', 'uniacid', 'member_id', 'gender', 'age', 'beauty')
            ->where('member_id', $memberId)
            ->orderBy('id', 'desc')->first();

        if (isset($logRs->id)) {
            $costAndGain = (new IntegralService)->getConsumeAndGain(
                $logRs->uniacid,
                $logRs->member_id,
                $logRs->beauty
            );
        } else {
            $costAndGain = (new IntegralService)->getConsumeAndGain(
                \YunShop::app()->uniacid,
                $memberId
            );
        }

        return $this->successJson('成功', [
            'gender' => isset($logRs->id) ? $logRs->gender : 0,
            'age' => isset($logRs->id) ? $logRs->age : 0,
            'beauty' => isset($logRs->id) ? $logRs->beauty : 0,
            'consume' => $costAndGain['consume'],
            'gain' => $costAndGain['gain'],
        ]);
    }

    public function record()
    {
        $page = intval(\YunShop::request()->page);
        $pageSize = intval(\YunShop::request()->pageSize);
        if ($page <= 0) {
            $page = 1;
        }
        if ($pageSize <= 0 || $pageSize > 100) {
            $pageSize = 10;
        }

        $logRs = FaceAnalysisLogModel::getList()
            ->select('id', 'url', 'gender', 'age', 'beauty')
            ->where('member_id', \YunShop::app()->getMemberId())
            ->orderBy('id', 'desc')
            ->limit($pageSize)
            ->offset(($page - 1) * $pageSize)->get();
        $return = array();
        foreach ($logRs as $log) {
            $return[] = [
                'id' => $log->id,
                'url' => $log->url,
                'gender' => $log->gender,
                'age' => $log->age,
                'beauty' => $log->beauty,
            ];
        }
        return $this->successJson('成功', $return);
    }
}
