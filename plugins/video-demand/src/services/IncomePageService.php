<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/5/9 下午5:19
 * Email: livsyitian@163.com
 */

namespace Yunshop\VideoDemand\services;


use app\frontend\modules\finance\interfaces\IIncomePage;

class IncomePageService implements IIncomePage
{

    /**
     * return string
     */
    public function getMark()
    {
        return 'video_demand';
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
        return "讲师分红";
    }


    /**
     * @return string
     */
    public function getIcon()
    {
        return 'icon-lecturer01';
    }


    /**
     * @return string
     */
    public function getTypeValue()
    {
        return 'Yunshop\VideoDemand\models\LecturerRewardLogModel';
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
        return 'courseIncome';
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
