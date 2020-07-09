<?php
/**
 * Created by PhpStorm.
 * User: king/QQ:995265288
 * Date: 2019/5/9
 * Time: 11:16 AM
 */

namespace Yunshop\Love\Common\Events;


use app\common\events\Event;
use Yunshop\Love\Common\Models\LoveWithdrawRecords;

class LoveWithdrawApplyEvent extends Event
{
    /**
     * @var LoveWithdrawRecords
     */
    private $loveWithdrawModel;

    public function __construct(LoveWithdrawRecords $loveWithdrawModel)
    {
        $this->loveWithdrawModel = $loveWithdrawModel;
    }

    /**
     * @return LoveWithdrawRecords
     */
    public function getLoveWithdrawModel()
    {
        return $this->loveWithdrawModel;
    }
}
