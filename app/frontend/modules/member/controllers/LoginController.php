<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 17/2/22
 * Time: 上午11:56
 */

namespace app\frontend\modules\member\controllers;

use app\common\components\ApiController;
use app\common\helpers\Client;
use app\common\helpers\Url;
use app\common\models\Member;
use app\common\services\Session;
use app\frontend\modules\member\services\factory\MemberFactory;
use app\frontend\modules\member\services\MemberService;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\DB;

class LoginController extends ApiController
{
    protected $publicController = ['Login'];
    protected $publicAction = ['index', 'phoneSetGet', 'chekAccount','loginMode','serviceLogin'];
    protected $ignoreAction = ['index', 'phoneSetGet', 'chekAccount','loginMode','serviceLogin'];
    private $w;
    public $yz_baseurl = "";
    public $app_type;

    public function __construct()
    {
        global $_W;
        $this->w = $_W;
        $this->app_type = \YunShop::request()->app_type;
        $this->yz_baseurl = $_W['siteroot'].'addons/yun_shop/api.php?i=39&type=2&app_type='.$this->app_type;
    }

    public function index()
    {
        $type = \YunShop::request()->type ;
        $uniacid = \YunShop::app()->uniacid;
        $mid = Member::getMid();
        if (empty($type) || $type == 'undefined') {
            $type = Client::getType();
        }

        if ($type == 8 && !(app('plugins')->isEnabled('alipay-onekey-login'))) {
            $type = Client::getType();
        }
        //判断是否开启微信登录
        if (\YunShop::request()->show_wechat_login) {
            return $this->init_login();
        }

        if(\Setting::get('shop.member.mobile_login_code') == 1 and \YunShop::request()->is_sms == 1){
            // todo 待优化，需要考虑其他很多种情况
            $type = 10;
        }

        if (!empty($type)) {
                $member = MemberFactory::create($type);

                if ($member !== NULL) {
                    $msg = $member->login();

                    if (!empty($msg)) {
                        if ($msg['status'] == 1) {
                            $url = Url::absoluteApp('member', ['i' => $uniacid, 'mid' => $mid]);

                            if (isset($msg['json']['redirect_url'])) {
                                $url = $msg['json']['redirect_url'];
                            }

                            $data = $msg['variable'];
                            $data['status'] = $msg['status'];
                            $data['url'] = $url;
                            return $this->successJson($msg['json'], $data);
                        } else {
                            return $this->errorJson($msg['json'], ['status'=> $msg['status']]);
                        }
                    } else {
                        return $this->errorJson('登录失败', ['status' => 3]);
                    }
                } else {
                    return $this->errorJson('登录异常', ['status'=> 2]);
                }
        } else {
            return $this->errorJson('客户端类型错误', ['status'=> 0]);
        }
    }

