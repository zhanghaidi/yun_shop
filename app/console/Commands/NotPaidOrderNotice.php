<?php
namespace app\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use app\Jobs\SendTemplateMsgJob;

class NotPaidOrderNotice extends Command
{
    protected $signature = 'command:notpaidordernotice';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '待支付订单提醒';

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

        // 公众号
        $wechat_account = DB::table('account_wechats')
            ->select('key', 'secret')
            ->where('uniacid', 39)
            ->first();
        $this->options['wechat'] = [
            'app_id' => $wechat_account['key'],
            'secret' => $wechat_account['secret'],
        ];

        // 小程序
        $wxapp_account = DB::table('account_wxapp')
            ->select('key', 'secret')
            ->where('uniacid', 45)
            ->first();
        $this->options['wxapp'] = [
            'app_id' => $wxapp_account['key'],
            'secret' => $wxapp_account['secret'],
        ];
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Log::info("------------------------ 待支付订单提醒定时任务 BEGIN -------------------------------");

        // 提醒配置
        $setting_trade = DB::table('yz_setting')
            ->where('uniacid', 39)
            ->where('group', 'shop')
            ->where('key', 'trade')
            ->value('value');
        $setting_trade = unserialize($setting_trade);
        $setting_notice = DB::table('yz_setting')
            ->where('uniacid', 39)
            ->where('group', 'shop')
            ->where('key', 'notice')
            ->value('value');
        $setting_notice = unserialize($setting_notice);
        $message_template = DB::table('yz_message_template')
            ->where('notice_type', 'order_not_paid')
            ->first();

        // 商城提醒未开启、待支付订单提醒未开启、待支付订单提醒模板未配置、待支付订单提醒时间设置为空，说明不执行提醒
        $doexec = true;
        if (empty(intval($setting_notice['toggle']))
            || empty(intval($setting_notice['order_not_paid']))
            || empty(intval($setting_trade['not_paid_notice_minutes']))
            || empty($message_template)) {
            $doexec = false;
        }
        if (!$doexec) {
            $this->doNotice($setting_trade, $setting_notice, $message_template);
        }

        // Log::info("------------------------ 待支付订单提醒定时任务 END -------------------------------\n");
    }

    /**
     * 扫描待支付订单并提醒
     * @param $setting_trade
     * @param $setting_notice
     * @param $message_template
     * @return array
     */
    private function doNotice($setting_trade, $setting_notice, $message_template)
    {
        // 模板消息内容
        $notice_data = json_decode($message_template['data'], true);

        // 1、查询待支付订单（下单时间距离现在n~n+1分钟）
        $time_now = time();
        $wait_seconds = 60 * $setting_trade['not_paid_notice_minutes'];
        $check_time_range = [$time_now - $wait_seconds - 60, $time_now - $wait_seconds];
        $not_paid_order = DB::table('yz_order')
            ->select('id', 'uid', 'order_sn', 'price', 'create_time')
            ->whereBetween('create_time', $check_time_range)
            ->get()->toArray();
        $result['check_time_range'] = $check_time_range;
        $result['not_paid_order'] = $not_paid_order;

        if (!empty($not_paid_order)) {

            // 2、查询待支付订单关联的商品
            $order_goods = DB::table('yz_order_goods')
                ->select('order_id', 'title')
                ->whereIn('order_id', array_unique(array_column($not_paid_order, 'id')))
                ->get()->toArray();

            // 3、查询订单用户openid
            $order_uid = array_unique(array_column($not_paid_order, 'uid'));
            $wxapp_user = DB::table('diagnostic_service_user')
                ->select('ajy_uid', 'openid', 'unionid')
                ->whereIn('ajy_uid', $order_uid)
                ->get()->toArray();
            $wx_unionid = array_column($wxapp_user, 'unionid');
            $wechat_user = DB::table('mc_mapping_fans')
                ->select('uid', 'unionid', 'openid')
                ->where('follow', 1)
                ->where('uniacid', 39)
                ->whereIn('unionid', $wx_unionid)
                ->get()->toArray();

            // 4、组装用户数据
            $order_user = [];
            foreach ($order_uid as $uid) {
                $order_user[] = ['user_id' => $uid];
            }
            array_walk($order_user, function (&$item, $key) use ($order_uid, $wxapp_user, $wechat_user) {
                foreach ($wxapp_user as $user) {
                    if ($user['ajy_uid'] == $item['user_id']) {
                        $item['unionid'] = $user['unionid'];
                        $item['wxapp_openid'] = $user['openid'];
                        break;
                    }
                }
                $item['wechat_openid'] = '';
                foreach ($wechat_user as $user) {
                    if ($user['unionid'] == $item['unionid']) {
                        $item['wechat_openid'] = $user['openid'];
                        break;
                    }
                }
            });

            // 6、组装队列数据
            $job_list = [];
            $jump_page = '/pages/template/rumours/index?share=1&shareUrl=';
            $value_key_sort = ['goods_title', 'amount', 'order_sn', 'create_time', 'expire_time'];
            foreach ($not_paid_order as $order) {
                $job_item = [
                    'order_sn' => $order['order_sn'],
                    'amount' => $order['price'],
                    'create_time' => date('Y-m-d H:i', $order['create_time']),
                    'expire_time' => date('Y-m-d H:i', $order['create_time'] + (intval($setting_trade['close_order_days']) * 86400)),
                ];
                foreach ($order_goods as $goods) {
                    if ($goods['order_id'] == $order['id']) {
                        $job_item['goods_title'] = $goods['title'];
                        break;
                    }
                }
                foreach ($order_user as $user) {
                    if ($user['user_id'] == $order['uid']) {
                        $type = ($user['wechat_openid'] != '') ? 'wechat' : 'wxapp';
                        $openid = ($user['wechat_openid'] != '') ? $user['wechat_openid'] : $user['wxapp_openid'];

                        $job_item['type'] = $type;
                        $job_item['openid'] = $openid;
                        $job_item['options'] = $this->options[$type];
                        $job_item['template_id'] = $message_template['template_id'];

                        $job_item['notice_data']['first'] = ['value' => $message_template['first'], 'color' => $message_template['first_color']];
                        $value_key_idx = 0;
                        foreach ($notice_data as $nd_item) {
                            $job_item['notice_data'][$nd_item['keywords']] = [
                                'value' => $job_item[$value_key_sort[$value_key_idx]],
                                'color' => $nd_item['color'],
                            ]; ;
                            $value_key_idx++;
                        }
                        $job_item['notice_data']['remark'] = ['value' => $message_template['remark'], 'color' => $message_template['remark_color']];

                        $jump_tail = '/pages/shopping/order_detail/index?id=' . $order['id'];
                        $job_item['page'] = $jump_page . urlencode($jump_tail);
                    }
                }
                $job_list[] = $job_item;
            }

            // 7、添加消息发送任务到消息队列
            foreach ($job_list as $job_item) {
                $job = new SendTemplateMsgJob($job_item['type'], $job_item['options'], $job_item['template_id'], $job_item['notice_data'],
                    $job_item['openid'], '', $job_item['page']);
                $dispatch = dispatch($job);
                Log::info("模板消息内容:", $job_item);
                if ($job_item['type'] == 'wechat') {
                    Log::info("队列已添加:发送公众号模板消息", ['job' => $job, 'dispatch' => $dispatch]);
                } elseif ($job_item['type'] == 'wxapp') {
                    Log::info("队列已添加:发送小程序订阅模板消息", ['job' => $job, 'dispatch' => $dispatch]);
                }
            }
        }
    }
}
