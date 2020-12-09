<?php

/**
 * 腾讯云 数据万象CI 部分公用方法
 */

namespace Yunshop\FaceAnalysis\services;

use app\common\traits\ValidatorTrait;
use app\common\facades\Setting;

class TencentCIService
{
    public static function safeBase64(string $content)
    {
        $content = base64_encode($content);
        $content = str_replace('+', '-', $content);
        $content = str_replace('/', '_', $content);
        return $content;
    }
}
