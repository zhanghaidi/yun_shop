<?php

namespace Yunshop\MinappContent\api;
use app\common\components\ApiController;
use app\frontend\modules\member\services\factory\MemberFactory;
use app\frontend\modules\finance\models\PointLog;
use app\frontend\models\Member;


class MemberController extends ApiController
{
    private $pagesize = 15;

    //获取用户关注公众号状态
    public function getFollow()
    {
        $uniacid = \YunShop::app()->uniacid;
        $user_id = \YunShop::app()->getMemberId();

        $service_user = pdo_get('diagnostic_service_user', array('ajy_uid' => $user_id));
        //粉丝是否关注养居益公众号
        $fan_user = pdo_get('mc_mapping_fans', array('uniacid' => $uniacid, 'unionid' => $service_user['unionid']));

        $is_follow = $fan_user['follow'] ? $fan_user['follow'] : 0 ;

        return $this->successJson('success', array('is_follow'=> $is_follow));

    }

    //根据邀请页面点击加入家庭
    public function familyAdd()
    {
        $uniacid = \YunShop::app()->uniacid;
        $user_id = \YunShop::app()->getMemberId();

        $family_id = intval(\YunShop::request()->family_id);
        if (!$family_id) {
            return $this->errorJson('家庭id不存在');
        }

        $user = pdo_get('diagnostic_service_user', array('ajy_uid' => $user_id), array('family_id', 'birthday', 'age', 'gender', 'telephone')); //读取本用户家庭

        if($user['family_id']){
            return $this->errorJson('此用户已有家庭', array('family_id' => $user['family_id']));
        }

        $res = pdo_update('diagnostic_service_user', array('family_id' => $family_id), array('ajy_uid' => $user_id));
        if(!$res){
            return $this->errorJson('加入家庭失败');
        }
        $memberData = [
            'uniacid' => $uniacid,
            'family_id' => $family_id,
            'user_id' => $user_id,
            'birthday' => $user['birthday'] ? $user['birthday'] : null,
            'age' => $user['age'],
            'gender' => $user['gender'],
            'telephone' => $user['telephone'],
            'add_time' => TIMESTAMP
        ];
        pdo_insert('diagnostic_service_family_member', $memberData);

        return $this->successJson('加入家庭成功', array('family_id' => $family_id));
    }

    //用户发起家庭邀请
    public function familyInvite()
    {
        $uniacid = \YunShop::app()->uniacid;
        $user_id = \YunShop::app()->getMemberId();

        $mid = intval(\YunShop::request()->mid); //家庭分享者id
        if (!$mid) {
            return $this->errorJson('分享者id不存在');
        }
        //获得分享者家庭id 没有创建家庭一个
        $family = pdo_get('diagnostic_service_user', array('ajy_uid' => $mid), array('nickname', 'family_id', 'birthday', 'age', 'gender', 'telephone'));
        if (!$family) {
            return $this->errorJson('未找到分享者用户信息');
        }
        $family_id = $family['family_id'];
        if (!$family_id) {
            //创建家庭
            $data = [
                'create_user_id' => $mid,
                'uniacid' => $uniacid,
                'name' => $family['nickname'] . '的家庭',
                'create_time' => TIMESTAMP
            ];
            $family_create = pdo_get('diagnostic_service_family', array('uniacid' => $uniacid, 'create_user_id' => $mid));
            if (!$family_create) {
                $res = pdo_insert('diagnostic_service_family', $data);
                $family_id = pdo_insertid();
            } else {
                $family_id = $family_create['id'];
                $res = pdo_update('diagnostic_service_family', $data, array('id' => $family_id));
            }
            if ($res) {
                pdo_update('diagnostic_service_user', array('family_id' => $family_id), array('ajy_uid' => $mid));
                //更新用户家庭 更新成员表信息
                $memberData = [
                    'uniacid' => $uniacid,
                    'family_id' => $family_id,
                    'user_id' => $mid,
                    'is_create_user' => 1,
                    'birthday' => $family['birthday'],
                    'age' => $family['age'],
                    'gender' => $family['gender'],
                    'telephone' => $family['telephone'],
                    'add_time' => TIMESTAMP
                ];
                pdo_insert('diagnostic_service_family_member', $memberData);
            }
        }
        $share_user = pdo_get('diagnostic_service_user', array('ajy_uid' => $mid), array('nickname', 'avatarurl', 'family_id'));
        return $this->successJson('获取分享者信息成功', $share_user);

    }

