<?php

namespace app\backend\modules\live\controllers;

use app\common\components\BaseController;
use app\common\services\tencentlive\LiveSetService;
use app\common\facades\Setting;
use app\common\helpers\Url;
use app\framework\Support\Facades\Log;
use app\common\models\notice\MessageTemp;
use app\common\models\notice\MinAppTemplateMessage;

class LiveSetController extends BaseController
{

    /**
     * 查看云直播设置
     */
    public function see()
    {
        $live = LiveSetService::getSetting();
        $im = LiveSetService::getIMSetting();
        $live_req = \YunShop::request()->live;
        $im_req = \YunShop::request()->im;

        if ($live_req && $im_req) {
            array_walk($live_req, function (&$item) {
                $item = trim($item);
            });
            array_walk($im_req, function (&$item) {
                $item = trim($item);
            });
            if (Setting::set('shop.live', $live_req) && Setting::set('shop.im', $im_req)) {
                return $this->message('云直播设置成功', Url::absoluteWeb('live.live-set.see'));
            } else {
                $this->error('云直播设置失败');
            }
        }

        $wechatTemplate = MessageTemp::select('id', 'title')
            ->where('uniacid', \YunShop::app()->uniacid)
            ->get()->toArray();

        $minAppTemplate = MinAppTemplateMessage::select('id', 'title')
            ->where('uniacid', \YunShop::app()->uniacid)
            ->get()->toArray();
        return view('live.set', [
            'live' => $live,
            'im' => $im,
            'wechat' => $wechatTemplate,
            'minapp' => $minAppTemplate,
        ])->render();
    }

}