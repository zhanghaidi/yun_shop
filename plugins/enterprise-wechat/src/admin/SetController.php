<?php

namespace Yunshop\EnterpriseWechat\admin;

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
        $set = Setting::get('plugin.enterprise-wechat');
        $requestModel = \YunShop::request()->setdata;

        if ($requestModel) {
            if (Setting::set('plugin.enterprise-wechat', $requestModel)) {
                return $this->message('设置成功', Url::absoluteWeb('plugin.enterprise-wechat.admin.set'));
            } else {
                $this->error('设置失败');
            }
        }

        return view('Yunshop\EnterpriseWechat::admin.set',
            [
                'setdata' => $set,
            ]
        )->render();
    }


}