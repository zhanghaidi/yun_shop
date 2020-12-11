<?php

namespace Yunshop\FaceAnalysis\api;


use app\common\components\ApiController;
use app\common\facades\Setting;
use app\common\models\Member;
use app\common\services\finance\PointService;
use app\frontend\modules\member\controllers\ServiceController;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Yunshop\FaceAnalysis\models\FaceAnalysisLogModel;
use Yunshop\FaceAnalysis\models\FaceBeautyRankingLikeLogModel;
use Yunshop\FaceAnalysis\models\FaceBeautyRankingModel;
use Yunshop\FaceAnalysis\services\AnalysisService;
use Yunshop\FaceAnalysis\services\FaceAnalysisService;
use Yunshop\FaceAnalysis\services\IntegralService;
use Yunshop\FaceAnalysis\services\RankingService;
use Yunshop\FaceAnalysis\Events\NewAnalysisSubmit;

class RankingController extends ApiController
{
    protected $publicAction = ['index', 'rule'];

    public function index()
    {
        $page = intval(\YunShop::request()->page);
        $pageSize = intval(\YunShop::request()->pageSize);
        if ($page <= 0) {
            $page = 1;
        }
        if ($pageSize <= 0 || $pageSize > 100) {
            $pageSize = 10;
        }


        $faceAnalysisService = new FaceAnalysisService();
        $label = Setting::get($faceAnalysisService->get('label') . '.ranking_status');
        if ($label <= 0) {
            $label = FaceBeautyRankingModel::getList()->select('id', 'label')
                ->orderBy('id', 'desc')->first();
            if (isset($label->label)) {
                $label = $label->lable;
            } else {
                $label = 1;
            }
        }

        $type = intval(\YunShop::request()->genre);
        $rankingRs = FaceBeautyRankingModel::getList()->select('id', 'member_id', 'beauty', 'like')
            ->where([
                'label' => $label,
                'status' => 1,
            ]);
        if ($type > 0) {
            $rankingRs = $rankingRs->where('type', $type);
        } else {
            $rankingRs = $rankingRs->groupBy('member_id');
        }
        $rankingRs = $rankingRs->orderBy('beauty', 'desc')
            ->orderBy('like', 'desc')
            ->orderBy('id', 'asc')
            ->limit($pageSize)
            ->offset(($page - 1) * $pageSize)->get()->toArray();

        $memberIds = array_column($rankingRs, 'member_id');
        if (isset($memberIds[0])) {
            $memberRs = Member::select('uid', 'nickname', 'avatar')->whereIn('uid', $memberIds)->get()->toArray();

            $memberId = \YunShop::app()->getMemberId();
            if ($memberId > 0) {
                $likeRs = FaceBeautyRankingLikeLogModel::select('id', 'object_member_id')
                    ->whereIn('object_member_id', $memberIds)->where([
                        'label' => $label,
                        'member_id' => $memberId
                    ])->get()->toArray();
            } else {
                $likeRs = [];
            }

            foreach ($rankingRs as $k1 => $v1) {
                $rankingRs[$k1]['nickname'] = '匿名';
                $rankingRs[$k1]['avatar'] = '';
                foreach ($memberRs as $v2) {
                    if ($v1['member_id'] != $v2['uid']) {
                        continue;
                    }
                    $rankingRs[$k1]['nickname'] = $v2['nickname'];
                    $rankingRs[$k1]['avatar'] = $v2['avatar'];
                    break;
                }

                $rankingRs[$k1]['is_like'] = 0;
                foreach ($likeRs as $v3) {
                    if ($v1['member_id'] != $v3['object_member_id']) {
                        continue;
                    }
                    $rankingRs[$k1]['is_like'] = 1;
                    break;
                }
            }
        }

        $baseRank = ($page - 1) * $pageSize + 1;
        foreach ($rankingRs as $k => $v) {
            $rankingRs[$k]['ranking'] = $baseRank + $k;
            unset($rankingRs[$k]['member_id']);
        }
        return $this->successJson('成功', $rankingRs);
    }

    public function like()
    {
        $id = intval(\YunShop::request()->id);
        if ($id <= 0) {
            return $this->errorJson('参数错误', $id);
        }

        $infoRs = FaceBeautyRankingModel::getList()->select('id', 'label', 'member_id')->where([
            'id' => $id,
            'status' => 1
        ])->first();
        if (!isset($infoRs->id)) {
            return $this->errorJson('参数错误 - 记录不存在', $id);
        }

        $memberId = \YunShop::app()->getMemberId();
        $likeRs = FaceBeautyRankingLikeLogModel::select('id')->where([
            'member_id' => $memberId,
            'object_member_id' => $infoRs->member_id,
            'label' => $infoRs->label,
        ])->first();
        if (isset($likeRs->id)) {
            return $this->errorJson('已点赞过，不能重复点赞', $id);
        }

        $lockCacheKey = 'FACEANALYSIS:RANKING:LIKE:' . $memberId . ':' . $id;
        $lockCacheRs = Redis::setnx($lockCacheKey, 1);
        if ($lockCacheRs != 1) {
            return $this->errorJson('操作太频繁了', $id);
        }
        Redis::expire($lockCacheKey, 5);

        DB::beginTransaction();
        try {
            $log = new FaceBeautyRankingLikeLogModel;
            $log->ranking_id = $id;
            $log->member_id = $memberId;
            $log->object_member_id = $infoRs->member_id;
            $log->label = $infoRs->label;
            $log->save();
            if (!isset($log->id) || $log->id <= 0) {
                throw new  Exception('点赞记录保存错误');
            }

            $listRs = FaceBeautyRankingModel::getList()->select('id', 'like')->where([
                'label' => $infoRs->label,
                'member_id' => $infoRs->member_id,
            ])->get()->toArray();
            $maxLike = array_column($listRs, 'like');
            $maxLike = max($maxLike);
            $maxLike += 1;
            foreach ($listRs as $v) {
                FaceBeautyRankingModel::where('id', $v['id'])->update([
                    'like' => $maxLike
                ]);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            return $this->errorJson('点赞失败', $e->getMessage());
        }

        return $this->successJson('点赞成功', $maxLike);
    }

    public function rule()
    {
        $faceAnalysisService = new FaceAnalysisService();
        $rule = Setting::get($faceAnalysisService->get('label') . '.rule');
        !isset($rule) && $rule = '';
        $rule = html_entity_decode($rule);
        return $this->successJson('成功', $rule);
    }
}
