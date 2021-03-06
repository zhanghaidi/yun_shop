<?php

namespace Yunshop\XiaoeClock\admin;

use app\common\components\BaseController;
use app\common\facades\Setting;
use app\common\helpers\Url;

/**
* 
*/
class SetController extends BaseController
{

    public function index()
    {
        $set = Setting::get('plugin.xiaoe-clock');

        $requestModel = \YunShop::request()->clock;
        if ($requestModel) {
            if (Setting::set('plugin.xiaoe-clock', $requestModel)) {
                return $this->message('设置成功', Url::absoluteWeb('plugin.xiaoe-clock.admin.set'));
            } else {
                $this->error('设置失败');
            }
        }
        return view('Yunshop\XiaoeClock::admin.set',
            [
                'set' => $set,
            ]
        )->render();
    }

}