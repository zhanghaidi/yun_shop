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
use Illuminate\Support\Facades\Log;

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
        Cache::flush();
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
     * @return mixed
     * @throws AppException
     */
    private function getToken()
    {
        $set = Setting::get('plugin.appletslive');
        $appId = $set['appId'];
        $secret = $set['secret'];

        if (empty($appId) || empty($secret)) {
            throw new AppException('请配置appId和secret');
        }

        $result = (new BaseService())->getToken($appId, $secret);
        if ($result['errcode'] != 0) {
            throw new AppException('appId或者secret错误'.$result['errmsg']);
        }

        return $result['access_token'];
    }

    /************************ 测试用代码 BEGIN ************************/

    /**
     * 测试发送微信公众号模板消息
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
                'value' => '最新视频【每次艾灸几个穴位合适】将于2020-08-05 12:00震撼发布!',
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
            'thing4' => ['value' => '最新视频【每次艾灸几个穴位合适】将在' . date('Y-m-d H:i', strtotime('+15 minutes')) . '震撼发布!', 'color' => '#173177'],
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
        Log::info("------------------------ 测试：课程提醒定时任务 BEGIN -------------------------------");

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

        // 1、查询距离当前时间点10-15分钟之间即将发布的视频
        $time_now = time();
        $time_check_point = $time_now + 900;
        $time_check_where = [$time_check_point, $time_check_point + 600];
        $replay_publish_soon = DB::table('appletslive_replay')
            ->select('id', 'rid', 'title', 'doctor', 'publish_time')
            ->whereBetween('publish_time', $time_check_where)
            ->get()->toArray();
        $result['publish_time_range'] = $time_check_where;
        $result['replay_publish_soon'] = $replay_publish_soon;

        Log::info('time_now: ' . $time_now);
        Log::info('time_check_where: ', $time_check_where);
        Log::info('replay_publish_soon: ', $replay_publish_soon);

        if (empty($replay_publish_soon)) {
            Log::info('未找到即将新发布的视频.');
        } else {

            // 2、查询即将发布的视频关联的课程
            $rela_room = DB::table('appletslive_room')
                ->whereIn('id', array_unique(array_column($replay_publish_soon, 'rid')))
                ->pluck('name', 'id')->toArray();

            // 3、查询关注了这些课程的所有小程序用户信息(openid)
            $subscribed_user = DB::table('appletslive_room_subscription')
                ->select('user_id', 'room_id')
                ->where('room_id', array_keys($rela_room))
                ->get()->toArray();
            if (empty($subscribed_user)) {
                Log::info('未找到订阅了课程的用户.');
            } else {
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
                $current_subscribed_user = [];
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
            Log::info("队列数据组装完成", $job_list);

            // 5、添加消息发送任务到消息队列
            // foreach ($job_list as $job) {
            //     $job = SendTemplateMsgJob($job['type'], $$job['options'], $job['template_id'], $job['notice_data'],
            //         $job['openid'], '', $job['page']);
            //     $dispatch = dispatch($job);
            //     if ($job['type'] == 'wechat') {
            //         Log::info("队列已添加:发送公众号模板消息", ['job' => $job, 'dispatch' => $dispatch]);
            //     } elseif ($job['type'] == 'wxapp') {
            //         Log::info("队列已添加:发送小程序订阅模板消息", ['job' => $job, 'dispatch' => $dispatch]);
            //     }
            // }
        }

        Log::info("------------------------ 测试：课程提醒定时任务 END -------------------------------\n");
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

    /************************ 测试用代码 END ************************/

    /**
     * 分页获取课程列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function roomlist()
    {
        $page = request()->get('page', 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $cache_key = "api_live_room_list";
        $cache_val = Cache::get($cache_key);
        $page_key = "$limit|$page";
        if (!$cache_val || !array_key_exists($page_key, $cache_val)) {
            $page_val = DB::table('appletslive_room')
                ->select('id', 'type', 'roomid', 'name', 'anchor_name', 'cover_img', 'start_time', 'end_time', 'live_status', 'desc')
                ->where('type', 1)
                ->orderBy('live_status', 'asc')
                ->orderBy('view_num', 'desc')
                ->offset($offset)->limit($limit)->get()
                ->toArray();
            Cache::put($cache_key, [$page_key => $page_val], 30);
        } else {
            $page_val = $cache_val[$page_key];
        }

        if (!empty($page_val)) {
            $numdata = CacheService::getRoomNum(array_column($page_val, 'id'));
            $my_subscription = CacheService::getUserSubscription($this->user_id);
            foreach ($page_val as $k => $v) {
                $key = 'key_' . $v['id'];
                $page_val[$k]['hot_num'] = $numdata[$key]['hot_num'];
                $page_val[$k]['subscription_num'] = $numdata[$key]['subscription_num'];
                $page_val[$k]['view_num'] = $numdata[$key]['view_num'];
                $page_val[$k]['comment_num'] = $numdata[$key]['comment_num'];
                $page_val[$k]['has_subscription'] = (array_search($page_val[$k]['id'], $my_subscription) === false) ? false : true;
                if ($page_val[$k]['type'] == 0) {
                    $page_val[$k]['start_time'] = date('Y-m-d H:i', $page_val[$k]['start_time']);
                    $page_val[$k]['end_time'] = date('Y-m-d H:i', $page_val[$k]['end_time']);
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
        $room_id = request()->get('room_id', 0);
        $cache_key = "api_live_room_info|$room_id";
        $cache_val = Cache::get($cache_key);
        if (!$cache_val) {
            $cache_val = DB::table('appletslive_room')
                ->select('id', 'type', 'roomid', 'name', 'anchor_name', 'cover_img', 'start_time', 'end_time', 'live_status', 'desc')
                ->where('id', $room_id)
                ->first();
            if (!$cache_val) {
                return $this->errorJson('课程不存在');
            }
            Cache::put($cache_key, $cache_val, 30);
        }

        CacheService::setRoomNum($room_id, 'view_num');
        $numdata = CacheService::getRoomNum($room_id);
        $subscription = CacheService::getRoomSubscription($room_id);
        $my_subscription = ($this->user_id ? CacheService::getUserSubscription($this->user_id) : []);
        $cache_val['hot_num'] = $numdata['hot_num'];
        $cache_val['subscription_num'] = $numdata['subscription_num'];
        $cache_val['view_num'] = $numdata['view_num'];
        $cache_val['comment_num'] = $numdata['comment_num'];
        $cache_val['subscription'] = $subscription;
        $cache_val['has_subscription'] = ($this->user_id ? ((array_search($room_id, $my_subscription) === false) ? false : true) : false);
        if ($cache_val['type'] == 0) {
            $cache_val['start_time'] = date('Y-m-d H:i', $cache_val['start_time']);
            $cache_val['end_time'] = date('Y-m-d H:i', $cache_val['end_time']);
        }

        return $this->successJson('获取成功', $cache_val);
    }

    /**
     * 订阅课程
     * @return \Illuminate\Http\JsonResponse
     */
    public function roomsubscription()
    {
        $this->needLogin();
        $this->needFollowAccount();

        $input = request()->all();
        if (!array_key_exists('room_id', $input)) {
            return $this->errorJson('缺少参数');
        }

        $table = 'appletslive_room_subscription';
        $map = [['room_id', '=', $input['room_id']], ['user_id', '=', $this->user_id]];
        if (!DB::table($table)->where($map)->first()) {
            DB::table($table)->insert([
                'uniacid' => $this->uniacid,
                'room_id' => $input['room_id'],
                'user_id' => $this->user_id,
                'create_time' => time(),
            ]);
            CacheService::setRoomNum($input['room_id'], 'subscription_num');
            CacheService::setUserSubscription($this->user_id, $input['room_id']);
            CacheService::setRoomSubscription($input['room_id'], $this->user_id);
            return $this->successJson('订阅成功');
        }

        return $this->errorJson('你已加入课程');
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
        if (!$this->user_id) {
            response()->json([
                'result' => 41009,
                'msg' => '请登录',
                'data' => null,
            ], 200, ['charset' => 'utf-8'])->send();
            exit;
        }
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
        $sensitive_check = $wxapp_base_service->msgSecCheck($content, $this->getToken());
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
            $parent = DB::table('appletslive_room_comment')->where('id', $input['parent_id'])->first();
            if ($parent) {
                $comment_num_inc = false;
                $insert_data['parent_id'] = $parent['id'];
                $insert_data['is_reply'] = 1;
                $insert_data['rele_user_id'] = $parent['user_id'];
            }
        }

        DB::table('appletslive_room_comment')->insert($insert_data);
        if ($comment_num_inc) {
            CacheService::setRoomNum($input['room_id'], 'comment_num');
        }
        CacheService::setRoomComment($input['room_id']);
        return $this->successJson('评论成功');
    }

    /**
     * 获取录播列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function replaylist()
    {
        $room_id = request()->get('room_id', 0);
        $page = request()->get('page', 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $cache_key = "api_live_replay_list|$room_id";
        $cache_val = Cache::get($cache_key);

        if (!$cache_val) {
            $cache_val = DB::table('appletslive_replay')
                ->select('id', 'rid', 'type', 'title', 'intro', 'cover_img', 'media_url', 'publish_time', 'time_long')
                ->where('rid', $room_id)
                ->orderBy('id', 'desc')
                ->get()->toArray();
            if (empty($cache_val)) {
                Cache::forever($cache_key, []);
            } else {
                array_walk($cache_val, function (&$item) {
                    $item['publish_status'] = 1;
                    if ($item['publish_time'] > time()) {
                        $item['publish_status'] = 0;
                        $item['media_url'] = '';
                    }
                    $item['minute'] = floor($item['time_long'] / 60);
                    $item['second'] = $item['time_long'] % 60;
                    $item['publish_time'] = date('Y-m-d H:i:s', $item['publish_time']);
                    unset($item['rid']);
                });
            }
            Cache::put($cache_key, $cache_val);
        }

        if (!empty($cache_val)) {
            $numdata = CacheService::getReplayNum(array_column($cache_val, 'id'));
            foreach ($cache_val as $k => $v) {
                $key = 'key_' . $v['id'];
                $cache_val[$k]['hot_num'] = $numdata[$key]['hot_num'];
                $cache_val[$k]['view_num'] = $numdata[$key]['view_num'];
                $cache_val[$k]['comment_num'] = $numdata[$key]['comment_num'];
            }
        }

        return $this->successJson('获取成功', [
            'total' => count($cache_val),
            'list' => array_slice($cache_val, $offset, $limit),
        ]);
    }

    /**
     * 获取录播详情信息
     * @return \Illuminate\Http\JsonResponse
     */
    public function replayinfo()
    {
        $replay_id = request()->get('replay_id', 0);
        $cache_key = "api_live_replay_info|$replay_id";
        $cache_val = Cache::get($cache_key);
        if (!$cache_val) {
            $cache_val = DB::table('appletslive_replay')
                ->select('id', 'rid', 'type', 'title', 'intro', 'cover_img', 'media_url', 'publish_time', 'time_long')
                ->where('id', $replay_id)
                ->first();
            if (!$cache_val) {
                return $this->errorJson('视频不存在');
            }
            $cache_val['minute'] = floor($cache_val['time_long'] / 60);
            $cache_val['second'] = $cache_val['time_long'] % 60;
            $cache_val['publish_time'] = date('Y-m-d H:i:s', $cache_val['publish_time']);
            unset($cache_val['rid']);
            Cache::put($cache_key, $cache_val, 30);
        }

        CacheService::setReplayNum($replay_id, 'view_num');
        $numdata = CacheService::getReplayNum($replay_id);
        $cache_val['hot_num'] = $numdata['hot_num'];
        $cache_val['view_num'] = $numdata['view_num'];
        $cache_val['comment_num'] = $numdata['comment_num'];

        return $this->successJson('获取成功', $cache_val);
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
     * 播视频添加评论
     * @return \Illuminate\Http\JsonResponse
     * @throws AppException
     */
    public function replaycommentadd()
    {
        if (!$this->user_id) {
            response()->json([
                'result' => 41009,
                'msg' => '请登录',
                'data' => null,
            ], 200, ['charset' => 'utf-8'])->send();
            exit;
        }
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
        $sensitive_check = $wxapp_base_service->msgSecCheck($content, $this->getToken());
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
            $parent = DB::table('appletslive_replay_comment')->where('id', $input['parent_id'])->first();
            if ($parent) {
                $comment_num_inc = false;
                $insert_data['parent_id'] = $parent['id'];
                $insert_data['is_reply'] = 1;
                $insert_data['rele_user_id'] = $parent['user_id'];
            }
        }

        DB::table('appletslive_replay_comment')->insert($insert_data);
        if ($comment_num_inc) {
            CacheService::setReplayNum($input['replay_id'], 'comment_num');
        }
        CacheService::setReplayComment($input['replay_id']);
        return $this->successJson('评论成功');
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
}
