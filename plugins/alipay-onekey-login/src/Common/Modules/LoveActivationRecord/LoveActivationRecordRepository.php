<?php


namespace Yunshop\Love\Common\Modules\LoveActivationRecord;


use Yunshop\Love\Common\Modules\Member\YzMemberRepository;
use Yunshop\Love\Common\Modules\Repository;

class LoveActivationRecordRepository
{
    private $data;
    private $yzMemberRepository;
    private $orderAmountRepository;
    private $loveRepository;
    private $memberLoveRepository;

    public function __construct(
        YzMemberRepository $yzMemberRepository,
        Repository $orderAmountRepository,
        Repository $loveRepository,
        Repository $memberLoveRepository
    )
    {
        $this->yzMemberRepository = $yzMemberRepository;
        $this->orderAmountRepository = $orderAmountRepository;
        $this->loveRepository = $loveRepository;
        $this->memberLoveRepository = $memberLoveRepository;
    }

    /**
     * @param $uid
     * @return LoveActivationRecord
     */
    public function find($uid)
    {
        if (!isset($this->data[$uid])) {
            $this->data[$uid] = new LoveActivationRecord(
                $uid,
                $this,
                $this->yzMemberRepository,
                $this->orderAmountRepository,
                $this->loveRepository,
                $this->memberLoveRepository
            );
        }
        return $this->data[$uid];
    }
}