    //用户中心获取家庭成员列表
    public function familyMember()
    {
        $uniacid = \YunShop::app()->uniacid;
        $user_id = \YunShop::app()->getMemberId();

        $family = pdo_get('diagnostic_service_user', array('ajy_uid' => $user_id), 'family_id');
        $family_id = $family['family_id'];

        if(!$family_id){
            return $this->errorJson('用户暂未加入家庭');
        }

        $params = array(
            ':uniacid' => $uniacid,
            ':user_id' => $user_id
        );

        $query = load()->object('query');
        $familyMember = $query->from('diagnostic_service_family_member', 'f')->select('f.user_id', 'u.nickname', 'u.avatarurl', 'u.province', 'u.city', 'f.gender', 'f.birthday', 'f.family_relation', 'f.real_name', 'f.is_create_user', 'f.telephone', 'f.medical_history', 'f.add_time')->leftjoin('diagnostic_service_user', 'u')->on('f.user_id', 'u.ajy_uid')->where(array('f.uniacid' => $uniacid, 'f.family_id' => $family_id))->orderby('f.add_time', 'ASC')->getall();

        //家庭成员列表

        return $this->successJson('家庭成员获取成功', $familyMember);
    }

    //获取家庭成员信息
    public function familyMemberInfo()
    {
        $uniacid = \YunShop::app()->uniacid;
        $user_id = \YunShop::app()->getMemberId();

        $family_user_id = intval(\YunShop::request()->family_user_id);
        if (!$family_user_id) {
            return $this->errorJson('请传入家庭成员用户id', array('status' => 0));
        }

        $family_user_info = pdo_get('diagnostic_service_family_member', array('user_id' => $family_user_id), array('user_id', 'real_name', 'telephone', 'birthday', 'age', 'medical_history', 'family_relation'));

        return $this->successJson('获取家庭成员信息', $family_user_info);
    }

    //修改家庭成员家庭信息
    public function familyMemberEdit()
    {
        $uniacid = \YunShop::app()->uniacid;
        $user_id = \YunShop::app()->getMemberId();

        $family_user_id = intval(\YunShop::request()->family_user_id);
        if (!$family_user_id) {
            return $this->errorJson('家庭用户id不存在', array('status' => 0));
        }

        $real_name = trim(\YunShop::request()->real_name);
        $telephone = trim(\YunShop::request()->telephone);
        $age = intval(\YunShop::request()->age);
        $birthday = \YunShop::request()->birthday;
        $medical_history = \YunShop::request()->medical_history;
        $family_relation = \YunShop::request()->family_relation;

        $data = array();

        if ($real_name) {
            $data['real_name'] = $real_name;
        }

        if ($age) {
            $data['age'] = $age;
        }

        if ($birthday) {
            $data['birthday'] = $birthday;
        }
        if ($telephone) {
            $data['telephone'] = $telephone;
        }

        if ($medical_history) {
            $data['medical_history'] = $medical_history;
        }

        if ($family_relation) {
            $data['family_relation'] = $family_relation;
        }

        $res = pdo_update('diagnostic_service_family_member', $data, array('user_id' => $family_user_id));
        if (!$res) {
            return $this->errorJson('修改失败', array('status' => 0));
        }

        $data['status'] = 1;
        return $this->successJson('修改成功', $data);
    }

