<?php

namespace app\backend\modules\live\controllers;

use app\common\components\BaseController;
use app\common\services\tencentlive\LiveSetService;
use app\common\facades\Setting;
use app\common\helpers\Url;
use app\framework\Support\Facades\Log;

class LiveSetController extends BaseController
{

    /**
     * 查看云直播设置
     */
    public function see()
    {
        $live = LiveSetService::getSetting();
        $requestModel = \YunShop::request()->live;

        if ($requestModel) {
            array_walk($requestModel, function (&$item) {
                $item = trim($item);
            });
            if (Setting::set('shop.live', $requestModel)) {
                return $this->message('云直播设置成功', Url::absoluteWeb('live.live-set.see'));
            } else {
                $this->error('云直播设置失败');
            }
        }
        return view('live.set', [
            'live' => $live
        ])->render();
    }

}