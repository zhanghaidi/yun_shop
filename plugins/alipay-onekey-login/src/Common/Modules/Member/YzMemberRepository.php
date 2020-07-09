<?php


namespace Yunshop\Love\Common\Modules\Member;


class YzMemberRepository
{
    private $memberGroup;

    public function __construct($members)
    {
        $memberGroup = [];
        foreach ($members as $member) {
            $memberGroup[$member['parent_id']][] = $member['member_id'];
        }
        $this->memberGroup = $memberGroup;

    }

    public function find($uid)
    {
        return $this->memberGroup[$uid];
    }
}