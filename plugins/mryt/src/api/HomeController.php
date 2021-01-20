<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2018/10/24
 * Time: 上午10:52
 */

namespace Yunshop\Mryt\api;


use app\common\components\ApiController;
use app\common\models\member\ChildrenOfMember;
use Yunshop\Mryt\common\models\MemberReferralAward;
use Yunshop\Mryt\common\models\MemberTeamAward;
use Yunshop\Mryt\common\models\OrderParentingAward;
use Yunshop\Mryt\common\models\OrderTeamAward;
use Yunshop\Mryt\common\models\TierAward;
use Yunshop\Mryt\models\MemberChildrenModel;
use Yunshop\Mryt\models\MemberParentModel;
use Yunshop\Mryt\models\MrytLevelModel;
use Yunshop\Mryt\models\MrytLevelUpgradeModel;
use Yunshop\Mryt\models\MrytMemberModel;
use Yunshop\Mryt\services\CommonService;
use Yunshop\SalesCommission\models\SalesCommission;

class HomeController extends ApiController
{

    // plugin.mryt.api.home.index
    public function index()
    {
        $member_id = \YunShop::app()->getMemberId();
        $mryt_member = MrytMemberModel::with([
            'hasOneLevel',
            'hasOneMember' => function ($member) {
                $member->select('uid', 'realname', 'nickname', 'avatar');
            },
            'hasOneUpgradeSet',
        ])
            ->where('uid', $member_id)
            ->first();
        // 累计团队管理奖
        $order_team_total = OrderTeamAward::select(['uid', 'amount'])
            ->where('uid', $member_id)
            ->sum('amount');
        // 累计团队奖
        $team_total = MemberTeamAward::select(['uid', 'amount'])
            ->where('uid', $member_id)
            ->where('award_type', 1)
            ->sum('amount');
        // 累计感恩奖
        $thankful_total = MemberTeamAward::select(['uid', 'amount'])
            ->where('uid', $member_id)
            ->where('award_type', 2)
            ->sum('amount');
        // 累计育人奖
        $parenting_total = OrderParentingAward::select(['uid', 'amount'])
            ->where('uid', $member_id)
            ->sum('amount');
        // 累计直推奖
        $referral_total = MemberReferralAward::select(['uid', 'amount'])
            ->where('uid', $member_id)
            ->sum('amount');
        // 累计平级奖
        $tier_total = TierAward::select(['uid', 'amount'])
            ->where('uid', $member_id)
            ->sum('amount');
        // 累计奖励总金额
        $all_amount = $referral_total + $parenting_total + $thankful_total + $team_total + $order_team_total + $tier_total;

        $set = CommonService::getSet();
        $plugin_name = $set['name'];


        $level_name = $set['default_level'];
        $team_manage_ratio = 0;
        $team = 0;
        $thankful = 0;
        $train_ratio = 0;
        $direct = $set['push_prize'];
        $tier = 0;
        $tier_amount = 0;
        if ($mryt_member->hasOneLevel) {
            $level_name = $mryt_member->hasOneLevel->level_name;
            $team_manage_ratio = $mryt_member->hasOneLevel->team_manage_ratio;
            $team = $mryt_member->hasOneLevel->team;
            $thankful = $mryt_member->hasOneLevel->thankful;
            $train_ratio = $mryt_member->hasOneLevel->train_ratio;
            $direct = $mryt_member->hasOneLevel->direct;
            $tier = $mryt_member->hasOneLevel->tier;
            $tier_amount = $mryt_member->hasOneLevel->tier_amount;
        }

        //新增
        $memberParentModel = new MemberParentModel();
        $team_cost_count = [];
        $team_statistics = [];
        $team_statistics[] = [
            'name' => '直推会员数量',
            'count' => $memberParentModel->getDirectMemberByMemberId($member_id)->count() ?: 0,
            'type' => 1,
            'level' => 0
        ];

        $team_statistics[] = $team_count = [
            'name' => '团队会员数量',
            'count' => $memberParentModel->getTeamMemberByMemberId($member_id)->count() ?: 0,
            'type' => 2,
            'level' => 0
        ];



        $levels = MrytLevelModel::orderBy('level_weight', 'asc')->get();
        foreach ($levels as $level) {
            $team_statistics[] = [
                'name' => '直推'.$level->level_name.'数量',
                'count' => $memberParentModel->getDirectLevelMember($member_id, $level->id)->count() ?: 0,
                'type' => 1,
                'level' => $level->id,
            ];
            $team_statistics[] = [
                'name' => '团队'. $level->level_name . '数量',
                'count' => $memberParentModel->getTeamLevelMember($member_id, $level->id)->count() ?: 0,
                'type' => 2,
                'level' => $level->id,
            ];
        }


        if ($mryt_member->hasOneLevel) {
            $level_weight = $mryt_member->hasOneLevel->level_weight;
        } else {
            $level_weight = 0;
        }
        $max_weight = $levels->max('level_weight');
        //最高级显示自己
        if ($max_weight == $level_weight) {
            $level_set = MrytLevelUpgradeModel::uniacid()->where('level_id', $mryt_member->hasOneLevel->id)->first();
        } else {
            $level_v1 = MrytLevelModel::orderBy('level_weight', 'asc')
                ->where('level_weight','>',$level_weight)
                ->with('hasOneUpgradeSet')
                ->first();
            $level_set = $level_v1->hasOneUpgradeSet;
        }

        $level_set = unserialize($level_set->parase);
        if ($level_set[0]['team_cost_count']) {
            $team_cost_count = [
                'name' => '团队中个人销售佣金达到'.$level_set[1]['team_cost_count'].'元的VIP人数',
                'count' => $this->teamCostCount($member_id, $level_set),
            ];
        }


        $data = [
            'member' => [
                'name' => $mryt_member->hasOneMember->nickname,
                'id' => $mryt_member->uid,
                'level' => $level_name,
                'avatar' => $mryt_member->hasOneMember->avatar
            ],
            // 团队管理奖 比例
            'team_rate' => $mryt_member->hasOneLevel->team_manage_ratio,
            // 团队奖
            'team_amount' => $mryt_member->hasOneLevel->team,
            // 感恩奖
            'thank_amount' => $mryt_member->hasOneLevel->thankful,
            // 育人奖
            'rate' => $train_ratio,
		    // 直推奖
		    'amount' => $direct,
		    // 平级奖层级
            'tier' => $tier,
            // 平级奖金额
            'tier_amount' => $tier_amount,
		    // 累计总金额
		    'all_amount' => $all_amount,
		    // 自定义名称
		    'plugin_name' => $plugin_name,
            'rewards' => [
                [
                    'reward_name' => $set['teammanage_name'],
                    'reward_amount' => $order_team_total,
                    'icon' => 'mryt_a',
                    'identifying' => 'teamManagement'
                ],
                [
                    'reward_name' => $set['team_name'],
                    'reward_amount' => $team_total,
                    'icon' => 'mryt_b',
                    'identifying' => 'team'
                ],
                [
                    'reward_name' => $set['thanksgiving_name'],
                    'reward_amount' => $thankful_total,
                    'icon' => 'mryt_c',
                    'identifying' => 'thanks'
                ],
                [
                    'reward_name' => $set['parenting_name'],
                    'reward_amount' => $parenting_total,
                    'icon' => 'mryt_d',
                    'identifying' => 'education'
                ],
                [
                    'reward_name' => $set['referral_name'],
                    'reward_amount' => $referral_total,
                    'icon' => 'mryt_e',
                    'identifying' => 'immediate'
                ],
                [
                    'reward_name' => $set['tier_name'],
                    'reward_amount' => $tier_total,
                    'icon' => 'mryt_e',
                    'identifying' => 'tier'
                ],
            ],

            //团队统计
            'team_statistics' => $team_statistics,
            'team_cost_count' => $team_cost_count,
            'set' => $set,

        ];

        return $this->successJson('ok', $data);
    }

