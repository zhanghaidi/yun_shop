<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/6/30 下午2:29
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace Yunshop\Love\Common\Services;


use Yunshop\Froze\Common\Services\SetService as FrozeSetService;
use Yunshop\Integral\Common\Services\SetService as IntegralSetService;

class ConstService extends \app\common\services\credit\ConstService
{

    /**
     * 爱心值：可用
     */
    const VALUE_TYPE_USABLE = 1;

    /**
     * 爱心值：冻结
     */
    const VALUE_TYPE_FROZE = 2;

    /**
     * 收入提现奖励
     */
    const SOURCE_WITHDRAW_AWARD = 12;

    /**
     * 直推奖奖励
     */
    const SOURCE_DIRECT_PUSH_AWARD = 13;

    /**
     * 团队代理等级奖励
     */
    const SOURCE_TEAM_LEVEL_AWARD = 14;

    /**
     * 转让--转入
     */
    const SOURCE_RECIPIENT = 15;

    /**
     * 冻结激活
     */
    const SOURCE_ACTIVATION = 16;

    /**
     * 购物上级获得
     */
    const SOURCE_PARENT_AWARD = 17;

    /**
     * 分销下线奖励
     */
    const SOURCE_COMMISSION_AWARD = 18;

    /**
     * 交易撤回
     */
    const SOURCE_REVOKE_AWARD = 19;

    /**
     * 交易收购
     */
    const SOURCE_PURCHASE_AWARD = 20;

    /**
     * 出售
     */
    const SOURCE_SELL_AWARD = 21;

    /**
     * 返现
     */
    const SOURCE_RETURN_AWARD = 22;

    /**
     * 积分转入
     */
    const SOURCE_POINT_TRANSFER = 23;

    /**
     * 任务奖励
     */
    const SOURCE_TASK_AWARD = 24;

    /**
     * 冻结币奖励
     */
    const SOURCE_FROZE_AWARD = 25;

    /**
     * 签到奖励
     */
    const SOURCE_SIGN_AWARD = 26;

    /**
     * 加速激活
     */
    const SOURCE_QUICKEN_ACTIVATION = 27;

    /**
     * 经销商分红转入
     */
    const SOURCE_TEAM_DIVIDEND = 28;

    /**
     * 分销商等级上级赠送
     */
    const SOURCE_COMMISSION_LEVEL_AWARD = 29;

    /**
     * 名片新增会员奖励
     */
    const CARD_REGISTER_AWARD = 30;

    /**
     * 名片访问奖励
     */
    const CARD_VISIT_AWARD = 31;

    /**
     * 配送站获得
     */
    const DELIVERY_STATION_ORDER = 32;

    /**
     * 服务站获取
     */
    const SERVICE_STATION_ORDER = 33;

    /**
     * 提至消费积分
     */
    const Cash_Consumption_Points = 34;

    /**
     * 充值奖励(一级)
     */
    const SOURCE_RECHARGE_AWARD_FIRST = 35;

    /**
     * 充值奖励(二级)
     */
    const SOURCE_RECHARGE_AWARD_SECOND = 36;

    /**
     * 抽奖获得
     */
    const DRAW_AWARD = 37;

    /**
     * 抽奖使用
     */
    const DRAW_AWARD_USED = 38;

    /**
     * 抽奖奖励
     */
    const DRAW_REWARD = 39;

    /**
     * 爱心值助手重新冻结
     */
    const LOVE_HELPER_FROZE = 40;

    /**
     * 余额转化
     */
    const  TRANSFORMATION_BALANCE = 99;


    /**
     * 变动值类型 key => value
     * @return array
     */
    public function valueTypeComment()
    {
        return [
            self::VALUE_TYPE_USABLE => '可用' . self::$title,
            self::VALUE_TYPE_FROZE  => '冻结' . self::$title,
        ];
    }

    /**
     * 业务类型 key => value
     * @return array
     */
    public function sourceComment()
    {
        return [
            self::SOURCE_RECHARGE               => '充值',
            self::SOURCE_RECHARGE_MINUS         => '后台扣除',
            self::SOURCE_TRANSFER               => '转让--转出',
            self::SOURCE_DEDUCTION              => '消费抵扣',
            self::SOURCE_AWARD                  => '购物奖励',
            self::SOURCE_WITHDRAW_AWARD         => '收入提现奖励',
            self::SOURCE_DIRECT_PUSH_AWARD      => '直推奖奖励',
            self::SOURCE_TEAM_LEVEL_AWARD       => '团队代理等级奖励',
            self::SOURCE_RECIPIENT              => '转让--转入',
            self::SOURCE_ACTIVATION             => '冻结激活',
            self::SOURCE_PARENT_AWARD           => '购物上级奖励',
            self::SOURCE_COMMISSION_AWARD       => '分销下线奖励',
            self::SOURCE_REVOKE_AWARD           => '交易撤回',
            self::SOURCE_PURCHASE_AWARD         => '交易收购',
            self::SOURCE_SELL_AWARD             => '交易出售',
            self::SOURCE_RETURN_AWARD           => '返现',
            self::SOURCE_POINT_TRANSFER         => '积分转入',
            self::SOURCE_WITHDRAWAL             => static::$title . '提现',
            self::SOURCE_TASK_AWARD             => '任务奖励',
            self::SOURCE_CANCEL_CONSUME         => '消费取消', //10
            self::SOURCE_SIGN_AWARD             => '签到奖励',
            self::SOURCE_FROZE_AWARD            => $this->getSourceFrozeAwardName(),
            self::SOURCE_QUICKEN_ACTIVATION     => '加速激活',
            self::SOURCE_TEAM_DIVIDEND          => '经销商分红转入',
            self::SOURCE_COMMISSION_LEVEL_AWARD => '分销商等级上级赠送',
            self::CARD_REGISTER_AWARD           => '名片新增会员奖励',
            self::CARD_VISIT_AWARD              => '名片访问奖励',
            self::Cash_Consumption_Points       => $this->getSourceIntegralName(),
            self::DELIVERY_STATION_ORDER        => '配送站获得',
            self::SERVICE_STATION_ORDER         => '服务站获得',
            self::TRANSFORMATION_BALANCE        => (\Setting::get('shop.shop.credit') ?: '余额') . '转化',
            self::SOURCE_RECHARGE_AWARD_FIRST   => '充值奖励(一级)',
            self::SOURCE_RECHARGE_AWARD_SECOND  => '充值奖励(二级)',
            self::DRAW_AWARD                    => '抽奖获得',
            self::DRAW_AWARD_USED               => '抽奖使用',
            self::DRAW_REWARD                   => '抽奖奖励',
            self::LOVE_HELPER_FROZE                   => '助手可用转冻结',
        ];
    }

    private function getSourceIntegralName()
    {
        $sourceIntegralName = "提现至消费积分";
        if (app('plugins')->isEnabled('integral')) {
            $sourceIntegralName = '提现至' . IntegralSetService::getIntegralName();
        }
        return $sourceIntegralName;
    }

    private function getSourceFrozeAwardName()
    {
        $sourceFrozeAwardName = "冻结币奖励";
        if (app('plugins')->isEnabled('froze')) {
            $sourceFrozeAwardName = FrozeSetService::getFrozeName() . '奖励';
        }
        return $sourceFrozeAwardName;
    }


}
