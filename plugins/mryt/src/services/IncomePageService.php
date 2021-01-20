<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/10/29
 * Time: 2:57 PM
 */

namespace Yunshop\Mryt\services;



use app\frontend\modules\finance\interfaces\IIncomePage;
use Yunshop\Mryt\common\models\MemberReferralAward;
use Yunshop\Mryt\common\models\MemberTeamAward;
use Yunshop\Mryt\common\models\OrderParentingAward;
use Yunshop\Mryt\common\models\OrderTeamAward;
use Yunshop\Mryt\common\models\TierAward;
use Yunshop\Mryt\models\MrytMemberModel;

class IncomePageService implements IIncomePage
{
    private $itemModel;

    public function __construct()
    {
        $this->itemModel = $this->getItemModel();
    }


    /**
     * 对应收入唯一标示
     *
     * return string
     */
    public function getMark()
    {
        return 'icon-extension-prize';
    }

    /**
     * 系统设置是否显示
     *
     * @return bool
     */
    public function isShow()
    {
        return true;
    }


    /**
     * 是否可用状态(属于更多权限或可用权限)
     *
     * @return bool
     */
    public function isAvailable()
    {
        return $this->itemModel ? true : false;
    }


    /**
     * 对应收入名称
     *
     * @return string
     */
    public function getTitle()
    {
        $set = \Setting::get('plugin.mryt_set');
        $plugin_name = $set['name'] ?: 'MRYT';
        return $plugin_name;
    }


    /**
     * 对应收入图标
     * @return string
     */
    public function getIcon()
    {
        return 'icon-extension-prize';
    }


    /**
     * 对应收入 type 字段 value 值
     * @return string
     */
    public function getTypeValue()
    {
        $member_id = \YunShop::app()->getMemberId();
        $referral_amount = MemberReferralAward::select(['uniacid', 'uid', 'amount'])
            ->where('uid', $member_id)
            ->sum('amount');
        $team_amount = MemberTeamAward::select(['uniacid', 'uid', 'amount'])
            ->where('uid', $member_id)
            ->sum('amount');
        $parent_amount = OrderParentingAward::select(['uniacid', 'uid', 'amount'])
            ->where('uid', $member_id)
            ->sum('amount');
        $order_team_amount = OrderTeamAward::select(['uniacid', 'uid', 'amount'])
            ->where('uid', $member_id)
            ->sum('amount');
        $tier_amount = TierAward::select(['uniacid', 'uid', 'amount'])
            ->where('uid', $member_id)
            ->sum('amount');
        $amount = round(($referral_amount + $team_amount + $parent_amount + $order_team_amount + $tier_amount), 2);
        return $amount;
    }


    /**
     * 对应收入 等级
     * @return string
     */
    public function getLevel()
    {
        return $this->itemModel->hasOneLevel->level_name;
    }


    //app 访问url
    public function getAppUrl()
    {
        return 'MRYT';
    }


    /**
     * 是否需要验证是推客，true 需要，false 不需要
     * @return bool
     */
    public function needIsAgent()
    {
        return false;
    }


    /**
     * 是否需要验证开启关系链，true 需要，false 不需要
     * @return bool
     */
    public function needIsRelation()
    {
        return false;
    }


    /**
     * @return mixed
     */
    private function getItemModel()
    {
        $member_id = \YunShop::app()->getMemberId();

        $model = MrytMemberModel::with(['hasOneLevel'])->where('uid', $member_id)->first();
        if ($model) {
            return true;
        }
        return false;
    }
}