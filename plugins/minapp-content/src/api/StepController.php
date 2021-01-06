<?php

namespace Yunshop\MinappContent\api;
use app\common\components\ApiController;
use app\frontend\modules\member\services\factory\MemberFactory;

class StepController extends ApiController
{
    private $pagesize = 10;

    public function index()
    {
        //用户今日可用步数/兑换最低步数接口
        $uniacid = \YunShop::app()->uniacid;
        $user_id = \YunShop::app()->getMemberId();
        $app_type = \YunShop::request()->app_type; //区分哪个小程序登录
        $code = \YunShop::request()->code;    //code
        $iv = \YunShop::request()->iv;  //偏移量
        $encryptedData = \YunShop::request()->encryptedData; //加密数据
        $type = \YunShop::request()->type;
        if (!$code) {
            return $this->errorJson( 'code不能为空');
        }
        if (!$iv || !$encryptedData) {
            return $this->errorJson('解密参数不能为空');
        }

        $member = MemberFactory::create($type);
        if($member == NULL){
            return $this->errorJson('type类型错误');
        }

        //获取session_key
        $info = $member->wxCode2SessionKey($code, $app_type);
        if ($info['errno'] != 0) {

            return $this->errorJson($info['msg'], ['status' => 1]);
        }

        $data = $info['res'];
        //步数数据近一月
        $stepsList = $data['stepInfoList'];

        //今日目前总步数
        $tody = date('Y-m-d', TIMESTAMP);
        $todySteps = $stepsList[30]['step'];

        //今日已兑换步数
        $exchangeLogs = pdo_getall('diagnostic_service_step_exchange', array('user_id' => $user_id, 'uniacid' => $uniacid, 'day' => $tody));
        $exchangeSteps = array_sum(array_column($exchangeLogs, 'steps'));
        //可用步数
        $usableSteps = intval($todySteps - $exchangeSteps);
        //兑换步数最低标准 fixBy-wk 20201021 新版首页二级页面 展示可用步数和步数最低兑换标准
        $system = pdo_get('diagnostic_service_system_step', array('id' => 1));
        $least_steps = intval($system['least_step']);
        $steps_data = [
            'usable_steps' => $usableSteps,
            'least_steps' => $least_steps
        ];
        return $this->successJson('ok', $steps_data);
    }


    //用户兑换接口
    public function exchange(){

        //步数兑换接口
        $uniacid = \YunShop::app()->uniacid;
        $user_id = \YunShop::app()->getMemberId();

        $steps = intval(\YunShop::request()->steps);

        $system = pdo_get('diagnostic_service_system_step', array('id' => 1));

        if ($steps < intval($system['least_step'])) {

            return $this->errorJson('可用步数达到 ' .$system['least_step']. ' 可兑换健康金');
        }

        $float_point = $steps * ($system['ratio'] / 1000); //获得兑换的积分数带余数

        $last_steps = ($float_point*1000) % 1000;   //获得步数余数

        $point = floor($float_point);   //向下取整健康金
        $exchange_steps = $steps-$last_steps; //兑换的步数

        if($point < 1){

            return $this->errorJson('步数兑换不足1健康金');
        }
        $before_point = pdo_getcolumn('mc_members', array('uid' => $user_id), 'credit1');
        $after_point = $before_point + $point;
        $data = array(
            'uniacid' => $uniacid,
            'user_id' => $user_id,
            'steps' => $exchange_steps,
            'point' => $point,
            'create_time' => TIMESTAMP,
            'day' => date('Y-m-d', TIMESTAMP)
        );
        pdo_begin();
        $res = pdo_insert('diagnostic_service_step_exchange', $data);
        if(!$res){
            pdo_rollback();
            return $this->errorJson('积分兑换记录失败');
        }

        $log = pdo_insert('yz_point_log', array(
            'uniacid' => $uniacid,
            'member_id' => $user_id,
            'point' => $point,
            'point_income_type' => 1, //增加减少
            'point_mode' => 127,       //变动类型 19签到 17活动 127步数兑换
            'before_point' => $before_point,
            'after_point' => $after_point,
            'remark' => '步数兑换积分：' . $point . '积分',  //备注
            'thirdStatus' => 1,
            'created_at' => TIMESTAMP,
            'updated_at' => TIMESTAMP
        ));
        if(!$log){
            pdo_rollback();
            return $this->errorJson('积分日志记录失败');
        }
        $re = pdo_update('mc_members', array('credit1 +=' => $point), array('uid' => $user_id));
        if(!$re){
            pdo_rollback();
            return $this->errorJson('更新积分失败');
        }
        pdo_commit();
        return $this->successJson('兑换成功', array('steps' => $exchange_steps, 'point' => $point));
    }

