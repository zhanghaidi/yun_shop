<?php

namespace Yunshop\FaceAnalysis\admin;

use app\common\components\BaseController;
use app\common\facades\Setting;
use app\common\helpers\Url;
use Yunshop\FaceAnalysis\models\FaceBeautyRankingModel;
use Yunshop\FaceAnalysis\services\SetService;
use Yunshop\FaceAnalysis\services\FaceAnalysisService;

class FaceAnalysisSetController extends BaseController
{
    public function index()
    {
        $faceAnalysis = new FaceAnalysisService();
        $set = Setting::getByGroup($faceAnalysis->get('label'));

        $requestData = \YunShop::request()->setdata;
        if ($requestData) {
            if (
                isset($set['ranking_status']) && $set['ranking_status'] == 0 &&
                isset($requestData['ranking_status']) && $requestData['ranking_status'] == 1
            ) {
                $label = FaceBeautyRankingModel::getList()->select('id', 'label')
                    ->orderBy('id', 'desc')->first();
                if (isset($label->id)) {
                    $requestData['ranking_status'] = $label->label + 1;
                }
            }

            if (
                isset($requestData['age_ranking']) && isset($requestData['age_ranking']['start']) &&
                isset($requestData['age_ranking']['start'][0])
            ) {
                foreach ($requestData['age_ranking']['start'] as $k => $v) {
                    if (!isset($requestData['age_ranking']['end'][$k])) {
                        return $this->message($this->error('年龄排行榜设置错误 - 开始、结束年龄都要填写'));
                    }
                    if ($v == '') {
                        $requestData['age_ranking']['start'][$k] = 0;
                        $v = 0;
                    }
                    if ($v < 0) {
                        return $this->message($this->error('年龄排行榜设置错误 - 开始年龄设置过小'));
                    }
                    if ($requestData['age_ranking']['end'][$k] == '') {
                        $requestData['age_ranking']['end'][$k] = 140;
                    }

                    if ($v >= $requestData['age_ranking']['end'][$k]) {
                        return $this->message($this->error('年龄排行榜设置错误 - 开始年龄必须小于结束年龄'));
                    }

                    if ($k == 0) {
                        continue;
                    }

                    if ($v <= $requestData['age_ranking']['end'][$k - 1]) {
                        return $this->message($this->error('年龄排行榜设置错误 - 设置的年龄有交集'));
                    }

                    if ($requestData['age_ranking']['end'][$k] > 140) {
                        return $this->message($this->error('年龄排行榜设置错误 - 结束年龄设置过大'));
                    }
                }
            }

            $result = SetService::storeSet($requestData);
            if ($result === true) {
                return $this->message('保存设置成功', Url::absoluteWeb('plugin.face-analysis.admin.face-analysis-set.index'));
            }
            return $this->error($result);
        }
        return view('Yunshop\FaceAnalysis::admin.set', [
            'set' => $set,
            'pluginName' => $faceAnalysis->get(),
        ]);
    }

    public function share()
    {
        $faceAnalysis = new FaceAnalysisService();
        $set = Setting::getByGroup($faceAnalysis->get('label'));

        $requestData = \YunShop::request()->setdata;
        if ($requestData) {
            $result = SetService::storeSet($requestData);
            if ($result === true) {
                return $this->message('保存设置成功', Url::absoluteWeb('plugin.face-analysis.admin.face-analysis-set.share'));
            }
            return $this->error($result);
        }
        return view('Yunshop\FaceAnalysis::admin.share', [
            'set' => $set,
            'pluginName' => $faceAnalysis->get(),
        ]);
    }

    public function rule()
    {
        $faceAnalysis = new FaceAnalysisService();
        $set = Setting::getByGroup($faceAnalysis->get('label'));

        $requestData = \YunShop::request()->setdata;
        if ($requestData) {
            $result = SetService::storeSet($requestData);
            if ($result === true) {
                return $this->message('保存设置成功', Url::absoluteWeb('plugin.face-analysis.admin.face-analysis-set.rule'));
            }
            return $this->error($result);
        }
        return view('Yunshop\FaceAnalysis::admin.rule', [
            'set' => $set,
            'pluginName' => $faceAnalysis->get(),
        ]);
    }
}
