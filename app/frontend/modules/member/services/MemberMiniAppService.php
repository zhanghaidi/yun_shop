<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 17/2/23
 * Time: 上午11:20
 */

namespace app\frontend\modules\member\services;

use app\common\exceptions\AppException;
use app\common\helpers\Client;
use app\common\models\MemberGroup;
use app\common\services\Session;
use app\frontend\modules\member\models\MemberMiniAppModel;
use app\frontend\modules\member\models\MemberUniqueModel;
use app\frontend\modules\member\models\McMappingFansModel;
use app\frontend\modules\member\models\SubMemberModel;
use Illuminate\Contracts\Encryption\DecryptException;

class MemberMiniAppService extends MemberService
{
    const LOGIN_TYPE    = 2;  //小程序

    public function __construct()
    {}

    public function login()
    {
        include dirname(__FILE__ ) . "/../vendors/wechat/wxBizDataCrypt.php";

        $uniacid = \YunShop::app()->uniacid;

        $min_set = \Setting::get('plugin.min_app');

        if (is_null($min_set) || 0 == $min_set['switch']) {
            return show_json(0,'未开启小程序');
        }

        $para = \YunShop::request();

        $data = array(
            'appid' => $min_set['key'],
            'secret' => $min_set['secret'],
            'js_code' => $para['code'],
            'grant_type' => 'authorization_code',
        );

        $url = 'https://api.weixin.qq.com/sns/jscode2session';

        $user_info = \Curl::to($url)
            ->withData($data)
            ->asJsonResponse(true)
            ->get();
           
        
        $data = '';  //json
        $pc = new \WXBizDataCrypt($min_set['key'], $user_info['session_key']);
        $errCode = $pc->decryptData($para['encryptedData'], $para['iv'], $data);

        \Log::debug('-------------min errcode-------', [$errCode]);
        if ($errCode == 0) {
            $json_user = json_decode($data, true);
        } else {
            return show_json(0,'登录认证失败');
        }
       
        if (!empty($json_user)) {
            if (isset($json_user['unionId'])) {
                $json_user['unionid']     = $json_user['unionId'];
            }

            $json_user['openid']     = $json_user['openId'];
            $json_user['nickname']   = $json_user['nickName'];
            $json_user['headimgurl'] = $json_user['avatarUrl'];
            $json_user['sex']        = $json_user['gender'];

            //Login
            $member_id = $this->memberLogin($json_user);

            Session::set('member_id', $member_id);
            //var_dump($member_id);

            $random = $this->wx_app_session($user_info);

            $result = array('session' => $random, 'wx_token' =>session_id(), 'uid' => $member_id);

            return show_json(1, $result, $result);
        } else {
            return show_json(0, '获取用户信息失败');
        }
    }

    /**
     * 小程序登录态
     *
     * @param $user_info
     * @return string
     */
    function wx_app_session($user_info)
    {
        if (empty($user_info['session_key']) || empty($user_info['openid'])) {
            return show_json(0,'用户信息有误');
        }

        $random = md5(uniqid(mt_rand()));

        $_SESSION['wx_app'] = array($random => iserializer(array('session_key'=>$user_info['session_key'], 'openid'=>$user_info['openid'])));

        return $random;
    }

    public function createMiniMember($json_user, $arg)
    {
        $user_info = MemberMiniAppModel::getUserInfo($json_user['openid']);

        if (!empty($user_info)) {
            MemberMiniAppModel::updateUserInfo($json_user['openid'],array(
                'nickname' => $json_user['nickname'],
                'avatar' => $json_user['headimgurl'],
                'gender' => $json_user['sex'],
            ));
        } else {
            MemberMiniAppModel::insertData(array(
                'uniacid' => $arg['uniacid'],
                'member_id' => $arg['member_id'],
                'openid' => $json_user['openid'],
                'nickname' => $json_user['nickname'],
                'avatar' => $json_user['headimgurl'],
                'gender' => $json_user['sex'],
            ));
        }
    }

    /**
     * 公众号开放平台授权登陆
     *
     * @param $uniacid
     * @param $userinfo
     * @return array|int|mixed
     */
    public function unionidLogin($uniacid, $userinfo, $upperMemberId = NULL)
    {
        $member_id = parent::unionidLogin($uniacid, $userinfo, $upperMemberId = NULL, self::LOGIN_TYPE);

        return $member_id;
    }

    public function updateMemberInfo($member_id, $userinfo)
    {
        parent::updateMemberInfo($member_id, $userinfo);

        $record = array(
            'openid' => $userinfo['openid'],
            'nickname' => stripslashes($userinfo['nickname'])
        );

        MemberMiniAppModel::updateData($member_id, $record);
    }

    public function addMemberInfo($uniacid, $userinfo)
    {
        $uid = parent::addMemberInfo($uniacid, $userinfo);

        //$this->addMcMemberFans($uid, $uniacid, $userinfo);
        $this->addFansMember($uid, $uniacid, $userinfo);

        return $uid;
    }

    public function addMcMemberFans($uid, $uniacid, $userinfo)
    {
        McMappingFansModel::insertData($userinfo, array(
            'uid' => $uid,
            'acid' => $uniacid,
            'uniacid' => $uniacid,
            'salt' => Client::random(8),
        ));
    }

