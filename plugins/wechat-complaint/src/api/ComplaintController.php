<?php

namespace Yunshop\WechatComplaint\api;

use app\common\components\ApiController;
use Illuminate\Support\Facades\Redis;
use Yunshop\WechatComplaint\models\ComplaintItemModel;
use Yunshop\WechatComplaint\models\ComplaintLogModel;
use Yunshop\WechatComplaint\models\ComplaintProjectModel;

class ComplaintController extends ApiController
{
    public function options()
    {
        $memberId = (int) \YunShop::app()->getMemberId();
        if ($memberId <= 0) {
            return $this->errorJson('用户未授权登录', ['status' => 1]);
        }

        $id = (int) \YunShop::request()->id;
        if ($id <= 0) {
            return $this->errorJson('参数错误', ['status' => 1]);
        }

        $projectRs = ComplaintProjectModel::where([
            'id' => $id,
            'uniacid' => \YunShop::app()->uniacid,
        ])->first();
        if (!isset($projectRs->id)) {
            return $this->errorJson('投诉功能已失效', ['status' => 1]);
        }

        $listRs = (new ComplaintItemModel)->getOrderList(\YunShop::app()->uniacid);

        foreach ($listRs as &$v1) {
            unset($v1['uniacid'], $v1['type'], $v1['submit_mode'], $v1['order'], $v1['created_at'], $v1['updated_at'], $v1['deleted_at']);
            if (!isset($v1['children']) || !is_array($v1['children'])) {
                unset($v1['children']);
                continue;
            }

            foreach ($v1['children'] as &$v2) {
                unset($v2['uniacid'], $v2['type'], $v2['submit_mode'], $v2['order'], $v2['created_at'], $v2['updated_at'], $v2['deleted_at']);
                if (!isset($v2['children']) || !is_array($v2['children'])) {
                    unset($v2['children']);
                    continue;
                }

                foreach ($v2['children'] as &$v3) {
                    unset($v3['uniacid'], $v3['type'], $v3['submit_mode'], $v3['order'], $v3['created_at'], $v3['updated_at'], $v3['deleted_at']);
                    unset($v3['children']);
                }
                unset($v3);

                $v2['children'] = array_values($v2['children']);
            }
            unset($v2);
            $v1['children'] = array_values($v1['children']);
        }
        unset($v1);
        $listRs = array_values($listRs);
        return $this->successJson('成功', $listRs);
    }

    public function submit()
    {
        $memberId = (int) \YunShop::app()->getMemberId();
        if ($memberId <= 0) {
            return $this->errorJson('用户未授权登录', ['status' => 1]);
        }

        $id = (int) \YunShop::request()->id;
        if ($id <= 0) {
            return $this->errorJson('参数错误', ['status' => 1]);
        }
        $itemId = (int) \YunShop::request()->item_id;
        if ($itemId <= 0) {
            return $this->errorJson('参数错误.', ['status' => 1]);
        }

        // 延时互斥锁
        $lockKey = 'AJYWXCOMPLAINT:SUBMIT:' . $memberId . ':' . $id . ':' . date('ymdHi');
        $lockRs = $this->DelayMutex($lockKey);
        if ($lockRs === false) {
            return $this->errorJson('投诉太快了', ['status' => 2]);
        }

        $projectRs = ComplaintProjectModel::select('id')->where([
            'id' => $id,
            'uniacid' => \YunShop::app()->uniacid,
        ])->first();
        if (!isset($projectRs->id)) {
            return $this->errorJson('投诉网页未找到', ['status' => 1]);
        }

        $itemRs = ComplaintItemModel::select('id')->where([
            'id' => $itemId,
            'uniacid' => \YunShop::app()->uniacid,
        ])->first();
        if (!isset($itemRs->id)) {
            return $this->errorJson('投诉项未找到', ['status' => 1]);
        }

        $log = new ComplaintLogModel;
        $log->uniacid = \YunShop::app()->uniacid;
        $log->member_id = $memberId;
        $log->project_id = $id;
        $log->item_id = $itemId;
        $log->save();
        if (!isset($log->id) || $log->id <= 0) {
            return $this->errorJson('投诉失败', ['status' => 1]);
        }

        return $this->successJson('投诉已提交');
    }

    private function DelayMutex(string $cacheKey, int $maxDelay = 10)
    {
        if ($maxDelay <= 0) {
            $maxDelay = 10;
        }

        $isLock = false;
        for ($i == 0; $i < $maxDelay; $i++) {
            $cacheRs = Redis::setnx($cacheKey, 1);
            if ($cacheRs != 1) {
                sleep(1);
                continue;
            } else {
                Redis::expire($cacheKey, intval(ceil($maxDelay / 3)));
                $isLock = true;
                break;
            }
        }
        return $isLock;
    }
}
