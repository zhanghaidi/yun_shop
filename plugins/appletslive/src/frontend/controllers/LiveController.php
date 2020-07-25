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
use Yunshop\Appletslive\common\services\NumService;

/**
 * Class LiveController
 * @package Yunshop\Appletslive\frontend\controllers
 */
class LiveController extends BaseController
{
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
            $record = DB::table('appletslive_room')
                ->orderBy('live_status', 'asc')->offset($offset)->limit($limit)->get()
                ->toArray();
            $page_val = $record;
            $cache_val = [$page_key => $page_val];
            Cache::put($cache_key, $cache_val, 30);
        } else {
            $page_val = $cache_val[$page_key];
        }

        $numdata = NumService::getRoomNum(array_column($page_val, 'id'));
        foreach ($page_val as $k => $v) {
            $key = 'key_' . $v['id'];
            $page_val[$k]['hot_num'] = $numdata[$key]['hot_num'];
            $page_val[$k]['subscription_num'] = $numdata[$key]['subscription_num'];
            $page_val[$k]['view_num'] = $numdata[$key]['view_num'];
            $page_val[$k]['comment_num'] = $numdata[$key]['comment_num'];
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
            $result = DB::table('appletslive_room')
                ->where('id', $room_id)->get()
                ->toArray();
            Cache::put($cache_key, $result, 30);
            $cache_val = $result;
        }

        $numdata = NumService::getRoomNum($room_id);
        $cache_val['hot_num'] = $numdata['hot_num'];
        $cache_val['subscription_num'] = $numdata['subscription_num'];
        $cache_val['view_num'] = $numdata['view_num'];
        $cache_val['comment_num'] = $numdata['comment_num'];

        return $this->successJson('获取成功', $cache_val);
    }

    /**
     * 分页获取课程评论列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function roomcommentlist()
    {
    }

    /**
     * 课程添加评论
     * @return \Illuminate\Http\JsonResponse
     */
    public function roomcommentadd()
    {
    }

    /**
     * 订阅课程
     * @return \Illuminate\Http\JsonResponse
     */
    public function roomsubscription()
    {
    }

    /**
     * 获取录播列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function replaylist()
    {
    }

    /**
     * 获取录播详情信息
     * @return \Illuminate\Http\JsonResponse
     */
    public function replayinfo()
    {
    }

    /**
     * 分页获取录播视频评论列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function replaycommentlist()
    {
    }

    /**
     * 录播视频添加评论
     * @return \Illuminate\Http\JsonResponse
     */
    public function replaycommentadd()
    {
    }
}
