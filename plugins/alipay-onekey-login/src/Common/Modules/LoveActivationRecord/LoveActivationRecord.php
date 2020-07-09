<?php

namespace Yunshop\Love\Common\Modules\LoveActivationRecord;


use Yunshop\Love\Common\Modules\Member\YzMemberRepository;
use Yunshop\Love\Common\Modules\Repository;

/**
 * 激活记录
 * Class LoveActivationRecord
 * @package Yunshop\Love\Common\Modules\LoveActivationRecord
 */
class LoveActivationRecord
{
    private $uid;
    private $orderAmount;// 个人订单金额
    private $teamOrderAmountGroup;// 团队订单金额组
    private $teamOrderAmount;// 团队订单金额
    private $children;// 一级下线爱心值记录集合
    private $yzMemberRepository;
    private $orderAmountRepository;
    private $loveRepository;
    private $memberLoveRepository;
    private $loveActivationRecordRepository;

    /**
     * LoveActivationRecord constructor.
     * @param int $uid
     * @param LoveActivationRecordRepository $loveActivationRecordRepository
     * @param YzMemberRepository $yzMemberRepository
     * @param Repository $orderAmountRepository
     * @param Repository $loveRepository
     * @param Repository $memberLoveRepository
     */
    public function __construct(
        $uid,
        LoveActivationRecordRepository $loveActivationRecordRepository,
        YzMemberRepository $yzMemberRepository,
        Repository $orderAmountRepository,
        Repository $loveRepository,
        Repository $memberLoveRepository
    )
    {
        $this->uid = $uid;
        $this->loveActivationRecordRepository = $loveActivationRecordRepository;
        $this->yzMemberRepository = $yzMemberRepository;
        $this->orderAmountRepository = $orderAmountRepository;
        $this->loveRepository = $loveRepository;
        $this->memberLoveRepository = $memberLoveRepository;
    }


    /**
     * 直属下线激活记录
     * @return array
     */
    private function getChildren()
    {
        if (!isset($this->children)) {
            $this->children = [];
            $childrenIds = $this->yzMemberRepository->find($this->uid);
            foreach ($childrenIds as $id){
                $this->children[] = $this->loveActivationRecordRepository->find($id);
            }
        }
        return $this->children;
    }

    /**
     * 自己的订单金额
     * @return mixed
     */
    private function getOrderAmount()
    {
        if (!isset($this->orderAmount)) {
            $this->orderAmount = $this->orderAmountRepository->find($this->uid)['amount'];
        }
        return $this->orderAmount;
    }

    /**
     * 等级团队金额(包含自己)
     * @param $level
     * @return int
     */
    public function getTeamOrderAmountGroup($level)
    {
        return $this->getOrderAmount() + $this->getChildrenTeamAmountGroup($level);
    }

    /**
     * 等级团队金额(不包含自己)
     * @param $level
     * @return int
     */
    public function getChildrenTeamAmountGroup($level)
    {
        if (!$level) {
            return 0;
        }
        if (!isset($this->teamOrderAmountGroup[$level])) {
            $amount = 0;
            foreach ($this->getChildren() as $child) {
                // 递归到指定层级
                /**
                 * @var self $child
                 */
                $amount += $child->getTeamOrderAmountGroup($level - 1);
            }
            // 保存每层团队金额
            $this->teamOrderAmountGroup[$level] = $amount;
        }
        return $this->teamOrderAmountGroup[$level];
    }

    /**
     * 团队金额(不包含自己)
     * @return int
     */
    public function getChildrenTeamAmount()
    {
        if (!isset($this->teamOrderAmount)) {
            $amount = 0;
            foreach ($this->getChildren() as $child) {
                // 递归到指定层级
                /**
                 * @var self $child
                 */
                $amount += $child->getTeamOrderAmount();
            }
            // 保存每层团队金额
            $this->teamOrderAmount = $amount;
        }
        return $this->teamOrderAmount;
    }

    /**
     * 团队金额(包含自己)
     * @return int
     */
    public function getTeamOrderAmount()
    {
        return $this->getOrderAmount() + $this->getChildrenTeamAmount();
    }

    public function getLastUpgradeTeamLevelAward()
    {
        return $this->loveRepository->find($this->uid);
    }

    public function getMemberLove()
    {
        return $this->memberLoveRepository->find($this->uid);
    }
}