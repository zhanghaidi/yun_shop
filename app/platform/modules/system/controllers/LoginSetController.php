<?php
/**
 * Created by PhpStorm.
 * User: CGOD
 * Date: 2020/1/7
 * Time: 9:30
 */

namespace app\platform\modules\system\controllers;
use app\platform\controllers\BaseController;
use app\platform\modules\system\models\SystemSetting;
use app\platform\modules\user\models\AdminUser;

class LoginSetController extends BaseController
{
    public function index()
    {
        $set_data = request()->setdata;
        $loginset = SystemSetting::settingLoad('loginset', 'system_loginset');
        if ($set_data) {
            $pwd_mark = false;//为了刷新修改密码时间
            $loginset = unserialize(app('SystemSetting')->get('loginset')['value']);//拒绝缓存
            if($loginset['force_change_pwd'] != 1)
            {
                $pwd_mark = true;
            }
            $site = SystemSetting::settingSave($set_data, 'loginset', 'system_loginset');
            if ($site) {
                if($set_data['force_change_pwd'] == 1  && $pwd_mark)
                {
                    AdminUser::where('uid','!=',1)->where('deleted_at',null)->update(['change_password_at' => time()]);
                }
                return $this->successJson('成功', '');
            } else {
                return $this->errorJson('失败', '');
            }
        }

        if ($loginset) {
            return $this->successJson('成功', $loginset);
        } else {
            return $this->errorJson('没有检测到数据', '');
        }
    }
}