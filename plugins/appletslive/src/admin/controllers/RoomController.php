<?php
/**
 * Created by PhpStorm.
 * User: weifeng
 * Date: 2019-12-18
 * Time: 16:00
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

namespace Yunshop\Appletslive\admin\controllers;

use Illuminate\Support\Facades\DB;
use app\common\exceptions\AppException;
use app\common\components\BaseController;
use app\common\helpers\Cache;
use app\common\facades\Setting;
use Yunshop\Appletslive\common\services\BaseService;

class RoomController extends BaseController
{
    // 房间列表
    public function index()
    {
        $type = request()->get('type', 0);
        $cache_key = 'live_room_info_' . $type;
        if (!in_array($type, [0, 1])) {
            throw new AppException('房间类型有误');
        }

        $room_list = Cache::get($cache_key);
        if ($type == 0) { // 直播

            // 同步房间列表
            $tag = request()->get('tag', '');
            if ($tag == 'refresh') {
                Cache::forget($cache_key);
                $room_list = Cache::get($cache_key);
            }

            if (empty($room_list)) {
                $result = (new BaseService())->getRooms($this->getToken());

                foreach ($result['room_info'] as &$room) {
                    $room['start_time'] = date('Y-m-d H:i:s', $room['start_time']);
                    $room['end_time'] = date('Y-m-d H:i:s', $room['end_time']);
                    if (strexists($room['cover_img'], 'http')) {
                        $room['cover_img'] = str_replace('http', 'https', $room['cover_img']);
                    }
                    if (strexists($room['share_img'], 'http')) {
                        $room['share_img'] = str_replace('http', 'https', $room['share_img']);
                    }
                    switch ($room['live_status']) {
                        case 101:
                            $room['live_status_text'] = '直播中';
                        case 102:
                            $room['live_status_text'] = '未开始';
                        case 103:
                            $room['live_status_text'] = '已结束';
                        case 104:
                            $room['live_status_text'] = '禁播';
                        case 105:
                            $room['live_status_text'] = '暂停';
                        case 106:
                            $room['live_status_text'] = '异常';
                        case 107:
                            $room['live_status_text'] = '已过期';
                        default:
                            $room['live_status_text'] = '未知';
                    }
                }

                Cache::put($cache_key, $result['room_info'], 3);
                $room_list = Cache::get($cache_key);
            }
        }

        if ($type == 1) { // 录播
            if (empty($room_list)) {
                $result = DB::table('appletslive_room')->where('type', 1)->get();

                foreach ($result as &$room) {
                    $room['start_time'] = date('Y-m-d H:i:s', $room['start_time']);
                    $room['end_time'] = date('Y-m-d H:i:s', $room['end_time']);
                    if (strexists($room['cover_img'], 'http')) {
                        $room['cover_img'] = str_replace('http', 'https', $room['cover_img']);
                    }
                    if (strexists($room['share_img'], 'http')) {
                        $room['share_img'] = str_replace('http', 'https', $room['share_img']);
                    }
                }

                Cache::put($cache_key, $result['room_info'], 3);
                $room_list = Cache::get($cache_key);
            }
        }


        return view('Yunshop\Appletslive::admin.room_index', [
            'type' => $type,
            'room_list' => $room_list,
        ])->render();
    }

    // 房间设置
    public function set()
    {
        $form_data = request()->form_data;
        if ($form_data) {
            return $this->successJson('保存成功', $form_data);
        }

        $roomid = request()->get('roomid', 0);
        $info = DB::table('appletslive_room')->where('roomid', $roomid)->first();

        if (!$info) {
            throw new AppException('房间号不存在');
        }

        return view('Yunshop\Appletslive::admin.room_set', [
            'info' => $info,
        ])->render();
    }

    // 回看列表
    public function replaylist()
    {
        $room_id = request()->get('room_id', 0);
        $replay_list = Cache::get('live_room_replay_list_' . $room_id);
        if (empty($replay_list)) {
            $result = (new BaseService())->getReplays($this->getToken(), $room_id);

            if (!$result || $result['errcode'] != 0) {
                if (is_array($result)) {
                    throw new AppException('获取回看列表失败【' . $result['errmsg'] . '】');
                }
                throw new AppException('获取回看列表失败');
            }

            foreach ($result['live_replay'] as &$replay) {
                $replay['create_time'] = date('Y-m-d H:i:s', $replay['create_time']);
                $replay['expire_time'] = date('Y-m-d H:i:s', $replay['expire_time']);
                if (strexists($replay['media_url'], 'http://')) {
                    $replay['media_url'] = str_replace('http://', 'https://', $replay['media_url']);
                }
            }

            Cache::put('live_room_replay_list_' . $room_id, $result['live_replay'], 3);
            $replay_list = Cache::get('live_room_replay_list_' . $room_id);
        }

        return view('Yunshop\Appletslive::admin.replay_list', [
            'replay_list' => $replay_list,
        ])->render();
    }


    private function getToken()
    {
        $set = Setting::get('plugin.appletslive');
        $appId = $set['appId'];
        $secret = $set['secret'];

        if (empty($appId) || empty($secret)) {
            throw new AppException('请填写appId和secret');
        }

        $result = (new BaseService())->getToken($appId, $secret);
        if ($result['errcode'] != 0) {
            throw new AppException('appId或者secret错误'.$result['errmsg']);
        }

        return $result['access_token'];
    }
}