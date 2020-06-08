<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2019/2/15
 * Time: 下午6:56
 */

namespace app\platform\controllers;


use app\common\services\Utils;
use app\frontend\modules\member\services\MemberService;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use app\platform\modules\user\models\AdminUser;
use app\platform\modules\system\models\SystemSetting;
use app\platform\modules\application\models\UniacidApp;
use app\platform\modules\application\models\AppUser;
use app\common\helpers\Url;
use app\common\helpers\Cache;

class LoginController extends BaseController
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/admin';
    protected $username;
    private $authRole = ['operator', 'clerk'];


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
   //     $this->middleware('guest:admin', ['except' => 'logout']);
    }

    /**
     * 自定义字段名
     * 可使用
     * @return array
     */
    public function atributeNames()
    {
        return [
            'username' => '用户名',
            'password' => '密码'
        ];
    }

    /**
     * 字段规则
     * @return array
     */
    public function rules()
    {
        return [
            'username' => 'required',
            'password' => 'required',
        ];
    }

    /**
     * 重写登录视图页面
     * @return [type]                   [description]
     */
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }
    /**
     * 自定义认证驱动
     * @return [type]                   [description]
     */
    protected function guard()
    {
        return auth()->guard('admin');
    }

    /**
     * 重写验证字段.
     *
     * @return string
     */
    public function username()
    {
        return 'username';
    }

    /**
     * Log the user out of the application.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        $this->guard('admin')->logout();
        request()->session()->flush();
        request()->session()->regenerate();

        Utils::removeUniacid();

        return $this->successJson('成功', []);
    }

    /**
     * 重写登录接口
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $request->offsetSet('password',base64_decode($request->password));
        try {
            $this->validate($this->rules(), $request, [], $this->atributeNames());
        } catch (\Exception $e) {
            return $this->errorJson($e->getMessage());
        }
        $loginset = SystemSetting::settingLoad('loginset', 'system_loginset');
        if($loginset['pic_verify'] == 1)
        {
            if (!$request->captcha || app('captcha')->platformCheck($request->captcha) == false)
            {
                return $this->errorJson('图形验证码错误');
            }
        }

        if($loginset['sms_verify'] == 1)
        {
            $user = AdminUser::where('username',$request->username)->with('hasOneProfile')->first();
            if(!$user || !\YunShop::request()->mobile || $user->hasOneProfile->mobile != \YunShop::request()->mobile)
            {
                return $this->errorJson('手机号填写错误');
            }

            //检查验证码是否正确
            if (!Cache::has(\YunShop::request()->mobile.'_code')) {
                return $this->errorJson('短信验证码已失效,请重新获取');
            }
            if (!\YunShop::request()->code || \YunShop::request()->code != Cache::get(\YunShop::request()->mobile.'_code')) {
                return $this->errorJson('短信验证码错误');
            }
        }

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        $limitLogin = $this->limiter()->tooManyAttempts(
            $this->throttleKey($request), $loginset->login_limit_num?:5, $loginset->login_limit_time?:30
        );
        if ($limitLogin) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request,$loginset->remember_pwd)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * 重写登录成功json返回
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();
        $loginToken = rand(1000, 9999).time();
        session(['login_token'=>$loginToken]);
        $this->clearLoginAttempts($request);

        $admin_user = AdminUser::where('uid', $this->guard()->user()->uid);
        $admin_user->update([
            'lastvisit' =>  time(),
            'lastip' => Utils::getClientIp(),
            'login_token' => $loginToken
        ]);

        $hasOneAppUser = $admin_user->first()->hasOneAppUser;
        if ($hasOneAppUser->role == 'clerk' || $hasOneAppUser->role == 'operator') {
            $sys_app = UniacidApp::getApplicationByid($hasOneAppUser->uniacid);
            if (!is_null($sys_app->deleted_at)) {
                return $this->successJson('平台已停用', ['status' => -5]);
            } elseif ($sys_app->validity_time !=0 && $sys_app->validity_time < mktime(0,0,0, date('m'), date('d'), date('Y'))) {
                return $this->successJson('平台已过期', ['status' => -5]);
            }
        }

        if ($this->guard()->user()->uid !== 1) {

            $user = $this->guard()->user();
            $loginset = SystemSetting::settingLoad('loginset', 'system_loginset');
            if($loginset['password_change'] == 1){
                if(!$user->change_password_at)
                {
                    $user->change_password_at = time();
                    $user->save();
                    return $this->successJson('成功', ['pwd_remind' => 1,'msg'=>'首次登陆，建议您修改密码']);
                }
            }
            if($user->change_password_at+6912000 <= time() && $user->change_remind == 0)
            {
                $user->change_remind = 1;
                $user->save();
                return $this->successJson('成功', ['pwd_remind' => 1,'msg'=>'您已长时间未修改密码，请您先修改密码，否则账号将在10天后冻结']);
            }

            $cfg = \config::get('app.global');
            $account = AppUser::getAccount($this->guard()->user()->uid);

            if (!is_null($account) && in_array($account->role, $this->authRole)) {
                $cfg['uniacid'] = $account->uniacid;
                Utils::addUniacid($account->uniacidb);

                \YunShop::app()->uniacid = $account->uniacid;
                \config::set('app.global', $cfg);

                return $this->successJson('成功', ['url' => Url::absoluteWeb('index.index', ['uniacid' => $account->uniacid])]);
            }
        }

        return $this->successJson('成功');
    }

    /**
     * 重写登录失败json返回
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendFailedLoginResponse(Request $request)
    {
        return $this->errorJson(Lang::get('auth.failed'), []);
    }

    /**
     * 重写登录失败次数限制
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendLockoutResponse(Request $request)
    {
        $seconds = $this->limiter()->availableIn(
            $this->throttleKey($request)
        );

        $message = Lang::get('auth.throttle', ['seconds' => $seconds]);

        return $this->errorJson($message);
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request,$rememberPwd = '')
    {
        if($rememberPwd == 1)
        {
            $remember = 1;
        }else{
            $remember = $request->remember;
        }
        return $this->guard()->attempt(
            $this->credentials($request),$remember
        );
    }

    public function site()
    {
        $default = [
            'name' => "芸众商城管理系统",
            'site_logo' => yz_tomedia("/static/images/site_logo.png"),
            'title_icon' => yz_tomedia("/static/images/title_icon.png"),
            'advertisement' => yz_tomedia("/static/images/advertisement.jpg"),
            'information' => '<p>&copy; 2019&nbsp;<a href="https://www.yunzmall.com/" target=\"_blank\" rel=\"noopener\">Yunzhong.</a>&nbsp;All Rights Reserved. 广州市芸众信息科技有限公司&nbsp;&nbsp;<a href="http://www.miit.gov.cn/" target="_blank\" rel="noopener\">&nbsp;粤ICP备17018310号-1</a>&nbsp;Powered by Yunzhong&nbsp;</p> <p><a href="https://bbs.yunzmall.com" target="_blank\" rel="noopener\">系统使用教程：bbs.yunzmall.com</a>&nbsp; &nbsp;&nbsp;<a href="https://bbs.yunzmall.com/plugin.php?id=it618_video:index" target="_blank\" rel="noopener\">视频教程</a></p>'
        ];

        $copyright = SystemSetting::settingLoad('copyright', 'system_copyright');

        $copyright['name'] = !isset($copyright['name']) ? $default['name'] : $copyright['name'];
        $copyright['site_logo'] = !isset($copyright['site_logo']) ? $default['site_logo'] : $copyright['site_logo'];
        $copyright['title_icon'] = !isset($copyright['title_icon']) ? $default['title_icon'] : $copyright['title_icon'];
        $copyright['advertisement'] = !isset($copyright['advertisement']) ? $default['advertisement'] : $copyright['advertisement'];
        $copyright['information'] = !isset($copyright['information']) ? $default['information'] : $copyright['information'];

        $loginset = SystemSetting::settingLoad('loginset', 'system_loginset');
        if($loginset['pic_verify'] == 1)
        {
            $copyright['captcha']['status'] = true;
            $captcha = app('captcha');
            $captcha_base64 = $captcha->create('default', true,true);
            $copyright['captcha']['img'] = $captcha_base64['img'];
        }else{
            $copyright['captcha']['status'] = false;
        }
        if($loginset['sms_verify'] == 1)
        {
            $copyright['sms']['status'] = true;
        }else{
            $copyright['sms']['status'] = false;
        }

        if ($copyright) {
            return $this->successJson('成功', $copyright);
        } else {
            return $this->errorJson('没有检测到数据', '');
        }
    }

    public function loginCode()
    {
        $mobile = request()->mobile;
        $state = \YunShop::request()->state ? : '86';
        if(!request()->username)
        {
            return $this->errorJson('请先完善账号信息');
        }
        $user = AdminUser::where('username',request()->username)->with('hasOneProfile')->first();
        if(!$user || !$mobile || $user->hasOneProfile->mobile != $mobile)
        {
            return $this->errorJson('手机号填写错误');
        }

        $code = rand(1000, 9999);

        Cache::put($mobile.'_code', $code, 60 * 10);

        return (new ResetpwdController())->sendSmsV2($mobile, $code, $state,'login','login');
    }

    public function refreshPic()
    {
        $captcha = app('captcha');
        $captcha_base64 = $captcha->create('default', true,true);
        return $this->successJson('成功', $captcha_base64['img']);
    }
}