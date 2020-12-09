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
use Yunshop\FaceAnalysis\services\TencentCIService;
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

    public function share()
    {
        $id = intval(\YunShop::request()->id);
        if ($id < 0) {
            $id = 0;
        }

        $memberId = \YunShop::app()->getMemberId();
        if ($memberId <= 0) {
            return $this->errorJson('登录信息获取错误', $memberId);
        }

        $logRs = FaceAnalysisLogModel::getList()
            ->select('id', 'uniacid', 'gender', 'age', 'beauty', 'label')
            ->where('member_id', $memberId);
        if ($id > 0) {
            $logRs = $logRs->where('id', $id);
        } else {
            $logRs = $logRs->orderBy('id', 'desc');
        }
        $logRs = $logRs->first();
        if (!isset($logRs->id)) {
            return $this->errorJson('检测记录错误错误');
        }

        $memberRs = DB::table('diagnostic_service_user')->select('id', 'nickname', 'avatar')
            ->where('ajy_uid', $memberId)->first();

        $rankPercent = (new RankingService())->getUserRanking($logRs->uniacid, $memberId, $logRs->label, $logRs->beauty);
        if (!isset($rankPercent[0])) {
            $rankPercent = 99;
        } else {
            $rankPercent = array_column($rankPercent, 'percent');
            $rankPercent = max($rankPercent);
        }

        // TODO 底图
        $url = 'https://dev-1300631469.cos.ap-beijing.myqcloud.com/images/45/2020/12/U9y7Z9c2LaUDL2988LEJ81d9je9S7s.png?';
        $url = 'https://dev-1300631469.cos.ap-beijing.myqcloud.com/images/45/2020/12/u7bchz3BbNdmMc6BcK4M6Qz6BB67Ka.png?';
        $url = 'https://dev-1300631469.cos.ap-beijing.myqcloud.com/images/45/2020/12/it9Yu997MbYth0td0u343b4d9T4B7p.png?';

        // 超越
        $url .= 'watermark/2/text/';
        $url .= TencentCIService::safeBase64($rankPercent . '%');
        $url .= '/font/' . TencentCIService::safeBase64('simhei黑体.ttf');
        $url .= '/fontsize/45';
        $url .= '/fill/' . TencentCIService::safeBase64('#FF622D');
        $url .= '/dissolve/100/gravity/northwest';
        if ($rankPercent < 10) {
            $url .= '/dx/135/dy/231';
        } elseif ($rankPercent < 100) {
            $url .= '/dx/125/dy/231';
        } else {
            $url .= '/dx/115/dy/231';
        }
        // var_dump($url);

        // 头像
        if (isset($memberRs['avatar']) && !empty($memberRs['avatar'])) {
            $avatar = yz_tomedia($memberRs['avatar']);
            $avatar = str_replace('https://', 'http://', $avatar);
            $avatar .= '?imageMogr2/thumbnail/105x105!';
            $avatar .= '|imageMogr2/rradius/52';
            // var_dump($avatar);

            $url .= '|watermark/1/image/';
            $url .= TencentCIService::safeBase64($avatar);
            $url .= '/gravity/northwest';
            $url .= '/dx/109/dy/353';
        }

        // 昵称
        if (isset($memberRs['nickname']) && !empty($memberRs['nickname'])) {
            // $memberRs['nickname'] = 'NO.9725【灸师助理】';
            // $memberRs['nickname'] = 'H 、y s(屏幕灯光音响设备租赁)';
            // $memberRs['nickname'] = '人生如賭局就怕你戒了賭';
            if (mb_strlen($memberRs['nickname']) > 4) {
                $memberRs['nickname'] = mb_substr($memberRs['nickname'], 0, 2) . '***' . mb_substr($memberRs['nickname'], mb_strlen($memberRs['nickname']) - 2);
            }
            $url .= '|watermark/2/text/';
            $url .= TencentCIService::safeBase64($memberRs['nickname']);
            $url .= '/font/' . TencentCIService::safeBase64('simhei黑体.ttf');
            $url .= '/fontsize/33';
            $url .= '/dissolve/100/gravity/northwest';
            if (mb_strlen($memberRs['nickname']) < 2) {
                $url .= '/dx/130/dy/475';
            } elseif (mb_strlen($memberRs['nickname']) < 4) {
                $url .= '/dx/120/dy/475';
            } else {
                if (strlen($memberRs['nickname']) >= 12) {
                    $url .= '/dx/75/dy/475';
                } else {
                    $url .= '/dx/95/dy/475';
                }
            }
        }

        // 检测信息 - 性别、年龄
        $sexAndAgeTxt = '性别：';
        if ($logRs->gender == 2) {
            $sexAndAgeTxt .= '男 ';
        } else {
            $sexAndAgeTxt .= '女 ';
        }
        $sexAndAgeTxt .= '年龄：' . $logRs->age . '岁';
        $url .= '|watermark/2/text/';
        $url .= TencentCIService::safeBase64($sexAndAgeTxt);
        $url .= '/font/' . TencentCIService::safeBase64('simhei黑体.ttf');
        $url .= '/fontsize/35';
        $url .= '/dissolve/100/gravity/northwest';
        $url .= '/dx/300/dy/377';

        // 检测信息 - 魅力
        $beautyTxt = '魅力值：' . $logRs->beauty . '分';
        $url .= '|watermark/2/text/';
        $url .= TencentCIService::safeBase64($beautyTxt);
        $url .= '/font/' . TencentCIService::safeBase64('simhei黑体.ttf');
        $url .= '/fontsize/35';
        $url .= '/dissolve/100/gravity/northwest';
        $url .= '/dx/300/dy/437';

        // 长度条 - 底
        $bg1Url = 'http://dev-1300631469.cos.ap-beijing.myqcloud.com/images/45/2020/12/jX9G5y0V4YXi1ysUgfX8gY4gP6YDdB.png';
        $bg1Url = str_replace('https://', 'http://', $bg1Url);
        $bg1Url .= '?imageMogr2/thumbnail/368x12!';
        $bg1Url .= '|imageMogr2/rradius/3';
        $url .= '|watermark/1/image/';
        $url .= TencentCIService::safeBase64($bg1Url);
        $url .= '/gravity/northwest/dx/300/dy/488';

        $bg2Url = 'http://dev-1300631469.cos.ap-beijing.myqcloud.com/images/45/2020/12/pa2JD0EMd0eKdaAmNty30m2p3ceJ11.png';
        $bg2Url = str_replace('https://', 'http://', $bg2Url);
        $bg2Url .= '?imageMogr2/thumbnail/';
        $bg2Url .= ceil($logRs->beauty / 100 * 368);
        $bg2Url .= 'x12!';
        $bg2Url .= '|imageMogr2/rradius/3';
        $url .= '|watermark/1/image/';
        $url .= TencentCIService::safeBase64($bg2Url);
        $url .= '/gravity/northwest/dx/300/dy/488';


        echo $url;
    }
}
