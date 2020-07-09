<?php
/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/3/16
 * Time: 下午5:38
 */

namespace Yunshop\Commission\admin;


use app\common\components\BaseController;
use app\common\facades\Setting;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;



use app\Jobs\OrderBonusUpdateJob;
use Illuminate\Foundation\Bus\DispatchesJobs;
use app\common\models\Order;
use Yunshop\Commission\Listener\OrderCreatedListener;
use Yunshop\Commission\models\AgentLevel;
use Yunshop\Commission\models\CommissionEditLog;
use Yunshop\Commission\models\CommissionOrder;
use Yunshop\Commission\models\Lose;
use Yunshop\Commission\services\CommissionOrderService;

class CommissionOrderController extends BaseController
{
    use DispatchesJobs;
    /**
     * @return string
     * @throws \Throwable
     */
    public function index()
    {
        set_time_limit(0);

        $set = Setting::get('plugin.commission');

        $clock_name = '早起打卡';
        if (app('plugins')->isEnabled('commission')) {
            $clock_set = \Setting::get('plugin.clock_in');
            $clock_name = $clock_set['plugin_name'] ?: '早起打卡';
        }

        $pageSize = 20;
        $agent_levels = AgentLevel::getLevels()->get();

        $search = CommissionOrderService::getSearch(\YunShop::request()->search);

//        dd($search);
        $builder = CommissionOrder::getOrder($search);
        if(request('scan_repetition')){
            // 查询重复的记录
            $builder->repetition();
        }
        $list = $builder->orderBy('id', 'desc')->paginate($pageSize);

        $pager = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());

        if ($search['statistics'] == 1) {
            $commission_total = CommissionOrder::getOrder($search)->sum('commission');
            $count['unpaid'] = CommissionOrder::getOrder($search)->where('status', '0')->sum('commission');
            $count['unliquidated'] = CommissionOrder::getOrder($search)->where('status', '1')->sum('commission');
            $count['already_settled'] = CommissionOrder::getOrder($search)->where('status', '2')->sum('commission');
            $count['not_present'] = CommissionOrder::getOrder($search)->where('status', '2')->where('withdraw', '0')->sum('commission');
            $count['withdraw'] = CommissionOrder::getOrder($search)->where('status', '2')->where('withdraw', '1')->sum('commission');

            $count['order_amount'] = CommissionOrder::getOrder($search)->sum('commission_amount');
        }

        if (!$search['time']) {
            $search['time']['start'] = date("Y-m-d H:i:s", time());
            $search['time']['end'] = date("Y-m-d H:i:s", time());
            $search['is_time'] = 0;
        }

