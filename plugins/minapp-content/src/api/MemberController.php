<?php

namespace Yunshop\MinappContent\api;

use app\common\components\ApiController;
use app\frontend\modules\member\services\factory\MemberFactory;
use app\frontend\modules\finance\models\PointLog;
use app\frontend\models\Member;
use Yunshop\EnterpriseWechat\services\QyWeChatService;


class MemberController extends ApiController
{
    private $pagesize = 15;
    protected $publicAction = ['familyInvite'];
    protected $ignoreAction = ['familyInvite'];
    protected $xc_uniacid = 3;

    public function index()
    {
        $uniacid = \YunShop::app()->uniacid;
        $user_id = \YunShop::app()->getMemberId();
        return $this->successJson('success');
    }

    //获取用户关注公众号状态
    public function getFollow()
    {
        $uniacid = \YunShop::app()->uniacid;
        $user_id = \YunShop::app()->getMemberId();

        $service_user = pdo_get('diagnostic_service_user', array('ajy_uid' => $user_id));
        //粉丝是否关注养居益公众号
        $fan_user = pdo_get('mc_mapping_fans', array('uniacid' => $uniacid, 'unionid' => $service_user['unionid']));

        $is_follow = $fan_user['follow'] ? $fan_user['follow'] : 0;

        return $this->successJson('success', array('is_follow' => $is_follow));

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

        if ($user['family_id']) {
            return $this->errorJson('此用户已有家庭', array('family_id' => $user['family_id']));
        }

        $res = pdo_update('diagnostic_service_user', array('family_id' => $family_id), array('ajy_uid' => $user_id));
        if (!$res) {
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

        if (!$family_id) {
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
        if (!$res) {
            return $this->errorJson('操作失败', array('status' => 0));
        }

        return $this->successJson('退出成功', array('status' => 1));

    }

    //关注我的用户列表
    public function followMe()
    {

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
    public function myFollow()
    {

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

    //积分记录
    public function pointLogs()
    {
        $member_id = \YunShop::app()->getMemberId();
        $pageSize = intval(\YunShop::request()->get('pagesize'));
        $pageSize = $pageSize ? $pageSize : $this->pagesize;

        $list = PointLog::getPointLogList($member_id)->paginate($pageSize);

        return $this->successJson('成功', $list);
    }

    //论坛帖子用户中心
    public function userPostCenter()
    {
        $uniacid = \YunShop::app()->uniacid;
        $user_id = \YunShop::app()->getMemberId();

        $uid = intval(\YunShop::request()->user_id); //用户个人中心id
        if ($uid <= 0) {
            return $this->errorJson('用户id不存在');
        }

        $user = pdo_get('diagnostic_service_user', array('ajy_uid' => $uid), array('nickname', 'avatarurl', 'ajy_uid', 'account'));

        if ($user) {
            if ($uid == $user_id) {
                $user['is_self'] = 1;
            } else {
                $user['is_self'] = 0;
                $follow = pdo_get('diagnostic_service_user_follow', array('uniacid' => $uniacid, 'user_id' => $user_id, 'fans_id' => $uid));
                $fan = pdo_get('diagnostic_service_user_follow', array('uniacid' => $uniacid, 'user_id' => $uid, 'fans_id' => $user_id));
                if ($follow) {
                    if ($fan) {
                        $user['is_follow'] = 3; //互相关注
                    } else {
                        $user['is_follow'] = 2; //已关注
                    }
                } else {
                    $user['is_follow'] = 1; //未关注
                }
            }
            //用户关注数(此用户关注了那些用户）
            $userFollowCount = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('diagnostic_service_user_follow') . " WHERE user_id = :user_id AND uniacid = :uniacid", array(':user_id' => $uid, ':uniacid' => $uniacid));

            //用户粉丝数（关注者id是此用户id)
            $userFansCount = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('diagnostic_service_user_follow') . " WHERE fans_id = :user_id AND uniacid = :uniacid", array(':user_id' => $uid, ':uniacid' => $uniacid));

            //用户帖子获赞数
            $userAcquireLikeCount = pdo_fetchcolumn("SELECT SUM(like_nums) FROM " . tablename('diagnostic_service_post') . " WHERE user_id = :user_id AND uniacid = :uniacid", array(':user_id' => $uid, ':uniacid' => $uniacid));

            //用户发布帖子数量
            $userPostCount = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('diagnostic_service_post') . " WHERE user_id = :user_id AND uniacid = :uniacid AND status = 1", array(':user_id' => $uid, ':uniacid' => $uniacid));

            //用户喜欢帖子数
            $userLikePostCount = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('diagnostic_service_post_like') . " l LEFT JOIN " . tablename('diagnostic_service_post') . " p ON l.post_id = p.id  WHERE l.user_id = :user_id AND l.uniacid = :uniacid AND p.status = 1 ", array(':user_id' => $uid, ':uniacid' => $uniacid));

            $user['followCount'] = intval($userFollowCount); //关注数
            $user['fansCount'] = intval($userFansCount); //粉丝数
            $user['acquireLikeCount'] = intval($userAcquireLikeCount); //被赞数
            $user['postCount'] = intval($userPostCount); //帖子发布数
            $user['likePostCount'] = intval($userLikePostCount); //点赞帖子数

            return $this->successJson('获取成功', $user);
        } else {
            return $this->errorJson('获取用户信息失败');
        }
    }


