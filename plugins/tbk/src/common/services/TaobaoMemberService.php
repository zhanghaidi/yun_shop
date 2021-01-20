<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com yangyu
 * Date: 2019/1/10
 * Time: 16:28
 */

namespace Yunshop\Tbk\common\services;


use app\common\exceptions\ShopException;
use Yunshop\Tbk\common\models\TbkMember;
use Yunshop\Tbk\common\models\TbkPid;

class TaobaoMemberService
{

    public function regTaobaoMember($memberId)
    {
        $tbkMember = TbkMember::select()->where('member_id', $memberId)->first();

        if ($tbkMember) {
            return $tbkMember;
        }

        $pid = TbkPid::select()->where('is_use', 0)->first();

        $tbkMemberModel = new TbkMember();
        $tbkMemberModel->uniacid = \YunShop::app()->uniacid;
        $tbkMemberModel->member_id = $memberId;
        $tbkMemberModel->pid = $pid->pid;
        $tbkMemberModel->save();

        $pid->update(['is_use' => 1]);
    }

    public function getMemberIdByPid($pid)
    {
        $tbkMember = TbkMember::select()->where('pid', $pid)->first();

        if (!$tbkMember) {
            throw new ShopException('无此pid用户. pid:' . $pid);
        }

        return $tbkMember;
    }

}