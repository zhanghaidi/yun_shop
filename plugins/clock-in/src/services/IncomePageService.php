<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/5/9 下午5:12
 * Email: livsyitian@163.com
 */

namespace Yunshop\ClockIn\services;


use app\frontend\modules\finance\interfaces\IIncomePage;

class IncomePageService implements IIncomePage
{

    /**
     * return string
     */
    public function getMark()
    {
        return 'clock_in';
    }

    /**
     * @return bool
     */
    public function isShow()
    {
        return true;
    }


    /**
     * @return bool
     */
    public function isAvailable()
    {
        return true;
    }


    /**
     * @return string
     */
    public function getTitle()
    {
        return (new ClockInService)->get('plugin_name');
    }


    /**
     * @return string
     */
    public function getIcon()
    {
        return 'icon-daka01';
    }


    /**
     * @return string
     */
    public function getTypeValue()
    {
        return 'Yunshop\ClockIn\models\ClockRewardLogModel';
    }


    /**
     * @return string
     */
    public function getLevel()
    {
        return '';
    }


    public function getAppUrl()
    {
        return 'ClockPunch';
    }


    /**
     * @return bool
     */
    public function needIsAgent()
    {
        return false;
    }


    /**
     * @return bool
     */
    public function needIsRelation()
    {
        return false;
    }


}
