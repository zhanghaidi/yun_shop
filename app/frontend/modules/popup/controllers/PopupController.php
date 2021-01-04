<?php

namespace app\frontend\modules\popup\controllers;

use app\common\components\ApiController;
use Yunshop\MinApp\Common\Services\PopupService;

class PopupController extends ApiController
{
    protected $publicAction = ['getPopup'];
    protected $ignoreAction = ['getPopup'];

    public function getPopup()
    {
        $popup = PopupService::getPopup();
        return $this->successJson('获取弹窗成功',$popup);
    }
}