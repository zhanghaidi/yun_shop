<?php

namespace app\backend\modules\siteSetting\controllers;

use app\common\components\BaseController;
use app\common\facades\SiteSetting;
use app\common\facades\Setting;
use app\common\helpers\Url;

class IndexController extends BaseController
{
    public function index()
    {
        $setting = SiteSetting::get('base');
    
        return view('siteSetting.index', [
            'setting' => json_encode($setting),
        ])->render();
    }

    //物理路径修改
    public function physicsPath()
    {
        $physics = \YunShop::request()->physics;

        if ($physics) {

            $old_url = addslashes($physics['old_url']);

            $setModel = \app\common\models\Setting::uniacid()->where("value","like","%{$old_url}%")->get();

            if (!empty($setModel)) {
                foreach ($setModel as $kk=>$vv) {
                    $set = Setting::get($vv['group'].'.'.$vv['key']);

                    if ($vv['type'] == 'string') {

                        $set = str_replace($physics['old_url'],$physics['new_url'],$set);

                        Setting::set($vv['group'].'.'.$vv['key'],$set);

                    } elseif ($vv['type'] == 'array') {

                        foreach ($set as $key=>$value) {
                            $set[$key] = str_replace($physics['old_url'],$physics['new_url'],$value);
                        }

                        Setting::set($vv['group'].'.'.$vv['key'],$set);
                    }
                }

                \Artisan::call('config:cache');
                \Cache::flush();

                return $this->message(' 物理路径更新成功', Url::absoluteWeb('siteSetting.index.physics-path'));

            } else {
                return $this->message(' 没有对应的数据', Url::absoluteWeb('siteSetting.index.physics-path'));
            }

        }

        return view('siteSetting.physics_path');
    }
}