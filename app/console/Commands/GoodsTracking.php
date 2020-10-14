<?php

namespace app\Console\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GoodsTracking extends Command
{
    protected $signature = 'command:goodstracking';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '商品埋点数据统计命令行工具';

    /**
     * 公众号和小程序配置信息
     * @var array
     */
    protected $options = [];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        Log::getMonolog()->popHandler();
        Log::useFiles(storage_path('logs/schedule.run.log'), 'info');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        Log::info('------------------------ 商品埋点数据统计定时任务 BEGIN -------------------------------');
        try {
            //防止重复执行定时任务 判断如果有当天的数据就return
            $exist_data = DB::table('diagnostic_service_goods_tracking_statistics')->where('statistics_time', date('Y-m-d', time()))->first();
            if($exist_data){
                Log::info('商品埋点数据统计定时任务，重复执行！');
                return ;
            }
            //1  查询所有商品  分块处理
            DB::table('yz_goods')->where('status',1)->select('id', 'uniacid')->whereNull('deleted_at')->chunk(100, function ($goods) {

                $time_now = time();
                $todayTimestamp = strtotime(date('Y-m-d', $time_now));
                $yesterdayTimestamp = strtotime(date('Y-m-d', strtotime("-1 day")));
                $whereBetween = [$todayTimestamp, $yesterdayTimestamp];
                //2 遍历商品 统计商品前一天的数据 根据商品ID 统计埋点表的相应数据 1：查看 2、收藏 3、加购 4：下单 5：支付
                foreach ($goods as $value){
                    $view_num = count(DB::table('diagnostic_service_goods_tracking')->where(['goods_id' => $value['id'],'action' => 1])->whereBetween('create_time', $whereBetween)->groupBy('user_id')->get());
                    $favorites_num = DB::table('diagnostic_service_goods_tracking')->where(['goods_id' => $value['id'],'action' => 2])->whereBetween('create_time', $whereBetween)->count();
                    $add_purchase_num = DB::table('diagnostic_service_goods_tracking')->where(['goods_id' => $value['id'],'action' => 3])->whereBetween('create_time', $whereBetween)->sum('val');
                    $create_order_num = DB::table('diagnostic_service_goods_tracking')->where(['goods_id' => $value['id'],'action' => 4])->whereBetween('create_time', $whereBetween)->count();
                    $order_payment_num = DB::table('diagnostic_service_goods_tracking')->where(['goods_id' => $value['id'],'action' => 5])->whereBetween('create_time', $whereBetween)->count();

                    // todo 统计该商品支付金额 ，和开得义确认 ，他说不确定的需求 ，暂定订单完成状态下的金额，需要再次确认。
                    $order_payment_amount = DB::table('yz_order_goods as og')
                        ->join('yz_order as o', 'og.order_id', '=', 'o.id')
                        ->select('u.avatarurl', 'p.address')
                        ->where('o.status', 4)
                        ->where('og.goods_id', $value['id'])
                        ->whereBetween('o.finish_time', $whereBetween)
                        ->sum('o.price');

                    //组装数据
                    $data = [
                        'goods_id' => $value['id'],
                        'view_num' => $view_num,
                        'favorites_num' => $favorites_num,
                        'add_purchase_num' => $add_purchase_num,
                        'create_order_num' => $create_order_num,
                        'order_payment_num' => $order_payment_num,
                        'order_payment_amount' => $order_payment_amount,
                        'created_at' => date('Y-m-d H:i:s', $time_now),
                        'updated_at' => date('Y-m-d H:i:s', $time_now),
                        'statistics_time' => $todayTimestamp
                    ];
                   $res =  DB::table('diagnostic_service_goods_tracking_statistics')->insert($data);
                   if(!$res){
                       Log::info("---- 统计数据入库更新ERROR -------\n");
                   }
                }
            });

            Log::info("------------------------ 商品埋点数据统计定时任务 END -------------------------------\n");
        } catch (Exception $exception) {
            Log::info('商品埋点数据统计定时任务: ' . $exception->getMessage() . $exception->getLine());
        }
    }
}
