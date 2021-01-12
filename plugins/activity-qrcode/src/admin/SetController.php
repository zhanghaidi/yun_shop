<?php

namespace Yunshop\ActivityQrcode\admin;

use app\common\components\BaseController;
use app\common\facades\Setting;
use app\common\helpers\Url;


class SetController extends BaseController
{

    public function index()
    {

        $setting = Setting::get('plugin.activity-qrcode');
        if (\Request::getMethod() == 'POST') {
            $data = \YunShop::request()->setting;
            if($data){
                if (\Setting::set('plugin.activity-qrcode', $data)) {
                    return $this->message('设置成功', Url::absoluteWeb('plugin.activity-qrcode.admin.set.index'));
                } else {
                    return $this->error('设置失败');
                }
            }
        }
        return view('Yunshop\ActivityQrcode::admin.set', [
            'setting' => $setting
        ])->render();
    }




}