    public function addFansMember($uid, $uniacid, $userinfo)
    {
        MemberMiniAppModel::insertData(array(
            'uniacid' => $uniacid,
            'member_id' => $uid,
            'openid' => $userinfo['openid'],
            'nickname' => $userinfo['nickname'],
            'avatar' => $userinfo['headimgurl'],
            'gender' => $userinfo['sex'],
        ));
    }

    public function getFansModel($openid)
    {
        $model = MemberMiniAppModel::getUId($openid);

        if (!is_null($model)) {
            $model->uid = $model->member_id;
        }

        return $model;
    }

    /**
     * 添加会员主表信息
     *
     * @param $uniacid
     * @param $userinfo
     * @return mixed
     */
    public function addMcMemberInfo($uniacid, $userinfo)
    {
        $uid = parent::addMemberInfo($uniacid, $userinfo);

        return $uid;
    }

    /**
     * 会员关联表操作
     *
     * @param $uniacid
     * @param $member_id
     * @param $unionid
     */
    public function addMemberUnionid($uniacid, $member_id, $unionid)
    {
        MemberUniqueModel::insertData(array(
            'uniacid' => $uniacid,
            'unionid' => $unionid,
            'member_id' => $member_id,
            'type' => self::LOGIN_TYPE
        ));
    }

    /**
     * 验证登录状态
     *
     * @return bool
     */
    public function checkLogged($login = null)
    {
        return MemberService::isLogged();
    }

    public function updateFansMember($fan, $member_id, $userinfo)
    {
        $record = array(
            'member_id'       => $member_id,
            'nickname' => stripslashes($userinfo['nickname']),
            'avatar' => isset($userinfo['headimgurl']) ? $userinfo['headimgurl'] : '',
            'gender' => isset($userinfo['sex']) ? $userinfo['sex'] : '-1',
        );

        MemberMiniAppModel::where('mini_app_id', $fan->mini_app_id)->update($record);
    }








    //fixby-zhd-改写小程序登录接口 至此以下方法全部为改写
    //code 获取sessionkey接口

    public function wxCode2SessionKey($code, $app_type)
    {
        $uniacid = \YunShop::app()->uniacid;
        $min_set = \Setting::get('plugin.min_app');
        if (is_null($min_set) || 0 == $min_set['switch']) {

            $data = array(
                'errno' => 1,
                'msg' => '未开启小程序'
            );
            return $data;

        }

        if($app_type == 'shop' ){
            if(empty($min_set['shop_key'] || $min_set['shop_secret'])){
                $data = array(
                    'errno' => 1,
                    'msg' => '商城小程序未开启'
                );
                return $data;

            }

            $postData = array(
                'appid' => $min_set['shop_key'],
                'secret' => $min_set['shop_secret'],
                'js_code' => $code,
                'grant_type' => 'authorization_code',
            );
        }else{
            $postData = array(
                'appid' => $min_set['key'],
                'secret' => $min_set['secret'],
                'js_code' => $code,
                'grant_type' => 'authorization_code',
            );
        }

        $url = 'https://api.weixin.qq.com/sns/jscode2session'; //根据code 调用微信jscode2session接口获取用户session_key // https://api.weixin.qq.com/sns/jscode2session?appid=APPID&secret=SECRET&js_code=JSCODE&grant_type=authorization_code
        $res = \Curl::to($url)
            ->withData($postData)
            ->asJsonResponse(true)
            ->get();

        if (empty($res)) {
            //返回数据失败
            $data = array(
                'errno' => 1,
                'msg' => 'wx服务器内部错误'
            );
            return $data;
        }

        //是否有错误码
        $loginFail = array_key_exists('errcode', $res);
        if ($loginFail) {
            \Log::debug('-------------min sessionkeyerrcode-------', [$res]);
            $data = array(
                'errno' => 1,
                'msg' => 'error:' . $res['errmsg']
            );
            return $data;
        }

        $data = array(
            'errno' => 0,
            'msg' => 'success',
            'res' => $res
        );
        return $data;
    }


    //解密用户信息
    public function wxDecodeInfo($session_key, $encryptedData, $iv, $app_type)
    {
        include dirname(__FILE__ ) . "/../vendors/wechat/wxBizDataCrypt.php";

        $min_set = \Setting::get('plugin.min_app');

        //解密信息
        if($app_type == 'shop' ){
            $appid = $min_set['shop_key']; //商城小程序appid
        }else{
            $appid = $min_set['key']; //养居益小程序
        }


        $data = ''; //json
        $pc = new \WXBizDataCrypt($appid, $session_key);
        $errCode = $pc->decryptData($encryptedData, $iv, $data);
        \Log::debug('-------------min errcode-------', [$errCode]);

        $data = json_decode($data, true);

        //解密成功
        if ($errCode == 0) {
            $data = array(
                'errno' => 0,
                'msg' => '解密成功',
                'res' => $data
            );
            return $data;
        } else {
            $data = array(
                'errno' => 1,
                'msg' => '解密用户信息失败',
                'res' => [
                    'errCode' => $errCode,
                    'data' => $data
                ]
            );
            return $data;
        }
    }
}
