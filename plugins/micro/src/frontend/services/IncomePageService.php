<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/5/8 下午6:32
 * Email: livsyitian@163.com
 */

namespace Yunshop\Micro\frontend\services;


use app\frontend\modules\finance\interfaces\IIncomePage;
use Yunshop\Micro\common\models\MicroShop;

class IncomePageService implements IIncomePage
{
    /**
     * 
     */
    private $itemModel;


    public function __construct()
    {
        $this->itemModel = $this->getItemModel();
    }


    /**
     * return string
     */
    public function getMark()
    {
        return 'micro';
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
        return $this->itemModel ? true : false;
    }


    /**
     * @return string
     */
    public function getTitle()
    {
        return trans('Yunshop\Micro::pack.micro_bonus');
    }


    /**
     * @return string
     */
    public function getIcon()
    {
        return 'icon-weidian01';
    }


    /**
     * @return string
     */
    public function getTypeValue()
    {
        return 'Yunshop\Micro\common\models\MicroShopBonusLog';
    }


    /**
     * @return string
     */
    public function getLevel()
    {
        if ($this->itemModel && $this->itemModel->hasOneMicroShopLevel) {
            return $this->itemModel->hasOneMicroShopLevel->level_name;
        }
        return '';
    }


    /**
     * @return string
     */
    public function getAppUrl()
    {
        return $this->isAvailable() ? 'microShop_ShopKeeperCenter' : 'microShop_apply';
    }


    /**
     * @return bool
     */
    public function needIsAgent()
    {
        return true;
    }


    /**
     * @return bool
     */
    public function needIsRelation()
    {
        return true;
    }


    /**
     * @return mixed
     */
    private function getItemModel()
    {
        $member_id = \YunShop::app()->getMemberId();

        $levelModel = MicroShop::uniacid()->select('member_id', 'level_id')
            ->with(['hasOneMicroShopLevel' => function ($query) {
                $query->select('id', 'level_name');
            }])
            ->whereMember_id($member_id)->first();

        return $levelModel;
    }

}
