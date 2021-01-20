<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2018/10/23
 * Time: 下午4:39
 */

namespace Yunshop\Mryt\admin;


use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use app\common\models\Member;
use app\common\models\user\UniAccountUser;
use Yunshop\Mryt\models\MrytLevelModel;
use Yunshop\Mryt\models\MrytMemberModel;
use Yunshop\Mryt\models\weiqing\UsersPermission;
use Yunshop\Mryt\models\weiqing\WeiQingUsers;
use Yunshop\Mryt\services\AwardService;
use Yunshop\Mryt\services\CommonService;

class MemberController extends BaseController
{
    protected $pageSize = 10;

    /**
     * @return string
     * @throws \Throwable
     */
    public function index()
    {
        $parames = \YunShop::request();
        $set = CommonService::getSet();

        if (strpos($parames['searchtime'], '×') !== FALSE) {
            $search_time = explode('×', $parames['searchtime']);

            if (!empty($search_time)) {
                $parames['searchtime'] = $search_time[0];

                $start_time = explode('=', $search_time[1]);
                $end_time = explode('=', $search_time[2]);

                $parames->times = [
                    'start' => $start_time[1],
                    'end' => $end_time[1]
                ];
            }
        }

        $list = MrytMemberModel::searchAgency($parames);

        $list = $list->paginate($this->pageSize);
        $list = $list->toArray();
        $pager = PaginationHelper::show($list['total'], $list['current_page'], $this->pageSize);

        $starttime =  $endtime = time();

        if (isset($parames['searchtime']) &&  $parames['searchtime'] == 1) {
            if ($parames['times']['start'] != '请选择' && $parames['times']['end'] != '请选择') {
                $starttime = strtotime($parames['times']['start']);
                $endtime = strtotime($parames['times']['end']);
            }
        }

        $level = MrytLevelModel::getList()->get();

        return view('Yunshop\Mryt::admin.memberlist', [
            'list' => $list,
            'endtime' => $endtime,
            'starttime' => $starttime,
            'total' => $list['total'],
            'pager' => $pager,
            'level' => $level,
            'set'   => $set,
            'total' => $list['current_page'] <= $list['last_page'] ? $list['total'] : 0,
            'request' => \YunShop::request(),
        ])->render();
    }

    public function add()
    {
        $level_list = MrytLevelModel::getList()->get();
        $set = \Setting::get('plugin.mryt_set');
        $default_level = $set['default_level'] ?: 'VIP会员';

        if (\Request::getMethod() == 'POST') {
            $data = \YunShop::request()->team;
            $member = MrytMemberModel::getMemberInfoByUid($data['uid']);

            if($member){
                return $this->message('添加失败,此会员已存在', '', 'error');
            }

            $agencl_model = new MrytMemberModel();
            $agencl_model->uniacid = \YunShop::app()->uniacid;
            $agencl_model->realname = $data['realname'];
            $agencl_model->mobile = $data['contact'];
            $agencl_model->uid = $data['uid'];
            $agencl_model->level = $data['level'];
            $agencl_model->username = $data['username'] ?: null;
            $agencl_model->password = $data['password'] ?: null;

            if ($agencl_model->save()) {
                if ($agencl_model->username && $agencl_model->password) {
                    $this->register($agencl_model);
                }

                $res = new AwardService($agencl_model->uid, $agencl_model->uniacid, '');
                $res->upgrateAward();
                return $this->message('操作成功', yzWebUrl('plugin.mryt.admin.member'));
            }
        }

        return view('Yunshop\Mryt::admin.member-add', [
            'level' => $level_list,
            'default_level' => $default_level
        ])->render();
    }

    /**
     * @return string
     * @throws \Throwable
     */
    public function editPassword()
    {
        $uid = \YunShop::request()->id;
        $username = '';
        $user_uid = '';
        $mryt_member = MrytMemberModel::getMemberInfoByUid($uid);
        if ($mryt_member) {
            $username = $mryt_member->username;
            $user_uid = $mryt_member->user_uid;
        }
        if (\Request::getMethod() == 'POST') {
            $data = \Yunshop::request()->data;
            //添加账户
            if (\Yunshop::request()->is_add) {
                $mryt_member->username = $data['username'];
                $mryt_member->password = $data['password'];
                $mryt_member->save();
                $this->register($mryt_member);
                return $this->message('操作成功', yzWebUrl('plugin.mryt.admin.member'));
            }

            //修改密码
            $mryt_member->password = $data['password'];
            $mryt_member->save();
            $user = WeiQingUsers::getUserByUid($mryt_member->user_uid)->first();
            $password = user_hash($data['password'], $user->salt);
            $user->password = $password;
            $user->save();
            return $this->message('修改密码成功', yzWebUrl('plugin.mryt.admin.member'));

        }
        return view('Yunshop\Mryt::admin.edit_password',[
            'nickname' => \Yunshop::request()->nickname,
            'username' => $username,
            'user_uid' => $user_uid,
        ])->render();
    }

