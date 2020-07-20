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
        $room_list = Cache::get('live_room_info');
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
                if (!empty($room['goods'])) {
                    foreach ($room['goods'] as &$goods) {
                        if (strexists($goods['cover_img'], 'http')) {
                            $goods['cover_img'] = str_replace('http', 'https', $goods['cover_img']);
                        }
                    }
                }
            }

            Cache::put('live_room_info', $result['room_info'], 3);
            $room_list = Cache::get('live_room_info');
        }

        return view('Yunshop\Appletslive::admin.room_index', [
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

        $room_id = request()->get('room_id', 0);
        $room_info = DB::table('users')->where('id', $room_id)->first();

        return view('Yunshop\Appletslive::admin.room_set', [
            'room_info' => $room_info,
        ])->render();
    }

    // 回看列表
    public function replaylist()
    {
        $room_id = request()->rid;
        $result = (new BaseService())->getReplays($this->getToken(), $room_id);

        return view('Yunshop\Appletslive::admin.replay_list', [
            'list' => $result,
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