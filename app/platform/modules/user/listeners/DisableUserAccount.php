<?php
/**
 * Created by PhpStorm.
 * User: CGOD
 * Date: 2020/1/3
 * Time: 11:29
 */

namespace app\platform\modules\user\listeners;

use app\platform\modules\user\models\AdminUser;
use Illuminate\Foundation\Bus\DispatchesJobs;
use app\platform\modules\system\models\SystemSetting;

class DisableUserAccount
{
    use DispatchesJobs;

    public function handle()
    {
        $loginset = \app\platform\modules\system\models\SystemSetting::settingLoad('loginset', 'system_loginset');
        if($loginset['force_change_pwd'] == 1)
        {
            AdminUser::where('uid','!=',1)->where('change_password_at','!=',null)->where('change_password_at','<=',time()-7776000)->update(['status' => 3]);
        }
        return;
    }

    public function subscribe()
    {
        \Event::listen('cron.collectJobs', function () {
            \Cron::add('Disable-User', '0 * * * *', function() {
                $this->handle();
                return;
            });
        });
    }
}