    public function userPostList()
    {
        //个人中心用户发布和喜欢列表
        $uniacid = \YunShop::app()->uniacid;
        $user_id = \YunShop::app()->getMemberId();

        $uid = intval(\YunShop::request()->user_id); //用户个人中心id
        if ($uid <= 0) {
            return $this->errorJson('用户id不存在');
        }

        $pindex = intval(\YunShop::request()->page) ? intval(\YunShop::request()->page) : 1; //初始页
        $psize = $this->pagesize;
        $is_like_list = intval(\Yunshop::request()->is_like_list);

        //用户话题列表(分页)
        $query = load()->object('query');

        if ($is_like_list) {
            //此用户点赞帖子列表
            $posts = $query->from('diagnostic_service_post', 'p')
                ->select('p.id', 'p.title', 'p.images', 'p.video', 'p.video_thumb', 'p.video_size', 'p.image_size', 'p.view_nums', 'p.comment_nums', 'p.like_nums', 'u.nickname', 'u.avatarurl')
                ->leftjoin('diagnostic_service_post_like', 'l')
                ->on('l.post_id', 'p.id')
                ->leftjoin('diagnostic_service_user', 'u')
                ->on('p.user_id', 'u.ajy_uid')
                ->where(array('l.uniacid' => $uniacid, 'l.user_id' => $uid, 'p.status' => 1))
                ->orderby('l.create_time', 'DESC')
                ->page($pindex, $psize)
                ->getall();

            $total = intval($query->getLastQueryTotal()); //总条数
            $totalPage = intval(($total + $psize - 1) / $psize);

        } else {
            //此用户发布的话题列表
            $posts = $query->from('diagnostic_service_post', 'p')
                ->select('p.id', 'p.title', 'p.images', 'p.video', 'p.video_thumb', 'p.video_size', 'p.image_size', 'p.view_nums', 'p.comment_nums', 'p.like_nums', 'u.nickname', 'u.avatarurl')
                ->leftjoin('diagnostic_service_user', 'u')
                ->on('p.user_id', 'u.ajy_uid')
                ->where(array('p.uniacid' => $uniacid, 'p.status' => 1, 'p.user_id ' => $uid))
                ->orderby('p.create_time', 'DESC')
                ->page($pindex, $psize)
                ->getall();
            $total = intval($query->getLastQueryTotal()); //总条数
            $totalPage = intval(($total + $psize - 1) / $psize);
        }

        foreach ($posts as $k => $v) {
            $posts[$k]['images'] = json_decode($v['images'], true);
            $posts[$k]['video_size'] = json_decode($v['video_size'], true);
            $posts[$k]['image_size'] = json_decode($v['image_size'], true);
            //$posts[$k]['time'] = $this->dataarticletime($v['create_time']);
            $posts[$k]['heat'] = 10 + ($v['like_nums'] * 30) + ($v['comment_nums'] * 50) + ($v['view_nums'] * 10);
        }

        $res = compact('total', 'totalPage', 'posts');
        return $this->successJson('成功获取此用户帖子列表', $res);
    }


