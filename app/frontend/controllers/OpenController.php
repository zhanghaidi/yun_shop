<?php

namespace app\frontend\controllers;

use app\common\components\BaseController;
use app\Jobs\SendTemplateMsgJob;
use Yunshop\Love\Modules\Goods\GoodsLoveRepository;

/**
 * 公共服务接口类
 * Class OpenController
 * @package app\frontend\controllers
 */
class OpenController extends BaseController
{
    /**
     * 发送模板消息
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendTemplateMsg()
    {
        $input = request()->all();
        return $this->successJson('ok', $input);
    }
}