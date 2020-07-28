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
use app\common\helpers\Cache;
use Illuminate\Support\Facades\DB;
use Yunshop\Appletslive\common\services\CacheService;
use Yunshop\Appletslive\common\services\BaseService;

/**
 * Class LiveController
 * @package Yunshop\Appletslive\frontend\controllers
 */
class LiveController extends BaseController
{
    protected $user_id = 0;
    protected $uniacid = 45;

    /**
     * LiveController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->user_id = \YunShop::app()->getMemberId();
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

    /**
     * 分页获取课程列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function roomlist()
    {
        $page = request()->get('page', 1);
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $cache_key = "api_live_room_list";
        $cache_val = Cache::get($cache_key);
        $page_key = "$limit|$page";
        $page_val = null;

        if (!$cache_val || !array_key_exists($cache_val, $page_key)) {
            Cache::forget($cache_key);
            $record = DB::table('appletslive_room')
                ->orderBy('live_status', 'asc')
                ->orderBy('view_num', 'desc')
                ->offset($offset)->limit($limit)->get()
                ->toArray();
            Cache::put($cache_key, [$page_key => $page_val], 30);
        } else {
            $page_val = $cache_val[$page_key];
        }

        $numdata = CacheService::getRoomNum(array_column($page_val, 'id'));
        $my_subscription = CacheService::getUserSubscription($this->user_id);
        foreach ($page_val as $k => $v) {
            $key = 'key_' . $v['id'];
            $page_val[$k]['hot_num'] = $numdata[$key]['hot_num'];
            $page_val[$k]['subscription_num'] = $numdata[$key]['subscription_num'];
            $page_val[$k]['view_num'] = $numdata[$key]['view_num'];
            $page_val[$k]['comment_num'] = $numdata[$key]['comment_num'];
            $page_val[$k]['has_subscription'] = (array_search($v['id'], $my_subscription) === false) ? false : true;
        }

        return $this->successJson('获取成功【user_id:' . $this->user_id . '】', $page_val);
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
            $result = DB::table('appletslive_room')
                ->where('id', $room_id)
                ->first();
            $result = (array) $result;
            Cache::put($cache_key, $result, 30);
            $cache_val = $result;
        }

        CacheService::setRoomNum($room_id, 'view_num');
        $numdata = CacheService::getRoomNum($room_id);
        $cache_val['hot_num'] = $numdata['hot_num'];
        $cache_val['subscription_num'] = $numdata['subscription_num'];
        $cache_val['view_num'] = $numdata['view_num'];
        $cache_val['comment_num'] = $numdata['comment_num'];

        $subscription = CacheService::getRoomSubscription($room_id);
        $cache_val['subscription'] = $subscription;

        $my_subscription = CacheService::getUserSubscription($this->user_id);
        $cache_val['has_subscription'] = (array_search($room_id, $my_subscription) === false) ? false : true;

        return $this->successJson('获取成功', $cache_val);
    }

    /**
     * 分页获取课程评论列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function roomcommentlist()
    {
        $room_id = request()->get('room_id', 0);
        $page = request()->get('page', 1);
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $cache_val = CacheService::getRoomComment($room_id);
        return $this->successJson('获取成功', [
            'total' => $cache_val['total'],
            'list' => array_slice($cache_val, $offset, $limit),
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
            return $this->errorJson('会员未登陆');
        }
        $param = request()->all();
        if (!array_key_exists('room_id', $param) || !array_key_exists('content', $param)) {
            return $this->errorJson('缺少参数');
        }
        if (is_string($param['content']) && strlen(trim($param['content']))) {
            return $this->errorJson('评论内容不能为空');
        }

        // 评论内容敏感词过滤
        $content = trim($param['content']);
        $wxapp_base_service = new BaseService();
        if (!$wxapp_base_service->msgSecCheck($content, $this->getToken())) {
            return $this->errorJson('评论内容包含敏感词');
        }
        $content = $wxapp_base_service->textCheck($content);
        $insert_data = [
            'uniacid' => $this->uniacid,
            'room_id' => $param['room_id'],
            'user_id' => $this->user_id,
            'content' => $content,
            'create_time' => time(),
        ];
        if (array_key_exists('parent_id', $param) && $param['parent_id'] > 0) {
            $parent = DB::table('appletslive_room_comment')->where('id', $param['parent_id'])->first();
            if ($parent) {
                $insert_data['parent_id'] = $parent->id;
                $insert_data['is_reply'] = 1;
                $insert_data['rele_user_id'] = $parent->user_id;
            }
        }

        // DB::table('appletslive_room_comment')->insert($insert_data);
        // CacheService::setRoomNum($param['room_id'], 'comment_num');
        // CacheService::setRoomComment($param['room_id']);
        return $this->successJson('评论成功', $insert_data);
    }

    /**
     * 订阅课程
     * @return \Illuminate\Http\JsonResponse
     */
    public function roomsubscription()
    {
        if (!$this->user_id) {
            return $this->errorJson('会员未登陆');
        }
        $param = request()->all();
        if (!array_key_exists('room_id', $param)) {
            return $this->errorJson('缺少参数');
        }

        DB::table('appletslive_room_subscription')->insert([
            'uniacid' => $this->uniacid,
            'room_id' => $param['room_id'],
            'user_id' => $this->user_id,
            'create_time' => time(),
        ]);
        CacheService::setRoomNum($param['room_id'], 'subscription_num');
        CacheService::setUserSubscription($this->user_id, $param['room_id']);
        CacheService::setRoomSubscription($param['room_id']);
        return $this->successJson('订阅成功');
    }

