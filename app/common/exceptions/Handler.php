<?php

namespace app\common\exceptions;

use app\common\components\ApiController;
use app\common\helpers\Client;
use app\common\helpers\Url;
use app\common\traits\JsonTrait;
use app\common\traits\MessageTrait;

use app\framework\Support\Facades\Log;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

use Ixudra\Curl\Facades\Curl;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class Handler extends ExceptionHandler
{
    use JsonTrait;
    use MessageTrait;
    private $isRendered;
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
        \EasyWeChat\Core\Exceptions\HttpException::class,
        \EasyWeChat\Core\Exceptions\InvalidArgumentException::class,
        NotFoundException::class,
        MemberNotLoginException::class,
        ShopException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     * @param Exception $exception
     * @throws Exception
     */
    public function report(Exception $exception)
    {
        if ($this->shouldntReport($exception)) {
            return;
        }
        try {
            // 记录错误日志
            if (!app()->runningInConsole()) {
                Log::error('http parameters', request()->input());
            }
            Log::error($exception);
            
            //生产环境发送错误报告
            if (app()->environment() == 'production') {
                Curl::to('https://dev9.yunzmall.com/api/error-log/upload')->withData([
                    'post_data[domain]' =>request()->getSchemeAndHttpHost(),
                    'post_data[title]' =>'',
                    'post_data[content]' =>
                        json_encode([
                            'file' => $exception->getFile(),
                            'line' => $exception->getLine(),
                            'message' => $exception->getMessage(),
                            'requestData' => request()->input()
                        ], true)
                ])->post();
            }
        } catch (Exception $ex) {
            dump($exception);
            dd($ex);
        }


        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception $exception
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Exception $exception)
    {

        if ($this->isRendered) {
            return;
        }
        $this->isRendered = true;

        if ($exception instanceof MemberErrorMsgException) {
            return $this->renderLoginErrorMsg($exception);
        }

        // 会员登录
        if ($exception instanceof MemberNotLoginException) {
            return $this->renderMemberNotLoginException($exception);
        }

        // 商城异常
        if ($exception instanceof ShopException) {
            return $this->renderShopException($exception);
        }
        // 404

        if ($exception instanceof NotFoundHttpException) {
            return $this->renderNotFoundException($exception);

        }

        //开发模式异常
        if (config('app.debug')) {
        //    return $this->renderExceptionWithWhoops($exception);
        }
        //api异常
        if (\YunShop::isApi()) {
            return $this->errorJson($exception->getMessage());
        }
        //默认异常
        if ($this->isHttpException($exception)) {
            return $this->renderHttpException($exception);
        }
        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Auth\AuthenticationException $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest('login');
    }

    protected function renderShopException(ShopException $exception)
    {
        if (request()->isFrontend() || request()->ajax()) {

            return $this->errorJson($exception->getMessage(), $exception->getData());
        }

        $redirect = $exception->redirect ?: '';
        return $this->message($exception->getMessage(), $redirect, 'error');
    }

    /**
     * Render an exception using Whoops.
     *
     * @param \Exception $e
     * @return \Illuminate\Http\Response
     */
    protected function renderExceptionWithWhoops(Exception $e)
    {
        $whoops = new \Whoops\Run;
        $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());
        if (method_exists($e, 'getStatusCode')) {
            return new \Illuminate\Http\Response(
                $whoops->handleException($e),
                $e->getStatusCode(),
                $e->getHeaders()
            );
        }
        return new \Illuminate\Http\Response(
            $whoops->handleException($e)
        );
    }

    protected function renderNotFoundException(NotFoundHttpException $exception)
    {
        if (\Yunshop::isPHPUnit()) {
            return $exception->getMessage();
        }

        if (\Yunshop::isApi() || request()->ajax()) {
            return $this->errorJson('不存在的接口');
        }
        $redirect = $exception->redirect ?: '';

        return $this->message('不存在的页面', $redirect, 'error');

    }

    protected function renderMemberNotLoginException(MemberNotLoginException $exception)
    {
        $data = $exception->getData();

        if (request()->isFrontend() || request()->ajax()) {
            $arr = [];
            $split_data = explode('&', $exception->getData());
            foreach ($split_data as $val) {
                $temp = explode('=', $val);

                $arr[$temp[0]] = $temp[1];
            }

            $type = $arr['type'];
            $i = $arr['i'];
            $mid = $arr['mid'];
            $scope = 'login';
            $extra = '';

            if (!is_null(\config('hflive'))) {
                $extra = ['hflive' => \config('hflive')];
            }

            if (Client::setWechatByMobileLogin($type)) {
                $type = 5;
            }

            if (Client::getType() == 8 && (app('plugins')->isEnabled('alipay-onekey-login'))) {
                $type = 8;
            }

            $queryString = ['type'=>$type,'i'=>$i, 'mid'=>$mid, 'scope' => $scope];

            $data = ['login_status' => 0, 'login_url' => Url::absoluteApi('member.login.index', $queryString), 'extra' => $extra];

            if (ApiController::MOBILE_TYPE == $type || ApiController::WEB_APP == $type || ApiController::NATIVE_APP == $type) {

                $data = ['login_status' => 1, 'login_url' => '', 'type' => $type, 'i' => \YunShop::app()->uniacid, 'mid' => $mid, 'scope' => $scope, 'extra' => $extra];
            }
        }

        return $this->errorJson($exception->getMessage(), $data);
    }

    protected function renderLoginErrorMsg(MemberErrorMsgException $exception)
    {
        $img = resource_get('static/warning.png');
        echo <<<EOT
          <div style="width:100%; ">
          <div style="margin-top: 300px;">&nbsp;</div>
          <div style="margin: auto; text-align: center; width:60%;"><img src="{$img}"> </div>
          <div style="padding-top: 20px;margin: auto; text-align: center; width:60%; font-size:40px; height: 40px; line-height: 40px;">{$exception->getMessage()}</div>
          </div>
EOT;
;

        exit;
    }
}
