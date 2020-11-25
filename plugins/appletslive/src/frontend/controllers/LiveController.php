<?php
/**
 * Created by PhpStorm.
 * User: weifeng
 * Date: 2020-02-28
 * Time: 12:27
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
 *     (((__) (__)))     梦之所想,心之所向.
 */
namespace Yunshop\Appletslive\frontend\controllers;

use app\common\exceptions\AppException;
use app\common\facades\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Yunshop\Appletslive\common\services\CacheService;
use Yunshop\Appletslive\common\services\BaseService;
use app\common\models\AccountWechats;
use app\Jobs\SendTemplateMsgJob;

/**
 * Class LiveController
 * @package Yunshop\Appletslive\frontend\controllers
 */
class LiveController extends BaseController
{
    protected $user_id = 0;
    protected $uniacid = 45;
    protected $is_follow_account = false;

    /**
     * LiveController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->user_id = \YunShop::app()->getMemberId();
        $this->is_follow_account = $this->checkIsFollowAccount();
    }

    /**
     * 检测是否关注了商城公众号(只有关注了公众号的用户才能发模板消息)
     * @return bool
     */
    private function checkIsFollowAccount()
    {
        if (!$this->user_id) {
            return false;
        }
        $wxapp_user = DB::table('diagnostic_service_user')->where('ajy_uid', $this->user_id)->first();
        if ($wxapp_user) {
            $wechat_fan_info = DB::table('mc_mapping_fans')
                ->where('uniacid', 39)
                ->where('unionid', $wxapp_user['unionid'])
                ->first();
            if (!empty($wechat_fan_info) && $wechat_fan_info['follow'] == 1) {
                return true;
            }
        }
        return false;
    }

    /**
     * 需要登录
     */
    private function needLogin()
    {
        if (!$this->user_id) {
            response()->json([
                'result' => 41009,
                'msg' => '请登录',
                'data' => null,
            ], 200, ['charset' => 'utf-8'])->send();
            exit;
        }
    }

    /**
     * 需要关注公众号
     */
    private function needFollowAccount()
    {
        if (!$this->is_follow_account) {
            response()->json([
                'result' => 41010,
                'msg' => '您还没有关注公众号',
                'data' => null,
            ], 200, ['charset' => 'utf-8'])->send();
            exit;
        }
    }

    /**
     * 获取关联公众号关注链接
     * @return \Illuminate\Http\JsonResponse
     */
    public function followlink()
    {
        $setting = Setting::get('plugin.min_app');
        return $this->successJson('获取成功', $setting['follow_link']);
    }

    /************************ 测试用代码 BEGIN ************************/

    /**
     * 测试群发微信公众号模板消息
     */
    public function testgroupsendtemplatemsg()
    {
        $result = [];
        $start_time = implode('.', array_reverse(explode(' ', substr(microtime(), 2))));

        // $openid_list = DB::table('mc_mapping_fans')
        //     ->where('uniacid', 39)
        //     ->pluck('openid');
        $openid_list = [
            'owVKQwWK2G_K6P22he4Fb2nLI6HI',
            'owVKQwY67eDMg2d4qkIp1wvd5jEA',
            'owVKQwV0BnkyMWAyfpboHr_ezSd4',
            'owVKQwXX9lpwoZanxQyvKH9zHrrU',
            'owVKQwWovCGMi5aV9PxtcVaa0lHc',
        ];
        $account = AccountWechats::getAccountByUniacid(39);
        $options = [
            'app_id' => $account['key'],
            'secret' => $account['secret'],
        ];
        $template_id = 'c-tYzcbVnoqT33trwq6ckW_lquLDPmqySXvntFJEMhE';
        $notice_data = [
            'first' => ['value' => '尊敬的用户,您订阅的课程有新视频要发布啦~', 'color' => '#173177'],
            'keyword1' => ['value' => '【和大师一起学艾灸】', 'color' => '#173177'],
            'keyword2' => ['value' => '长期有效', 'color' => '#173177'],
            'keyword3' => ['value' => '更新中', 'color' => '#173177'],
            'remark' => [
                'value' => '最新视频【每次艾灸几个穴位合适】将于' . date('Y-m-d H:i', strtotime('+15 minutes')) . '震撼发布!',
                'color' => '#173177',
            ],
        ];
        foreach ($openid_list as $openid) {
            for ($i = 0; $i < 10; $i++) {
                $job = new SendTemplateMsgJob('wechat', $options, $template_id, $notice_data, $openid, '', '');
                $dispatch = dispatch($job);
                $result[] = [
                    'tips' => '队列已添加:发送公众号模板消息', 'job' => $job, 'dispatch' => $dispatch
                ];
            }
        }

        $end_time = implode('.', array_reverse(explode(' ', substr(microtime(), 2))));
        return $this->successJson('课程提醒队列测试', [
            'start_time' => $start_time,
            'end_time' => $end_time,
            'cost' => bcsub($end_time, $start_time, 8) . ' seconds',
            'result' => $result,
        ]);
    }

    /**
     * 测试发送一条微信公众号模板消息
     */
    public function testsendtemplatemsg()
    {
        $start_time = implode('.', array_reverse(explode(' ', substr(microtime(), 2))));

        $account = AccountWechats::getAccountByUniacid(39);
        $options = [
            'app_id' => $account['key'],
            'secret' => $account['secret'],
        ];
        $template_id = 'c-tYzcbVnoqT33trwq6ckW_lquLDPmqySXvntFJEMhE';
        $notice_data = [
            'first' => ['value' => '尊敬的用户,您订阅的课程有新视频要发布啦~', 'color' => '#173177'],
            'keyword1' => ['value' => '【和大师一起学艾灸】', 'color' => '#173177'],
            'keyword2' => ['value' => '长期有效', 'color' => '#173177'],
            'keyword3' => ['value' => '更新中', 'color' => '#173177'],
            'remark' => [
                'value' => '最新视频【每次艾灸几个穴位合适】将于' . date('Y-m-d H:i', strtotime('+15 minutes')) . '震撼发布!',
                'color' => '#173177',
            ],
        ];
        $openid = 'owVKQwWK2G_K6P22he4Fb2nLI6HI';

        $job = new SendTemplateMsgJob('wechat', $options, $template_id, $notice_data, $openid, '', '');
        $dispatch = dispatch($job);
        $result['wechat'] = ['job' => $job, 'dispatch' => $dispatch];

        $options = [
            'app_id' => 'wxcaa8acf49f845662',
            'secret' => 'f627c835de1b4ba43fe2cbcb95236c52',
        ];
        $template_id = 'ABepy-L03XH_iU0tPd03VUV9KQ_Vjii5mClL7Qp8_jc';
        $notice_data = [
            'thing1' => ['value' => '课程更新', 'color' => '#173177'],
            'thing2' => ['value' => '【和大师一起学艾灸】', 'color' => '#173177'],
            'name3' => ['value' => '艾居益灸师', 'color' => '#173177'],
            'thing4' => ['value' => date('Y-m-d H:i', strtotime('+15 minutes')), 'color' => '#173177'],
        ];
        $openid = 'oP9ym5Bxp6D_sERpj340uIxuaUIo';
        $page = 'pages/template/rumours/index?room_id=5';

        $job = new SendTemplateMsgJob('wxapp', $options, $template_id, $notice_data, $openid, '', $page);
        $dispatch = dispatch($job);
        $result['wxapp'] = ['job' => $job, 'dispatch' => $dispatch];

        $end_time = implode('.', array_reverse(explode(' ', substr(microtime(), 2))));
        return $this->successJson('课程提醒队列测试', [
            'start_time' => $start_time,
            'end_time' => $end_time,
            'cost' => bcsub($end_time, $start_time, 8) . ' seconds',
            'result' => $result,
        ]);
    }

