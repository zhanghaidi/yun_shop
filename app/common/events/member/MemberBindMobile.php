<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2020/3/6
 * Time: 下午3:40
 */

namespace app\common\events\member;


use app\common\events\Event;

class MemberBindMobile extends Event
{
    private $data = '';

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function getMemberModel()
    {
        return $this->data;
    }
}