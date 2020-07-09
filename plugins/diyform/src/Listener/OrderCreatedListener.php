<?php


namespace Yunshop\Diyform\Listener;

use app\common\facades\Setting;
use app\common\models\Member;
use app\common\models\Order;
use app\Jobs\OrderBonusJob;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Facades\Log;
use Yunshop\Diyform\models\DiyformOrderContentModel;
use Yunshop\TeamDividend\models\TeamDividendAgencyModel;
use Yunshop\TeamDividend\models\TeamDividendLevelModel;
use Yunshop\TeamDividend\models\TeamDividendModel;
use Yunshop\TeamDividend\models\YzMemberModel;
use Yunshop\TeamDividend\services\GetAgentsService;
use Yunshop\TeamDividend\services\MessageService;
use Yunshop\TeamDividend\services\OrderCreatedService;
use Yunshop\TeamDividend\models\GoodsTeamDividend;
use Yunshop\TeamDividend\services\TeamReturnService;


class OrderCreatedListener
{
    use DispatchesJobs;

    public static $uniqueAccountId = 0;
    public $AgentData;
    public $Rate;
    private $finish_rate = 0;
    private $finish_price = 0;
    public $AwardHierarchy;
    public $ExtraHierarchy;
    public $dividend_amount = 0;
    public $dividend_amount_to_flat = 0;
    public $hierarchy_num = 0;
    public $totalDividend = 0;
    private $recursion_filter = [];

    public function __construct()
    {
        self::$uniqueAccountId = \YunShop::app()->uniacid;

    }

    /**
     *
     * @param  Dispatcher $events
     * @return mixed
     */
    public function subscribe(Dispatcher $events)
    {

        $events->listen(\app\common\events\order\AfterOrderCreatedEvent::class, function ($event) {
            date_default_timezone_set("PRC");
            //订单model
            $model = $event->getOrderModel();
            $this->handle($model);
        });
    }

    public function handle($model)
    {
        $order_id = $model->id;
        $goods_id = array();
        for($i = 0, $len = count($model['hasManyOrderGoods']); $i < $len; ++$i) {
            $goods_id[] = $model['hasManyOrderGoods'][$i]['goods_id'];
        }
        $goods_id = explode(',',implode($goods_id, ','));

        DiyformOrderContentModel::uniacid()
            ->where('member_id',$model->uid)
            ->whereIn('goods_id',$goods_id)
            ->whereNull('order_id')
            ->update(['order_id'=>$order_id]);
        \Log::info('订单写入自定义表单1'.$model->id);

        return;
    }







}
