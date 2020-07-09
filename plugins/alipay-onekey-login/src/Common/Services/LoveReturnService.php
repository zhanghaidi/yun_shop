<?php
/**
 * Created by PhpStorm.
 * User: king/QQ:995265288
 * Date: 2019/12/5
 * Time: 1:58 PM
 */

namespace Yunshop\Love\Common\Services;


use app\backend\modules\finance\models\Income;
use app\common\facades\Setting;
use Illuminate\Support\Facades\Log;
use Yunshop\Love\Common\Models\LoveReturnLogModel;
use Yunshop\Love\Common\Models\MemberLove;

class LoveReturnService
{
    /**
     * @var MemberLove
     */
    private $memberLove;

    public function handle()
    {
        $memberLoves = MemberLove::getReturnLove($this->returnRate())->get();
        if ($memberLoves->isNotEmpty()) {
            foreach ($memberLoves as $key => $memberLove) {
                $this->loveReturn($memberLove);
            }
        }
        Log::info('========爱心值返现UNIACID:' . \YunShop::app()->uniacid . '返现完成========');
    }

    private function loveReturn($memberLove)
    {
        $this->memberLove = $memberLove;

        $this->_loveReturn();
    }

    private function _loveReturn()
    {
        $recordId = $this->addReturnRecord();

        $this->addMemberIncome($recordId);

        $this->updateMemberLove();
    }

    private function addReturnRecord()
    {
        $data = [
            'member_id'  => $this->memberLove->member_id,
            'uniacid'    => \YunShop::app()->uniacid,
            'amount'     => $this->returnAmount(),
            'created_at' => time(),
            'updated_at' => time()
        ];
        return LoveReturnLogModel::insertGetId($data);
    }

    private function addMemberIncome($recordId)
    {
        $incomeData = [
            'uniacid'          => \YunShop::app()->uniacid,
            'member_id'        => $this->memberLove->member_id,
            'incometable_type' => LoveReturnLogModel::class,
            'incometable_id'   => $recordId,
            'type_name'        => $this->loveName() . '返现',
            'amount'           => $this->returnAmount(),
            'status'           => 0,
            'pay_status'       => 0,
            'create_month'     => date('Y-m'),
            'created_at'       => time()
        ];
        Income::insert($incomeData);
    }

    private function updateMemberLove()
    {
        $data = [
            'member_id'    => $this->memberLove->member_id,
            'change_value' => $this->returnAmount(),
            'operator'     => ConstService::OPERATOR_SHOP,
            'operator_id'  => '',
            'remark'       => $this->loveName() . '返现' . $this->returnAmount(),
            'relation'     => ''
        ];
        (new LoveChangeService())->returnAward($data);
    }

    private function returnAmount()
    {
        return sprintf("%.2f", ($this->memberLove->usable / 100 * $this->returnRate()));
    }

    private function returnRate()
    {
        $set = Setting::get('plugin.love_return');

        return $set['return_rate'] ? $set['return_rate'] : 0;
    }

    private function loveName()
    {
        return SetService::getLoveName();
    }
}
