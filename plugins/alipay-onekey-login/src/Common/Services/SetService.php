<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/6/26 下午2:28
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace Yunshop\Love\Common\Services;


use app\common\exceptions\AppException;
use app\common\traits\ValidatorTrait;

class SetService
{
    use ValidatorTrait;


    /**
     * 获取爱心值插件所有设置
     */
    public static function getLoveSet($key = '')
    {
        //todo 优化查询
        $love_set = array_pluck(\Setting::getAllByGroup('love')->toArray(), 'value', 'key');
        $love_set['name'] ?: trans('Yunshop\Love::love.name');
        if ($key) {
            return isset($love_set[$key]) ? $love_set[$key] : '0';
        }
        return $love_set;
    }


    /**
     * 获取插件自定义名称
     * @return mixed|string
     */
    public static function getLoveName()
    {
        return static::getLoveSet('name') ?: trans('Yunshop\Love::love.name');
    }


    /**
     * 转让开关状态
     * @return bool
     */
    public static function getTransferStatus()
    {
        //todo 0:转让关闭， 1：转让开启，
        //todo team_dividend_transfer 0:转让关闭， 1：转让开启，
        if (self::getLoveSet('transfer') == 0) {
            return false;
        }
        return true;
//        return self::getLoveSet('transfer') ? true : false;
    }


    /**
     * 转让手续费比例
     * @return string
     */
    public static function getTransferPoundageProportion()
    {
        return self::getLoveSet('transfer_poundage') ?: '0';
    }

    /**
     * 转让限制：转让最小额度
     * @return string
     */
    public static function getTransferFetter()
    {
        return self::getLoveSet('transfer_fetter') ?: '0';
    }

    /**
     *  转让限制：转让倍数
     * @return string
     */
    public static function getTransferMultiple()
    {
        return self::getLoveSet('transfer_multiple') ?: '';
    }


    /**
     * 购物抵扣开关状态
     * @return bool
     */
    public static function getDeductionStatus()
    {
        return self::getLoveSet('deduction') ? true : false;
    }


    /**
     * 购物抵扣最高百分比
     * @return string
     */
    public static function getDeductionProportion()
    {
        return self::getLoveSet('deduction_proportion') ?: '0';
    }

    /**
     * 购物抵扣最高百分比
     * @return string
     */
    public static function getDeductionProportionLow()
    {
        return self::getLoveSet('deduction_proportion_low') ?: '0';
    }


    /**
     * 插件说明标题
     * @return string
     */
    public static function getExplainTitle()
    {
        return self::getLoveSet('explain_title') ?: '';
    }


    /**
     * 插件说明内容
     * @return string
     */
    public static function getExplainContent()
    {
        return self::getLoveSet('explain_content') ?: '';
    }


    /**
     * 奖励赠送类型，可用 usable : 冻结 froze
     * @return string
     */
    public static function getAwardType()
    {
        return self::getLoveSet('award_type') ?: 'froze';
    }

    /**
     * 购物奖励开关状态
     * @return bool
     */
    public static function getAwardStatus()
    {
        return self::getLoveSet('award') ? true : false;
    }

    /**
     * 购物上级奖励开关状态
     * @return bool
     */
    public static function getParentAwardStatus()
    {
        return self::getLoveSet('parent_award') ?: false;
    }

    /**
     * 购物奖励开关状态
     * @return bool
     */
    public static function getWithdrawAwardStatus()
    {
        return self::getLoveSet('withdraw_award') ? true : false;
    }

    /**
     * 购物加速激活爱心值开关状态
     * @return bool
     */
    public static function getAcceleratedActivationStatus()
    {
        return self::getLoveSet('activation_state') ? true : false;
    }

    /**
     * 分销商等级上级赠送爱心值开关状态
     * @return bool
     */
    public static function getCommissionLevelGiveStatus()
    {
        return self::getLoveSet('commission_level_give') ? true : false;
    }

    /**
     * 分销商等级上级赠送比例
     * @return bool
     */
    public static function getCommissionLevelGiveScale()
    {
        return self::getLoveSet('commission') ?: 0;
    }

    /**
     * 购物加速激活爱心值比例
     * @return bool
     */
    public static function getAcceleratedActivationOfLoveRatio()
    {
        return self::getLoveSet('love_accelerate') ?: 0;
    }

    /**
     * 购物加速激活爱心值商品比例
     * @return bool
     */
    public static function getGoodsAcceleratedActivationOfLoveRatio()
    {
        return self::getLoveSet('love_accelerate') ?: 0;
    }

    /**
     * 购物奖励赠送比例
     * @return string
     */
    public static function getAwardProportion()
    {
        return self::getLoveSet('award_proportion') ?: '0';
    }

    public static function getFirstParentAwardProportion()
    {
        return self::getLoveSet('parent_award_proportion') ?: '0';
    }

    public static function getSecondParentAwardProportion()
    {
        return self::getLoveSet('second_award_proportion') ?: '0';
    }

    public static function getThirdParentAwardProportion()
    {
        return self::getLoveSet('third_award_proportion') ?: '0';
    }