    public function exchangeSort(){
        //步数兑换排行榜

        $uniacid = \YunShop::app()->uniacid;
        $user_id = \YunShop::app()->getMemberId();
        $pindex = intval(\YunShop::request()->page) ? intval(\YunShop::request()->page) : 1; //初始页
        $psize = $this->pagesize; //每页条数
        $query = load()->object('query');
//暂未加当月 如果根据当月/一周 +时间戳限定
//查询本周排行榜
        $ftime = mktime(0, 0, 0, date("m"), date("d") - date("w") + 1, date("Y"));
        $ltime = mktime(23, 59, 59, date("m"), date("d") - date("w") + 7, date("Y"));
//获取本月起始时间戳和结束时间戳
        $beginThismonth = mktime(0, 0, 0, date('m'), 1, date('Y'));
        $endThismonth = mktime(23, 59, 59, date('m'), date('t'), date('Y'));

        $exchangeUsers = $query->from('diagnostic_service_step_exchange', 'e')
            ->select('e.user_id', 'u.nickname', 'u.avatarurl')
            ->leftjoin('diagnostic_service_user', 'u')
            ->on('e.user_id', 'u.ajy_uid')
            ->where(array('e.uniacid' => $uniacid, 'e.create_time >' => $beginThismonth))
            //->having('count(*) > ', 2)
            ->groupby('e.user_id')
            ->getall();

        foreach ($exchangeUsers as $k => $v) {
            $steps = pdo_get('diagnostic_service_step_exchange', array('user_id' => $v['user_id'], 'create_time >' => $beginThismonth), array('SUM(steps) AS steps', 'SUM(point) AS point'));
            $exchangeUsers[$k]['steps'] = $steps['steps'];
            $exchangeUsers[$k]['point'] = $steps['point'];

        }
        //根据兑换步数总和进行排序
        $exchangeSort = $this->arraySortByOneField($exchangeUsers, 'point');
        $mine = [];
        foreach ($exchangeSort as $key => $value) {
            $exchangeSort[$key]['sort'] = $key + 1;
            if ($value['user_id'] == $user_id) {
                $mine = $exchangeSort[$key];
            }
        }

        $total = intval(count($exchangeSort)); //总条数
        $total_page = intval(($total + $psize - 1) / $psize); //总页数
        $exchangeSort = array_slice($exchangeSort, ($pindex - 1) * $psize, $psize);

        return $this->successJson('ok', compact('mine', 'total', 'total_page', 'exchangeSort'));
    }

    //我的步数兑换记录
    public function exchangeLog()
    {
        $uniacid = \YunShop::app()->uniacid;
        $user_id = \YunShop::app()->getMemberId();
        $pindex = intval(\YunShop::request()->page) ? intval(\YunShop::request()->page) : 1; //初始页
        $psize = $this->pagesize; //每页条数

        $query = load()->object('query');
        $exchangeLogs = $query->from('diagnostic_service_step_exchange')
            ->select('steps', 'point', 'create_time', 'day')
            ->where(array('uniacid' => $uniacid, 'user_id' => $user_id))
            ->orderby('create_time', 'DESC')
            ->page($pindex, $psize)
            ->getall();
        $total = intval($query->getLastQueryTotal()); //总条数
        $total_page = intval(($total + $psize - 1) / $psize);

        return $this->successJson('ok', $exchangeLogs);

    }

    public function UserPoint()
    {
        $uniacid = \YunShop::app()->uniacid;
        $user_id = \YunShop::app()->getMemberId();

        $mc_member = pdo_get('mc_members', array('uid' => $user_id), array('credit1', 'credit2'));

        if ($mc_member) {
            $total = pdo_get('yz_point_log', array('uniacid' => $uniacid, 'member_id' => $user_id, 'point_income_type' => 1), array('SUM(point) AS total_point')); //收入
            $used = pdo_get('yz_point_log', array('uniacid' => $uniacid, 'member_id' => $user_id, 'point_income_type' => -1), array('SUM(point) AS total_point')); //支出
            $data = array(
                'total_point' => $total['total_point'] ? $total['total_point'] : '0.00',
                'used_point' => $used['total_point'] ? $used['total_point'] : '0.00',
                'credit1' => $mc_member['credit1'],
                'credit2' => $mc_member['credit2']
            );
            return $this->successJson('获取健康金成功', $data);
        } else {
            return $this->errorJson('获取失败');
        }
    }

    //根据字段对多维数组进行排序
    private function arraySortByOneField($data, $field, $sort = SORT_DESC)
    {
        $field = array_column($data, $field);
        array_multisort($field, $sort, $data);
        return $data;
    }

}
