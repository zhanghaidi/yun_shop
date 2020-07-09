<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/12/13
 * Time: 下午7:55
 */

namespace Yunshop\Commission\admin;

use app\common\components\BaseController;
use app\common\models\Order;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Facades\DB;
use Yunshop\Commission\Jobs\UpgrateByOrderJob;
use Yunshop\Commission\Jobs\UpgrateByRegisterJob;
use Yunshop\Commission\Listener\OrderCreatedListener;
use Yunshop\Commission\models\Agents;
use Yunshop\Commission\models\CommissionOrder;
use Yunshop\Commission\services\UpgradeService;

/**
 * 修复重复生成的分佣记录
 * Class FixController
 * @package Yunshop\Commission\admin
 */
class FixController extends BaseController
{
    use DispatchesJobs;
    public $transactionActions = ['*'];

    public function test()
    {
        (new OrderCreatedListener())->handler(Order::find(3371));
    }

    public function up()
    {
        $order = collect();
        $order->uniacid = \Yunshop::app()->uniacid;
        $order->id = 1;
        $set = \Setting::get('plugin.commission');
        $agents = Agents::uniacid()->where('agent_level_id',0)->get();
        $levels = UpgradeService::getLevelUpgraded();
        foreach ($agents as $agent) {
            (new UpgrateByOrderJob($agent->member_id, 1, $order, $levels, $set))->handle();
        }
    }

    public function getStatement()
    {
        return CommissionOrder::uniacid()
            ->with(['order' => function ($query) {
                $query->select('id', 'order_sn', 'status');
            }])
            ->with(['OrderGoods' => function ($query) {
                $query->select('order_id', 'title', 'goods_price');
            }])
            ->with(['agent' => function ($query) {
                $query->select('member_id', 'agent_level_id');
                $query->with(['agentLevel' => function ($query) {
                    $query->select('id', 'name');
                }]);
            }])
            ->where(function ($query) {
                return $query->where(DB::raw('ifnull(`recrive_at`, 0) + (`settle_days` * 86400)'), '<=', time())
                    ->orWhere('settle_days', '=', '0');
            })
            ->where('status', '1')
            ->get();
    }

    public function error(){
        (new \Yunshop\Commission\admin\modules\dividend\WrongDataRepair())->handle();
        echo '执行成功';
        dd(1);
    }
}