<?php

/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/7/25
 * Time: 下午5:11
 */

namespace Yunshop\Mryt\job;


use app\common\models\member\ChildrenOfMember;
use app\common\models\member\ParentOfMember;
use EasyWeChat\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Yunshop\Mryt\services\UpgradeService;
use Yunshop\Mryt\models\MrytMemberModel;
use Yunshop\Mryt\services\UpgradeConditionsService;

class UpgradeByRegisterJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $uid;
    protected $levels;

    public function __construct($uid, $levels)
    {
        $this->uid = $uid;
        $this->levels = $levels;
    }

    public function handle()
    {
        $agentModel = MrytMemberModel::getMemberInfoByUid($this->uid);
        if (!$agentModel) {
            return;
        }
        $parent = ParentOfMember::uniacid()->where('member_id', $this->uid)->select('parent_id')->get()->toArray();
        $parent[] = ['parent_id' => $this->uid];
        foreach ($parent as $member) {
            $agentModel = MrytMemberModel::getMemberInfoByUid($member['parent_id']);
            if (empty($agentModel)) {
                continue;
            }
            $agent_level_weight = isset($agentModel->level) ? $agentModel->level : 0;
            foreach ($this->levels as $level) {
                if ($level['level'] <= $agent_level_weight) {
                    continue;
                }
                if (!$level['upgraded']) {
                    continue;
                }
                $is_upgrade = true;
                foreach ($level['upgraded'][0] as $upgradeType => $value) {
                    $function_name = Str::camel($upgradeType);
                    if(method_exists(new upgradeConditionsService(), $function_name)) {
                        $is_upgrade = upgradeConditionsService::$function_name($member['parent_id'], $level, false);
                        if (!$is_upgrade) {
                            break;
                        }
                    }
                }
                if ($is_upgrade) {
                    // 升级
                    UpgradeService::upgrade($level, $member['parent_id'], $agentModel);
                    break;
                }
            }
        }
    }
}