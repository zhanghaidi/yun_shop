<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/5/10 上午9:16
 * Email: livsyitian@163.com
 */

namespace Yunshop\Love\Common\Services;


use app\frontend\modules\finance\interfaces\IIncomePage;

class IncomeLoveWithdrawService implements IIncomePage
{

    /**
     * return string
     */
    public function getMark()
    {
        return 'love_withdraw';
    }

    /**
     * @return bool
     */
    public function isShow()
    {
        return SetService::getLoveSet('withdraw_status') ? true : false;
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
        return CommonService::getLoveName();
    }


    /**
     * @return string
     */
    public function getIcon()
    {
        return 'icon-love-value';
    }


    /**
     * @return string
     */
    public function getTypeValue()
    {
        return 'Yunshop\Love\Common\Models\LoveWithdrawRecords';
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
        return 'love_index';
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
