<?php

namespace app\common\middleware;

use app\platform\modules\system\models\SystemSetting;
use app\common\traits\JsonTrait;
use Closure;

class CheckPasswordSafe
{
    use JsonTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = \Auth::guard('admin')->user();
        if($user->uid != 1)
        {
            $loginset = SystemSetting::settingLoad('loginset', 'system_loginset');
            if($loginset['password_change'] == 1){
                if(!$user->change_password_at)
                {
                    $user->change_password_at = time();
                    $user->save();
                    return $this->successJson('成功', ['pwd_remind' => 1,'msg'=>'首次登陆，建议您修改密码']);
                }
            }else{
                if(!$user->change_password_at)
                {
                    $user->change_password_at = time();
                    $user->save();
                }
            }
            if($user->change_password_at && $user->change_password_at+6912000 <= time() && $user->change_remind == 0 && $loginset['force_change_pwd'] == 1)
            {
                $user->change_remind = 1;
                $user->save();
                return $this->successJson('成功', ['pwd_remind' => 1,'msg'=>'您已长时间未修改密码，请您先修改密码，否则账号将在10天后冻结']);
            }
        }
        return $next($request);
    }
}