    /**
     * 获取录播列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function replaylist()
    {
        $room_id = request()->get('room_id', 0);
        $page = request()->get('page', 1);
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $cache_key = "api_live_replay_list|$room_id";
        $cache_val = Cache::get($cache_key);

        if (!$cache_val) {
            $record = DB::table('appletslive_replay')
                ->where('id', $room_id)
                ->orderBy('id', 'desc')
                ->get()->toArray();
            if (empty($record)) {
                Cache::forever($cache_key, []);
            } else {
                array_walk($record, function (&$item) {
                    $item['publish_status'] = 1;
                    if ($item['publish_time'] > time()) {
                        $item['publish_status'] = 0;
                        $item['media_url'] = '';
                    }
                    $item['minute'] = floor($item['time_long'] / 60);
                    $item['second'] = $item['time_long'] % 60;
                    $item['create_time'] = date('Y-m-d H:i:s', $item['create_time']);
                    $item['publish_time'] = date('Y-m-d H:i:s', $item['publish_time']);
                });
            }
            Cache::put($cache_key, $record);
            $cache_val = $record;
        }

        $numdata = CacheService::getReplayNum(array_column($cache_val, 'id'));
        foreach ($cache_val as $k => $v) {
            $key = 'key_' . $v['id'];
            $cache_val[$k]['view_num'] = $numdata[$key]['view_num'];
            $cache_val[$k]['comment_num'] = $numdata[$key]['comment_num'];
        }

        return $this->successJson('获取成功', array_slice($cache_val, $offset, $limit));
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
            $result = DB::table('appletslive_replay')
                ->where('id', $replay_id)
                ->first();
            $result = (array) $result;
            Cache::put($cache_key, $result, 30);
            $cache_val = $result;
        }

        CacheService::setReplayNum($replay_id, 'view_num');
        $numdata = CacheService::getReplayNum($replay_id);
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
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $cache_val = CacheService::getReplayComment($replay_id);
        return $this->successJson('获取成功', [
            'total' => $cache_val['total'],
            'list' => array_slice($cache_val, $offset, $limit),
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
            return $this->errorJson('会员未登陆');
        }
        $param = request()->all();
        if (!array_key_exists('replay_id', $param) || !array_key_exists('content', $param)) {
            return $this->errorJson('缺少参数');
        }
        if (is_string($param['content']) && strlen(trim($param['content']))) {
            return $this->errorJson('评论内容不能为空');
        }

        // 评论内容敏感词过滤
        $content = trim($param['content']);
        $wxapp_base_service = new BaseService();
        if (!$wxapp_base_service->msgSecCheck($content, $this->getToken())) {
            return $this->errorJson('评论内容包含敏感词');
        }
        $content = $wxapp_base_service->textCheck($content);
        $insert_data = [
            'uniacid' => $this->uniacid,
            'replay_id' => $param['replay_id'],
            'user_id' => $this->user_id,
            'content' => $content,
            'create_time' => time(),
        ];
        if (array_key_exists('parent_id', $param) && $param['parent_id'] > 0) {
            $parent = DB::table('appletslive_replay_comment')->where('id', $param['parent_id'])->first();
            if ($parent) {
                $insert_data['parent_id'] = $parent->id;
                $insert_data['is_reply'] = 1;
                $insert_data['rele_user_id'] = $parent->user_id;
            }
        }

        // DB::table('appletslive_replay_comment')->insert($insert_data);
        // CacheService::setReplayNum($param['replay_id'], 'comment_num');
        // CacheService::setReplayComment($param['replay_id']);
        return $this->successJson('评论成功', $insert_data);
    }
}