    public function getMemberList()
    {
        $pageSize = 20;
        $level_id = \YunShop::request()->level;
        $type = \YunShop::request()->type;
        $member_id = \YunShop::app()->getMemberId();
        $memberParentModel = new MemberParentModel();
        $level_name = '';

        $data = [];
        if (empty($level_id)) {
            if ($type == 1) {
                $data = $memberParentModel->getDirectMemberByMemberId($member_id)->orderBy('member_id','desc')
                    ->with('hasOneMember')->paginate($pageSize)->toArray();
                $level_name = '直推会员';
            }
            if ($type == 2) {
                $data = $memberParentModel->getTeamMemberByMemberId($member_id)->orderBy('member_id','desc')
                    ->with('hasOneMember')->paginate($pageSize)->toArray();
                $level_name = '团队会员';
            }

        } else {
            $level = MrytLevelModel::getLevelById($level_id);
            if ($type == 1) {
                $data = $memberParentModel->getDirectLevelMember($member_id, $level_id)->orderBy('member_id','desc')
                    ->with('hasOneMember')->paginate($pageSize)->toArray();
                $level_name = '直推'.$level->level_name;
            }
            if ($type == 2) {
                $data = $memberParentModel->getTeamLevelMember($member_id, $level_id)->orderBy('member_id','desc')
                    ->with('hasOneMember')->paginate($pageSize)->toArray();
                $level_name = '团队'.$level->level_name;
            }
        }

        return $this->successJson('ok', ['member' => $data,'level_name' => $level_name]);

    }

    /**
     * 团队销售金额、人数
     * @param $uid
     * @return bool
     */
    public function teamCostCount($uid, $level_set)
    {
        $team_cost_count = $level_set[1]['team_cost_count'];
        $children = MemberChildrenModel::uniacid()
            ->where('member_id', $uid)
            ->whereHas('hasOneMrytMember',function ($query) {
                $query->where('level',0);
            })
            ->whereHas('hasOneSalesCommission' , function($query) use($team_cost_count) {
                $query->selectRaw('sum(dividend_amount) as dividend')->having('dividend', '>=' ,$team_cost_count)->groupBy('member_id');
            })
            ->count();

        return $children;

    }
}