    /**
     * 初始化登录，判断是否开启微信登录
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function init_login () {
        $weixin_oauth = \Setting::get('shop_app.pay.weixin_oauth');
        return $this->successJson('', ['status'=> 1, 'wetach_login' => $weixin_oauth]);
    }

    public function phoneSetGet()
    {
        $phone_oauth = \Setting::get('shop_app.pay.phone_oauth');

        if (empty($phone_oauth)) {
            $phone_oauth = '0';
        }
        return $this->successJson('ok', ['phone_oauth' => $phone_oauth]);
    }

    public function chekAccount()
    {
        $type = \YunShop::request()->type ;

        if (1 == $type) {
            $member = MemberFactory::create($type);
            $member->chekAccount();
        }
    }

    public function checkLogin()
    {
        return $this->successJson('已登录');
    }

    public function loginMode()
    {
        $data = [];
        //增加验证码功能
        $status = \Setting::get('shop.sms.status');
        if (extension_loaded('fileinfo')) {
            if ($status == 1) {
                $captcha = self::captcha();
                $result['captcha'] = $captcha;
                $result['captcha']['status'] = $status;
            } else {
                $result['captcha']['status'] = $status;
            }
        }

        $data['sms'] = $result;
        $data['mobile_login_code'] = \Setting::get('shop.member.mobile_login_code') ?: 0;

        return $this->successJson('ok', $data);
    }

    //增加验证码功能
    public function captcha()
    {
        $captcha = app('captcha');
        $captcha_base64 = $captcha->create('default', true);

        return $captcha_base64;
    }

    //fixby-zhd-2021-1-4 改写增加小程序单独登录接口
    public function serviceLogin()
    {

        load()->func('logging');
        load()->model('mc');

        $type = \YunShop::request()->type;
        $uniacid = \YunShop::app()->uniacid;
        $mid = Member::getMid();
        if (empty($type) || $type == 'undefined') {
            return $this->errorJson('传递type类型错误');
        }

        $member = MemberFactory::create($type);
        if($member == NULL){
            return $this->errorJson('登录异常');
        }


        $code = \YunShop::request()->code;    //小程序code码
        $rawData = html_entity_decode(\YunShop::request()->rawData); //明文信息字符串
        $app_type = \YunShop::request()->app_type; //区分哪个小程序登录(shop/不传)
        $signature = \YunShop::request()->signature; //sha1签名
        $iv = \YunShop::request()->iv;  //偏移量
        $encryptedData = \YunShop::request()->encryptedData; //加密数据
        if (!$code) {
            return $this->errorJson('code码不能为空');
        }
        if (!$rawData || !$signature || !$iv || !$encryptedData) {

            return $this->errorJson('Data参数不能为空');
        }

        //获取session_key
        $res = $member->wxCode2SessionKey($code, $app_type);
        if ($res['errno'] != 0) {

            return $this->errorJson($res['msg'], ['status' => 0]);
        }

        $openid = $res['res']['openid'];     //用户opneid
        $session_key = $res['res']['session_key']; //用户sessionkey

        //解密用户信息
        $info = $member->wxDecodeInfo($session_key, $encryptedData, $iv, $app_type);
        if($info['errno'] != 0){

            return $this->errorJson($res['msg'], ['status' => 1]);
        }
        $data = $info['res']; //解密用户全部数据

        //每次登陆首先查询微擎粉丝表是否关注，在查询小程序会员是否有此用户，有的话比对会员和粉丝会员memberid是否一致，如果一致的话
        $openid_token = $this->getOpenidToken($openid); //用户身份令牌

        //粉丝表是否有此用户
        $fan_user = DB::table('mc_mapping_fans')->where(array('uniacid' => $uniacid, 'unionid' => $data['unionId']))->first();

        //微擎mc_member表是否有此用户
        //$user = pdo_get('yz_member_unique', array('unionid' => $data['unionId'], 'uniacid' => \YunShop::app()->uniacid));
        $user = DB::table('yz_member_unique')->where(array('unionid' => $data['unionId'], 'uniacid' => $uniacid))->first();
        if (!empty($user)) {
            //判断粉丝的member和小程序和芸众的member是否是同一条
            if (!empty($fan_user) && ($fan_user['uid'] != $user['member_id'])) {
                $mcUpdatStatus = DB::table('mc_mapping_fans')->where(array('uniacid' => $uniacid, 'unionid' => $data['unionId']))->update(array('uid' => $user['member_id']));

                if (!$mcUpdatStatus) {
                    return $this->errorJson('粉丝表会员uid修改失败');
                }
                DB::table('mc_members')->where('uid', $fan_user['uid'])->delete();
            }

            //判断有没有小程序用户
            $service_user = DB::table('diagnostic_service_user')->where(array('ajy_uid' => $user['member_id']))->first();

            if (empty($service_user)) {
                $this->service_user_insert($user['member_id'], $data, $openid_token, $app_type);
            } else {
                //更新diagnostic_service_user表数据
                $this->service_user_update($user['member_id'], $data, $openid_token, $app_type);
            }

            $user_id = $user['member_id'];
            //添加芸众小程序用户表
            $this->member_mini_app_insert($data, $user_id, $app_type);

            //老会员登陆进行会员同步处理
            $synchronous_member = DB::table('diagnostic_service_user')->where(array('ajy_uid' => $user['member_id']))->first();
            if ($synchronous_member['status'] == 0) {
                $this->member_synchronous($user_id, $mid, $fan_user['openid']);
            }

        } else {
            if ($fan_user) {
                $user_id = $fan_user['uid'];  //新增会员id为此粉丝uid的会员信息
            } else {
                //新增用户添加mc_member表数据
                $user_id = $this->mc_member_insert($openid, $data);

            }

            if ($user_id) {
                //添加diagnostic_service_user表数据
                $this->service_user_insert($user_id, $data, $openid_token, $app_type);

                //添加yz_member_unique表数据
                $this->member_unique_insert($data, $user_id);

                //添加yz_member_mini_app表数据
                $this->member_mini_app_insert($data, $user_id, $app_type);

                //调用会员同步接口 添加芸众member子表数据
                $this->member_synchronous($user_id, $mid, $fan_user['openid']);
            }
        }

        //企业微信信息更新
        $this->member_qy_wechat_update($data, $user_id);

        //与用户表连表联查
        $user = DB::table('diagnostic_service_user')->where(array('ajy_uid' => $user['member_id']))->first();
        $mc_member = DB::table('mc_members')->where(array('uid' => $user_id))->select('credit1', 'credit2')->first();
        $memberLevel = DB::table('yz_member')->where(array('member_id' => $user_id))->value('level_id');

        /*if($user['is_verify'] == 1)//如果手机号验证之后 手机号脱敏处理
            $user['account'] = $this->dataDesensitization($user['account'],3,4);*/
        //登录成功返回前台缓存信息
        $data = [
            'uid' => $user['ajy_uid'],
            'account' => $user['account'],
            'nickname' => $user['nickname'],
            'county' => $user['county'],
            'age' => $user['age'],
            'birthday' => $user['birthday'],
            'city' => $user['city'],
            'province' => $user['province'],
            'country' => $user['country'],
            'gender' => $user['gender'],
            'avatarurl' => $user['avatarurl'],
            'telephone' =>$user['telephone'],
            //'telephone' => $this->dataDesensitization($user['telephone'],3,4),//脱敏数据
            //'encrypt_telephone' => (new Aes($this->aesKey,$this->aesIv))->encrypt($user['telephone']),//Aes加密数据
            'openidToken' => $openid_token,
            'status' => 1,
            'credit1' => $mc_member['credit1'],  //积分
            'credit2' => $mc_member['credit2'],  //余额
            'memberLevel' => $memberLevel
        ];
        return $this->successJson('登录成功', $data);

    }


    //生成用户令牌
    public function getOpenidToken($openid){
        $salt = random(4); //salt
        $openid_token = md5($openid . $salt); //缓存key

        return $openid_token;
    }


    //添加艾居益公众号微擎mc_member表
    public function mc_member_insert($openid = '', $user = '')
    {
        $email = md5($openid) . '@we7.cc';
        $member_id = DB::table('mc_members')->where(array('uniacid' => \YunShop::app()->uniacid, 'email' => $email))->value('uid');
        if (!$member_id) {
            $default_groupid = DB::table('mc_groups')
                ->where(array(
                    'uniacid' => \YunShop::app()->uniacid, //公众号会员
                    'isdefault' => 1
                ))
                ->value('groupid');
            $data = array(
                'uniacid' => \YunShop::app()->uniacid,
                'email' => $email,
                'salt' => random(8),
                'groupid' => $default_groupid,
                'createtime' => time(),
                'password' => md5(123456),
                'nickname' => $user['nickName'],
                'avatar' => $user['avatarUrl'],
                'gender' => $user['gender'],
                'nationality' => $user['country'],
                'resideprovince' => $user['province'] . '省',
                'residecity' => $user['city'] . '市',
                'user_from' => 1,
            );

            $member_id = DB::table('mc_members')->insertGetId($data);
            //注册送积分
            $this->register_add_integral($member_id);
        }

        return $member_id;
    }

    private function register_add_integral($uid)
    {
        $integralSet = pdo_get('yz_setting', array('uniacid' => \YunShop::app()->uniacid, 'group' => 'shop', 'key' => 'member'));
        $integralSet = unserialize($integralSet['value']);

        if (!isset($integralSet['register_initial_integral']) ||
            $integralSet['register_initial_integral'] <= 0
        ) {
            return false;
        }

        $user = DB::table('mc_members')->where(array('uid' => $uid))->first();

        $after = $user['credit1'] + $integralSet['register_initial_integral'];
        $data = [
            'uniacid' => \YunShop::app()->uniacid,
            'member_id' => $uid,
            'point' => $integralSet['register_initial_integral'],
            'point_income_type' => 1,
            'point_mode' => 50,
            'before_point' => $user['credit1'],
            'after_point' => $after,
            'remark' => '新用户注册赠送积分： ' . intval($integralSet['register_initial_integral']) . '积分',
            'thirdStatus' => 1,
            'order_id' => 0,
            'created_at' => TIMESTAMP,
            'deleted_at' => 0,
            'updated_at' => TIMESTAMP,
        ];
        DB::table('yz_point_log')->insert($data);

        $data = ['credit1' => $after];
        DB::table('mc_members')->where(array('uid' => $uid))->update($data);

        return true;
    }


    //diagnostic_service_user表数据添加
    public function service_user_insert($uid, $data, $openid_token, $app_type)
    {
        $userInfo = [
            'ajy_uid' => $uid,
            'uniacid' => \YunShop::app()->uniacid,
            'container' => $this->w['container'],
            'os' => $this->w['os'],
            'last_ip' => $this->w['clientip'],
            'last_login_time' => date('Y-m-d H:i:s', time()),
            'login_nums +=' => 1,
            'unionid' => $data['unionId'],
            'nickname' => $data['nickName'],
            'avatarurl' => $data['avatarUrl'],
            'gender' => $data['gender'],
            'country' => $data['country'],
            'province' => $data['province'],
            'city' => $data['city'],
            'account' => 'ajy_00' . random(12),
            'add_time' => time()
        ];
        if($app_type == 'shop'){
            $userInfo['shop_openid'] = $data['openId'];
            $userInfo['shop_openid_token'] = $openid_token;
        }else{
            $userInfo['openid'] = $data['openId'];
            $userInfo['openid_token'] = $openid_token;
        }
        
        $year = date('Y', time());
        $month = date('m', time());

        $avatarPath = "avatar/" . $this->w['uniacid'] . "/" . $year . "/" . $month . "/";

        $avatarName = md5($this->w['uniacid'] . time() . random(6)) . ".png"; //文件名
        $avatarFile = $avatarPath . $avatarName;  //文件路径 avatar/45/2020/5/dfsafdggcs.png


        $user_avatarUrl = file_get_contents($data['avatarUrl']);

        $res = $this->file_write($avatarFile, $user_avatarUrl); //写入文件

        if ($res) {
            $userInfo['avatar'] = $avatarFile;
            if (!empty($this->w['setting']['remote']['type'])) { // 判断系统是否开启了远程附件
                $remotestatus = file_remote_upload($avatarFile); //上传图片到远程
            }
        }

        pdo_insert('diagnostic_service_user', $userInfo);
        return pdo_insertid();
    }

    private function file_write($filename, $data) {
        $filename = ATTACHMENT_ROOT . '/' . $filename;
        mkdirs(dirname($filename));
        file_put_contents($filename, $data);
        @chmod($filename, $this->w['config']['setting']['filemode']);

        return is_file($filename);
    }

    //添加yz_member_unique表数据
    public function member_unique_insert($user, $mc_uid)
    {

        $data = array(
            'uniacid' => \YunShop::app()->uniacid,
            'unionid' => $user['unionId'],
            'member_id' => $mc_uid,
            'type' => 1,
            'created_at' => time(),
            'updated_at' => time(),

        );

        pdo_insert('yz_member_unique', $data);
        return pdo_insertid();
    }

    //添加yz_mini_app表用户
    public function member_mini_app_insert($user, $mc_uid, $app_type)
    {
        $mini_app_user = pdo_get('yz_member_mini_app', array('member_id' => $mc_uid));

        if ($mini_app_user) {
            $data = array(
                //'uniacid' => 39,
                //'member_id' => $mc_uid,
                //'openid' => $user['openId'],
                'nickname' => $user['nickName'],
                'avatar' => $user['avatarUrl'],
                'gender' => $user['gender'],
                'updated_at' => TIMESTAMP,
            );
            if($app_type == 'shop'){
                $data['shop_openid'] = $user['openId'];
            }else{
                $data['openid'] = $user['openId'];
            }

            $res = pdo_update('yz_member_mini_app', $data, array('member_id' => $mc_uid));
            return $mini_app_user['id'];
        } else {
            $data = array(
                'uniacid' => \YunShop::app()->uniacid,
                'member_id' => $mc_uid,
                //'openid' => $user['openId'],
                'nickname' => $user['nickName'],
                'avatar' => $user['avatarUrl'],
                'gender' => $user['gender'],
                'created_at' => TIMESTAMP,
            );
            if($app_type == 'shop'){
                $data['shop_openid'] = $user['openId'];
            }else{
                $data['openid'] = $user['openId'];
            }
            pdo_insert('yz_member_mini_app', $data);
            return pdo_insertid();
        }
    }

    //更新service_user_update表数据
    public function service_user_update($user_id, $data = '', $openid_token, $app_type='')
    {
        $ajyData = [
//            fixBy-wk-20201209 用户昵称和头像支持自定 不再同步昵称和头像
//            'avatarurl' => $data['avatarUrl'],
//            'nickname' => $data['nickName'],
            'last_login_time' => date('Y-m-d H:i:s', time()),
            'last_ip' => $this->w['clientip'],
            'login_nums +=' => 1,
            'update_time' => time()
        ];
        if($app_type == 'shop'){
            $ajyData['shop_openid'] = $data['openId'];
            $ajyData['shop_openid_token'] = $openid_token;
        }else{
            $ajyData['openid'] = $data['openId'];
            $ajyData['openid_token'] = $openid_token;
        }
        pdo_update('diagnostic_service_user', $ajyData, array('ajy_uid' => $user_id));
    }

    //更新yz_member_qywechat表数据
    public function member_qy_wechat_update($data, $member_id){
        $qywechat_user = pdo_get('yz_member_qywechat', array('unionid' => $data['unionId']));
        if($qywechat_user){
            $res = pdo_update('yz_member_qywechat',array('member_id' => $member_id,'status' => 1), array('unionid' => $data['unionId']));
            if($res){
                $this->addQyWechatUserTag($qywechat_user);
            }

        }
    }

    /**
     * @param $user_id       会员uid
     * @param int $mid 上级会员uid
     */
    public function member_synchronous($uid, $mid, $openid = '')
    {
        //芸众商城会员同步
        $mid = intval($mid);
        if ($mid == $uid) {
            $mid = 0;
        }
        $yz_z_member = pdo_get('yz_member', array('member_id' => $uid));
        $mid = $yz_z_member['parent_id'] > 0 ? $yz_z_member['parent_id'] : $mid;
        if (!$yz_z_member) {
            $zmemberData = array(
                'uniacid' => \YunShop::app()->uniacid,
                'member_id' => $uid,
                'inviter' => 0,  //fixby-zlt 修复发展下线积分问题
                'yz_openid' => $openid,
                'created_at' => TIMESTAMP,
                'updated_at' => TIMESTAMP
            );
            pdo_insert('yz_member', $zmemberData);
        }

        //api地址
        $url = $this->yz_baseurl . "&uid=" . $uid . "&mid=" . $mid . "&route=member.member.memberFromHXQModule";
        //$url = "https://www.aijuyi.net/addons/yun_shop/api.php?i=". $i ."&type=". $type ."&uid=". $uid ."&mid=".$mid."&route=member.member.memberFromHXQModule";  //芸众会员同步API
        $resdata = ihttp_request($url);
        $res = json_decode($resdata['content'], true);

        pdo_insert('diagnostic_service_member_synchronous_logs', array('uid' => $uid, 'mid' => $mid, 'create_time' => TIMESTAMP, 'content' => $resdata));
        if ($res['result'] == 1) {
            //调用接口同步成功
            pdo_update('diagnostic_service_user', array('mid' => $mid, 'status' => 1, 'session_key' => $resdata), array('ajy_uid' => $uid));
        } else {
            pdo_update('diagnostic_service_user', array('mid' => $mid, 'status' => 0, 'session_key' => $resdata), array('ajy_uid' => $uid));
        }
    }


    //给企业微信用户添加标签
    public function addQyWechatUserTag($qywechat_user){


        $qyWechatsetting = \Setting::get('plugin.enterprise-wechat');
        $corpId = $qyWechatsetting['corpid']; //艾居益企业微信id
        $corpSecret = $qyWechatsetting['corpsecret']; //企业微信secret
        if(empty($qyWechatsetting) || empty($corpId) || empty($corpSecret)){
            return false;
        }
        $accessToken = $this->getEnterpriseAccessToken($corpId, $corpSecret);
        if (!$accessToken) {
            logging_run('======accessToken获取失败======：\n\n' . $accessToken, $type = 'trace', $filename = 'ajy_run');
        }

        //请求方式：POST（HTTPS） 请求地址：https://qyapi.weixin.qq.com/cgi-bin/externalcontact/mark_tag?access_token=ACCESS_TOKEN

        /*
         * { "group_id": "etNsyYDQAA2iDJgRFL-fVu718SrY4kug", "group_name": "会员组", "create_time": 1606278321, "tag": [ { "id": "etNsyYDQAAtG6wmbyOQfmr5kFZq4oLkg", "name": "小程序会员", "create_time": 1606278321, "order": 0 } ], "order": 0 } ] }
         * {
    "userid":"zhangsan",
    "external_userid":"woAJ2GCAAAd1NPGHKSD4wKmE8Aabj9AAA",
    "add_tag":["TAGID1","TAGID2"],
    "remove_tag":["TAGID3","TAGID4"]
}
         *
         * */
        $tagUrl = "https://qyapi.weixin.qq.com/cgi-bin/externalcontact/mark_tag?access_token=".$accessToken;

        if($qywechat_user['follow_user']){
            $follow_user = json_decode($qywechat_user['follow_user'], true);
            $follow_user = $follow_user[0]['userid'];
        }else{
            //客户详情API
            $customerInfoUrl = "https://qyapi.weixin.qq.com/cgi-bin/externalcontact/get?access_token={$accessToken}&external_userid={$qywechat_user['external_user_id']}";
            $info = ihttp_get($customerInfoUrl);
            $customerInfo = @json_decode($info['content'], true);
            if ($customerInfo['errcode'] != 0) {
                logging_run($customerInfo['errcode'] . "======客服详情API接口失败======\n\n", $type = 'trace', $filename = 'ajy_run');
            }

            $res = pdo_update('yz_member_qywechat', array('follow_user' => json_encode($customerInfo['follow_user'])), array('external_user_id'=> $qywechat_user['external_user_id']));
            if(!$res){
                logging_run('======更新用户客服失败======：\n\n' , $type = 'trace', $filename = 'ajy_run');
            }
            $follow_user = $customerInfo['follow_user'][0]['userid'];
        }

        $postData = array(
            'userid' => $follow_user,
            'external_userid' => $qywechat_user['external_user_id'],
            'add_tag' => array('etNsyYDQAAtG6wmbyOQfmr5kFZq4oLkg'), //小程序会员 的tagid
            'remove_tag' => array(),
        );
        $info = ihttp_request($tagUrl, json_encode($postData));

        $content = @json_decode($info['content'], true);
        if ($content['errcode'] != 0) {
            logging_run($content['errcode'] . "======企业微信客户打标签失败======\n\n", $type = 'trace', $filename = 'ajy_run');
        }
        return true;

    }

    /**
     * @param $corpid
     * @param $corpsecret
     * 封装企业微信accesstoken
     */
    public function getEnterpriseAccessToken($corpid, $corpsecret) {
        $cachekey = cache_system_key('enterprise_token', array('uniacid' => $this->w['uniacid']));
        $cache = cache_load($cachekey);

        if (!empty($cache) && !empty($cache['token']) && $cache['expire'] > TIMESTAMP) {
            //$account_access_token = $cache;

            return $cache['token'];
        }

        if (empty($corpid) || empty($corpsecret)) {
            return false;
        }
        //https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=ID&corpsecret=SECRET
        //$url = "https://openapi.baidu.com/oauth/2.0/token?grant_type=client_credentials&client_id={$this->account['key']}&client_secret={$this->account['secret']}";

        $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid={$corpid}&corpsecret={$corpsecret}";
        $content = ihttp_get($url);
        $token = @json_decode($content['content'], true);

        //var_dump($token);die;
        $record = array();
        $record['token'] = $token['access_token'];
        $record['expire'] = TIMESTAMP + $token['expires_in'] - 200;
        //$account_access_token = $record;
        cache_write($cachekey, $record);

        return $record['token'];
    }
}