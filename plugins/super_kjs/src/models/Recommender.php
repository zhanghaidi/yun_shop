<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/3/27
 * Time: 14:43
 */

namespace Yunshop\SuperKjs\models;

use app\common\helpers\Url;
use app\common\models\Member;

class Recommender extends Member
{
    /**
     * 我的推荐人 v2
     *
     * @return array
     */
    public static function getMyReferral($uid)
    {
        $member_info     = self::getMyReferrerInfo($uid)->first();

        $set = \Setting::get('shop.member');

        $data = [];

        if (!empty($member_info)) {
            if (isset($set) && $set['headimg']) {
                $avatar = replace_yunshop(yz_tomedia($set['headimg']));
            } else {
                $avatar = Url::shopUrl('static/images/photo-mr.jpg');
            }

            $member_info = $member_info->toArray();

            $builder = self::getUserInfos($member_info['yz_member']['parent_id']);
            $referrer_info = self::getMemberRole($builder)->first();

            $member_role = self::convertRoleText($referrer_info);

            if ($member_info['yz_member']['inviter'] == 1) {
                if (!empty($referrer_info)) {
                    $info = $referrer_info->toArray();
                    $data = [
                        'uid' => $info['uid'],
                        'avatar' => $info['avatar'],
                        'nickname' => $info['nickname'],
                        'level' => $info['yz_member']['level']['level_name'],
                        'is_show' => $set['is_referrer'],
                        'role'   => $member_role
                    ];
                } else {
                    $data = [
                        'uid' => '',
                        'avatar' => $avatar,
                        'nickname' => '总店',
                        'level' => '',
                        'is_show' => $set['is_referrer'],
                        'role'   => $member_role
                    ];
                }
            } else {
                $data = [
                    'uid' => '',
                    'avatar' => $avatar,
                    'nickname' => '暂无',
                    'level' => '',
                    'is_show' => $set['is_referrer'],
                    'role'   => $member_role
                ];
            }
        }

        return $data;
    }

    /**
     * 我的推荐人信息
     *
     * @param $uid
     * @return mixed
     */
    public static function getMyReferrerInfo($uid)
    {
        return self::select(['uid'])->uniacid()
            ->where('uid', $uid)
            ->with([
                'yzMember' => function ($query) {
                    return $query->select(['member_id', 'parent_id', 'is_agent', 'group_id', 'level_id', 'is_black', 'alipayname', 'alipay', 'status', 'inviter'])
                        ->where('is_black', 0)
                        ->with(['level'=>function($query2){
                            return $query2->select(['id','level_name'])->uniacid();
                        }]);
                }
            ]);
    }

    public static function convertRoleText($member_modle)
    {
        $commission = self::langFiled('commission');

        $member_role = '';

        if (!is_null($member_modle)) {
            if (app('plugins')->isEnabled('commission')) {
                if (!is_null($member_modle->hasOneAgent)) {
                    $member_role .= $commission['agent'] ?:'分销商';
                    $member_role .= '&';
                }
            }

            if (app('plugins')->isEnabled('team-dividend')) {
                if (!is_null($member_modle->hasOneTeamDividend)) {
                    $member_role .= '经销商&';
                }
            }

            if (app('plugins')->isEnabled('area-dividend')) {
                if (!is_null($member_modle->hasOneAreaDividend)) {
                    $member_role .= '区域代理&';
                }
            }

            if (app('plugins')->isEnabled('merchant')) {
                if (!is_null($member_modle->hasOneMerchant)) {
                    $member_role .= '招商员&';
                }

                if (!is_null($member_modle->hasOneMerchantCenter)) {
                    if (1 == $member_modle->hasOneMerchant->is_center) {
                        $member_role .= '招商中心&';
                    }
                }
            }

            if (app('plugins')->isEnabled('micro')) {
                if (!is_null($member_modle->hasOneMicro)) {
                    $member_role .= '微店店主&';
                }
            }

            if (app('plugins')->isEnabled('supplier')) {
                if (!is_null($member_modle->hasOneSupplier)) {
                    $member_role .= '供应商&';
                }
            }
        }

        if (!empty($member_role)) {
            $member_role = rtrim($member_role, '&');
        }

        return $member_role;
    }

    private static function langFiled($filed)
    {
        $lang = \Setting::get('shop.lang', ['lang' => 'zh_cn']);
        $set = $lang[$lang['lang']];

        return $set[$filed];
    }
}