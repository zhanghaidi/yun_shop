<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019/1/16
 * Time: 6:08 PM
 */

namespace Yunshop\Nominate\admin;


use app\common\components\BaseController;
use app\common\events\member\MemberLevelValidityEvent;
use app\common\models\MemberShopInfo;
use app\common\models\Order;
use Yunshop\Nominate\jobs\AwardJob;
use Yunshop\Nominate\listeners\MemberLevelListener;
use Yunshop\Nominate\models\MemberParent;

class FixController extends BaseController
{
    public function test()
    {
        $parents = MemberParent::select()
            ->where('member_id', 37)
            ->orderBy('level', 'asc')
            ->get();
        $model = new MemberParent();
        $model->fill([
            'uniacid' => 3,
            'parent_id' => 37,
            'level' => 0,
            'member_id' => 37
        ]);
        dd($parents->prepend($model));
        exit;
    }

    public function index()
    {
        $member = MemberShopInfo::where('member_id', 2618)->first();
        (new MemberLevelListener())->test($member, 1, 14);
        /*if (!intval(request()->uid)) {
            dd('uid错误');
            exit;
        }
        // 测试 会员升级相关奖励
        $member = MemberShopInfo::where('member_id', intval(request()->uid))->first();
        event(new MemberLevelValidityEvent($member, 1, 16));*/
        dd('ok');
        exit;
    }

    public function order()
    {
        // 测试 团队业绩奖
        $order = Order::find(2333);
        (new AwardJob($order))->handle();
        dd('ok');
        exit;
    }
}