    public function searchAgency()
    {
        $keyword = \YunShop::request()->keyword;

        $member = Member::getMemberInfoByNickName($keyword);

        return view('Yunshop\TeamDividend::admin.query', [
            'members' => $member
        ])->render();
    }

    public function export()
    {
        $file_name = date('Ymdhis', time()) . '会员导出';
        $set = \Setting::get('plugin.mryt_set');
        $default_level = $set['default_level'] ?: 'VIP会员';

        $parames = \YunShop::request();

        if (strpos($parames['searchtime'], '×') !== FALSE) {
            $search_time = explode('×', $parames['searchtime']);

            if (!empty($search_time)) {
                $parames['searchtime'] = $search_time[0];

                $start_time = explode('=', $search_time[1]);
                $end_time = explode('=', $search_time[2]);

                $parames->times = [
                    'start' => $start_time[1],
                    'end' => $end_time[1]
                ];
            }
        }

        $list = MrytMemberModel::searchAgency($parames)
            ->get()
            ->toArray();

        $export_data[0] = ['ID', '会员信息', '等级名称', '累计直推奖', '累计团队管理奖', '累计团队奖', '累计感恩奖', '累计育人奖', '累计总奖励', '是否签劳动合同'];

        foreach ($list as $key => $item) {

            if (!empty($item['status'])) {
                $status = '已签约';
            } else {
                $status = '未签约';
            }

            $level = $item['has_one_level']['level_name'] ?: $default_level;
            $export_data[$key + 1] = [$item['id'],$item['has_one_member']['nickname'],$level, $item['referral_total'],$item['order_team_total'],$item['team_total'],$item['thankful_total'],$item['parenting_total'],$item['referral_total'] + $item['order_team_total'] + $item['team_total'] + $item['thankful_total'] + $item['parenting_total'],$status];
        }

        \Excel::create($file_name, function ($excel) use ($export_data) {
            // Set the title
            $excel->setTitle('Office 2005 XLSX Document');

            // Chain the setters
            $excel->setCreator('芸众商城')
                ->setLastModifiedBy("芸众商城")
                ->setSubject("Office 2005 XLSX Test Document")
                ->setDescription("Test document for Office 2005 XLSX, generated using PHP classes.")
                ->setKeywords("office 2005 openxml php")
                ->setCategory("report file");

            $excel->sheet('info', function ($sheet) use ($export_data) {
                $sheet->rows($export_data);
            });


        })->export('xls');
    }
    public function deletedAgency()
    {
        $id = \YunShop::request()->id;
        $agency = MrytMemberModel::find($id);
        if(!$agency) {
            return $this->message('无此会员或已经删除','','error');
        }
        $result = MrytMemberModel::daletedAgency($id);
        if($result) {
            return $this->message('删除成功',Url::absoluteWeb('plugin.mryt.admin.member'));
        }else{
            return $this->message('删除失败','','error');
        }
    }

    public function change()
    {
        $id = \YunShop::request()->id;
        $agency = MrytMemberModel::find($id);
        $agency->level = \YunShop::request()->value;

        $agency->save();
    }

    public function verifyAccount()
    {
        $username = \YunShop::request()->username;
        $result = WeiQingUsers::getUserByUserName($username)->first();
        if ($result) {
//            return $this->message('添加失败,此会员账号系统已存在，请更换其他账号', '', 'error');
            echo show_json(-1, [
                'msg' => '账号已存在'
            ]);
            exit;
        }
        echo show_json(1);
        exit;
    }

    private function register($agencl_model)
    {
        $user_uid = user_register(array('username' => $agencl_model->username, 'password' => $agencl_model->password));

        WeiQingUsers::updateType($user_uid);

        $uni_model = new UniAccountUser();
        $uni_model->fill([
            'uid'       => $user_uid,
            'uniacid'   => \YunShop::app()->uniacid,
            'role'      => 'clerk'
        ]);
        $uni_model->save();

        $agencl_model->user_uid = $user_uid;
        $agencl_model->save();

        (new UsersPermission())->addPermission($user_uid);
    }
}