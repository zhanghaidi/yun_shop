<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/28
 * Time: 上午10:49
 */

namespace app\common\components;

use app\common\exceptions\ShopException;
use app\common\exceptions\UniAccountNotFoundException;
use app\common\helpers\Client;
use app\common\helpers\Url;
use app\common\middleware\BasicInformation;
use app\common\models\Member;
use app\common\models\UniAccount;
use app\common\modules\shop\models\Shop;
use app\frontend\modules\member\services\factory\MemberFactory;
use app\common\services\Session;
use Illuminate\Support\Facades\DB;

class ApiController extends BaseController
{
    const MOBILE_TYPE = 5;
    const WEB_APP = 7;
    const NATIVE_APP = 9;
    const LOGIN_APP_ANCHOR = 14;

    protected $publicController = [];
    protected $publicAction = [];
    protected $ignoreAction = [];

    public $jump = false;

    /**
     * @throws ShopException
     * @throws UniAccountNotFoundException
     */
    public function preAction()
    {
        parent::preAction();
        if (!UniAccount::checkIsExistsAccount(\YunShop::app()->uniacid)) {
            throw new UniAccountNotFoundException('无此公众号', ['login_status' => -2]);
        }

        $relaton_set = Shop::current()->memberRelation;

        $mid = Member::getMid();
        $mark = \YunShop::request()->mark;
        $mark_id = \YunShop::request()->mark_id;
        $type = \YunShop::request()->type;
        $openid_token = \YunShop::request()->openid_token;
        $ju_sign = \YunShop::request()->ju_sign;

        if (Client::setWechatByMobileLogin(\YunShop::request()->type)) {
            $type = 5;
        }

        if (self::is_alipay()) {
            $type = 8;
        }

        if ($type == 66 && $ju_sign == '6040bea') {

        } else {
            //fixbyzhd-zhd-20201125 暂时注释陆洋增加session，因为全局member_id会在下面生成
            /*$member_id = Session::get('member_id');
            if ($type == 2) {
                if (!$member_id && !empty($openid_token)) {
                    $member=Db::table('diagnostic_service_user')->where('openid_token', $openid_token)->orWhere('shop_openid_token', $openid_token)->first();
                    if(!empty($member)){
                        session::set('member_id', $member['ajy_uid']);
                    }

                }
            }*/
            $member = MemberFactory::create($type);

            if (is_null($member)) {
                throw new ShopException('应用登录授权失败', ['login_status' => -4]);
            }

            if (!$member->checkLogged()) {
                if (($relaton_set->status == 1 && !in_array($this->action, $this->ignoreAction))
                    || ($relaton_set->status == 0 && !in_array($this->action, $this->publicAction))
                ) {
                    $this->jumpUrl($type, $mid);
                }
            } else {
                if (\app\frontend\models\Member::current()->yzMember->is_black) {
                    throw new ShopException('黑名单用户，请联系管理员', ['login_status' => -1]);
                }

                //发展下线
                Member::chkAgent(\YunShop::app()->getMemberId(), $mid, $mark, $mark_id);
            }
        }

    }

    public static function is_alipay()
    {
        if (!empty($_SERVER['HTTP_USER_AGENT']) && strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'alipay') !== false && (app('plugins')->isEnabled('alipay-onekey-login'))) {
            return true;
        }
        return false;
    }

    /**
     * @param $type
     * @param $mid
     * @throws ShopException
     */
    protected function jumpUrl($type, $mid)
    {
        if (empty($type) || $type == 'undefined') {
            $type = Client::getType();
        }

        $scope = \YunShop::request()->scope ?: '';
        $route = \YunShop::request()->route;
        $extra = '';

        $queryString = ['type' => $type, 'i' => \YunShop::app()->uniacid, 'mid' => $mid, 'scope' => $scope];

        if (!is_null(\config('hflive'))) {
            $extra = ['hflive' => \config('hflive')];
        }

        if ($type == 11 || $type == 12) {
            return $this->errorJson('请登录', ['login_status' => 0, 'login_url' => Url::absoluteApi('member.login.index', $queryString), 'extra' => $extra]);
        } else {
            if ($scope == 'home' && !$mid) {
                return;
            }

            if ($scope == 'pass') {
                return;
            }

            if (self::MOBILE_TYPE == $type || self::WEB_APP == $type || self::NATIVE_APP == $type || self::LOGIN_APP_ANCHOR == $type) {
                return $this->errorJson('请登录', ['login_status' => 1, 'login_url' => '', 'type' => $type, 'i' => \YunShop::app()->uniacid, 'mid' => $mid, 'scope' => $scope, 'extra' => $extra]);
            }
            if ($type == 2) {
                response()->json([
                    'result' => 41009,
                    'msg' => '请登录',
                    'data' => '',
                ], 200, ['charset' => 'utf-8'])->send();
                exit;
            }

          return $this->errorJson('请登录', ['login_status' => 0, 'login_url' => Url::absoluteApi('member.login.index', $queryString), 'extra' => $extra]);

        }
    }
}