    /**
     * 测试课程提醒定时任务
     */
    public function testcoursereminder()
    {
        // 公众号配置信息
        $wechat_account = DB::table('account_wechats')
            ->select('key', 'secret')
            ->where('uniacid', 39)
            ->first();
        $options['wechat'] = [
            'app_id' => $wechat_account['key'],
            'secret' => $wechat_account['secret'],
        ];

        // 小程序配置信息
        $wxapp_account = DB::table('account_wxapp')
            ->select('key', 'secret')
            ->where('uniacid', 45)
            ->first();
        $options['wxapp'] = [
            'app_id' => $wxapp_account['key'],
            'secret' => $wxapp_account['secret'],
        ];

        // 1、查询距离当前时间点15~20分钟之间即将发布的视频
        $time_now = time();
        $check_time_range = [$time_now + 900, $time_now + 1200];
        $replay_publish_soon = DB::table('yz_appletslive_replay')
            ->select('id', 'rid', 'title', 'doctor', 'publish_time')
            ->whereBetween('publish_time', $check_time_range)
            ->get()->toArray();
        $result = [
            'time_now' => $time_now,
            'check_time_range' => $check_time_range,
            'replay_publish_soon' => $replay_publish_soon,
        ];

        if (!empty($replay_publish_soon)) {

            // 2、查询即将发布的视频关联的课程
            $rela_room = DB::table('yz_appletslive_room')
                ->whereIn('id', array_unique(array_column($replay_publish_soon, 'rid')))
                ->pluck('name', 'id')->toArray();

            // 3、查询关注了这些课程的所有小程序用户信息(openid)
            $subscribed_user = DB::table('yz_appletslive_room_subscription')
                ->select('user_id', 'room_id')
                ->where('room_id', array_keys($rela_room))
                ->get()->toArray();
            if (!empty($subscribed_user)) {
                $subscribed_uid = array_unique(array_column($subscribed_user, 'user_id'));
                // 3.1、存在已关注课程的用户，查询用户openid
                $wxapp_user = DB::table('diagnostic_service_user')
                    ->select('ajy_uid', 'openid', 'unionid')
                    ->whereIn('ajy_uid', $subscribed_uid)
                    ->get()->toArray();
                $subscribed_unionid = array_column($wxapp_user, 'unionid');
                $wechat_user = DB::table('mc_mapping_fans')
                    ->select('uid', 'openid', 'follow')
                    ->whereIn('unionid', $subscribed_unionid)
                    ->get()->toArray();
                array_walk($subscribed_user, function (&$item) use ($wxapp_user, $wechat_user) {
                    foreach ($wxapp_user as $user) {
                        if ($user['ajy_uid'] == $item['user_id']) {
                            $item['unionid'] = $user['unionid'];
                            $item['wxapp_openid'] = $user['openid'];
                            break;
                        }
                    }
                    $item['wechat_openid'] = '';
                    foreach ($wechat_user as $user) {
                        if ($user['unionid'] == $item['unionid'] && $user['follow'] == 1) {
                            $item['wechat_openid'] = $user['openid'];
                            break;
                        }
                    }
                });
            }
            $result['subscribed_uid'] = $subscribed_uid;

            // 4、组装队列数据
            $job_list = [];
            foreach ($replay_publish_soon as $replay) {
                // 4.1、当前课程有哪些订阅用户
                foreach ($subscribed_user as $user) {
                    if ($user['room_id'] == $replay['rid']) {
                        $type = ($user['wechat_openid'] != '') ? 'wechat' : 'wxapp';
                        $openid = ($user['wechat_openid'] != '') ? $user['wechat_openid'] : $user['wxapp_openid'];
                        $job_param = $this->makeJobParam($type, $rela_room[$replay['rid']], $replay);
                        $page = 'pages/template/rumours/index?room_id=' . $replay['rid'];
                        array_push($job_list, [
                            'type' => $type,
                            'options' => $options[$type],
                            'template_id' => $job_param['template_id'],
                            'notice_data' => $job_param['notice_data'],
                            'openid' => $openid,
                            'page' => $page,
                        ]);
                    }
                }
            }

            $result['job_list'] = $job_list;
        }

        return $this->successJson('测试：课程提醒定时任务', $result);
    }

    /**
     * 组装Job任务需要的参数
     * @param $type
     * @param $room_name
     * @param $replay_info
     * @return array
     */
    private function makeJobParam($type, $room_name, $replay_info)
    {
        $param = [];
        if ($type == 'wechat') {
            $param['template_id'] = 'c-tYzcbVnoqT33trwq6ckW_lquLDPmqySXvntFJEMhE';
            $param['notice_data'] = [
                'first' => ['value' => '尊敬的用户,您订阅的课程有新视频要发布啦~', 'color' => '#173177'],
                'keyword1' => ['value' => '【' . $room_name . '】', 'color' => '#173177'],
                'keyword2' => ['value' => '长期有效', 'color' => '#173177'],
                'keyword3' => ['value' => '更新中', 'color' => '#173177'],
                'remark' => [
                    'value' => '最新视频【' . $replay_info['title'] . '】将于' . date('Y-m-d H:i', $replay_info['publish_time']) . '震撼发布!',
                    'color' => '#173177',
                ],
            ];
        } elseif ($type == 'wxapp') {
            $param['template_id'] = 'ABepy-L03XH_iU0tPd03VUV9KQ_Vjii5mClL7Qp8_jc';
            $param['notice_data'] = [
                'thing1' => ['value' => '课程更新', 'color' => '#173177'],
                'thing2' => ['value' => '【' . $room_name . '】', 'color' => '#173177'],
                'name3' => ['value' => $replay_info['doctor'], 'color' => '#173177'],
                'thing4' => ['value' => '最新视频【' . $replay_info['title'] . '】将于' . date('Y-m-d H:i', $replay_info['publish_time']) . '震撼发布!', 'color' => '#173177'],
            ];
        }
        return $param;
    }

