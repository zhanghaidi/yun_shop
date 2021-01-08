<?php

namespace Yunshop\EnterpriseWechat\admin;

use app\common\components\BaseController;
use app\common\facades\Setting;
use app\common\helpers\Url;


class SetController extends BaseController
{

    public function index()
    {
        $set = Setting::get('plugin.activity-qrcode');
        $requestModel = \YunShop::request()->setdata;

        if ($requestModel) {
            if (Setting::set('plugin.activity-qrcode', $requestModel)) {
                return $this->message('设置成功', Url::absoluteWeb('plugin.activity-qrcode.admin.set'));
            } else {
                $this->error('设置失败');
            }
        }

        return view('Yunshop\ActivityQrcode::admin.set',
            [
                'setdata' => $set,
            ]
        )->render();
    }


}