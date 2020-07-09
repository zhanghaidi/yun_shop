<?php

use Illuminate\Database\Migrations\Migration;

class UpdateAgentsCommissionPay extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (class_exists('\Yunshop\Commission\models\Agents')) {
            $agentModels = \Yunshop\Commission\models\Agents::get();
            $agentModels->each(function (\Yunshop\Commission\models\Agents $agent) {
                $amount = $this->getAmountByMemberId($agent->member_id);
                if ($amount) {
                    $agent->commission_pay = $amount;

                    $agent->save();
                }
            });
        }
    }

    private $amountItems;

    private function getAmountItems()
    {
        if (!isset($this->amountItems)) {
            $this->amountItems = \app\common\models\Withdraw::select(['member_id', \Illuminate\Support\Facades\DB::raw('sum(`actual_amounts`)')])->
            where('type', 'Yunshop\Commission\models\CommissionOrder')
                ->where('status', 2)->groupBy('member_id')
                ->get();
        }
        return $this->amountItems;
    }

    private function getAmountByMemberId($memberId)
    {
        $amountItem = $this->getAmountItems()->where('member_id', $memberId)->first();
        return $amountItem;
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
