<?php

namespace Yunshop\Examination\admin;

use app\common\components\BaseController;
use app\common\facades\Setting;
use app\common\helpers\Url;
use Yunshop\Examination\services\ExaminationService;

class ExaminationSetController extends BaseController
{
    public function index()
    {
        $faceAnalysis = new ExaminationService();
        $set = Setting::getByGroup($faceAnalysis->get('label'));

        $requestData = \YunShop::request()->setdata;
        if ($requestData) {
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
}