    /**
     * 爱心值变动通知标题
     * @return string|\Symfony\Component\Translation\TranslatorInterface
     */
    public static function getChangeNoticeTitle()
    {
        return self::getLoveSet('change_title') ?: trans('Yunshop\Love::notice_set.change_title_hint');
    }


    /**
     * 爱心值变动通知内容
     * @return string
     */
    public static function getChangeNoticeContent()
    {
        return self::getLoveSet('change_content') ?: '';
    }

    public static function getActivationNoticeTitle()
    {
        return self::getLoveSet('activation_title') ?: trans('Yunshop\Love::notice_set.activation_title_hint');
    }

    public static function getActivationNoticeContent()
    {
        return self::getLoveSet('activation_content') ?: '';
    }


    /**
     * 获取激活时间设置，0为关闭激活
     * @return string
     */
    public static function getActivationTime()
    {
        return self::getLoveSet('activation_time') ?: '0';
    }

    public static function getActivationHour()
    {
        return self::getLoveSet('activation_time_hour') ?: '0';
    }

    public static function getActivationWeek()
    {
        return self::getLoveSet('activation_time_week') ?: '0';
    }


    /**
     * 一级下线激活比例
     * @return string
     */
    public static function getLv1ProPortion()
    {
        return self::getLoveSet('level_one_proportion') ?: "0";
    }


    /**
     * 二、三级下线激活比例
     * @return string
     */
    public static function getLv2AndLv3ProPortion()
    {
        return self::getLoveSet('level_two_proportion') ?: "0";
    }

    public static function getTeamProportion()
    {
        return self::getLoveSet('team_proportion') ?: "0";
    }

    public static function getProfitProportion()
    {
        return self::getLoveSet('profit_proportion') ?: "0";
    }

    /**
     * 二、三级下线激活束缚比例，（束缚条件比例）
     * @return string
     */
    public static function getLevelTwoFetterProportion()
    {
        return self::getLoveSet('level_two_fetter_proportion') ?: "0";
    }


    public static function getFixedActivationProportion()
    {
        return self::getLoveSet('activation_proportion') ?: "0";
    }

    /**
     * 二、三级下线激活束缚比例，（束缚条件比例）
     * @return string
     */


    /**
     * 保存设置数据
     * @param array $array
     * @return bool|\Illuminate\Support\MessageBag
     */
    public static function storeSet(array $array)
    {
//        dd($array);
        $validator = (new SetService())->validator($array);
        if ($validator->fails()) {
            return $validator->messages();
        }
        foreach ($array as $key => $item) {
            \Setting::set('love.' . $key, $item);
        }
//        \Cache::forget('plugin.love.set_' . \YunShop::app()->uniacid);
        return true;
    }


    /**
     * 字段验证规则
     * @return array
     */
    public function rules()
    {
        return [
            'name'                 => 'max:45',
            'transfer'             => 'regex:/^[0123]$/',
            'transfer_poundage'    => 'regex:/^\d+(\.\d{1,2})?$/|numeric|min:0|max:100',
            'transfer_fetter'      => 'regex:/^\d+(\.\d{1,2})?$/|numeric|min:0',
            'transfer_multiple'    => 'regex:/^\d+(\.\d{1,2})?$/|numeric|min:1',
            'deduction'            => 'regex:/^-?(?!0\d)\d+$/',
            'deduction_proportion' => 'numeric|min:0|max:100',
            'explain_title'        => 'max:45',
            'explain_content'      => 'max:200',

            'award'            => 'regex:/^[01]$/',
            'award_proportion' => 'numeric|min:0',

            'change_title'       => 'max:45',
            'change_content'     => 'max:200',
            'activation_title'   => 'max:45',
            'activation_content' => 'max:200',

//            'recharge_rate_money'    => 'required|numeric|integer|min:1',
//            'recharge_rate_love'    => 'required|numeric|integer|min:1',


        ];
    }


    /**
     * 字段名称
     * @return array
     */
    public function atributeNames()
    {
        return [
            'name'              => '爱心值名称',
            'transfer'          => '转让开关',
            'transfer_poundage' => '转让手续费',
            'transfer_fetter'   => '转让最小额度',
            'transfer_multiple' => '转让倍数',

            'deduction'            => '购物抵扣开关',
            'deduction_proportion' => '购物抵扣比例',
            'explain_title'        => '爱心值说明标题',
            'explain_content'      => '爱心值说明内容',

            'award'            => '购物赠送开关',
            'award_proportion' => '购物赠送比例',

            'change_title'       => '变动通知标题',
            'change_content'     => '变动通知内容',
            'activation_title'   => '激活通知标题',
            'activation_content' => '激活通知内容',

            'recharge_rate_money' => '充值金额比例',
            'recharge_rate_love'  => '充值' . LOVE_NAME . '比例',
        ];

    }

    /*
     *加速激活状态
     * */
    public function getActivationState()
    {
        return self::getLoveSet('activation_state') ?: "0";
    }
}