    //用户搜索记录
    public function mySerch()
    {
        //我的搜索记录
        $uniacid = \YunShop::app()->uniacid;
        $user_id = \YunShop::app()->getMemberId();

        $myserch = pdo_getall('diagnostic_service_search', array('user_id' => $user_id, 'uniacid' => $uniacid, 'is_delete' => 0, 'is_success' => 1), array('id', 'keywords'), '', 'add_time DESC', array(1, 8));

        return $this->successJson('success', $myserch);
    }

    //清除用户搜索记录
    public function deleteSerch()
    {
        $uniacid = \YunShop::app()->uniacid;
        $user_id = \YunShop::app()->getMemberId();

        $result = pdo_update('diagnostic_service_search', array('is_delete' => 1), array('user_id' => $user_id, 'uniacid' => $uniacid, 'is_delete' => 0, 'is_success' => 1));
        if ($result) {
            return $this->successJson('清除成功');
        } else {
            return $this->errorJson('清除失败');
        }

    }

    //显示用户收藏列表 to_type_id 1穴位，2病例 3文章
    public function userCollect()
    {
        $uniacid = \YunShop::app()->uniacid;
        $user_id = \YunShop::app()->getMemberId();
        $to_type_id = intval(\YunShop::request()->to_type_id);
        if (empty($to_type_id)) {
            return $this->errorJson('to_type_id参数错误');
        }

        $collects = array();
        if ($to_type_id == 1) {
            //穴位收藏列表
            $acupointCollects = pdo_getall('diagnostic_service_collect', array('user_id' => $user_id, 'uniacid' => $uniacid, 'to_type_id' => 1), array('info_id', 'title', 'description', 'image', 'to_type_id'));
            $collects = $acupointCollects;

        } elseif ($to_type_id == 2) {
            //病例收藏列表
            $caseCollects = pdo_getall('diagnostic_service_collect', array('user_id' => $user_id, 'uniacid' => $uniacid, 'to_type_id' => 2), array('info_id', 'title', 'description', 'image', 'to_type_id'));
            $collects = $caseCollects;
        } elseif ($to_type_id == 3) {
            //文章收藏
            $articleCollects = pdo_getall('diagnostic_service_collect', array('user_id' => $user_id, 'uniacid' => $uniacid, 'to_type_id' => 3), array('info_id', 'title', 'description', 'image', 'to_type_id'));
            $collects = $articleCollects;
        } else {
            //所有收藏列表
            //$yzgoods_collects = pdo_getall('yz_member_favorite', array('member_id' => $user_id, 'deleted_at' => null), array());
            $collects = pdo_getall('diagnostic_service_collect', array('user_id' => $user_id, 'uniacid' => $uniacid), array('info_id', 'title', 'description', 'image', 'to_type_id'));
        }

        return $this->successJson('ok', $collects);
    }