    /**
     * 测试待支付订单提醒定时任务
     */
    public function testnotpaidordernotice()
    {
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
        if (empty(intval($setting_notice['toggle']))
            || empty(intval($setting_notice['order_not_paid']))
            || empty(intval($setting_trade['not_paid_notice_minutes']))
            || empty($message_template)) {
            return $this->successJson('测试：待支付订单提醒未开启', [
                'setting_trade' => $setting_trade,
                'setting_notice' => $setting_notice,
                'message_template' => $message_template,
            ]);
        }

        // 公众号配置信息
        $wechat_account = DB::table('account_wechats')
            ->select('key', 'secret')
            ->where('uniacid', 39)
            ->first();
        $options['wechat'] = [
            'app_id' => $wechat_account['key'],
            'secret' => $wechat_account['secret'],
        ];

        // 小程序配置信息
        $wxapp_account = DB::table('account_wxapp')
            ->select('key', 'secret')
            ->where('uniacid', 45)
            ->first();
        $options['wxapp'] = [
            'app_id' => $wxapp_account['key'],
            'secret' => $wxapp_account['secret'],
        ];

        // 模板消息内容
        $notice_data = json_decode($message_template['data'], true);

        // 1、查询待支付订单（下单时间距离现在10~15分钟）
        $time_now = time();
        $check_time_range = [$time_now - 1200, $time_now - 900];
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
                ->select('uid', 'unionid', 'openid', 'follow')
                ->whereIn('unionid', $wx_unionid)
                ->get()->toArray();

            // 4、组装用户数据
            $order_user = [];
            foreach ($order_uid as $uid) {
                $order_user[] = ['user_id' => $uid];
            }
            array_walk($order_user, function (&$item) use ($order_uid, $wxapp_user, $wechat_user) {
                foreach ($wxapp_user as $user) {
                    if ($user['ajy_uid'] == $item['user_id']) {
                        $item['unionid'] = $user['unionid'];
                        $item['wxapp_openid'] = $user['openid'];
                        break;
                    }
                }
                $item['wechat_openid'] = '';
                foreach ($wechat_user as $user) {
                    if ($user['unionid'] == $item['unionid'] && $user['follow'] == 1) {
                        $item['wechat_openid'] = $user['openid'];
                        break;
                    }
                }
            });
            $result['order_user_list'] = $order_user;

            // 5、组装队列数据
            $job_list = [];
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
                        $page = 'pages/template/rumours/index?order_id=' . $order['id'];
                        $job_item['type'] = $type;
                        $job_item['options'] = $options[$type];
                        $job_item['template_id'] = $message_template['template_id'];

                        foreach ($value_key_sort as $value_key_idx => $value_key_val) {
                            $notice_data[$value_key_idx]['value'] = $job_item[$value_key_val];
                        }
                        $job_item['notice_data'] = $notice_data;

                        $job_item['openid'] = $openid;
                        $job_item['page'] = $page;
                    }
                }
                $job_list[] = $job_item;
            }

            $result['job_list'] = $job_list;
        }

        return $this->successJson('测试：待支付订单提醒定时任务', $result);
    }

    /**
     * 测试订单提交推送给灸师
     * @return \Illuminate\Http\JsonResponse
     */
    public function testcreateorder()
    {
        $jiushi_id_arr = ['hxy'];
        $jiushi_id = $jiushi_id_arr[mt_rand(0, count($jiushi_id_arr) - 1)];
        $order = ['id' => time(), 'price' => (mt_rand(1, 30000) . '.' . mt_rand(10, 99))];

        $cache_key = 'to_push_list_' . $jiushi_id;
        $cache_tag = 'swoole_websocket:JiushiOrderPusher';
        $to_push_list = Cache::tags([$cache_tag])->get($cache_key);

        if (empty($to_push_list)) {
            $to_push_list = [['jiushi_id' => $jiushi_id, 'order' => $order]];
        } else {
            $to_push_list[] = ['jiushi_id' => $jiushi_id, 'order' => $order];
        }
        Cache::tags([$cache_tag])->forever($cache_key, $to_push_list);

        return $this->successJson('ok', Cache::tags([$cache_tag])->get($cache_key));
    }

    /************************ 测试用代码 END ************************/

    /************************ 课程/录播 相关接口 BEGIN ************************/

    /**
     * 分页获取课程列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function roomlist()
    {
        $page = request()->get('page', 1);
        $limit = request()->get('limit', 10);

        $page_val = CacheService::getRecordedRoomList($page, $limit);
        if (!empty($page_val['list'])) {
            $page_val['list'] = $page_val['list']->toArray();
            $numdata = CacheService::getRoomNum(array_column($page_val['list'], 'id'));
            $my_subscription = ($this->user_id ? CacheService::getUserSubscription($this->user_id) : []);

            foreach ($page_val['list'] as $k => $v) {

                $key = 'key_' . $v['id'];
                $page_val['list'][$k]['hot_num'] = $numdata[$key]['hot_num'];
                $page_val['list'][$k]['subscription_num'] = $numdata[$key]['subscription_num'];
                $page_val['list'][$k]['view_num'] = $numdata[$key]['view_num'];
                $page_val['list'][$k]['comment_num'] = $numdata[$key]['comment_num'];
                $page_val['list'][$k]['has_subscription'] = (array_search($page_val['list'][$k]['id'], $my_subscription) === false) ? false : true;
                $page_val['list'][$k]['goods_info'] = [];
                if($v['goods_id'] > 0 && $v['buy_type'] == 1){
                    $page_val['list'][$k]['goods_info'] = DB::table('yz_goods')->where('id',$v['goods_id'])->first();
                }

            }
        }

        return $this->successJson('获取成功', $page_val);
    }

    /**
     * 获取课程详情信息
     * @return \Illuminate\Http\JsonResponse
     */
    public function roominfo()
    {
        // $sharer_uid = request()->get('sharer_uid', 0);
        // if ($sharer_uid) {
        //     Log::info('someone visit room detail page via sharing link. sharer uid id:' . $sharer_uid);
        // }

        $room_id = request()->get('room_id', 0);
        $room_info = CacheService::getRoomInfo($room_id);
        if (!$room_info) {
            return $this->errorJson('课程不存在', $room_info);
        }

        CacheService::setRoomNum($room_id, 'view_num');
        $numdata = CacheService::getRoomNum($room_id);
        // $subscription = CacheService::getRoomSubscription($room_id);
        // $room_info['subscription'] = $subscription;
        $room_info['hot_num'] = $numdata['hot_num'];
        $room_info['subscription_num'] = $numdata['subscription_num'];
        $room_info['view_num'] = $numdata['view_num'];
        $room_info['comment_num'] = $numdata['comment_num'];
        $my_subscription = ($this->user_id ? CacheService::getUserSubscription($this->user_id) : []);
        $room_info['has_subscription'] = ($this->user_id ? ((array_search($room_id, $my_subscription) === false) ? false : true) : false);
        if ($room_info['type'] == 0) {
            $room_info['start_time'] = date('Y-m-d H:i', $room_info['start_time']);
            $room_info['end_time'] = date('Y-m-d H:i', $room_info['end_time']);
        }

        return $this->successJson('获取成功', $room_info);
    }

    /**
     * 订阅课程
     * @return \Illuminate\Http\JsonResponse
     */
    public function roomsubscription()
    {
        $this->needLogin();
        //$this->needFollowAccount();

        $input = request()->all();
        if (!array_key_exists('room_id', $input)) {
            return $this->errorJson('缺少参数');
        }

        $table = 'yz_appletslive_room_subscription';
        $map = [['room_id', '=', $input['room_id']], ['user_id', '=', $this->user_id]];
        $subscripInfo = DB::table($table)->where($map)->first();
        if (!$subscripInfo) {
            DB::table($table)->insert([
                'uniacid' => $this->uniacid,
                'room_id' => $input['room_id'],
                'user_id' => $this->user_id,
                'create_time' => time(),
                'type' => APPLETSLIVE_ROOM_TYPE_COURSE,
            ]);
            CacheService::setRoomNum($input['room_id'], 'subscription_num');
            CacheService::setUserSubscription($this->user_id, $input['room_id']);
            CacheService::setRoomSubscription($input['room_id'], $this->user_id);

            $msg = '订阅成功';

        }else{

            if($subscripInfo['status'] == 1){
                DB::table($table)->where($map)->update(['status' => 0]);
                $msg = '取消订阅成功';
            }else{
                DB::table($table)->where($map)->update(['status' => 1]);
//                CacheService::setRoomNum($input['room_id'], 'subscription_num');
//                CacheService::setUserSubscription($this->user_id, $input['room_id']);
//                CacheService::setRoomSubscription($input['room_id'], $this->user_id);
                $msg = '订阅成功';
            }
            //刷新缓存
            $room_id   = $input['room_id'];

            $cache_key = "api_live_room_subscription|$room_id";
            Cache::forget($cache_key);

            $cache_key_user_subscription = "api_live_user_subscription|$this->user_id";
            Cache::forget($cache_key_user_subscription);

            Cache::forget(CacheService::$cache_keys['brandsale.albumsubscription']);

            Cache::forget(CacheService::$cache_keys['brandsale.albumusersubscription']);

            $cache_key_room_num = 'api_live_room_num';
            Cache::forget($cache_key_room_num);
        }
        
        return $this->successJson($msg);

    }

    /**
     * 我订阅的课程
     * @return \Illuminate\Http\JsonResponse
     */
    public function mysubscription()
    {
        $this->needLogin();

        $page = request()->get('page', 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $my_subscription = CacheService::getUserSubscription($this->user_id);
        $list = array_slice($my_subscription, $offset, $limit);
        foreach ($list as $k => $v) {
            $list[$k] = CacheService::getRoomInfo($v);
        }

        if (!empty($list)) {
            $numdata = CacheService::getRoomNum(array_column($list, 'id'));
            foreach ($list as $k => $v) {
                $key = 'key_' . $v['id'];
                $list[$k]['hot_num'] = $numdata[$key]['hot_num'];
                $list[$k]['subscription_num'] = $numdata[$key]['subscription_num'];
                $list[$k]['view_num'] = $numdata[$key]['view_num'];
                $list[$k]['comment_num'] = $numdata[$key]['comment_num'];
                if ($list[$k]['type'] == 0) {
                    $list[$k]['start_time'] = date('Y-m-d H:i', $list[$k]['start_time']);
                    $list[$k]['end_time'] = date('Y-m-d H:i', $list[$k]['end_time']);
                }
            }
        }

        $total = count($my_subscription);
        return $this->successJson('获取成功', [
            'total' => $total,
            'totalPage' => ceil($total / $limit),
            'list' => $list,
        ]);
    }

    /**
     * 分页获取课程评论列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function roomcommentlist()
    {
        $room_id = request()->get('room_id', 0);
        $page = request()->get('page', 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $cache_val = CacheService::getRoomComment($room_id);
        return $this->successJson('获取成功', [
            'total' => $cache_val['total'],
            'list' => array_slice($cache_val['list'], $offset, $limit),
        ]);
    }

    /**
     * 课程添加评论
     * @return \Illuminate\Http\JsonResponse
     * @throws AppException
     */
    public function roomcommentadd()
    {
        $this->needLogin();
        $input = request()->all();
        if (!array_key_exists('room_id', $input) || !array_key_exists('content', $input)) {
            return $this->errorJson('缺少参数');
        }
        if (is_string($input['content']) && strlen(trim($input['content'])) == 0) {
            return $this->errorJson('评论内容不能为空');
        }

        // 评论内容敏感词过滤
        $content = trim($input['content']);
        $wxapp_base_service = new BaseService();
        $sensitive_check = $wxapp_base_service->msgSecCheck($content);
        if (!is_bool($sensitive_check) || $sensitive_check === false) {
            return $this->errorJson('评论内容包含敏感词', $sensitive_check);
        }
        $content = $wxapp_base_service->textCheck($content);

        // 组装插入数据
        $comment_num_inc = true;
        $insert_data = [
            'uniacid' => $this->uniacid,
            'room_id' => $input['room_id'],
            'user_id' => $this->user_id,
            'content' => $content,
            'create_time' => time(),
        ];
        if (array_key_exists('parent_id', $input) && $input['parent_id'] > 0) {
            $parent = DB::table('yz_appletslive_room_comment')->where('id', $input['parent_id'])->first();
            if ($parent) {
                $comment_num_inc = false;
                $insert_data['parent_id'] = $parent['id'];
                $insert_data['is_reply'] = 1;
                $insert_data['rele_user_id'] = $parent['user_id'];
            }
        }

        $id = DB::table('yz_appletslive_room_comment')->insertGetId($insert_data);
        if ($comment_num_inc) {
            CacheService::setRoomNum($input['room_id'], 'comment_num');
        }
        CacheService::setRoomComment($input['room_id']);
        return $this->successJson('评论成功', ['id' => $id, 'content' => $content]);
    }

    /**
     * 课程删除评论
     * @return \Illuminate\Http\JsonResponse
     * @throws AppException
     */
    public function roomcommentdel()
    {
        $this->needLogin();
        $input = request()->all();
        if (!array_key_exists('comment_id', $input) || empty(intval($input['comment_id']))) {
            return $this->errorJson('评论id有误');
        }
        $is_mine = DB::table('yz_appletslive_room_comment')
            ->where('id', $input['comment_id'])
            ->where('user_id', $this->user_id)
            ->first();
        if (!$is_mine) {
            return $this->errorJson('只能删除自己的评论');
        }
        DB::table('yz_appletslive_room_comment')->where('id', $input['comment_id'])->delete();
        if ($is_mine['parent_id'] == 0) {
            CacheService::setRoomNum($is_mine['room_id'], 'comment_num', true);
        }
        CacheService::setRoomComment($is_mine['room_id']);
        return $this->successJson('删除成功');
    }

    /**
     * 获取录播列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function replaylist()
    {
        $room_id = request()->get('room_id', 0);
        $page = request()->get('page', 1);
        $limit = request()->get('limit', 10);
        $offset = ($page - 1) * $limit;
        $replays = CacheService::getRoomReplays($room_id);
        $numdata = CacheService::getReplayNum(array_column($replays['list'], 'id'));
        foreach ($replays['list'] as $k => $v) {
            $key = 'key_' . $v['id'];
            $replays['list'][$k]['hot_num'] = $numdata[$key]['hot_num'];
            $replays['list'][$k]['view_num'] = $numdata[$key]['view_num'];
            $replays['list'][$k]['comment_num'] = $numdata[$key]['comment_num'];
            $replays['list'][$k]['watch_num'] = $numdata[$key]['watch_num'];
        }
        return $this->successJson('获取成功', [
            'total' => $replays['total'],
            'list' => array_slice($replays['list'], $offset, $limit),
            'replays' => $replays,
            'numdata' => $numdata,
        ]);
    }

    /**
     * 获取录播详情信息
     * @return \Illuminate\Http\JsonResponse
     */
    public function replayinfo()
    {
        $replay_id = request()->get('replay_id', 0);
        $replay_info = CacheService::getReplayInfo($replay_id);
        if (!$replay_info) {
            return $this->errorJson('视频不存在', $replay_info);
        }

        CacheService::setReplayNum($replay_id, 'view_num');
        if ($this->user_id && $replay_info['media_url'] != '') {
            CacheService::setReplayNum($replay_id, 'watch_num', $this->user_id);
            CacheService::setUserWatch($this->user_id, $replay_info['id']);
        }
        $numdata = CacheService::getReplayNum($replay_id);
        // $my_watch = ($this->user_id ? CacheService::getUserWatch($this->user_id) : []);
        $replay_info['hot_num'] = $numdata['hot_num'];
        $replay_info['view_num'] = $numdata['view_num'];
        $replay_info['comment_num'] = $numdata['comment_num'];
        $replay_info['watch_num'] = $numdata['watch_num'];

        return $this->successJson('获取成功', $replay_info);
    }

    /**
     * 分页获取录播视频评论列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function replaycommentlist()
    {
        $replay_id = request()->get('replay_id', 0);
        $page = request()->get('page', 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $cache_val = CacheService::getReplayComment($replay_id);
        return $this->successJson('获取成功', [
            'total' => $cache_val['total'],
            'list' => array_slice($cache_val['list'], $offset, $limit),
        ]);
    }

    /**
     * 录播视频添加评论
     * @return \Illuminate\Http\JsonResponse
     * @throws AppException
     */
    public function replaycommentadd()
    {
        $this->needLogin();
        $input = request()->all();
        if (!array_key_exists('replay_id', $input) || !array_key_exists('content', $input)) {
            return $this->errorJson('缺少参数');
        }
        if (is_string($input['content']) && strlen(trim($input['content'])) == 0) {
            return $this->errorJson('评论内容不能为空');
        }

        // 评论内容敏感词过滤
        $content = trim($input['content']);
        $wxapp_base_service = new BaseService();
        $sensitive_check = $wxapp_base_service->msgSecCheck($content);
        if (!is_bool($sensitive_check) || $sensitive_check === false) {
            return $this->errorJson('评论内容包含敏感词', $sensitive_check);
        }
        $content = $wxapp_base_service->textCheck($content);

        // 组装插入数据
        $comment_num_inc = true;
        $insert_data = [
            'uniacid' => $this->uniacid,
            'replay_id' => $input['replay_id'],
            'user_id' => $this->user_id,
            'content' => $content,
            'create_time' => time(),
        ];
        if (array_key_exists('parent_id', $input) && $input['parent_id'] > 0) {
            $parent = DB::table('yz_appletslive_replay_comment')->where('id', $input['parent_id'])->first();
            if ($parent) {
                $comment_num_inc = false;
                $insert_data['parent_id'] = $parent['id'];
                $insert_data['is_reply'] = 1;
                $insert_data['rele_user_id'] = $parent['user_id'];
            }
        }

        $id = DB::table('yz_appletslive_replay_comment')->insertGetId($insert_data);
        if ($comment_num_inc) {
            CacheService::setReplayNum($input['replay_id'], 'comment_num');
        }
        CacheService::setReplayComment($input['replay_id']);
        return $this->successJson('评论成功', ['id' => $id, 'content' => $content]);
    }

    /**
     * 录播视频删除评论
     * @return \Illuminate\Http\JsonResponse
     * @throws AppException
     */
    public function replaycommentdel()
    {
        $this->needLogin();
        $input = request()->all();
        if (!array_key_exists('comment_id', $input) || empty(intval($input['comment_id']))) {
            return $this->errorJson('评论id有误');
        }
        $is_mine = DB::table('yz_appletslive_replay_comment')
            ->where('id', $input['comment_id'])
            ->where('user_id', $this->user_id)
            ->first();
        if (!$is_mine) {
            return $this->errorJson('只能删除自己的评论');
        }
        DB::table('yz_appletslive_replay_comment')->where('id', $input['comment_id'])->delete();
        if ($is_mine['parent_id'] == 0) {
            CacheService::setReplayNum($is_mine['replay_id'], 'comment_num', true);
        }
        CacheService::setReplayComment($is_mine['replay_id']);
        return $this->successJson('删除成功');
    }

    /************************ 课程/录播 相关接口 END ************************/

    /************************ 课程/品牌特卖 相关接口 BEGIN ************************/

    /**
     * 分页获取品牌特卖专辑列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function brandsalelist()
    {
        $page = request()->get('page', 1);
        $limit = request()->get('limit', 10);

        $page_val = CacheService::getBrandSaleAlbumList($page, $limit);
        if (!empty($page_val['list'])) {
            $numdata = CacheService::getBrandSaleAlbumNum(array_column($page_val['list'], 'id'));
            $my_subscription = ($this->user_id ? CacheService::getUserBrandSaleAlbumSubscription($this->user_id) : []);
            foreach ($page_val['list'] as $k => $v) {
                $key = 'key_' . $v['id'];
                $page_val['list'][$k]['hot_num'] = $numdata[$key]['hot_num'];
                $page_val['list'][$k]['subscription_num'] = $numdata[$key]['subscription_num'];
                $page_val['list'][$k]['view_num'] = $numdata[$key]['view_num'];
                $page_val['list'][$k]['comment_num'] = $numdata[$key]['comment_num'];
                $page_val['list'][$k]['has_subscription'] = (array_search($page_val['list'][$k]['id'], $my_subscription) === false) ? false : true;

                if (in_array($v['live_status'], [101, 102])) {
                    $countdown_seconds = intval(bcsub(strtotime($v['start_time']), time(), 0));
                    $page_val['list'][$k]['countdown_seconds'] = ($countdown_seconds < 0 ? 0 : $countdown_seconds);
                }
            }
        }

        return $this->successJson('获取成功', $page_val);
    }

    /**
     * 获取品牌特卖专辑详情
     * @return \Illuminate\Http\JsonResponse
     */
    public function brandsaleinfo()
    {
        $album_id = request()->get('album_id', 0);
        $album_info = CacheService::getBrandSaleAlbumInfo($album_id);
        if (!$album_info) {
            return $this->errorJson('专辑不存在', $album_info);
        }

        CacheService::setBrandSaleAlbumNum($album_id, 'view_num');
        $numdata = CacheService::getBrandSaleAlbumNum($album_id);
        // $subscription = CacheService::getBrandSaleAlbumSubscription($room_id);
        // $album_info['subscription'] = $subscription;
        $album_info['hot_num'] = $numdata['hot_num'];
        $album_info['subscription_num'] = $numdata['subscription_num'];
        $album_info['view_num'] = $numdata['view_num'];
        $album_info['comment_num'] = $numdata['comment_num'];

        if (in_array($album_info['live_status'], [101, 102])) {
            $countdown_seconds = intval(bcsub(strtotime($album_info['start_time']), time(), 0));
            $album_info['countdown_seconds'] = ($countdown_seconds < 0 ? 0 : $countdown_seconds);
        }

        $my_subscription = ($this->user_id ? CacheService::getUserBrandSaleAlbumSubscription($this->user_id) : []);
        $album_info['has_subscription'] = ($this->user_id ? ((array_search($album_id, $my_subscription) === false) ? false : true) : false);

        return $this->successJson('获取成功', $album_info);
    }

    /**
     * 页获取品牌特卖专辑下属直播间列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function brandsaleliverooms()
    {
        $album_id = request()->get('album_id', 0);
        $page = request()->get('page', 1);
        $limit = request()->get('limit', 10);
        $offset = ($page - 1) * $limit;
        $rooms = CacheService::getBrandSaleAlbumLiveRooms($album_id);
        $numdata = CacheService::getBrandSaleLiveRoomNum(array_column($rooms['list'], 'id'));

        $time = time();
        foreach ($rooms['list'] as $k => $v) {
            $key = 'key_' . $v['id'];
            $rooms['list'][$k]['view_num'] = $numdata[$key]['view_num'];

            if (in_array($v['live_status'], [101, 102])) {
                $countdown_seconds = intval(bcsub(strtotime($v['start_time']), $time, 0));
                $rooms['list'][$k]['countdown_seconds'] = ($countdown_seconds < 0 ? 0 : $countdown_seconds);
            }
        }

        return $this->successJson('获取成功', [
            'total' => $rooms['total'],
            'list' => array_slice($rooms['list'], $offset, $limit),
        ]);
    }

    /**
     * 订阅品牌特卖专辑
     * @return \Illuminate\Http\JsonResponse
     */
    public function brandsalesubscription()
    {
        $this->needLogin();
        //$this->needFollowAccount();

        $input = request()->all();
        if (!array_key_exists('album_id', $input)) {
            return $this->errorJson('缺少参数');
        }

        $table = 'yz_appletslive_room_subscription';
        $map = [['room_id', '=', $input['album_id']], ['user_id', '=', $this->user_id]];
        $subscripInfo = DB::table($table)->where($map)->first();
        if (!$subscripInfo) {
            DB::table($table)->insert([
                'uniacid' => $this->uniacid,
                'room_id' => $input['album_id'],
                'user_id' => $this->user_id,
                'create_time' => time(),
                'type' => APPLETSLIVE_ROOM_TYPE_BRANDSALE,
            ]);
            CacheService::setBrandSaleAlbumNum($input['album_id'], 'subscription_num');
            CacheService::setUserBrandSaleAlbumSubscription($this->user_id, $input['album_id']);
            CacheService::setBrandSaleAlbumSubscription($input['album_id'], $this->user_id);
            $msg = '订阅成功';
        } else {
            if ($subscripInfo['status'] == 1) {
                DB::table($table)->where($map)->update(['status' => 0]);
                $msg = '取消订阅成功';
            } else {
                DB::table($table)->where($map)->update(['status' => 1]);
                $msg = '订阅成功';
            }

            //刷新缓存
            Cache::forget(CacheService::$cache_keys['brandsale.albumnum']);
            Cache::forget(CacheService::$cache_keys['brandsale.albumusersubscription']);
            Cache::forget(CacheService::$cache_keys['brandsale.albumsubscription']);
        }
        return $this->successJson($msg);

    }

    /**
     * 我订阅的品牌特卖专辑
     * @return \Illuminate\Http\JsonResponse
     */
    public function mybrandsalesubscription()
    {
        $this->needLogin();

        $page = request()->get('page', 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $my_subscription = CacheService::getUserBrandSaleAlbumSubscription($this->user_id);
        $list = array_slice($my_subscription, $offset, $limit);
        foreach ($list as $k => $v) {
            $list[$k] = CacheService::getBrandSaleAlbumInfo($v);
        }

        if (!empty($list)) {
            $numdata = CacheService::getBrandSaleAlbumNum(array_column($list, 'id'));
            foreach ($list as $k => $v) {
                $key = 'key_' . $v['id'];
                $list[$k]['hot_num'] = $numdata[$key]['hot_num'];
                $list[$k]['subscription_num'] = $numdata[$key]['subscription_num'];
                $list[$k]['view_num'] = $numdata[$key]['view_num'];
                $list[$k]['comment_num'] = $numdata[$key]['comment_num'];
            }
        }

        $total = count($my_subscription);
        return $this->successJson('获取成功', [
            'total' => $total,
            'totalPage' => ceil($total / $limit),
            'list' => $list,
        ]);
    }

    /**
     * 分页获取品牌特卖专辑评论列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function brandsalecommentlist()
    {
        $album_id = request()->get('album_id', 0);
        $page = request()->get('page', 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $cache_val = CacheService::getBrandSaleAlbumComment($album_id);
        return $this->successJson('获取成功', [
            'total' => $cache_val['total'],
            'list' => array_slice($cache_val['list'], $offset, $limit),
        ]);
    }

    /**
     * 品牌特卖专辑添加评论
     * @return \Illuminate\Http\JsonResponse
     * @throws AppException
     */
    public function brandsalecommentadd()
    {
        $this->needLogin();
        $input = request()->all();
        if (!array_key_exists('album_id', $input) || !array_key_exists('content', $input)) {
            return $this->errorJson('缺少参数');
        }
        if (is_string($input['content']) && strlen(trim($input['content'])) == 0) {
            return $this->errorJson('评论内容不能为空');
        }

        // 评论内容敏感词过滤
        $content = trim($input['content']);
        $wxapp_base_service = new BaseService();
        $sensitive_check = $wxapp_base_service->msgSecCheck($content);
        if (!is_bool($sensitive_check) || $sensitive_check === false) {
            return $this->errorJson('评论内容包含敏感词', $sensitive_check);
        }
        $content = $wxapp_base_service->textCheck($content);

        // 组装插入数据
        $comment_num_inc = true;
        $insert_data = [
            'uniacid' => $this->uniacid,
            'room_id' => $input['album_id'],
            'user_id' => $this->user_id,
            'content' => $content,
            'create_time' => time(),
        ];
        if (array_key_exists('parent_id', $input) && $input['parent_id'] > 0) {
            $parent = DB::table('yz_appletslive_room_comment')->where('id', $input['parent_id'])->first();
            if ($parent) {
                $comment_num_inc = false;
                $insert_data['parent_id'] = $parent['id'];
                $insert_data['is_reply'] = 1;
                $insert_data['rele_user_id'] = $parent['user_id'];
            }
        }

        $id = DB::table('yz_appletslive_room_comment')->insertGetId($insert_data);
        if ($comment_num_inc) {
            CacheService::setBrandSaleAlbumNum($input['album_id'], 'comment_num');
        }
        CacheService::setBrandSaleAlbumComment($input['album_id']);
        return $this->successJson('评论成功', ['id' => $id, 'content' => $content]);
    }

    /**
     * 品牌特卖专辑删除评论
     * @return \Illuminate\Http\JsonResponse
     * @throws AppException
     */
    public function brandsalecommentdel()
    {
        $this->needLogin();
        $input = request()->all();
        if (!array_key_exists('comment_id', $input) || empty(intval($input['comment_id']))) {
            return $this->errorJson('评论id有误');
        }
        $is_mine = DB::table('yz_appletslive_room_comment')
            ->where('id', $input['comment_id'])
            ->where('user_id', $this->user_id)
            ->first();
        if (!$is_mine) {
            return $this->errorJson('只能删除自己的评论');
        }
        DB::table('yz_appletslive_room_comment')->where('id', $input['comment_id'])->delete();
        if ($is_mine['parent_id'] == 0) {
            CacheService::setBrandSaleAlbumNum($is_mine['room_id'], 'comment_num', true);
        }
        CacheService::setBrandSaleAlbumComment($is_mine['room_id']);
        return $this->successJson('删除成功');
    }

    /**
     * 记录品牌特卖直播间观看人数
     * @return \Illuminate\Http\JsonResponse
     */
    public function brandsaleliveroomviewreport()
    {
        $live_room_id = request()->get('live_room_id', 0);
        if (!$live_room_id) {
            return $this->errorJson('缺少参数');
        }
        CacheService::setBrandSaleLiveRoomNum($live_room_id, 'view_num');
        return $this->successJson('记录成功');
    }

    /************************ 课程/品牌特卖 相关接口 END ************************/

    /**
     * 分页获取精选课程列表 fixby--wk -20201019 获取精选课程
     * @return \Illuminate\Http\JsonResponse
     */
    public function roomselectedlist()
    {
        $page = request()->get('page', 1);
        $limit = request()->get('limit', 3);

        $page_val = CacheService::getRecordedSelectedRoomList($page, $limit);
        if (!empty($page_val['list'])) {
            $page_val['list'] = $page_val['list']->toArray();
            $numdata = CacheService::getRoomNum(array_column($page_val['list'], 'id'));
            $my_subscription = ($this->user_id ? CacheService::getUserSubscription($this->user_id) : []);
            foreach ($page_val['list'] as $k => $v) {
                $key = 'key_' . $v['id'];
                $page_val['list'][$k]['hot_num'] = $numdata[$key]['hot_num'];
                $page_val['list'][$k]['subscription_num'] = $numdata[$key]['subscription_num'];
                $page_val['list'][$k]['view_num'] = $numdata[$key]['view_num'];
                $page_val['list'][$k]['comment_num'] = $numdata[$key]['comment_num'];
                $page_val['list'][$k]['has_subscription'] = (array_search($page_val['list'][$k]['id'], $my_subscription) === false) ? false : true;
                $page_val['list'][$k]['goods_info'] = [];
                if($v['goods_id'] > 0 && $v['buy_type'] == 1){
                    $page_val['list'][$k]['goods_info'] = DB::table('yz_goods')->where('id',$v['goods_id'])->first();
                }
            }
        }

        return $this->successJson('获取成功', $page_val);
    }
    /**
     * 分页获取正在直播的直播间列表 fixby--wk -20201110
     * @return \Illuminate\Http\JsonResponse
     */
    public function liverooms()
    {
        $live_room_status = request()->get('live_room_status', 101);
        $page = request()->get('page', 1);
        $limit = request()->get('limit', 10);
        $offset = ($page - 1) * $limit;
        $total = DB::table('yz_appletslive_liveroom')->where('live_status', $live_room_status)->count();

        $live_rooms = DB::table('yz_appletslive_liveroom')
            ->select('id','roomid','name', 'cover_img', 'anchor_name', 'share_img', 'goods', 'goods_ids')
            ->where('live_status', $live_room_status)
            ->orderBy('start_time', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get()
            ->toArray();

        return $this->successJson('获取成功', [
            'total' => $total,
            'totalPage' => ceil($total / $limit),
            'list' => $live_rooms,
        ]);
    }

    /**
     * 校验 课程是否购买，是否在有效期，更新用户到期时间 fixby--wk -20201124
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateCourse()
    {

        $room_id = request()->get('room_id');
        $room_info = DB::table('yz_appletslive_room')
            ->select('id', 'name', 'buy_type', 'expire_time', 'goods_id')
            ->where('type', 1)
            ->where('id', $room_id)
            ->where('delete_time', 0)
            ->first();

        $is_buy = 0; //课程是否购买
        $is_expire = 0; //课程是否过期
        $course_expire = 1; //课程过期时间戳

        if ($room_info['buy_type'] == 1) { //付费课程判断流程 免费课程不用判断
            //验证登录
            $this->needLogin();
            //验证是否购买过 是否有购买完成的订单
            $orders_info = DB::table('yz_order_goods')
                ->join('yz_order', 'yz_order.id', '=', 'yz_order_goods.order_id')
                ->select('yz_order_goods.id', 'yz_order_goods.course_expire_status', 'yz_order_goods.goods_id', 'yz_order_goods.order_id', 'yz_order.status', 'yz_order.pay_time')
                ->where([
                    ['yz_order.status', '=', '3'], //订单状态 -1关闭,0待付款,1待发货,2待收货,3已完成
                    ['yz_order_goods.goods_id', '=', $room_info['goods_id']],
                    ['yz_order_goods.uid', '=', $this->user_id]
                ])->get()->toArray();
            if (!empty($orders_info)) { //有购买完成的订单

                DB::beginTransaction();//开启事务

                $is_buy = 1;
                //自动订阅课程
                $this->autoSubscription($room_info['id']);

                //累加课程过期时间 ---- 开始
                //查询未累加时长的订单
                $order_expire_status_info = DB::table('yz_order_goods')
                    ->join('yz_order', 'yz_order.id', '=', 'yz_order_goods.order_id')
                    ->select('yz_order_goods.id', 'yz_order_goods.course_expire_status', 'yz_order_goods.goods_id', 'yz_order_goods.order_id', 'yz_order.status', 'yz_order.pay_time')
                    ->where([
                        ['yz_order.status', '=', '3'], //订单状态 -1关闭,0待付款,1待发货,2待收货,3已完成
                        ['yz_order_goods.goods_id', '=', $room_info['goods_id']],
                        ['yz_order_goods.uid', '=', $this->user_id],
                        ['yz_order_goods.course_expire_status', '=', 0]
                    ])->orderBy('yz_order.pay_time', 'asc')
                    ->get()->toArray();

                if (!empty($order_expire_status_info)) { //如果存在未累加的过期时间订单  循环订单更新课程过期时间

                    $course_expire_time = 0;
                    foreach ($order_expire_status_info as $k => $val) {
                        if ($val['course_expire_status'] == 1) {
                            continue;
                        }
                        //计算过期时间 更新时间 和 商品状态  更新商品管理order_goods表状态
                        $course_expire_time += $room_info['expire_time'] * 86400;
                        //累加之后更新订单状态
                        $up_order_goods_res = DB::table('yz_order_goods')->where([['id', '=', $val['id']], ['goods_id', '=', $val['goods_id']], ['uid', '=', $this->user_id]])->update(['course_expire_status' => 1]);
                        if (!$up_order_goods_res) {
                            DB::rollBack();//事务回滚
                            return $this->errorJson('验证失败，更新', [
                                'buy_type' => $room_info['buy_type'], //课程是否付款
                                'expire_time' => $room_info['expire_time'], //课程过期天数
                                'is_buy' => $is_buy, // 是否购买 0否 1是
                                'is_expire' => $is_expire, // 是否过期 0否 1是
                                'course_expire' => 0,  //课程到期时间
                                'course_expire_day' => 0  //课程剩余天数
                            ]);
                        }
                    }

                    //判断课程是否过期 如果已过期就是支付时间 + 购买时长，如果没有过期，购买时长累计
                    $room_subscription_info = DB::table('yz_appletslive_room_subscription')->where([['room_id', '=', $room_id], ['user_id', '=', $this->user_id]])->first();
                    if (time() <= $room_subscription_info['course_expire']) {
                        //未过期 现有过期时间戳 + 累计时长
                        $course_expire = $room_subscription_info['course_expire'] + $course_expire_time;
                    } else {
                        //获取支付当天凌晨的时间
                        $pay_time = strtotime(date('Y-m-d', $order_expire_status_info[0]['pay_time']));
                        //已过期 支付时间 + 购买时长
                        $course_expire = $pay_time + $course_expire_time;
                    }

                    //更新课程过期时间 如果后期允许一个商品关联多个课程，过期时间需要根据goods_id 关联的课程都更新下
                    $up_course_expire_res = DB::table('yz_appletslive_room_subscription')->where([['room_id', '=', $room_id], ['user_id', '=', $this->user_id]])->update(['course_expire' => $course_expire]);
                    if(!$up_course_expire_res){
                        DB::rollBack();//事务回滚
                        return $this->errorJson('验证失败', [
                            'buy_type' => $room_info['buy_type'], //课程是否付款
                            'expire_time' => $room_info['expire_time'], //课程过期天数
                            'is_buy' => $is_buy, // 是否购买 0否 1是
                            'is_expire' => $is_expire, // 是否过期 0否 1是
                            'course_expire' => 0,  //课程到期时间
                            'course_expire_day' => 0  //课程剩余天数
                        ]);
                    }
                } else {
                    //如果不存在未累加的过期时间订单  查询课程过期时间
                    $room_subscription_info = DB::table('yz_appletslive_room_subscription')->where([['room_id', '=', $room_id], ['user_id', '=', $this->user_id]])->first();
                    $course_expire = $room_subscription_info['course_expire'];
                }

                if (time() <= $course_expire) { //判断是否过期
                    $is_expire = 0;
                    $course_expire_day = 0;
                }else{
                    //计算剩余天数
                    $course_expire_day = floor(($course_expire - time())/86400);
                }

            }
            DB::commit();//事务提交
        }
        return $this->successJson('验证成功', [
            'buy_type' => $room_info['buy_type'], //课程是否付款
            'expire_time' => $room_info['expire_time'], //课程过期天数
            'is_buy' => $is_buy, // 是否购买 0否 1是
            'is_expire' => $is_expire, // 是否过期 0否 1是
            'course_expire' => date('Y-m-d H:i:s', $course_expire),  //课程到期时间
            'course_expire_day' => $course_expire_day  //课程剩余天数
        ]);
    }

    /**
     * 购买课程自动订阅 fixby--wk -20201124
     * @return \Illuminate\Http\JsonResponse
     */
    public function autoSubscription($room_id)
    {

        $table = 'yz_appletslive_room_subscription';
        $map = [['room_id', '=', $room_id], ['user_id', '=', $this->user_id]];
        $subscripInfo = DB::table($table)->where($map)->first();
        if (!$subscripInfo) {

            DB::table($table)->insert([
                'uniacid' => $this->uniacid,
                'room_id' => $room_id,
                'user_id' => $this->user_id,
                'create_time' => time(),
                'type' => APPLETSLIVE_ROOM_TYPE_COURSE,
            ]);

            CacheService::setRoomNum($room_id, 'subscription_num');
            CacheService::setUserSubscription($this->user_id, $room_id);
            CacheService::setRoomSubscription($room_id, $this->user_id);

        } else {

            if ($subscripInfo['status'] == 0) {
                DB::table($table)->where($map)->update(['status' => 1]);
            }
            //刷新缓存
            $cache_key = "api_live_room_subscription|$room_id";
            Cache::forget($cache_key);
            $cache_key_user_subscription = "api_live_user_subscription|$this->user_id";
            Cache::forget($cache_key_user_subscription);
            Cache::forget(CacheService::$cache_keys['brandsale.albumsubscription']);
            Cache::forget(CacheService::$cache_keys['brandsale.albumusersubscription']);
            $cache_key_room_num = 'api_live_room_num';
            Cache::forget($cache_key_room_num);

        }
    }
}
