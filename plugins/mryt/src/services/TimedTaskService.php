<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2018/11/5
 * Time: 上午11:54
 */

namespace Yunshop\Mryt\services;


use app\common\facades\Setting;
use app\common\models\UniAccount;
use Yunshop\Mryt\models\MrytLevelModel;
use Yunshop\Mryt\models\MrytMemberAddUpVipModel;
use Yunshop\Mryt\models\MrytMemberModel;

class TimedTaskService
{
    public function handle()
    {
        $search_month = [
            date('Ym', strtotime('-1 month')),
            date('Ym', strtotime('-2 month')),
            date('Ym', strtotime('-3 month'))
        ];

        $nums = 60;

        $uniAccount = UniAccount::get();

        foreach ($uniAccount as $u) {
            $contract = [];
            $contract_level = [];

            \YunShop::app()->uniacid = $u->uniacid;
            Setting::$uniqueAccountId = $u->uniacid;

            $set = Setting::get('plugin.mryt_set');

            if (isset($set['switch']) && $set['switch'] == 1) {
                //检索设置签署合同的等级
                $level = MrytLevelModel::getList()->get();

                if (!is_null($level)) {
                    foreach ($level as $item) {
                        if (1 == $item->contract) {
                            $contract_level[] = $item->id;
                        }
                    }
                }

                 //检索前3个月达标会员&未签合同
                $member = MrytMemberAddUpVipModel::getMemberForReachTheStandard($search_month, $contract_level, $nums);

                if (!is_null($member)) {
                    foreach ($member as $item) {
                        $contract[] = $item->uid;
                    }
                }

                MrytMemberModel::updateContractOfMember($contract);
            }
        }
    }
}