    //验证微信地址是否存在芸众地址数据库中
    public function userAddressValidate()
    {
        $uniacid = \YunShop::app()->uniacid;
        $user_id = \YunShop::app()->getMemberId();
        $province = \YunShop::request()->province; //省份
        $city = \YunShop::request()->city;//市
        $district = \YunShop::request()->district;//区、县

        $data = array();
        if (!$province || !$city || !$district) {
            return $this->errorJson('参数有误！');
        }
        if ($province) {
            $data['province'] = $province;
        }
        if ($city) {
            $data['city'] = $city;
        }
        if ($district) {
            $data['district'] = $district;
        }

        //判断有没有省份
        $has_province = pdo_get('yz_address', array('areaname' => trim($province), 'parentid' => 0, 'level' => 1));
        if ($has_province) {
            //判断有没有市
            $has_city = pdo_get('yz_address', array('areaname' => trim($city), 'parentid' => $has_province['id'], 'level' => 2));
            if ($has_city) {
                //判断有没有 区、县
                $has_district = pdo_get('yz_address', array('areaname' => trim($district), 'parentid' => $has_city['id'], 'level' => 3));
                if ($has_district) {
                    return $this->successJson('验证成功', $data);
                }
            }
        }

        //验证失败 增加地址记录表
        $data['uniacid'] = $uniacid;
        $data['uid'] = $user_id;
        pdo_insert('yz_address_validate', $data);
        return $this->errorJson('地址正在努力收集中，请手动添加！');
    }

    public function zmCode()
    {
        global $_W;
        //防伪码调用
        $uniacid = \YunShop::app()->uniacid;
        $user_id = \YunShop::app()->getMemberId();

        $i = $this->xc_uniacid;    //仙草集团公众号id
        $do = 'a'; //a 方法名
        $co = \YunShop::request()->co;  //co: 防伪码 传过来的参数
        $ip = $_W['clientip'];   //ip： 用户查询ip
        $address = \YunShop::request()->address;

        $key = 'qd4bPIyzLrUpc2mey4dBTq9BODoWmakT'; //通信秘钥zm微防伪后台设置的key

        if (!$co) {
            return $this->errorJson('防伪码不能为空');
        }
        if (!$ip) {
            return $this->errorJson('ip不能为空');
        }

        $user = pdo_get('diagnostic_service_user', array('ajy_uid' => $user_id)); //小程序用户信息
        $unionid = $user['unionid']; //小程序公众平台unionid

        //获得仙草集团公众号粉丝信息
        $xc_fans = pdo_get('mc_mapping_fans', array('uniacid' => $i, 'unionid' => $user['unionid']));

        $xc_fans['gender'] = $user['gender']; //会员性别
        $xc_fans['os'] = $_W['os'];
        $xc_fans['container'] = $_W['container'];

        $data = [
            'c' => 'entry', //固定传入参数（不得改动）
            'do' => $do,  //固定传入参数（不得改动）
            'm' => 'zmcn_fw',    //固定传入参数（不得改动）
            'i' => $i,           //变量传入--公众号ID（这个数字就是当前公众号ID，可直接用）
            'co' => $co,    //变量传入--防伪码
            'ip' => $ip,    //变量传入--客户IP（浏览网页之人IP，用于记录查询者地区）
            'ke' => MD5($i . $co . $ip . $key), //加密鉴权（注意按示例排序）
            'xc_fans' => json_encode($xc_fans),  //仙草集团粉丝信息
            'address' => $address  //经纬度地址
        ];

        //https://www.aijuyi.net/app/index.php?i=39&co=FW2763150201310708&ip=127.0.0.1&c=entry&ke=546566&m=zmcn_fw&do=a
        $url = $_W['siteroot'] . "app/index.php";  //请求地址url拼接

        $resJson = $this->https_request($url, $data);

        if (!$resJson) {
            return $this->errorJson('请求失败');
        }

        \Log::info('--------防伪查询接口请求返回值-------', $resJson);

        if ($resJson['content'] == 0) {
            return $this->successJson('success', json_decode($resJson, true));
        } else {
            $errCode = $resJson;
            // 1888：服务器API接口关闭
            //1839：非授权IP
            //1877：防伪码格式不对
            //1876：加密串格式不对
            //1875：加密鉴权失败
            //1899：防伪码验证错误
            //1866：没有该批次或批号出错
            if ($resJson == 1888) {
                return $this->errorJson('服务器API接口关闭', $resJson);
            } elseif ($resJson == 1839) {
                return $this->errorJson('非授权IP', $resJson);
            } elseif ($resJson == 1877) {
                return $this->errorJson('防伪码格式不对', $resJson);
            } elseif ($resJson == 1876) {
                return $this->errorJson('加密串格式不对', $resJson);
            } elseif ($resJson == 1875) {
                return $this->errorJson('加密鉴权失败', $resJson);
            } elseif ($resJson == 1899) {
                return $this->errorJson('防伪码验证错误', $resJson);
            } elseif ($resJson == 1866) {
                return $this->errorJson('没有该批次或批号出错', $resJson);
            } else {
                return $this->errorJson('未知error', $resJson);
            }

        }
    }

