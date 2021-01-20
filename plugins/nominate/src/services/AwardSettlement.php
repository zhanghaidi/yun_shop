<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019/1/18
 * Time: 11:37 AM
 */

namespace Yunshop\Nominate\services;


use app\common\models\Income;
use Yunshop\Nominate\models\TeamPrize;

class AwardSettlement
{
    private $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    public function handle()
    {
        $teamPrizes = $this->getTeamPrizes();
        if ($teamPrizes->isEmpty()) {
            return;
        }
        $teamPrizes->each(function ($teamPrize) {
            $teamPrize->status = TeamPrize::SUCCESS;
            $teamPrize->save();
            $this->addIncome($teamPrize);
        });
    }

    private function addIncome($teamPrize)
    {
        // 收入
        $class = get_class($teamPrize);
        $income_data = [
            'uniacid'           => $teamPrize->uniacid,
            'member_id'         => $teamPrize->uid,
            'incometable_type'  => $class,
            'incometable_id'    => $teamPrize->id,
            'type_name'         => '团队业绩奖',
            'amount'            => $teamPrize->amount,
            'status'            => 0,
            'pay_status'        => 0,
            'detail'            => '',
            'create_month'      => date('Y-m', time())
        ];
        Income::create($income_data);
    }

    private function getTeamPrizes()
    {
        return TeamPrize::select()
            ->byStatus(TeamPrize::WARTING)
            ->byOrderId($this->order->id)
            ->get();
    }
}