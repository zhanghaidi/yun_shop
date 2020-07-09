<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/17
 * Time: 上午9:57
 */

namespace Yunshop\Love\Frontend\Modules\Coin;

use app\common\exceptions\AppException;
use app\common\models\VirtualCoin;
use app\frontend\models\MemberCoin;
use Yunshop\Love\Common\Models\LoveCoin;
use Yunshop\Love\Common\Services\ConstService;
use Yunshop\Love\Common\Services\LoveChangeService;
use Yunshop\Love\Frontend\Models\MemberLove;

class MemberLoveCoin extends MemberCoin
{
    /**
     * @var MemberLove
     */
    private $memberLove;

    function __construct($member)
    {
        parent::__construct($member);
        $this->memberLove = MemberLove::where('member_id', $member->uid)->first();
    }

    public function getMaxUsableCoin()
    {
        $coinAmount = isset($this->memberLove) ? $this->memberLove->getMaxUsablePoint() : 0;
        return (new LoveCoin())->setCoin($coinAmount);
    }

    public function lockCoin($coin)
    {
        if ($coin > $this->memberLove->usable) {
            throw new AppException("用户(ID:{$this->memberLove->uid})".(new LoveCoin())->getName()."余额不足");
        }
        $this->memberLove->usable-= $coin;
    }

    public function consume(VirtualCoin $coin, $data)
    {
        (new LoveChangeService())->deduction([
            'member_id' => $this->member->uid,
            'change_value' => $coin->getCoin(),
            'operator' => ConstService::OPERATOR_MEMBER,
            'operator_id' => $this->member->uid,
            'remark' => '下单抵扣',
            'relation' => $data['order_sn']
        ]);
    }
}