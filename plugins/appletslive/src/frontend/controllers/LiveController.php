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
use Yunshop\Appletslive\common\services\BaseService;

class LiveController extends BaseController
{
    public function getRoom()
    {
        $page = request()->page;

        $room_info = Cache::get('live_room_info');
        if (empty($room_info)) {
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

            return $this->successJson('获取成功', $result['room_info']);
        }

        return $this->successJson('获取成功', $room_info);
    }

    public function getReplay()
    {
        $room_id = request()->rid;
        $page = request()->page;

        $result = (new BaseService())->getReplays($this->getToken(), $room_id);

        return $this->successJson('获取成功', $result);
    }

    public function getToken()
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