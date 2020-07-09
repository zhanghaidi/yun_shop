<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/12/2
 * Time: 下午2:39
 */

namespace Yunshop\RechargeCode\frontend;


use app\common\components\ApiController;
use app\common\services\credit\ConstService;
use app\common\services\finance\PointService;
use Illuminate\Support\Facades\DB;
use Yunshop\RechargeCode\common\models\RechaergeCodeBindLog;
use Yunshop\RechargeCode\common\models\RechargeCode;
use Yunshop\RechargeCode\common\services\RechargeBalance;
use Yunshop\Love\Common\Services\LoveChangeService;


class CodeController extends ApiController
{
    private $recharge_code;
    private $uid;

    private function common()
    {
        $code_key = trim(request()->code_key);
        if (!$code_key) {
            return $this->errorJson('参数错误', [
                'status' => 0
            ]);
        }
        
        $recharge_code = RechargeCode::getCodeByKey($code_key)->first();
        if (!$recharge_code) {
            return $this->errorJson('未找到充值码', [
                'status' => 0
            ]);
        }
        if ($recharge_code->status == 1) {
            return $this->errorJson('充值码已过期', [
                'status' => 0
            ]);
        }
        if ($recharge_code->is_bind == 1) {
            return $this->errorJson('充值码已使用', [
                'status' => 0
            ]);
        }
        $this->recharge_code = $recharge_code;
        $this->uid = \YunShop::app()->getMemberId();
    }

    //plugin.recharge-code.frontend.code.get-recharge-code
    public function getRechargeCode()
    {
        $this->common();
        return $this->successJson('成功', [
            'status' => 1,
            'recharge_code' => $this->recharge_code
        ]);
    }

    public function recharge()
    {
        $this->common();
        
        if (!app('plugins')->isEnabled('love') && in_array($this->recharge_code->type, [ 3, 4 ] )) {
            return $this->errorJson('未开启'.trans('Yunshop\Love::love.name').'插件', [
                'status' => 0
            ]);
        }
        
        DB::transaction(function () {
            $this->recharge_code->is_bind = 1;
            $this->recharge_code->save();
            // 充值积分
            if ($this->recharge_code->type == 1) {
                $this->rechargePoint();
            }
            // 充值余额
            if ($this->recharge_code->type == 2) {
                $this->rechargeBalance();
            }
            // 充值可用爱心值
            if ($this->recharge_code->type == 3) {
                $this->rechargeLove('usable');
            }
            // 充值冻结爱心值
            if ($this->recharge_code->type == 4) {
                $this->rechargeLove('frozen');
            }
            // 添加充值记录
            $this->addRechargeLog();
        });
        //查出跳转链接
        $jump_setting = \Setting::get('plugin.recharge-code');
        $jump_url = \YunShop::request()->type == 2? $jump_setting['small_jump_link']:$jump_setting['jump_link'];
        return $this->successJson('充值成功', [
            'status' => 1,
            'jump_url'=>$jump_url
        ]);
    }

    private function addRechargeLog()
    {
        $bind_log_model = new RechaergeCodeBindLog();
        $bind_log_model->fill([
            'uniacid'           => \YunShop::app()->uniacid,
            'code_id'           => $this->recharge_code->id,
            'uid'               => $this->uid,
            'bind_time'         => time(),
            'code_information'  => $this->recharge_code
        ]);
    }

    private function rechargePoint()
    {
        (new PointService([
            'point_income_type' => PointService::POINT_INCOME_GET,
            'point_mode' => PointService::POINT_MODE_RECHARGE_CODE,
            'member_id' => $this->uid,
            'point' => $this->recharge_code->price,
            'remark' => "充值码[".$this->recharge_code->code_key."]充值积分[".$this->recharge_code->price."]"
        ]))->changePoint();
    }

    private function rechargeBalance()
    {
        (new RechargeBalance())->rechargeCode([
            'member_id'     => $this->uid,
            'remark'        => '充值码充值' . $this->recharge_code->price . "元",
            'change_value'  => $this->recharge_code->price,
            'relation'      => $this->recharge_code->code_key,
            'operator'      => ConstService::OPERATOR_MEMBER,
            'operator_id'   => $this->uid
        ]);
    }

    private function rechargeLove($type)
    {
        $love_type == 'usable' ? '可用' : '冻结';

        $loveData = [
            'member_id' => \YunShop::app()->getMemberId(),
            'change_value' => $this->recharge_code->price,
            'operator' => ConstService::OPERATOR_SHOP,
            'operator_id' => '0',
            'remark' => '充值码充值' . $this->recharge_code->price. '个'.$love_type.'爱心值',
            'relation' => ''
        ];

        (new LoveChangeService($type))->recharge($loveData);
    }

    // plugin.recharge-code.frontend.code.is-open
    public function isOpen()
    {
        $exist = app('plugins')->isEnabled('recharge-code');
        if (!$exist) {
            return $this->errorJson('未开启', [
                'status' => 0
            ]);
        }
        return $this->successJson('已开启', [
            'status' => 1
        ]);
    }
}