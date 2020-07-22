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
use app\common\helpers\Url;
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

                $dbroom = DB::table('appletslive_room')->where('type', 0)->get();
                $insert = [];
                $present = $result['room_info'];
                foreach ($present as $psk => $psv) {
                    $exist = false;
                    foreach ($dbroom as $drk => $drv) {
                        if ($drv['roomid'] == $psv['roomid']) {
                            $present[$psk]['id'] = $drv['id'];
                            // 房间信息在数据库中存在，实时更新数据
                            DB::table('appletslive_room')->where('id', $drv['id'])->update([
                                'name' => $psv['name'],
                                'anchor_name' => $psv['anchor_name'],
                                'cover_img' => $psv['cover_img'],
                                'share_img' => $psv['share_img'],
                                'start_time' => $psv['start_time'],
                                'end_time' => $psv['end_time'],
                                'live_status' => $psv['live_status'],
                            ]);
                            $exist = true;
                            break;
                        }
                    }
                    // 房间信息在数据库中不存在，实时记录数据
                    if (!$exist) {
                        array_push($insert, [
                            'type' => 0,
                            'roomid' => $psv['roomid'],
                            'name' => $psv['name'],
                            'anchor_name' => $psv['anchor_name'],
                            'cover_img' => $psv['cover_img'],
                            'share_img' => $psv['share_img'],
                            'start_time' => $psv['start_time'],
                            'end_time' => $psv['end_time'],
                            'live_status' => $psv['live_status'],
                            'create_time' => time(),
                        ]);
                    }
                }
                // 添加新增的直播间
                if ($insert) {
                    DB::table('appletslive_room')->insert($insert);
                }
                // 移除删掉的直播间
                foreach ($dbroom as $drk => $drv) {
                    foreach ($present as $psv) {
                        if ($drv['roomid'] == $psv['roomid']) {
                            $dbroom[$drk] = null;
                        }
                    }
                }
                $todel = array_filter($dbroom);
                if ($todel) {
                    DB::table('appletslive_room')->where('id', 'in', array_column($todel, 'id'))->delete();
                }

                foreach ($present as &$room) {
                    $room['start_time'] = date('Y-m-d H:i:s', $room['start_time']);
                    $room['end_time'] = date('Y-m-d H:i:s', $room['end_time']);
                    if (strexists($room['cover_img'], 'http://')) {
                        $room['cover_img'] = str_replace('http://', 'https://', $room['cover_img']);
                    }
                    if (strexists($room['share_img'], 'http://')) {
                        $room['share_img'] = str_replace('http://', 'https://', $room['share_img']);
                    }
                    switch ($room['live_status']) {
                        case 101:
                            $room['live_status_text'] = '直播中';
                            break;
                        case 102:
                            $room['live_status_text'] = '未开始';
                            break;
                        case 103:
                            $room['live_status_text'] = '已结束';
                            break;
                        case 104:
                            $room['live_status_text'] = '禁播';
                            break;
                        case 105:
                            $room['live_status_text'] = '暂停';
                            break;
                        case 106:
                            $room['live_status_text'] = '异常';
                            break;
                        case 107:
                            $room['live_status_text'] = '已过期';
                            break;
                        default:
                            $room['live_status_text'] = '未知';
                            break;
                    }
                }

                Cache::put($cache_key, $present, 3);
                $room_list = Cache::get($cache_key);
            }
        }

        if ($type == 1) { // 录播
            $result = DB::table('appletslive_room')->where('type', 1)->get();
            foreach ($result as &$room) {
                if (strexists($room['cover_img'], 'http://')) {
                    $room['cover_img'] = str_replace('http://', 'https://', $room['cover_img']);
                }
            }
            $room_list = $result;
        }

        return view('Yunshop\Appletslive::admin.room_index', [
            'type' => $type,
            'room_list' => $room_list,
        ])->render();
    }

    // 房间设置
    public function set()
    {
        if (request()->isMethod('post')) {
            $param = request()->all();
            $id = $param['id'] ? $param['id'] : 0;
            $desc = $param['desc'] ? $param['desc'] : '';
            $room = DB::table('appletslive_room')->where('id', $id)->first();
            if (!$room) {
                return $this->message('无效的房间ID', Url::absoluteWeb(''), 'danger');
            }
            DB::table('appletslive_room')->where('id', $id)->update(['desc' => $desc]);
            return $this->message('保存成功', Url::absoluteWeb('plugin.appletslive.admin.controllers.room.index', ['type' => $room['type']]));
        }

        $rid = request()->get('rid', 0);
        $info = DB::table('appletslive_room')->where('id', $rid)->first();

        if (!$info) {
            return $this->message('房间不存在', Url::absoluteWeb(''), 'danger');
        }

        return view('Yunshop\Appletslive::admin.room_set', [
            'rid' => $rid,
            'info' => $info,
        ])->render();
    }

    // 回看列表
    public function replaylist()
    {
        $rid = request()->get('rid', 0);
        $room = DB::table('appletslive_room')->where('id', $rid)->first();
        $type = $room['type'];
        $cache_key = 'live_room_replay_list_' . $type . '_' . $rid;
        $replay_list = Cache::get($cache_key);

        if ($type == 0) { // 直播回看列表
            if (empty($replay_list)) {
                $result = (new BaseService())->getReplays($this->getToken(), $room['roomid']);

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

                Cache::put($cache_key, $result['live_replay'], 3);
                $replay_list = Cache::get($cache_key);
            }
        }

        if ($type == 1) { // 上传录播列表
            $result = [
                ['id' => 2, 'title' => '知识点2', 'cover_img' => 'https://attachment-1300631469.file.myqcloud.com/image/f0593e5fc43740f2081dc7650bf3614a.jpg',
                    'create_time' => date('Y-m-d H:i:s'), 'expire_time' => '2099-12-31 23:59:59'],
                ['id' => 1, 'title' => '知识点1', 'cover_img' => 'https://attachment-1300631469.file.myqcloud.com/image/f0593e5fc43740f2081dc7650bf3614a.jpg',
                    'create_time' => date('Y-m-d H:i:s'), 'expire_time' => '2099-12-31 23:59:59'],
            ];

            foreach ($result as &$replay) {
                $replay['create_time'] = date('Y-m-d H:i:s', $replay['create_time']);
                $replay['expire_time'] = date('Y-m-d H:i:s', $replay['expire_time']);
                if (strexists($replay['cover_img'], 'http://')) {
                    $replay['cover_img'] = str_replace('http://', 'https://', $replay['cover_img']);
                }
            }

            $replay_list = $result;
        }

        return view('Yunshop\Appletslive::admin.replay_list', [
            'rid' => $rid,
            'type' => $type,
            'replay_list' => $replay_list,
        ])->render();
    }

    // 视频设置
    public function replayset()
    {
        if (request()->isMethod('post')) {
            $param = request()->all();
            $id = $param['id'] ? $param['id'] : 0;
            $title = $param['title'] ? $param['title'] : '';
            $cover_img = $param['cover_img'] ? $param['cover_img'] : '';
            $intro = $param['intro'] ? $param['intro'] : '';
            $replay = DB::table('appletslive_room_replay')->where('id', $id)->first();
            if (!$replay) {
                return $this->message('无效的回放或视频ID', Url::absoluteWeb(''), 'danger');
            }
            DB::table('appletslive_room_replay')->where('id', $id)->update([
                'title' => $title, 'cover_img' => $cover_img, 'intro' => $intro,
            ]);
            return $this->message('保存成功', Url::absoluteWeb('plugin.appletslive.admin.controllers.room.replaylist', ['rid' => $replay['rid']]));
        }

        $id = request()->get('id', 0);
        $info = DB::table('appletslive_room_replay')->where('id', $id)->first();

        if (!$info) {
            return $this->message('回放或视频不存在', Url::absoluteWeb(''), 'danger');
        }

        return view('Yunshop\Appletslive::admin.replay_set', [
            'id' => $id,
            'info' => $info,
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