    //检测企业微信加入状态
    public function qyWechatJoinStatus()
    {
        $uniacid = \YunShop::app()->uniacid;
        $user_id = \YunShop::app()->getMemberId();

        $cachekey = 'joinChatStatus' . $uniacid . $user_id;
        $cache = cache_load($cachekey);
        if ($cache) {
            $chatStatus = $cache['chat_status'];
            return $this->successJson('ok', array('chat_status' => $chatStatus));
        }

        $chatStatus = 0;
        $qywechat_user = pdo_get('yz_member_qywechat', array('member_id' => $user_id));
        if (!empty($qywechat_user)) {
            $qyWechatSetting = \Setting::get('plugin.enterprise-wechat');
            if ($qyWechatSetting && $qyWechatSetting['corpid'] && $qyWechatSetting['secret']) {
                $chatStatus = $this->chekJoinChat($qyWechatSetting['corpid'], $qyWechatSetting['secret'], $qywechat_user);
            }
        }
        cache_write($cachekey, array('chat_status' => $chatStatus));
        return $this->successJson('ok', array('chat_status' => $chatStatus));
    }

    protected function chekJoinChat($corpId, $corpSecret, $qywechat_user)
    {
        $chatStatus = 0;
        $accessToken = QyWeChatService::getEnterpriseAccessToken($corpId, $corpSecret);
        if ($accessToken) {
            //群列表 https://qyapi.weixin.qq.com/cgi-bin/externalcontact/groupchat/list?access_token=ACCESS_TOKEN
            $chatListUrl = "https://qyapi.weixin.qq.com/cgi-bin/externalcontact/groupchat/list?access_token={$accessToken}";
            $postData = array(
                "status_filter" => 0,
                "owner_filter" => array(),
                "offset" => 0,
                "limit" => 100
            );
            $info = ihttp_request($chatListUrl, json_encode($postData));
            $chatList = @json_decode($info['content'], true);
            if ($chatList['errcode'] == 0) {
                foreach ($chatList['group_chat_list'] as $chat) {
                    $chatInfoUrl = "https://qyapi.weixin.qq.com/cgi-bin/externalcontact/groupchat/get?access_token={$accessToken}";
                    $data = array(
                        'chat_id' => $chat['chat_id']
                    );
                    $chatInfo = ihttp_request($chatInfoUrl, json_encode($data));
                    $chatInfo = @json_decode($chatInfo['content'], true);
                    if ($chatInfo['errcode'] == 0) {
                        foreach ($chatInfo['group_chat']['member_list'] as $member) {
                            if ($member['type'] == 2) {
                                if ($member['unionid'] == $qywechat_user['unionid']) {
                                    $chatStatus = 1;
                                }
                            }
                        }
                    }
                }
            }
        }
        return $chatStatus;
    }

    //curl 防伪查询方法post
    private function https_request($url, $data = null)
    {
        if (function_exists('curl_init')) {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($curl, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
            if (!empty($data)) {
                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            }
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $output = curl_exec($curl);
            curl_close($curl);
            return $output;
        } else {
            return false;
        }
    }

}
