<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/11/21
 * Time: 9:06 AM
 */

namespace Yunshop\Mryt\admin;


use app\common\components\BaseController;
use app\common\models\Income;
use app\common\models\MemberShopInfo;
use app\common\services\member\MemberRelation;
use Yunshop\Mryt\common\models\MemberReferralAward;
use Yunshop\Mryt\common\models\MemberTeamAward;
use Yunshop\Mryt\models\MrytMemberModel;
use Yunshop\Mryt\services\TestService;
use Yunshop\Mryt\services\UpgrateAwardService;

class FixController extends BaseController
{
    public $transactionActions = ['*'];

    public function test1()
    {
        (new TestService(768, 2, 0))->handleAward();
        dd('ok');
        exit;
    }

    public function statistics()
    {
        $mryt_members = MrytMemberModel::select()->get();
        $mryt_members->each(function ($member) {
            $referrals = MemberReferralAward::where('uid', $member->uid)->sum('amount');
            if ($referrals != $member->direct) {
                dump("UID[{$member->uid}]直推奖[{$member->direct}]修改后[{$referrals}]");
                $member->direct = $referrals;
                $member->save();
            }
            $teams = MemberTeamAward::where('uid', $member->uid)->where('award_type', 1)->sum('amount');
            if ($teams != $member->team) {
                dump("UID[{$member->uid}]团队奖[{$member->team}]修改后[{$teams}]");
                $member->team = $teams;
                $member->save();
            }
            $thankful = MemberTeamAward::where('uid', $member->uid)->where('award_type', 2)->sum('amount');
            if ($thankful != $member->thankful) {
                dump("UID[{$member->uid}]感恩奖[{$member->thankful}]修改后[{$thankful}]");
                $member->thankful = $thankful;
                $member->save();
            }
        });
        dump('ok');
    }

    public function income()
    {
        $incomes = Income::select()->whereIn('type_name', ['直推奖', '团队奖', '感恩奖'])->where('status', 0)->uniacid()->get();
        $num = 0;
        $sum = 0;
        foreach ($incomes as $income) {
            $class = $income->incometable_type;
            $ret = $class::select()->where('id', $income->incometable_id)->first();
            if (!$ret) {
                $num += 1;
                $sum += $income->amount;
                dump("删除收入id[{$income->id}]金额[{$income->amount}]");
                $income->delete();
            }
        }
        dump($num);
        dump($sum);
        dump('ok');
    }

    public function test()
    {
        dd('222');
        exit;
        $ret = MemberShopInfo::select(['member_id', 'parent_id', 'is_agent', 'status'])
            ->get();
        $ret->each(function ($member) {
            if ($member->is_agent == 0 && $member->status != 2) {
                $mryt_ret = MrytMemberModel::select()->where('uid', $member->member_id)->first();
                if ($mryt_ret) {
                    dump("MRYT-uid[{$member->member_id}]");
                    $mryt_ret->delete();
                }
                $mryt_parent = MrytMemberModel::select()->where('uid', $member->parent_id)->first();
                // 删除直推奖
                $referrals = MemberReferralAward::where('source_uid', $member->member_id)->get();
                if (!$referrals->isEmpty()) {
                    $referrals->each(function ($referral) use ($mryt_parent) {
                        if ($mryt_parent && $mryt_parent->direct > 0) {
                            $mryt_parent->direct = $mryt_parent->direct - $referral->amount;
                            if ($mryt_parent->direct < 0) {
                                $mryt_parent->direct = 0;
                            }
                            $mryt_parent->save();
                        }
                        dump("直推奖记录ID[{$referral->id}]");
                        $referral->delete();
                    });
                }
                // 删除团队奖、感恩奖
                $teams = MemberTeamAward::where('source_uid', $member->member_id)->get();
                if (!$teams->isEmpty()) {
                    $teams->each(function ($team) use ($mryt_parent) {
                        if ($team->award_type == 1) {
                            if ($mryt_parent && $mryt_parent->team > 0) {
                                $mryt_parent->team -= $team->amount;
                                if ($mryt_parent->team < 0) {
                                    $mryt_parent->team = 0;
                                }
                                $mryt_parent->save();
                            }
                        }
                        if ($team->award_type == 2) {
                            if ($mryt_parent && $mryt_parent->thankful > 0) {
                                $mryt_parent->thankful -= $team->amount;
                                if ($mryt_parent->thankful < 0) {
                                    $mryt_parent->thankful = 0;
                                }
                                $mryt_parent->save();
                            }
                        }
                        dump("团队奖记录ID[{$team->id}]");
                        $team->delete();
                    });
                }
            }
        });
        dd('ok');
        exit;
    }

    public function fixRelation()
    {
        $member_relation = new MemberRelation();

        $members = [
            [4806, 4860],
            [4860, 4423],
            [4896, 5330],
            [6766, 6078],
            [6808, 5348]
        ];

        foreach ($members as $item) {
            $member_relation->build($item[0], $item[1]);
        }
    }
}