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
use Yunshop\Appletslive\common\services\BaseService;
use Yunshop\Appletslive\common\services\CacheService;
use Yunshop\Appletslive\common\models\Room;
use Yunshop\Appletslive\common\models\Replay;
use Yunshop\Appletslive\common\models\LiveRoom;
use app\common\helpers\PaginationHelper;
use Illuminate\Support\Facades\Log;

class RoomController extends BaseController
{
    // 房间列表
    public function index()
    {
        $type = request()->get('type', 0);
        if (!in_array($type, [0, 1, 2])) {
            throw new AppException('房间类型有误');
        }

        $input = \YunShop::request();
        $limit = 10;

        if ($type == 0) { // 直播

            // 同步房间列表
            $tag = request()->get('tag', '');
            if ($tag == 'refresh') {

                // 重新查询并同步直播间列表
                $room_from_weixin = (new BaseService())->getRooms();
                $present = $room_from_weixin['room_info'];
                $stored = DB::table('yz_appletslive_liveroom')
                    ->orderBy('id', 'desc')
                    ->limit(100)
                    ->get();

                // 添加新增的直播间
                $insert = [];
                $update = [];
                $present = array_reverse($present);
                foreach ($present as $psk => $psv) {
                    $exist = false;
                    foreach ($stored as $drk => $drv) {
                        if ($drv['roomid'] == $psv['roomid']) {
                            // 房间信息在数据库中存在，实时更新数据
                            if ($drv['name'] != $psv['name'] || $drv['anchor_name'] != $psv['anchor_name']
                                || $drv['live_status'] != $psv['live_status'] || $drv['start_time'] != $psv['start_time']) {
                                array_push($update, [
                                    'id' => $drv['id'],
                                    'name' => $psv['name'],
                                    'cover_img' => $psv['cover_img'],
                                    'share_img' => $psv['share_img'],
                                    'live_status' => $psv['live_status'],
                                    'start_time' => $psv['start_time'],
                                    'end_time' => $psv['end_time'],
                                    'anchor_name' => $psv['anchor_name'],
                                    'goods' => json_encode($psv['goods']),
                                ]);
                            }
                            $exist = true;
                            break;
                        }
                    }
                    // 房间信息在数据库中不存在，实时记录数据
                    if (!$exist) {
                        array_push($insert, [
                            'name' => $psv['name'],
                            'roomid' => $psv['roomid'],
                            'cover_img' => $psv['cover_img'],
                            'share_img' => $psv['share_img'],
                            'live_status' => $psv['live_status'],
                            'start_time' => $psv['start_time'],
                            'end_time' => $psv['end_time'],
                            'anchor_name' => $psv['anchor_name'],
                            'goods' => json_encode($psv['goods']),
                        ]);
                    }
                }
                if ($update) {
                    foreach ($update as $item) {
                        DB::table('yz_appletslive_liveroom')->where('id', $item['id'])->update([
                            'name' => $item['name'],
                            'cover_img' => $item['cover_img'],
                            'share_img' => $item['share_img'],
                            'live_status' => $item['live_status'],
                            'start_time' => $item['start_time'],
                            'end_time' => $item['end_time'],
                            'anchor_name' => $item['anchor_name'],
                            'goods' => $item['goods'],
                        ]);
                    }
                    Log::info('同步微信直播间数据:更新直播间信息', ['count' => count($update)]);
                }
                if ($insert) {
                    DB::table('yz_appletslive_liveroom')->insert($insert);
                    Log::info('同步微信直播间数据:新增直播间', ['count' => count($insert)]);
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
                    DB::table('yz_appletslive_liveroom')->whereIn('id', $todel)->update(['live_status' => 108]);
                    DB::table('yz_appletslive_replay')->whereIn('room_id', $todel)->update(['delete_time' => time()]);
                    Log::info('同步微信直播间数据:移除直播间', ['count' => count($todel)]);
                }

                Cache::forget(CacheService::$cache_keys['brandsale.albumlist']);
                Cache::forget(CacheService::$cache_keys['brandsale.albuminfo']);
                Cache::forget(CacheService::$cache_keys['brandsale.albumliverooms']);
            }

            // 处理搜索条件
            $where = [];
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
            }

            $list = LiveRoom::where($where)
                ->orderBy('id', 'desc')
                ->paginate($limit);
            $pager = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());
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
            $list = Room::where($where)
                ->orderBy('sort', 'desc')
                ->orderBy('id', 'desc')
                ->paginate($limit);
            $pager = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());
        }

        if ($type == 2) { // 品牌特卖
            // 处理搜索条件
            $where[] = ['type', '=', 2];
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
            $list = Room::where($where)
                ->orderBy('sort', 'desc')
                ->orderBy('id', 'desc')
                ->paginate($limit);
            $pager = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());
        }

        return view('Yunshop\Appletslive::admin.room_index', [
            'type' => $type,
            'room_list' => $list,
            'pager' => $pager,
            'request' => $input,
        ])->render();
    }

    // 房间编辑
    public function edit()
    {
        if (request()->isMethod('post')) {

            $param = request()->all();
            $upd_data = ['sort' => intval($param['sort'])];
            if (array_key_exists('name', $param)) { // 房间名
                $upd_data['name'] = $param['name'] ? trim($param['name']) : '';
            }
            if (array_key_exists('cover_img', $param)) { // 房间封面
                $upd_data['cover_img'] = $param['cover_img'] ? $param['cover_img'] : '';
            }
            if (array_key_exists('desc', $param)) { // 房间介绍
                $upd_data['desc'] = $param['desc'] ? $param['desc'] : '';
            }
            $id = $param['id'] ? $param['id'] : 0;
            $room = DB::table('yz_appletslive_room')->where('id', $id)->first();
            if (!$room) {
                return $this->message('无效的ID', Url::absoluteWeb(''), 'danger');
            }
            if (DB::table('yz_appletslive_room')->where('name', $upd_data['name'])->where('id', '<>', $id)->first()) {
                return $this->message('名称已存在', Url::absoluteWeb(''), 'danger');
            }
            DB::table('yz_appletslive_room')->where('id', $id)->update($upd_data);

            // 刷新接口数据缓存
            if ($room['type'] == 1) {
                Cache::forget(CacheService::$cache_keys['recorded.roomlist']);
                Cache::forget(CacheService::$cache_keys['recorded.roominfo']);
            } elseif ($room['type'] == 2) {
                Cache::forget(CacheService::$cache_keys['brandsale.albumlist']);
                Cache::forget(CacheService::$cache_keys['brandsale.albuminfo']);
            }

            return $this->message('保存成功', Url::absoluteWeb('plugin.appletslive.admin.controllers.room.index', ['type' => $room['type']]));
        }

        $id = request()->get('id', 0);
        $info = DB::table('yz_appletslive_room')->where('id', $id)->first();

        if (!$info) {
            return $this->message('无效的ID', Url::absoluteWeb(''), 'danger');
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

            $param = request()->all();
            if (!array_key_exists('type', $param) || !in_array($param['type'], [1, 2])) { // 类型
                return $this->message('类型参数有误', Url::absoluteWeb(''), 'danger');
            }
            $ist_data = ['type' => $param['type'], 'sort' => intval($param['sort'])];
            if (array_key_exists('name', $param)) { // 房间名
                $ist_data['name'] = $param['name'] ? trim($param['name']) : '';
            }
            if (array_key_exists('cover_img', $param)) { // 房间封面
                $ist_data['cover_img'] = $param['cover_img'] ? $param['cover_img'] : '';
            }
            if (array_key_exists('desc', $param)) { // 房间介绍
                $ist_data['desc'] = $param['desc'] ? $param['desc'] : '';
            }
            if (DB::table('yz_appletslive_room')->where('name', $ist_data['name'])->first()) {
                return $this->message('课程名称已存在', Url::absoluteWeb(''), 'danger');
            }
            DB::table('yz_appletslive_room')->insert($ist_data);

            // 刷新接口数据缓存
            if ($param['type'] == 1) {
                Cache::forget(CacheService::$cache_keys['recorded.roomlist']);
                Cache::forget(CacheService::$cache_keys['recorded.roominfo']);
            } elseif ($param['type'] == 2) {
                Cache::forget(CacheService::$cache_keys['brandsale.albumlist']);
                Cache::forget(CacheService::$cache_keys['brandsale.albuminfo']);
            }

            return $this->message('保存成功', Url::absoluteWeb('plugin.appletslive.admin.controllers.room.index', ['type' => $param['type']]));
        }

        $type = request()->get('type', 0);
        if (!$type) {
            return $this->message('无效的类型', Url::absoluteWeb(''), 'danger');
        }
        return view('Yunshop\Appletslive::admin.room_add', ['type' => $type])->render();
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

        // 刷新接口数据缓存
        if ($room->type == 1) {
            Cache::forget(CacheService::$cache_keys['recorded.roomlist']);
            Cache::forget(CacheService::$cache_keys['recorded.roominfo']);
        } elseif ($room->type == 2) {
            Cache::forget(CacheService::$cache_keys['brandsale.albumlist']);
            Cache::forget(CacheService::$cache_keys['brandsale.albuminfo']);
        }

        return $this->message('修改成功', Url::absoluteWeb('plugin.appletslive.admin.controllers.room.index', ['type' => $room->type]));
    }

    // 视频列表|直播列表
    public function replaylist()
    {
        $rid = request()->get('rid', 0);
        $room = DB::table('yz_appletslive_room')->where('id', $rid)->first();
        $room_type = $room['type'];

        $input = \YunShop::request();
        $limit = 10;

        $where[] = ['rid', '=', $rid];

        // 录播视频
        if ($room_type == 1) {

            // 处理搜索条件
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
            $replay_list = Replay::where($where)
                ->with('liveroom')
                ->orderBy('sort', 'desc')
                ->orderBy('id', 'desc')
                ->paginate($limit);
        }

        // 特卖直播
        if ($room_type == 2) {

            $with_where = [];

            // 处理搜索条件
            if (isset($input->search)) {

                $search = $input->search;
                if (intval($search['roomid']) > 0) {
                    $with_where[] = ['roomid', '=', intval($search['roomid'])];
                }
                if (trim($search['name']) !== '') {
                    $with_where[] = ['name', 'like', '%' . trim($search['name']) . '%'];
                }
                if (trim($search['live_status']) !== '') {
                    $with_where[] = ['live_status', '=', $search['live_status']];
                }
                if (trim($search['status']) !== '') {
                    if ($search['status'] === '0') {
                        $where[] = ['delete_time', '>', 0];
                    } else {
                        $where[] = ['delete_time', '=', 0];
                    }
                }
            }

            if (!empty($with_where)) {
                $where_in_room_id = LiveRoom::where($with_where)->pluck('id');
            }

            $queryer = Replay::where($where);
            if (isset($where_in_room_id)) {
                $queryer->whereIn('room_id', $where_in_room_id);
            }
            $replay_list = $queryer
                ->with('liveroom')
                ->orderBy('sort', 'desc')
                ->orderBy('id', 'desc')
                ->paginate($limit);
        }

        $pager = PaginationHelper::show($replay_list->total(), $replay_list->currentPage(), $replay_list->perPage());

        return view('Yunshop\Appletslive::admin.replay_list', [
            'rid' => $rid,
            'room_type' => $room_type,
            'replay_list' => $replay_list,
            'pager' => $pager,
            'request' => $input,
        ])->render();
    }

    // 视频|直播设置
    public function replayedit()
    {
        if (request()->isMethod('post')) {
            $upd_data = [];
            $param = request()->all();
            $id = $param['id'] ? $param['id'] : 0;

            $upd_data['room_id'] = array_key_exists('room_id', $param) ? intval($param['room_id']) : 0;
            $upd_data['type'] = intval($param['type']);
            $upd_data['title'] = $param['title'] ? trim($param['title']) : '';
            $upd_data['cover_img'] = $param['cover_img'] ? $param['cover_img'] : '';
            $upd_data['media_url'] = $param['media_url'] ? $param['media_url'] : '';
            $upd_data['intro'] = $param['intro'] ? $param['intro'] : '';
            $upd_data['doctor'] = $param['doctor'] ? $param['doctor'] : '';
            $upd_data['sort'] = intval($param['sort']);
            $upd_data['time_long'] = ((intval($param['minute']) * 60) + intval($param['second']));

            if ($upd_data['type'] != 0) {
                $upd_data['publish_time'] = strtotime($param['publish_time']) <= time() ? time() : strtotime($param['publish_time']);
            }

            $replay = DB::table('yz_appletslive_replay')->where('id', $id)->first();
            if (!$replay) {
                return $this->message('无效的回放或视频ID', Url::absoluteWeb(''), 'danger');
            }
            if (DB::table('yz_appletslive_replay')->where('title', $upd_data['title'])->where('rid', $replay->rid)->where('id', '<>', $id)->first()) {
                return $this->message('视频名称已存在', Url::absoluteWeb(''), 'danger');
            }
            DB::table('yz_appletslive_replay')->where('id', $id)->update($upd_data);

            // 刷新接口数据缓存
            if ($replay['type'] == 0) {
                Cache::forget(CacheService::$cache_keys['brandsale.albumlist']);
                Cache::forget(CacheService::$cache_keys['brandsale.albuminfo']);
                Cache::forget(CacheService::$cache_keys['brandsale.albumliverooms']);
            } else {
                Cache::forget(CacheService::$cache_keys['recorded.roomlist']);
                Cache::forget(CacheService::$cache_keys['recorded.roominfo']);
                Cache::forget(CacheService::$cache_keys['recorded.roomreplays']);
            }

            return $this->message('保存成功', Url::absoluteWeb('plugin.appletslive.admin.controllers.room.replaylist', ['rid' => $replay['rid']]));
        }

        $id = request()->get('id', 0);
        $info = DB::table('yz_appletslive_replay')->where('id', $id)->first();
        if (!$info) {
            return $this->message('视频不存在', Url::absoluteWeb(''), 'danger');
        }
        $info['minute'] = floor($info['time_long'] / 60);
        $info['second'] = $info['time_long'] % 60;

        $liverooms = LiveRoom::whereIn('live_status', [101, 102, 105])
            ->orWhere('id', '=', $info['room_id'])
            ->get();
        return view('Yunshop\Appletslive::admin.replay_edit', [
            'id' => $id,
            'info' => $info,
            'liverooms' => $liverooms,
        ])->render();
    }

    // 视频|直播添加
    public function replayadd()
    {
        if (request()->isMethod('post')) {
            $param = request()->all();
            $rid = $param['rid'] ? intval($param['rid']) : 0;
            $room = DB::table('yz_appletslive_room')->where('id', $rid)->first();
            if (!$room) {
                return $this->message('房间不存在', Url::absoluteWeb(''), 'danger');
            }
            $type = array_key_exists('type', $param) ? intval($param['type']) :  0;
            $ist_data = [
                'rid' => $rid,
                'type' => $type,
                'room_id' => array_key_exists('room_id', $param) ? intval($param['room_id']) : 0,
                'title' => $param['title'] ? trim($param['title']) : '',
                'cover_img' => $param['cover_img'] ? $param['cover_img'] : '',
                'media_url' => $param['media_url'] ? $param['media_url'] : '',
                'intro' => $param['intro'] ? $param['intro'] : '',
                'doctor' => $param['doctor'] ? $param['doctor'] : '',
                'sort' => intval($param['sort']),
                'time_long' => ((intval($param['minute']) * 60) + intval($param['second'])),
            ];

            if ($type > 0) {
                $ist_data['create_time'] = time();
                $ist_data['expire_time'] = strtotime('2099-12-31 23:59:59');
                $ist_data['publish_time'] = strtotime($param['publish_time']) <= time() ? time() : strtotime($param['publish_time']);
            }

            if ($type > 0 && DB::table('yz_appletslive_replay')->where('title', $ist_data['title'])->where('rid', $rid)->first()) {
                return $this->message('名称已存在', Url::absoluteWeb(''), 'danger');
            }
            DB::table('yz_appletslive_replay')->insert($ist_data);

            // 刷新接口数据缓存
            if ($type == 0) {
                Cache::forget(CacheService::$cache_keys['brandsale.albumlist']);
                Cache::forget(CacheService::$cache_keys['brandsale.albuminfo']);
                Cache::forget(CacheService::$cache_keys['brandsale.albumliverooms']);
            } else {
                Cache::forget(CacheService::$cache_keys['recorded.roomlist']);
                Cache::forget(CacheService::$cache_keys['recorded.roominfo']);
                Cache::forget(CacheService::$cache_keys['recorded.roomreplays']);
            }

            return $this->message('保存成功', Url::absoluteWeb('plugin.appletslive.admin.controllers.room.replaylist', ['rid' => $rid]));
        }

        $rid = request()->get('rid', 0);
        $room = DB::table('yz_appletslive_room')->where('id', $rid)->first();
        if (!$room) {
            return $this->message('数据不操作', Url::absoluteWeb(''), 'danger');
        }
        $liverooms = LiveRoom::whereIn('live_status', [101, 102, 105])->get();
        return view('Yunshop\Appletslive::admin.replay_add', [
            'rid' => $rid,
            'room' => $room,
            'liverooms' => $liverooms,
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
            return $this->message('数据不存在', Url::absoluteWeb(''), 'danger');
        }
        $delete_time = ($replay->delete_time > 0) ? 0 : time();
        Replay::where('id', $replay->id)->update(['delete_time' => $delete_time]);

        // 刷新接口数据缓存
        if ($replay->type == 0) {
            Cache::forget(CacheService::$cache_keys['brandsale.albumlist']);
            Cache::forget(CacheService::$cache_keys['brandsale.albuminfo']);
            Cache::forget(CacheService::$cache_keys['brandsale.albumliverooms']);
        } else {
            Cache::forget(CacheService::$cache_keys['recorded.roomlist']);
            Cache::forget(CacheService::$cache_keys['recorded.roominfo']);
            Cache::forget(CacheService::$cache_keys['recorded.roomreplays']);
        }

        return $this->message('修改成功', Url::absoluteWeb('plugin.appletslive.admin.controllers.room.replaylist', ['rid' => $replay->rid]));
    }
}
