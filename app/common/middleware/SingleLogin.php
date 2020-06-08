<?php
/**
 * Created by PhpStorm.
 * User: CGOD
 * Date: 2020/1/10
 * Time: 9:45
 */

namespace app\common\middleware;

use app\common\exceptions\ShopException;
use app\common\traits\JsonTrait;
use app\platform\modules\system\models\SystemSetting;
use Closure;
use app\common\services\Utils;

class SingleLogin
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
        if (config('app.framework') == 'platform') {
            $loginset = SystemSetting::settingLoad('loginset', 'system_loginset');
            if($loginset['single_login'] == 1)
            {
                $user = \Auth::guard('admin')->user();
                if(session('login_token') != $user->login_token)
                {
                    \Auth::guard('admin')->logout();
                    request()->session()->flush();
                    request()->session()->regenerate();

                    Utils::removeUniacid();

                    return $this->successJson('成功',[]);
                }
            }
        }
        return $next($request);
    }
}