<?php

namespace Yunshop\FaceAnalysis\services;

use app\common\facades\Setting;

class FaceAnalysisService
{
    public $pluginLabel = 'face_analysis';

    public $pluginName = '人脸检测与分析';

    public function get($key = null)
    {
        if ($key == 'label') {
            $value = $this->getPluginLabel();
        } else {
            $value = $this->getPluginName();
        }

        return $value;
    }

    public function getPluginLabel()
    {
        return $this->pluginLabel;
    }

    public function getPluginName()
    {
        return $this->pluginName;
    }
}
