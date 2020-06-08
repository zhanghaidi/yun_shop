<?php
/**
 * Created by PhpStorm.
 *
 *    .--,       .--,
 *   ( (  \.---./  ) )
 *    '.__/o   o\__.'
 *       {=  ^  =}
 *        >  -  <
 *       /       \
 *      //       \\
 *     //|   .   |\\
 *     "'\       /'"_.-~^`'-.
 *        \  _  /--'         `
 *      ___)( )(___
 *     (((__) (__)))            高山仰止,景行行止.虽不能至,心向往之.生活步步是坎坷,笑到最后是大哥QQ：751818588.
 *
 * ---------------------*/

namespace app\common\services\finance;


use app\common\events\MessageEvent;
use app\common\facades\Setting;
use \app\common\models\Withdraw;
use app\common\models\notice\MessageTemp;

class MessageService
{
    /**
     * @var Withdraw
     */
    private $withdraw;

    /**
     * @var array
     */
    private $withdrawSet;

    /**
     * @var string
     */
    private $templateKey;

    private $apply=2;

    /**
     * @var array
     */
    private $statusComment = [
        Withdraw::STATUS_INVALID => '审核无效',
        Withdraw::STATUS_INITIAL => '提现申请',
        Withdraw::STATUS_AUDIT   => '审核通过',
        Withdraw::STATUS_PAY     => '已打款',
        Withdraw::STATUS_REBUT   => '审核驳回',
        Withdraw::STATUS_PAYING  => '打款中',
    ];


    public function __construct(Withdraw $withdraw)
    {
        $this->withdraw = $withdraw;
        $this->withdrawSet = Setting::get('withdraw.notice');
    }

    /**
     * @return int
     */
    private function getTemplateId()
    {
        return isset($this->withdrawSet[$this->templateKey]) ? $this->withdrawSet[$this->templateKey] : 0;
    }

    /**
     * 收入提现申请消息通知
     */
    public function applyNotice()
    {
        $this->templateKey = 'income_withdraw';
        $this->apply = 1;
        $this->sendNotice();
    }

    /**
     * 收入提现审核消息通知
     */
    public function auditNotice()
    {
        $this->templateKey = 'income_withdraw_check';
        $this->apply = 2;
        $this->sendNotice();
    }

    /**
     * 收入提现打款消息通知
     */
    public function payedNotice()
    {
        $this->templateKey = 'income_withdraw_pay';
        $this->apply = 2;
        $this->sendNotice();
    }

    /**
     * 收入提现申请消息通知
     */
    public function arrivalNotice()
    {
        $this->templateKey = 'income_withdraw_arrival';
        $this->apply = 2;
        $this->sendNotice();
    }

    /**
     * 收入提现错误消息通知
     *
     * @param $memberId
     */
    public function failureNotice($memberId)
    {
        $this->templateKey = 'income_withdraw_fail';
        $this->apply = 2;
        $this->sendNotice($memberId);
    }

    /**
     * 收入提现申请消息通知
     * @param int $memberId
     */
    private function sendNotice($memberId = 0)
    {
        $memberId = $memberId ? $memberId : $this->withdraw->member_id;

        if ($templateId = $this->getTemplateId()) {

            $news_link = MessageTemp::find($templateId)->news_link;
            $news_link = $news_link ?:'';

            event(new MessageEvent(
                $memberId,
                $templateId,
                $this->noticeParams(),
                $news_link
            ));
        }
    }

    /**
     * 收入提现申请消息通知
     */
    private function noticeParams()
    {
        return [
            [
                'name'  => '昵称',
                'value' => $this->nickname()
            ], [
                'name'  => '时间',
                'value' => $this->timeString()
            ], [
                'name'  => '收入类型',
                'value' => $this->typeName()
            ], [
                'name'  => '金额',
                'value' => $this->amount()
            ], [
                'name'  => '手续费',
                'value' => $this->poundage()
            ], [
                'name'  => '提现方式',
                'value' => $this->payWayName()
            ], [
                'name'  => '状态',
                'value' => $this->statusName()
            ], [
                'name'  => '提现单号',
                'value' => $this->withdrawSn()
            ], [
                'name'  => '审核通过金额',
                'value' => $this->actualAmount()
            ], [
                'name'  => '审核时间',
                'value' => $this->examineTime()
            ], [
                'name'  => '劳务税金额',
                'value' => $this->actualServiceTax()
            ], [
                'name'  => '提现到账金额',
                'value' => $this->actualAmount()
            ],
        ];
    }

    /**
     * @return string
     */
    private function nickname()
    {
        return $this->withdraw->hasOneMember ? $this->withdraw->hasOneMember->nickname : '';
    }

    /**
     * @return string
     */
    private function timeString()
    {
        return $this->withdraw->created_at;
    }

    /**
     * @return bool|string
     */
    private function examineTime()
    {
        return date("Y-m-d H:i:s",time());
    }

    /**
     * @return string
     */
    private function typeName()
    {
        return $this->withdraw->type_name;
    }

    /**
     * @return string
     */
    private function payWayName()
    {
        return $this->withdraw->getPayWayNameAttribute();
    }

    /**
     * @return string
     */
    private function statusName()
    {
        return $this->getStatusNameAttribute();
    }

    /**
     * @return double
     */
    private function amount()
    {

        return $this->withdraw->amounts;

    }

    /**
     * @return double
     */
    private function poundage()
    {
        if ($this->apply == 1) {
            return $this->withdraw->poundage;
        }

        return $this->withdraw->actual_poundage;
    }

    /**
     * @return mixed
     */
    private function actualServiceTax()
    {
        if ($this->apply == 1) {
            return $this->withdraw->servicetax;
        }

        return $this->withdraw->actual_servicetax;
    }

    /**
     * @return string
     */
    private function withdrawSn()
    {
        return $this->withdraw->withdraw_sn;
    }

    /**
     * @return double
     */
    private function actualAmount()
    {
        return $this->withdraw->actual_amounts;
    }

    /**
     * @return string
     */
    private function getStatusNameAttribute()
    {
        return $this->getStatusComment($this->withdraw->status);
    }

    /**
     * @param int $status
     * @return string
     */
    private function getStatusComment($status)
    {
        return isset($this->statusComment[$status]) ? $this->statusComment[$status] : '';
    }

}
