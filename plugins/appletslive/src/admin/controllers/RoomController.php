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
        $cache_val = Cache::get($cache_key);
        $room_list = $cache_val;

        if ($type == 0) { // 直播

            // 同步房间列表
            $tag = request()->get('tag', '');
            if ($tag == 'refresh') {
                Cache::forget($cache_key);
                $cache_val = Cache::get($cache_key);

                // 刷新课程列表接口数据缓存
                Cache::forget("api_live_room_list");
            }

            // 缓存失效，重新查询并同步房间列表
            if (empty($cache_val)) {
                $already_exist_room_in_db = DB::table('appletslive_room')
                    ->where('type', 0)
                    ->orderBy('id', 'desc')
                    ->get();
                $room_from_weixin = (new BaseService())->getRooms($this->getToken());

                $insert = [];
                $present = $room_from_weixin['room_info'];
                foreach ($present as $psk => $psv) {
                    $exist = false;
                    foreach ($already_exist_room_in_db as $drk => $drv) {
                        if ($drv['roomid'] == $psv['roomid']) {
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
                $todel = [];
                foreach ($already_exist_room_in_db as $drk => $drv) {
                    $match = false;
                    foreach ($present as $psv) {
                        if ($drv['roomid'] == $psv['roomid']) {
                            $match = true;
                            break;
                        }
                    }
                    if (!$match) {
                        $todel[] = $drv['id'];
                    }
                }
                if ($todel) {
                    DB::table('appletslive_room')->where('id', 'in', $todel)->delete();
                }

                // 如果删除或新增了房间，重新查询列表
                if ($todel || $insert) {
                    $already_exist_room_in_db = DB::table('appletslive_room')
                        ->where('type', 0)
                        ->orderBy('id', 'desc')
                        ->get()->toArray();
                }

                // 转换时间和直播状态
                array_walk($already_exist_room_in_db, function (&$item) {
                    $item['start_time'] = date('Y-m-d H:i:s', $item['start_time']);
                    $item['end_time'] = date('Y-m-d H:i:s', $item['end_time']);
                    switch ($item['live_status']) {
                        case 101:
                            $item['live_status_text'] = '直播中';
                            break;
                        case 102:
                            $item['live_status_text'] = '未开始';
                            break;
                        case 103:
                            $item['live_status_text'] = '已结束';
                            break;
                        case 104:
                            $item['live_status_text'] = '禁播';
                            break;
                        case 105:
                            $item['live_status_text'] = '暂停';
                            break;
                        case 106:
                            $item['live_status_text'] = '异常';
                            break;
                        case 107:
                            $item['live_status_text'] = '已过期';
                            break;
                        default:
                            $item['live_status_text'] = '未知';
                            break;
                    }
                });

                Cache::put($cache_key, $already_exist_room_in_db, 3);
                $room_list = $already_exist_room_in_db;
            }
        }

        if ($type == 1) { // 录播
            $already_exist_room_in_db = DB::table('appletslive_room')
                ->where('type', 1)
                ->orderBy('id', 'desc')
                ->get();
            $room_list = $already_exist_room_in_db;
        }

        return view('Yunshop\Appletslive::admin.room_index', [
            'type' => $type,
            'room_list' => $room_list,
        ])->render();
    }

    // 房间编辑
    public function edit()
    {
        if (request()->isMethod('post')) {
            $upd_data = [];
            $param = request()->all();
            if (array_key_exists('name', $param)) { // 房间名
                $upd_data['name'] = $param['name'] ? $param['name'] : '';
            }
            if (array_key_exists('cover_img', $param)) { // 房间封面
                $upd_data['cover_img'] = $param['cover_img'] ? $param['cover_img'] : '';
            }
            if (array_key_exists('desc', $param)) { // 房间介绍
                $upd_data['desc'] = $param['desc'] ? $param['desc'] : '';
            }
            $id = $param['id'] ? $param['id'] : 0;
            $room = DB::table('appletslive_room')->where('id', $id)->first();
            if (!$room) {
                return $this->message('无效的房间ID', Url::absoluteWeb(''), 'danger');
            }
            DB::table('appletslive_room')->where('id', $id)->update($upd_data);

            // 刷新课程详情和课程列表接口数据缓存
            Cache::forget("api_live_room_info|$id");
            Cache::forget("api_live_room_list");

            return $this->message('保存成功', Url::absoluteWeb('plugin.appletslive.admin.controllers.room.index', ['type' => $room['type']]));
        }

        $rid = request()->get('rid', 0);
        $info = DB::table('appletslive_room')->where('id', $rid)->first();

        if (!$info) {
            return $this->message('房间不存在', Url::absoluteWeb(''), 'danger');
        }

        return view('Yunshop\Appletslive::admin.room_edit', [
            'rid' => $rid,
            'info' => $info,
        ])->render();
    }

    // 添加录播房间
    public function add()
    {
        if (request()->isMethod('post')) {
            $ist_data = ['type' => 1, 'live_status' => 255];
            $param = request()->all();
            if (array_key_exists('name', $param)) { // 房间名
                $ist_data['name'] = $param['name'] ? $param['name'] : '';
            }
            if (array_key_exists('cover_img', $param)) { // 房间封面
                $ist_data['cover_img'] = $param['cover_img'] ? $param['cover_img'] : '';
            }
            if (array_key_exists('desc', $param)) { // 房间介绍
                $ist_data['desc'] = $param['desc'] ? $param['desc'] : '';
            }
            DB::table('appletslive_room')->insert($ist_data);

            // 刷新课程列表接口数据缓存
            Cache::forget("api_live_room_list");

            return $this->message('保存成功', Url::absoluteWeb('plugin.appletslive.admin.controllers.room.index', ['type' => 1]));
        }

        return view('Yunshop\Appletslive::admin.room_add')->render();
    }

    /**
     * 回看列表
     * @return mixed|string
     * @throws AppException
     * @throws \Throwable
     */
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
                        return $this->message('获取回看列表失败【' . $result['errmsg'] . '】', Url::absoluteWeb(''), 'danger');
                    }
                    return $this->message('获取回看列表失败', Url::absoluteWeb(''), 'danger');
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

        if ($type == 1) { // 录播列表
            $result = DB::table('appletslive_replay')
                ->where('rid', $rid)
                ->orderBy('id', 'desc')
                ->get()->toArray();
            array_walk($result, function (&$item) {
                $item['create_time'] = date('Y-m-d H:i:s', $item['create_time']);
                $item['publish_time'] = date('Y-m-d H:i:s', $item['publish_time']);
                $item['minute'] = floor($item['time_long'] / 60);
                $item['second'] = $item['time_long'] % 60;
            });
            $replay_list = $result;
        }

        return view('Yunshop\Appletslive::admin.replay_list', [
            'rid' => $rid,
            'type' => $type,
            'replay_list' => $replay_list,
        ])->render();
    }

    // 视频设置
    public function replayedit()
    {
        if (request()->isMethod('post')) {
            $upd_data = [];
            $param = request()->all();
            $id = $param['id'] ? $param['id'] : 0;
            $upd_data['title'] = $param['title'] ? $param['title'] : '';
            $upd_data['cover_img'] = $param['cover_img'] ? $param['cover_img'] : '';
            $upd_data['media_url'] = $param['media_url'] ? $param['media_url'] : '';
            $upd_data['intro'] = $param['intro'] ? $param['intro'] : '';
            $upd_data['time_long'] = ((intval($param['minute']) * 60) + intval($param['second']));
            $upd_data['publish_time'] = strtotime($param['publish_time']) <= time() ? time() : strtotime($param['publish_time']);
            $replay = DB::table('appletslive_replay')->where('id', $id)->first();
            if (!$replay) {
                return $this->message('无效的回放或视频ID', Url::absoluteWeb(''), 'danger');
            }
            DB::table('appletslive_replay')->where('id', $id)->update($upd_data);

            // 刷新录播详情和录播列表接口数据缓存
            Cache::forget("api_live_replay_info|$id");
            Cache::forget("api_live_replay_list|" . $replay->rid);

            return $this->message('保存成功', Url::absoluteWeb('plugin.appletslive.admin.controllers.room.replaylist', ['rid' => $replay['rid']]));
        }

        $id = request()->get('id', 0);
        $info = DB::table('appletslive_replay')->where('id', $id)->first();
        if (!$info) {
            return $this->message('回放或视频不存在', Url::absoluteWeb(''), 'danger');
        }
        $info['minute'] = floor($info['time_long'] / 60);
        $info['second'] = $info['time_long'] % 60;

        $room = DB::table('appletslive_room')->where('id', $info['rid'])->first();
        return view('Yunshop\Appletslive::admin.replay_edit', [
            'id' => $id,
            'room' => $room,
            'info' => $info,
        ])->render();
    }

    // 视频添加(录播)
    public function replayadd()
    {
        if (request()->isMethod('post')) {
            $param = request()->all();
            $rid = $param['rid'] ? intval($param['rid']) : 0;
            $room = DB::table('appletslive_room')->where('id', $rid)->first();
            if (!$room) {
                return $this->message('房间不存在', Url::absoluteWeb(''), 'danger');
            }
            $ist_data = [
                'rid' => $rid,
                'title' => $param['title'] ? $param['title'] : '',
                'cover_img' => $param['cover_img'] ? $param['cover_img'] : '',
                'media_url' => $param['media_url'] ? $param['media_url'] : '',
                'intro' => $param['intro'] ? $param['intro'] : '',
                'create_time' => time(),
                'expire_time' => strtotime('2099-12-31 23:59:59'),
                'time_long' => ((intval($param['minute']) * 60) + intval($param['second'])),
                'publish_time' => strtotime($param['publish_time']) <= time() ? time() : strtotime($param['publish_time']),
            ];
            DB::table('appletslive_replay')->insert($ist_data);

            // 刷新录播列表接口数据缓存
            Cache::forget("api_live_replay_list|$rid");

            return $this->message('保存成功', Url::absoluteWeb('plugin.appletslive.admin.controllers.room.replaylist', ['rid' => $rid]));
        }

        $rid = request()->get('rid', 0);
        $room = DB::table('appletslive_room')->where('id', $rid)->first();
        if (!$room || $room['type'] == 0) {
            return $this->message('不可操作', Url::absoluteWeb(''), 'danger');
        }

        return view('Yunshop\Appletslive::admin.replay_add', [
            'rid' => $rid,
        ])->render();
    }

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
}