<?php
/**
 * Created by PhpStorm.
 * User: BC
 * Date: 2018/10/29
 * Time: 0:32
 */

namespace Yunshop\Mryt\services;


use app\backend\modules\charts\models\YzMember;
use app\common\models\Member;
use app\common\models\user\UniAccountUser;
use Yunshop\Mryt\admin\MemberController;
use Yunshop\Mryt\models\MrytLevelModel;
use Yunshop\Mryt\models\MrytLevelUpgradeModel;
use Yunshop\Mryt\models\MrytMemberModel;
use Yunshop\Mryt\models\weiqing\UsersPermission;
use Yunshop\Mryt\models\weiqing\WeiQingUsers;

class UpgradeService
{
    public static function getLevelUpgraded()
    {
        $result = MrytLevelUpgradeModel::uniacid()->orderBy('level_id', 'desc')->get()->toArray();
        $levelData = [];
        foreach ($result as $key => $level) {
            $levelData[$key] = [
                'id' => $level['id'],
                'level' => $level['level_id'],
                'upgraded' => unserialize($level['parase']),
            ];
        }
        return $levelData;
    }

    public static function upgrade($level, $memberId, $agent)
    {
        \Log::info('MRYT升级',$memberId);
        $set = \Setting::get('plugin.mryt_set');
        $newLevel = MrytLevelModel::getLevelByid($level['level']);
        $oldLevel = MrytLevelModel::getLevelByid($agent->level);

        if (!$oldLevel) {
            $oldLevel['level_name'] = 'VIP会员';
            $oldLevel['direct'] = $set['push_prize'] ? sprintf("%.2f",$set['push_prize']) : 0;
            $oldLevel['team_manage_ratio'] = 0;
            $oldLevel['train_ratio'] = 0;
            $oldLevel['thankful'] = 0;
            $oldLevel['team'] = 0;
        }
        $member = Member::where('uid', $memberId)->first();
        if($member){
            $noticeData = [
                'newLevel' => $newLevel,
                'oldLevel' => $oldLevel,
                'member' => $member,
            ];
            MessageService::upgrateMessage($newLevel['uniacid'],$noticeData, $memberId);
        }
        \Log::info('MRYT升级通知'.$memberId.'等级：',$level);
        MrytMemberModel::updatedLevelByMemberId($level['level'], $memberId);

        //添加商家账号
        if ($newLevel->is_username) {
            $agencl_model = MrytMemberModel::getMemberInfoByUid($memberId);
            if (!$agencl_model->user_uid) {
                $agencl_model->username = $newLevel->username ? $newLevel->username . $memberId : '合伙人'. $memberId;
                $agencl_model->password = $newLevel->password ?: 'ch123456';
                $agencl_model->save();
                $result = WeiQingUsers::getUserByUserName($agencl_model->username)->first();
                if (!$result) {
                    self::register($agencl_model);
                }
            }
        }
    }

    private static function register($agencl_model)
    {
//        $user_uid = user_register(array('username' => $agencl_model->username, 'password' => $agencl_model->password));

        global $_W;
        $user = array('username' => $agencl_model->username, 'password' => $agencl_model->password);

        if (empty($user) || !is_array($user)) {
            return 0;
        }
        if (isset($user['uid'])) {
            unset($user['uid']);
        }
        $user['salt'] = random(8);
        $user['password'] = sha1("{$user['password']}-{$user['salt']}-{$_W['config']['setting']['authkey']}");
        $user['joinip'] = '127.0.0.1';
        $user['joindate'] = time();
        $user['lastip'] = '127.0.0.1';
        $user['lastvisit'] = time();
        if (empty($user['status'])) {
            $user['status'] = 2;
        }
        if (empty($user['type'])) {
            $user['type'] = 1;
        }
        $wq_user = WeiQingUsers::create($user);
        $user_uid = $wq_user->uid;
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