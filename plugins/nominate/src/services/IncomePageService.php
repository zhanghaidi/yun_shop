<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019/1/24
 * Time: 10:48 AM
 */

namespace Yunshop\Nominate\services;


use app\frontend\modules\finance\interfaces\IIncomePage;
use Yunshop\Nominate\models\NominateBonus;
use Yunshop\Nominate\models\ShopMember;
use Yunshop\Nominate\models\TeamPrize;

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
        return 'icon-member_task';
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
        $set = \Setting::get('plugin.nominate');
        $plugin_name = $set['plugin_name'] ?: '推荐奖励';
        return $plugin_name;
    }


    /**
     * 对应收入图标
     * @return string
     */
    public function getIcon()
    {
        return 'icon-member_task';
    }


    /**
     * 对应收入 type 字段 value 值
     * @return string
     */
    public function getTypeValue()
    {
        $uid = \YunShop::app()->getMemberId();

        // 直推奖累计金额
        $nominatePrizeAmount = NominateBonus::select()
            ->where('uid', $uid)
            ->where('type', NominateBonus::NOMINATE_PRIZE)
            ->sum('amount');
        // 直推极差奖累计金额
        $nominatePoorPrizeAmount = NominateBonus::select()
            ->where('uid', $uid)
            ->where('type', NominateBonus::NOMINATE_POOR_PRIZE)
            ->sum('amount');
        // 团队奖累计金额
        $teamPrizeAmount = NominateBonus::select()
            ->where('uid', $uid)
            ->where('type', NominateBonus::TEAM_PRIZE)
            ->sum('amount');
        // 团队业绩奖累计金额
        $teamManagePrizeAmount = TeamPrize::select()
            ->where('uid', $uid)
            ->sum('amount');
        // 累计奖励金额
        $amountTotal = round($nominatePrizeAmount + $nominatePoorPrizeAmount + $teamPrizeAmount + $teamManagePrizeAmount, 2);

        return $amountTotal;
    }


    /**
     * 对应收入 等级
     * @return string
     */
    public function getLevel()
    {
        return $this->itemModel->shopMemberLevel->level_name;
    }


    //app 访问url
    public function getAppUrl()
    {
        return 'PartnershipTeam';
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
        $uid = \YunShop::app()->getMemberId();

        $model = ShopMember::with(['shopMemberLevel'])
            ->where('member_id', $uid)
            ->first();

        if ($model->level_id) {
            return $model;
        }
        return false;
    }
}