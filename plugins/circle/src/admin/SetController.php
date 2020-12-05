<?php

namespace Yunshop\Circle\admin;

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
        $set = Setting::get('plugin.circle');
        $requestModel = \YunShop::request()->circle;
        if ($requestModel) {
            if (Setting::set('plugin.circle', $requestModel)) {
                return $this->message('设置成功', Url::absoluteWeb('plugin.circle.admin.set'));
            } else {
                $this->error('设置失败');
            }
        }


        return view('Yunshop\Circle::admin.set',
            [
                'set' => $set,
            ]
        )->render();
    }

}