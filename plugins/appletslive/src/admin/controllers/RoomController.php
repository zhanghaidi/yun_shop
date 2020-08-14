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
use Illuminate\Support\Facades\Cache;
use app\common\exceptions\AppException;
use app\common\components\BaseController;
use app\common\helpers\Url;
use app\common\facades\Setting;
use Yunshop\Appletslive\common\services\BaseService;
use Yunshop\Appletslive\common\models\Room;
use Yunshop\Appletslive\common\models\Replay;
use app\common\helpers\PaginationHelper;

class RoomController extends BaseController
{
    // 房间列表
    public function index()
    {
        $type = request()->get('type', 0);
        if (!in_array($type, [0, 1])) {
            throw new AppException('房间类型有误');
        }

        $input = \YunShop::request();
        $limit = 10;

        if ($type == 0) { // 直播

            $cache_key = 'live_room_list';
            $cache_val = Cache::get($cache_key);

            // 同步房间列表
            $tag = request()->get('tag', '');
            if ($tag == 'refresh' || empty($cache_val)) {
                Cache::forget($cache_key);
                $cache_val = null;

                // 刷新课程列表接口数据缓存
                Cache::forget("api_live_room_list");

                // 重新查询并同步直播间列表
                $room_from_weixin = (new BaseService())->getRooms($this->getToken());
                $present = $room_from_weixin['room_info'];
                Cache::put($cache_key, $present, 10);
                $stored = DB::table('appletslive_room')
                    ->where('type', 0)
                    ->orderBy('id', 'desc')
                    ->get();

                // 添加新增的直播间
                $insert = [];
                foreach ($present as $psk => $psv) {
                    $exist = false;
                    foreach ($stored as $drk => $drv) {
                        if ($drv['roomid'] == $psv['roomid']) {
                            // 房间信息在数据库中存在，实时更新数据
                            DB::table('appletslive_room')->where('id', $drv['id'])->update([
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
                if ($insert) {
                    DB::table('appletslive_room')->insert($insert);
                }

                // 移除删掉的直播间
                $todel = [];
                foreach ($stored as $drk => $drv) {
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
            }

            // 处理搜索条件
            $where[] = ['type', '=', 0];
            if (isset($input->search)) {
                $search = $input->search;
                if (intval($search['roomid']) > 0) {
                    $where[] = ['roomid', '=', intval($search['roomid'])];
                }
                if (trim($search['name']) !== '') {
                    $where[] = ['name', 'like', '%' . trim($search['name']) . '%'];
                }
                if ($search['searchtime'] !== '') {
                    $time_field = ($search['searchtime'] === '0') ? 'start_time' : 'end_time';
                    $where[] = [$time_field, 'between', [strtotime($search['date']['start']), strtotime($search['date']['end'] . ' 23:59:59')]];
                }
                if (trim($search['live_status']) !== '') {
                    $where[] = ['live_status', '=', $search['live_status']];
                }
                if (trim($search['status']) !== '') {
                    if ($search['status'] === '0') {
                        $where[] = ['delete_time', '>', 0];
                    } else {
                        $where[] = ['delete_time', '=', 0];
                    }
                }
            }
        }

        if ($type == 1) { // 录播
            // 处理搜索条件
            $where[] = ['type', '=', 1];
            if (isset($input->search)) {
                $search = $input->search;
                if (intval($search['id']) > 0) {
                    $where[] = ['id', '=', intval($search['id'])];
                }
                if (trim($search['name']) !== '') {
                    $where[] = ['name', 'like', '%' . trim($search['name']) . '%'];
                }
                if (trim($search['status']) !== '') {
                    if ($search['status'] === '0') {
                        $where[] = ['delete_time', '>', 0];
                    } else {
                        $where[] = ['delete_time', '=', 0];
                    }
                }
            }
        }

        $room_list = Room::where($where)
            ->orderBy('id', 'desc')
            ->paginate($limit);
        $pager = PaginationHelper::show($room_list->total(), $room_list->currentPage(), $room_list->perPage());

        return view('Yunshop\Appletslive::admin.room_index', [
            'type' => $type,
            'room_list' => $room_list,
            'pager' => $pager,
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

        $id = request()->get('id', 0);
        $info = DB::table('appletslive_room')->where('id', $id)->first();

        if (!$info) {
            return $this->message('房间不存在', Url::absoluteWeb(''), 'danger');
        }

        return view('Yunshop\Appletslive::admin.room_edit', [
            'id' => $id,
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

    // 录播房间显示/隐藏
    public function showhide()
    {
        $input = request()->all();
        $id_invalid = false;
        if (!array_key_exists('id', $input)) { // 房间id
            $id_invalid = true;
        }
        $room = Room::where('id', intval($input['id']))->first();
        if (empty($room)) {
            $id_invalid = true;
        }
        if ($id_invalid) {
            return $this->message('无效的课程ID', Url::absoluteWeb(''), 'danger');
        }
        $delete_time = ($room->delete_time > 0) ? 0 : time();
        Room::where('id', $room->id)->update(['delete_time' => $delete_time]);

        // 刷新课程列表接口数据缓存
        Cache::forget("api_live_room_list");

        return $this->message('修改成功', Url::absoluteWeb('plugin.appletslive.admin.controllers.room.index', ['type' => $room->type]));
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
        $room_type = $room['type'];
        $cache_key = 'live_room_replay_list_' . $room_type . '_' . $rid;
        $replay_list = Cache::get($cache_key);

        $input = \YunShop::request();
        $limit = 10;

        if ($room_type == 0) { // 直播回看列表
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

                Cache::put($cache_key, $result['live_replay'], 10);
                $replay_list = Cache::get($cache_key);
            }
        }

        if ($room_type == 1) { // 录播列表
            // 处理搜索条件
            $where[] = ['rid', '=', $rid];
            if (isset($input->search)) {
                $search = $input->search;
                if (intval($search['id']) > 0) {
                    $where[] = ['id', '=', intval($search['id'])];
                }
                if (trim($search['title']) !== '') {
                    $where[] = ['title', 'like', '%' . trim($search['title']) . '%'];
                }
                if (trim($search['type']) !== '') {
                    $where[] = ['type', '=', $search['type']];
                }
                if (trim($search['status']) !== '') {
                    if ($search['status'] === '0') {
                        $where[] = ['delete_time', '>', 0];
                    } else {
                        $where[] = ['delete_time', '=', 0];
                    }
                }
            }
            $result = Replay::where($where)
                ->orderBy('id', 'desc')
                ->paginate($limit);
            $replay_list = $result;
        }

        $pager = PaginationHelper::show($replay_list->total(), $replay_list->currentPage(), $replay_list->perPage());

        return view('Yunshop\Appletslive::admin.replay_list', [
            'rid' => $rid,
            'room_type' => $room_type,
            'replay_list' => $replay_list,
            'pager' => $pager,
        ])->render();
    }

    // 视频设置
    public function replayedit()
    {
        if (request()->isMethod('post')) {
            $upd_data = [];
            $param = request()->all();
            $id = $param['id'] ? $param['id'] : 0;
            $upd_data['type'] = intval($param['type']);
            $upd_data['title'] = $param['title'] ? $param['title'] : '';
            $upd_data['cover_img'] = $param['cover_img'] ? $param['cover_img'] : '';
            $upd_data['media_url'] = $param['media_url'] ? $param['media_url'] : '';
            $upd_data['intro'] = $param['intro'] ? $param['intro'] : '';
            $upd_data['doctor'] = $param['doctor'] ? $param['doctor'] : '';
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
                'type' => intval($param['type']),
                'title' => $param['title'] ? $param['title'] : '',
                'cover_img' => $param['cover_img'] ? $param['cover_img'] : '',
                'media_url' => $param['media_url'] ? $param['media_url'] : '',
                'intro' => $param['intro'] ? $param['intro'] : '',
                'doctor' => $param['doctor'] ? $param['doctor'] : '',
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

    // 视频显示/隐藏
    public function replayshowhide()
    {
        $input = request()->all();
        $id_invalid = false;
        if (!array_key_exists('id', $input)) { // 房间id
            $id_invalid = true;
        }
        $replay = Replay::where('id', intval($input['id']))->first();
        if (empty($replay)) {
            $id_invalid = true;
        }
        if ($id_invalid) {
            return $this->message('无效的视频ID', Url::absoluteWeb(''), 'danger');
        }
        $delete_time = ($replay->delete_time > 0) ? 0 : time();
        Replay::where('id', $replay->id)->update(['delete_time' => $delete_time]);

        // 刷新录播列表接口数据缓存
        Cache::forget('api_live_replay_list|' . $replay->rid);

        return $this->message('修改成功', Url::absoluteWeb('plugin.appletslive.admin.controllers.room.replaylist', ['rid' => $replay->rid]));
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
