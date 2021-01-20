<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/12/11
 * Time: 16:54
 */

namespace Yunshop\Mryt\services;


use app\common\events\withdraw\WithdrawApplyEvent;
use app\common\models\UniAccount;
use Yunshop\Mryt\models\MrytLevelModel;
use Yunshop\Mryt\models\MrytMemberModel;
use app\common\facades\Setting;
use app\common\models\Income;
use app\frontend\modules\withdraw\models\Withdraw;
use app\common\events\withdraw\WithdrawAppliedEvent;
use app\common\events\withdraw\WithdrawApplyingEvent;
use app\common\exceptions\AppException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutoWithdrawService
{
    //提现设置
    private $withdraw_set;
    private $uid;
    //收入设置
    private $income_set;
    //提现方式
    private $pay_way;
    //手续费比例
    private $poundage_rate;
    //劳务税比例
    private $service_tax_rate;
    //
    private $special_poundage_rate;
    //
    private $special_service_tax_rate;
    //提现金额
    private $withdraw_amounts;
    private $withdraw_data;

    public function handle()
    {
        $uniAccount = UniAccount::get();
        foreach ($uniAccount as $u) {
            \Log::debug('自动提现uni:'.$u->uniacid);
            \YunShop::app()->uniacid = $u->uniacid;
            Setting::$uniqueAccountId = $u->uniacid;
            $this->autoWithdraw();
        }
    }

    public function isWithdraw($uid)
    {
        $mryt_member = MrytMemberModel::getMemberAutoWithdrawByUid($uid);
        if ($mryt_member) {
            return $mryt_member->hasOneLevel->withdraw_time;
        }
        return 0;
    }

    public function autoWithdraw()
    {
        $day = date('d', time());
        $levels = MrytLevelModel::getAutoWithdrawLevel($day)->get();
        if (empty($levels)) {
            return;
        }
        foreach ($levels as $level) {
            $is_execute = $this->isExecuted($level);
            if ($is_execute) {
                foreach ($level->hasManyMrytMember as $mryt_member) {
                    $this->uid = $mryt_member->uid;
                    $withdraw_data = $this->getWithdraw();
                    if($withdraw_data) {
                        $this->withdrawStart();
                        \Log::debug('自动提现成功'.$mryt_member->uid);
                    }
                }
                $level->current_md = date('m-d');
                $level->save();

            }

        }
    }


    /**
     * 今天（日）是否执行过
     *
     * @return bool
     */
    private function isExecuted($level)
    {
        if ($level->current_md == date('m-d')) {
            return false;
        }
        return true;
    }

    /**
     * 可提现数据接口【完成】
     * @return array
     */
    public function getWithdraw()
    {
        $this->withdraw_set = \Setting::get('withdraw.income');

        $income_config = \Config::get('income');

        $income_data = [];
        foreach ($income_config as $key => $income) {

            //余额不计算
            if ($income['type'] == 'balance') {
                continue;
            }

            //获取收入独立设置
            $this->setIncomeSet($income['type']);

            //附值手续费、劳务税(收银台不计算手续费、劳务税)
            if ($income['type'] == 'Mryt' || $income['type'] == 'StoreWithdraw' || $income['type'] == 'kingtimes_provider' || $income['type'] == 'kingtimes_distributor') {
                $this->poundage_rate = 0;
                $this->service_tax_rate = 0;
                $this->special_poundage_rate = 0;
                $this->special_service_tax_rate = 0;
            } else {
                $this->setPoundageRate($income['type']);
                $this->setServiceTaxRate();
                $this->setSpecialPoundageRate();
                $this->setSpecialServiceTaxRate();
            }


            $income_data[] = $this->getItemData($key, $income);
        }

        if ($income_data) {
            $this->withdraw_data = $income_data;
            return $income_data;

//            $data = [
//                'data' => $income_data,
//                'setting' => ['balance_special' => $this->getBalanceSpecialSet()]
//            ];
//            return $data;
        }
        return [];
    }

    /**
     * @return bool
     * @throws AppException
     */
    private function withdrawStart()
    {
//        $amount = '0';
        DB::transaction(function() {
            foreach ($this->withdraw_data as $key => $item) {

                if ($item['income'] <= 1) {
                    continue;
                }
                $withdrawModel = new Withdraw();

                $withdrawModel->mark = $item['key_name'];
                //todo 自动提现临时解决方法
                $withdrawModel->is_auto = 1;

                $withdrawModel->withdraw_set = $this->withdraw_set;
                $withdrawModel->income_set = $this->getIncomeSet($item['key_name']);

                $withdrawModel->fill($this->getWithdrawData($item));
//            dd($withdrawModel);
                event(new WithdrawApplyEvent($withdrawModel));

                $validator = $withdrawModel->validator();
                if ($validator->fails()) {
                    throw new AppException("ERROR:Data anomaly -- {$item['key_name']}::{$validator->messages()->first()}");
                }

                event(new WithdrawApplyingEvent($withdrawModel));
                if (!$withdrawModel->save()) {
                    throw new AppException("ERROR:Data storage exception -- {$item['key_name']}");
                }
                event(new WithdrawAppliedEvent($withdrawModel));

            }
        });
        return true;

    }

    private function getWithdrawData($withdraw_item)
    {
        //dd($withdraw_item);
        return [
            'withdraw_sn'       => Withdraw::createOrderSn('WS', 'withdraw_sn'),
            'uniacid'           => \YunShop::app()->uniacid,
            'member_id'         => $this->uid,
            'type'              => $withdraw_item['type'],
            'type_name'         => $withdraw_item['type_name'],
            'type_id'           => $withdraw_item['type_id'],
            'amounts'           => $withdraw_item['income'],
            'poundage'          => '0.00',
            'poundage_rate'     => '0.00',
            'actual_poundage'   => '0.00',
            'actual_amounts'    => '0.00',
            'servicetax'        => '0.00',
            'servicetax_rate'   => '0.00',
            'actual_servicetax' => '0.00',
            'pay_way'           => Withdraw::WITHDRAW_WITH_MANUAL,
            'manual_type'       => !empty($this->withdraw_set['manual_type']) ? $this->withdraw_set['manual_type'] : 1,
            'status'            => Withdraw::STATUS_INITIAL,
            'audit_at'          => null,
            'pay_at'            => null,
            'arrival_at'        => null,
            'created_at'        => time(),
            'updated_at'        => time(),
        ];
    }

    public function getLangTitle($data)
    {
        $lang = \Setting::get('shop.lang');
        $langData = $lang[$lang['lang']];
        $titleType = '';
        foreach ($langData as $key => $item) {
            $names = explode('_', $key);
            foreach ($names as $k => $name) {
                if ($k == 0) {
                    $titleType = $name;
                } else {
                    $titleType .= ucwords($name);
                }
            }

            if ($data == $titleType) {
                return $item[$key];
            }
        }

    }

    /**
     * @param $income_type
     * @return int|mixed
     */
    private function setPoundageRate($income_type)
    {
        !isset($this->income_set) && $this->income_set = $this->setIncomeSet($income_type);

        $value = array_get($this->income_set, 'poundage_rate', 0);

        //如果使用 提现到余额独立手续费
        if ($this->isUseBalanceSpecialSet()) {
            $value = array_get($this->withdraw_set, 'special_poundage', 0);
        }
        return $this->poundage_rate = empty($value) ? 0 : $value;
    }

    /**
     * @return int|mixed
     */
    private function setServiceTaxRate()
    {
        $value = array_get($this->withdraw_set, 'servicetax_rate', 0);

        //如果使用 提现到余额独立劳务税
        if ($this->isUseBalanceSpecialSet()) {
            $value = array_get($this->withdraw_set, 'special_service_tax', 0);
        }
        return $this->service_tax_rate = empty($value) ? 0 : $value;
    }

    /**
     * 提现到余额独立手续费比例
     * @return int|mixed
     */
    private function setSpecialPoundageRate()
    {
        $value = array_get($this->withdraw_set, 'special_poundage', 0);

        return $this->special_poundage_rate = empty($value) ? 0 : $value;
    }

    /**
     * 提现到余额独立劳务税
     * @return int|mixed
     */
    private function setSpecialServiceTaxRate()
    {
        $value = array_get($this->withdraw_set, 'special_service_tax', 0);

        return $this->special_service_tax_rate = empty($value) ? 0 : $value;
    }

    /**
     * 是否使用余额独立手续费、劳务税
     * @return bool
     */
    private function isUseBalanceSpecialSet()
    {
        if ($this->pay_way == Withdraw::WITHDRAW_WITH_BALANCE &&
            $this->getBalanceSpecialSet()
        ) {
            return true;
        }
        return false;
    }

    /**
     * 是否开启提现到余额独立手续费、劳务税
     * @return bool
     */
    private function getBalanceSpecialSet()
    {
        return empty(array_get($this->withdraw_set, 'balance_special', 0)) ? false : true;
    }

    /**
     * 手续费计算公式
     * @param $amount
     * @param $rate
     * @return string
     */
    private function poundageMath($amount, $rate)
    {
        return bcmul(bcdiv($amount,100,4),$rate,2);
    }


    /**
     * 获取收入类型独立设置
     * @param $income_type
     * @return mixed
     */
    private function setIncomeSet($income_type)
    {
        return $this->income_set = Setting::get('withdraw.' . $income_type);
    }

    private function getIncomeSet($mark)
    {
        return Setting::get('withdraw.' . $mark);
    }

    /**
     * @return mixed
     */
    private function getIncomeModel()
    {
        return Income::uniacid()->canWithdraw()
            ->where('member_id', $this->uid);
//        ->where('incometable_type', $this->item['class']);
    }

    /**
     * 可提现数据 item
     * @return array
     */
    private function getItemData($key, $income)
    {
        $this->withdraw_amounts = $this->getIncomeModel()->where('incometable_type', $income['class'])->sum('amount');

        $poundage = $this->poundageMath($this->withdraw_amounts, $this->poundage_rate);
        $service_tax = $this->poundageMath($this->withdraw_amounts - $poundage, $this->service_tax_rate);


        $special_poundage = $this->poundageMath($this->withdraw_amounts, $this->special_poundage_rate);
        $special_service_tax = $this->poundageMath(($this->withdraw_amounts - $special_poundage), $this->special_service_tax_rate);
        $can = $this->incomeIsCanWithdraw();
        if (in_array($income['type'], ['Mryt', 'StoreWithdraw'])) {
            $can = true;
        }
        return [
            'type'              => $income['class'],
            'key_name'          => $income['type'],
            'type_name'         => $this->getLangTitle($key) ? $this->getLangTitle($key) : $income['title'],
            'income'            => $this->withdraw_amounts,
            'poundage'          => $poundage,
            'poundage_rate'     => $this->poundage_rate,
            'servicetax'        => $service_tax,
            'servicetax_rate'   => $this->service_tax_rate,
            //'roll_out_limit'    => $this->getIncomeAmountFetter(),
            'can'               => $can,
            //'selected'          => $this->incomeIsCanWithdraw(),
            'type_id'           => $this->getIncomeTypeIds($income['class']),
            'special_poundage'  => $special_poundage,
            'special_poundage_rate'  => $this->special_poundage_rate,
            'special_service_tax'    => $special_service_tax,
            'special_service_tax_rate' => $this->special_service_tax_rate,
        ];
    }

    /**
     * 提现最小额度
     * @return string
     */
    private function getIncomeAmountFetter()
    {
        $value = array_get($this->income_set,'roll_out_limit', 0);
        return empty($value) ? 0 : $value;
    }

    /**
     * 是否可以提现
     * @return bool
     */
    private function incomeIsCanWithdraw()
    {
        if (bccomp($this->withdraw_amounts,0,2) != 1) {
            return false;
        }
        return true;
    }

    /**
     * 获取 item 对应 id 集
     * @return string
     */
    private function getIncomeTypeIds($income_class)
    {
        //if ($this->incomeIsCanWithdraw()) {
            $type_ids = '';
            foreach ($this->getIncomeModel()->where('incometable_type', $income_class)->get() as $ids) {
                $type_ids .= $ids->id . ",";
            }
            return $type_ids;
        //}
        //return '';
    }
}