    //移除家庭成员家庭管理员权限
    public function familyMemberDel()
    {
        $uniacid = \YunShop::app()->uniacid;
        $user_id = \YunShop::app()->getMemberId();

        //家庭成员id

        $family = pdo_get('diagnostic_service_user', array('ajy_uid' => $user_id), 'family_id');
        if (!$family['family_id']) {
            return $this->errorJson('您暂未加入家庭', array('status' => 0));
        }

        //家庭成员列表
        $familyMember = pdo_get('diagnostic_service_family_member', array('family_id' => $family['family_id'], 'user_id' => $user_id));
        if ($familyMember) {
            pdo_delete('diagnostic_service_family_member', array('family_id' => $family['family_id'], 'user_id' => $user_id));
        }

        $res = pdo_update('diagnostic_service_user', array('family_id' => 0), array('ajy_uid' => $user_id));
        if(!$res){
            return $this->errorJson('操作失败', array('status' => 0));
        }

        return $this->successJson('退出成功', array('status' => 1));

    }

    //关注我的用户列表
    public function followMe(){

        $uniacid = \YunShop::app()->uniacid;
        $user_id = \YunShop::app()->getMemberId();
        $pindex = intval(\YunShop::request()->page) ? intval(\YunShop::request()->page) : 1; //初始页
        $psize = $this->pagesize;
        $params = array(
            ':uniacid' => $uniacid,
            ':user_id' => $user_id
        );

        $where = " WHERE f.uniacid = :uniacid AND f.fans_id = :user_id";
        $order = " ORDER BY f.`create_time` DESC ";
        $sql = "SELECT u.ajy_uid,u.nickname,u.avatarurl,u.province,u.city,f.create_time FROM " . tablename('diagnostic_service_user_follow') . " f LEFT JOIN " . tablename('diagnostic_service_user') . " u ON f.user_id = u.ajy_uid " . $where . $order;

        $fans = pdo_fetchall($sql, $params);
        foreach ($fans as $k => $v) {
            $follow = pdo_get('diagnostic_service_user_follow', array('uniacid' => $uniacid, 'user_id' => $user_id, 'fans_id' => $v['ajy_uid']));
            if ($follow) {
                $fans[$k]['is_follow'] = 3;
            } else {
                $fans[$k]['is_follow'] = 1;
            }

        }
        $total = intval(count($fans));
        $totalPage = intval(($total + $psize - 1) / $psize);
        $fans = array_slice($fans, ($pindex - 1) * $psize, $psize);
        return $this->successJson('获取用户粉丝列表', compact('total', 'totalPage', 'fans'));

    }

    //我关注的用户列表
    public function myFollow(){

        $uniacid = \YunShop::app()->uniacid;
        $user_id = \YunShop::app()->getMemberId();
        $pindex = intval(\YunShop::request()->page) ? intval(\YunShop::request()->page) : 1; //初始页
        $psize = $this->pagesize;
        $params = array(
            ':uniacid' => $uniacid,
            ':user_id' => $user_id
        );

        $where = " WHERE f.uniacid = :uniacid AND f.user_id = :user_id";
        $order = " ORDER BY f.`create_time` DESC "; //. ($pindex - 1) * $psize . ',' . $psize;

        $sql = "SELECT u.ajy_uid,u.nickname,u.avatarurl,u.province,u.city,f.create_time FROM " . tablename('diagnostic_service_user_follow') . " f LEFT JOIN " . tablename('diagnostic_service_user') . " u ON f.fans_id = u.ajy_uid " . $where . $order;

        $follow = pdo_fetchall($sql, $params);
        foreach ($follow as $k => $v) {

            $fan = pdo_get('diagnostic_service_user_follow', array('uniacid' => $uniacid, 'user_id' => $v['ajy_uid'], 'fans_id' => $user_id));
            if ($fan) {
                $follow[$k]['is_follow'] = 3;  //互相关注
            } else {
                $follow[$k]['is_follow'] = 2; //已关注
            }

        }

        $total = intval(count($follow));
        $totalPage = intval(($total + $psize - 1) / $psize);
        $follow = array_slice($follow, ($pindex - 1) * $psize, $psize);

        return $this->successJson('获取用户关注列表', compact('total', 'totalPage', 'follow'));
    }

    public function pointLogs()
    {
        $member_id = \YunShop::app()->getMemberId();
        $pageSize = intval(\YunShop::request()->get('pagesize'));
        $pageSize = $pageSize ? $pageSize : $this->pagesize;

        $list = PointLog::getPointLogList($member_id)->paginate($pageSize);

        return $this->successJson('成功', $list);
    }

}