        return view('Yunshop\Commission::admin.commission_order', [
            'set' => $set,
            'list' => $list,
            'total' => $list->total(),
            'pager' => $pager,
            'commission_total' => $commission_total,
            'count' => $count,
            'search' => $search,
            'agent_levels' => $agent_levels,
            'clock_name' => $clock_name,
            'defaultLevelName' => AgentLevel::getDefaultLevelName()
        ])->render();
    }

    public function getOrderAmount($list)
    {
        $amount = 0;
        foreach ($list as $item) {
            $amount += $item->order['price'];
        }
        return $amount;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit()
    {
        $id = intval(\YunShop::request()->id);
        $commissionModel = CommissionOrder::getOrderById($id, ['0', '1']);
        if (!$commissionModel) {
            return $this->errorJson('无此记录或已被删除', 'error');
        }

        $data_log = [
            'role' => \YunShop::app()->role,
            'content' => "修改佣金:" . $commissionModel->commission . "->",
            'type' => "update",
            'created_at' => time(),
        ];
        $commissionModel->setRawAttributes(['commission' => \YunShop::request()->commission]);

        $data_log['content'] .= $commissionModel->commission ;
        CommissionEditLog::addCommissionLog($data_log);

        if ($commissionModel->save()) {
            //修改分红时修改订单分红表
            $this->dispatch(new OrderBonusUpdateJob('yz_commission_order', 'commission', 'ordertable_id', 'ordertable_id', 'commission', $commissionModel->ordertalbe_id));
            return $this->successJson('佣金修改成功!', 'success');
        }
        return $this->errorJson('佣金修改失败!', 'error');
    }

    /**
     * @return string
     * @throws \Throwable
     */
    public function details()
    {
        $set = Setting::get('plugin.commission');
        $clock_name = '早起打卡';
        if (app('plugins')->isEnabled('commission')) {
            $clock_set = \Setting::get('plugin.clock_in');
            $clock_name = $clock_set['plugin_name'] ?: '早起打卡';
        }
        $id = request()->id;
        $commissionModel = CommissionOrder::find($id);
        $lose = Lose::select()->where('award_id', $commissionModel->id)->first();
        return view('Yunshop\Commission::admin.commission_details', [
            'commission' => $commissionModel,
            'clock_name' => $clock_name,
            'set' => $set,
            'lose' => $lose,
        ])->render();
    }

    public function export()
    {
        $file_name = date('Ymdhis', time()) . '分销订单导出';
        $search = CommissionOrderService::getSearch(\YunShop::request()->search);
        $list = CommissionOrder::getOrder($search)->orderBy('id', 'desc')->get();

        $export_data[0] = [
            'ID',
            '订单号',
            '购买者ID',
            '购买者信息',
            '订单金额',
            '分销计算金额/计算方式',
            '推荐人ID',
            '推荐者信息',
            '推荐者分销等级/分销层级/佣金比例',
            '佣金金额',
            '佣金状态',
            '订单完成时间'
        ];

        foreach ($list as $key => $item) {
            $status = $this->getStatusName($item['status'],$item['withdraw']);
            
            $level_name = $item['agent']['agent_level']['name'] == '默认等级' ? : AgentLevel::getDefaultLevelName();

            $export_data[$key + 1] = [
                $item['id'],
                $item['order']['order_sn'],
                $item['buy_id'],
                $item['order']['belongsToMember']['realname']?:$item['order']['belongsToMember']['username'],
                $item['order']['price'],
                $item['commission_amount'].'/'.$item['formula'],
                $item['parentMember']['uid'],
                $item['parentMember']['realname']?:$item['parentMember']['username'],
                $level_name.'/'.$item['hierarchy'].'/'.$item['commission_rate'],
                $item['commission'],
                $status,
                !empty(strtotime($item['order']['finish_time'])) ? $item['order']['finish_time'] : '' ,
            ];
        }
        \Excel::create($file_name, function ($excel) use ($export_data) {
            // Set the title
            $excel->setTitle('Office 2005 XLSX Document');

            // Chain the setters
            $excel->setCreator('芸众商城')
                ->setLastModifiedBy("芸众商城")
                ->setSubject("Office 2005 XLSX Test Document")
                ->setDescription("Test document for Office 2005 XLSX, generated using PHP classes.")
                ->setKeywords("office 2005 openxml php")
                ->setCategory("report file");

            $excel->sheet('info', function ($sheet) use ($export_data) {
                $sheet->rows($export_data);
            });
        })->export('xls');

    }
    public function getStatusName($status,$withdraw)
    {
        switch ($status) {
            case -1:
                return '无效佣金';
                break;
            case 0:
                return '预计佣金';
                break;
            case 1:
                return '未结算';
                break;
            case 2:
                if($withdraw == 0){
                    return '未提现';
                }elseif($withdraw == 1){
                    return '已提现';
                }else{
                    return '已结算';
                }
                break;

        }

    }

    public function fixOrder()
    {
        $error    = [];
        $orderIds = \YunShop::request()->id?:'';
        $config = \app\common\modules\shop\ShopConfig::current()->get('plugin.commission');
        $ordertable_type = $config['order_class'];

        $fixOrder = new OrderCreatedListener();

        if (!empty($orderIds)) {
            $orderIds = explode(',', $orderIds);

            foreach ($orderIds as $order_id) {
                if (!intval($order_id)) {
                    continue;
                }

                //分销订单中是否存在orderId
                $commissionOrderModel = CommissionOrder::getOrderByOrderId($ordertable_type,$order_id)->first();

                if (!is_null($commissionOrderModel)) {
                    array_push($error, $order_id);
                    continue;
                }

                $orderModel = Order::where('id', $order_id)->with('hasManyOrderGoods')->first();

                $fixOrder->fixCreatedOrder($orderModel);
            }
        }
    }

}