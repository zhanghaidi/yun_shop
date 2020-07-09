<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/5/8 下午4:40
 * Email: livsyitian@163.com
 */

namespace Yunshop\Commission\Frontend\Services;


use app\frontend\modules\finance\interfaces\IIncomePage;
use Yunshop\Commission\models\Agents;
use Yunshop\Commission\models\AgentLevel;

class IncomePageService implements IIncomePage
{
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
        return 'commission';
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
        return trans('Yunshop\Commission::index.title');
    }


    /**
     * @return string
     */
    public function getIcon()
    {
        return 'icon-fenxiao01';
    }


    /**
     * @return string
     */
    public function getTypeValue()
    {
        return 'Yunshop\Commission\models\CommissionOrder';
    }


    /**
     * @return string
     */
    public function getLevel()
    {
        if ($this->itemModel) {
            if ($this->itemModel->agentLevel && $this->itemModel->agentLevel->name) {
                return $this->itemModel->agentLevel->name;
            }
            return AgentLevel::getDefaultLevelName();
        }
        return "";
    }


    public function getAppUrl()
    {
        return 'distribution';
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

        $levelModel = Agents::select('member_id', 'agent_level_id')
            ->with(['agentLevel' => function ($query) {
                $query->select('id', 'name');
            }])
            ->whereMember_id($member_id)->first();

        return $levelModel;
    }
}
