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
use Yunshop\Appletslive\common\services\CacheService;
use Yunshop\Appletslive\common\models\Room;
use Yunshop\Appletslive\common\models\Replay;
use Yunshop\Appletslive\common\models\LiveRoom;
use app\common\helpers\PaginationHelper;
use Yunshop\Appletslive\common\models\RoomComment;

class RoomController extends BaseController
{
    // 房间列表
    public function index()
    {
        $type = request()->get('type', 1);
        if (!in_array($type, [1, 2])) {
            throw new AppException('房间类型有误');
        }

        $input = \YunShop::request();
        $limit = 20;
        $uniacid = \YunShop::app()->uniacid;
        if ($type == 1) { // 录播
            // 处理搜索条件
            $where[] = ['type', '=', 1];
            $where[] = ['uniacid', '=', $uniacid];
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
                if (trim($search['is_selected']) !== '') {
                    if ($search['is_selected'] === '0') {
                        $where[] = ['is_selected', '=', 0];
                    } else {
                        $where[] = ['is_selected', '=', 1];
                    }
                }
            }
            $list = Room::where($where)
                ->orderBy('sort', 'desc')
                ->orderBy('id', 'desc')
                ->paginate($limit);
            if ($list->total() > 0) {
                foreach ($list as $k => &$comment_value) {
                    $comment_value['comment_num'] = RoomComment::where([['room_id', '=', $comment_value['id']]])->count();
                }
            }
            $pager = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());
        }

        if ($type == 2) { // 品牌特卖
            // 处理搜索条件
            $where[] = ['type', '=', 2];
            $where[] = ['uniacid', '=', $uniacid];
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
            if ($list->total() > 0) {
                foreach ($list as $k => &$comment_value) {
                    $comment_value['comment_num'] = RoomComment::where([['room_id', '=', $comment_value['id']]])->count();
                }
            }
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
        $uniacid = \YunShop::app()->uniacid;
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
            $room = DB::table('yz_appletslive_room')->where('uniacid',$uniacid)->where('id', $id)->first();
            if (!$room) {
                return $this->message('无效的ID', Url::absoluteWeb(''), 'danger');
            }
            if (DB::table('yz_appletslive_room')->where('uniacid',$uniacid)->where('name', $upd_data['name'])->where('id', '<>', $id)->first()) {
                return $this->message('名称已存在', Url::absoluteWeb(''), 'danger');
            }

            // {{--fixby-wk-课程设置精选 20201019--}}
            if($room['type'] == 1){//课程状态 0筹备中 1更新中 2已完结
                //{{--fixby-wk-课程付费 20201124 一个课程只能关联一个商品--}}
                if ($param['goods_id'] > 0) {
                    if (DB::table('yz_appletslive_room')->where('uniacid',$uniacid)->where('goods_id', $param['goods_id'])->where('id', '<>', $id)->first()) {
                        return $this->message('该商品已经关联其它课程', Url::absoluteWeb(''), 'danger');
                    }
                    //{{--fixby-wk-课程付费 20201125 商品必须是虚拟商品--}}
                    $goods_info = DB::table('yz_goods')->where('id', $param['goods_id'])->first();
                    if ($goods_info['type'] == 1) {
                        return $this->message('关联商品必须是虚拟商品', Url::absoluteWeb(''), 'danger');
                    }
                }

                $upd_data['live_status'] = intval($param['live_status']);
                $upd_data['is_selected'] = intval($param['is_selected']);//是否精选 0否 1是
                $upd_data['tag'] = $param['tag'];//课程标签
                if ($param['buy_type'] == 1) {
                    if (empty($param['goods_id'])) {
                        return $this->message('请选择关联商品', Url::absoluteWeb(''), 'danger');
                    }
                    if (!preg_match('/^(-1)|\d+$/', $param['expire_time'])) {
                        return $this->message('课程有效期必须为整数', Url::absoluteWeb(''), 'danger');
                    }
                    if ($param['expire_time'] == 0) {
                        return $this->message('课程有效期不能为零', Url::absoluteWeb(''), 'danger');
                    }
                    $upd_data['buy_type'] = 1;
                    $upd_data['expire_time'] = $param['expire_time'];
                    $upd_data['goods_id'] = $param['goods_id'];
                    $upd_data['ios_open'] = $param['ios_open'];
                } else {
                    $upd_data['buy_type'] = 0;
                    $upd_data['expire_time'] = 0;
                    $upd_data['goods_id'] = 0;
                    $upd_data['ios_open'] = 0;
                }
            }

            $upd_data['display_type'] = Room::setDisplayStatus($param);

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
        $info = DB::table('yz_appletslive_room')->where('uniacid',$uniacid)->where('id', $id)->first();
        $info['is_display'] = Room::getIsDisplayAttribute($info);
        $info['is_share'] = Room::getIsShareAttribute($info);

        if (!$info) {
            return $this->message('无效的ID', Url::absoluteWeb(''), 'danger');
        }

        if($info['goods_id']){
            $info['goods_name'] = \app\common\models\Goods::uniacid()->where('id',$info['goods_id'])->first()->title;
        }else{
            $info['goods_name'] = '';
        }

        return view('Yunshop\Appletslive::admin.room_edit', [
            'id' => $id,
            'info' => $info,
        ])->render();
    }

    // 添加录播房间
    public function add()
    {
        $uniacid = \YunShop::app()->uniacid;
        if (request()->isMethod('post')) {

            $param = request()->all();
            if (!array_key_exists('type', $param) || !in_array($param['type'], [1, 2])) { // 类型
                return $this->message('类型参数有误', Url::absoluteWeb(''), 'danger');
            }
            $ist_data = ['type' => $param['type'], 'sort' => intval($param['sort'])];
            $ist_data['uniacid'] = $uniacid;
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
            // {{--fixby-wk-课程设置精选 20201019--}}
            if($param['type'] == 1){//课程状态 0筹备中 1更新中 2已完结
                //{{--fixby-wk-课程付费 20201124 一个课程只能关联一个商品--}}
                if ($param['goods_id'] > 0) {
                    if (DB::table('yz_appletslive_room')->where('goods_id', $param['goods_id'])->first()) {
                        return $this->message('该商品已经关联其它课程', Url::absoluteWeb(''), 'danger');
                    }
                    //{{--fixby-wk-课程付费 20201125 商品必须是虚拟商品--}}
                    $goods_info = DB::table('yz_goods')->where('id', $param['goods_id'])->first();
                    if ($goods_info['type'] == 1) {
                        return $this->message('关联商品必须是虚拟商品', Url::absoluteWeb(''), 'danger');
                    }
                }

                $ist_data['live_status'] = intval($param['live_status']);
                $ist_data['is_selected'] = intval($param['is_selected']);//是否精选 0否 1是
                $ist_data['tag'] = $param['tag'];//课程标签
                if ($param['buy_type'] == 1) {
                    if (empty($param['goods_id'])) {
                        return $this->message('请选择关联商品', Url::absoluteWeb(''), 'danger');
                    }
                    if (!preg_match('/^(-1)|\d+$/', $param['expire_time'])) {
                        return $this->message('课程有效期必须为整数', Url::absoluteWeb(''), 'danger');
                    }
                    if ($param['expire_time'] == 0) {
                        return $this->message('课程有效期不能为零', Url::absoluteWeb(''), 'danger');
                    }
                    $ist_data['buy_type'] = 1;
                    $ist_data['expire_time'] = $param['expire_time'];
                    $ist_data['goods_id'] = $param['goods_id'];
                    $ist_data['ios_open'] = $param['ios_open'];
                } else {
                    $ist_data['buy_type'] = 0;
                    $ist_data['expire_time'] = 0;
                    $ist_data['goods_id'] = 0;
                    $ist_data['ios_open'] = 0;
                }
            }

            $ist_data['display_type'] = Room::setDisplayStatus($param);

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
        $limit = 20;
        $uniacid = \YunShop::app()->uniacid;
        // 录播视频
        if ($room_type == 1) {

            $where[] = ['rid', '=', $rid];
            $where[] = ['uniacid', '=', $uniacid];
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

            $where[] = ['yz_appletslive_replay.rid', '=', $rid];
            $where[] = ['uniacid', '=', $uniacid];
            // 处理搜索条件
            if (isset($input->search)) {

                $search = $input->search;
                if (intval($search['roomid']) > 0) {
                    $where[] = ['yz_appletslive_liveroom.roomid', '=', intval($search['roomid'])];
                }
                if (trim($search['name']) !== '') {
                    $where[] = ['yz_appletslive_liveroom.name', 'like', '%' . trim($search['name']) . '%'];
                }
                if (trim($search['live_status']) !== '') {
                    $where[] = ['yz_appletslive_liveroom.live_status', '=', $search['live_status']];
                }
                if (trim($search['status']) !== '') {
                    if ($search['status'] === '0') {
                        $where[] = ['yz_appletslive_replay.delete_time', '>', 0];
                    } else {
                        $where[] = ['yz_appletslive_replay.delete_time', '=', 0];
                    }
                }
            }

            $replay_list = DB::table('yz_appletslive_replay')
                ->join('yz_appletslive_liveroom', 'yz_appletslive_replay.room_id', '=', 'yz_appletslive_liveroom.id')
                ->select('yz_appletslive_replay.id', 'yz_appletslive_liveroom.roomid', 'yz_appletslive_liveroom.name',
                    'yz_appletslive_liveroom.cover_img', 'yz_appletslive_liveroom.anchor_name',
                    'yz_appletslive_liveroom.anchor_name', 'yz_appletslive_liveroom.live_status',
                    'yz_appletslive_liveroom.start_time', 'yz_appletslive_liveroom.end_time',
                    'yz_appletslive_replay.delete_time')
                ->where($where)
                ->whereIn('yz_appletslive_liveroom.live_status', [101, 102, 103, 105, 107])
                ->orderBy('yz_appletslive_liveroom.start_time', 'desc')
                ->orderBy('yz_appletslive_replay.id', 'desc')
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
        $uniacid = \YunShop::app()->uniacid;
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
                //$upd_data['publish_time'] = strtotime($param['publish_time']) <= time() ? time() : strtotime($param['publish_time']);
                $upd_data['publish_time'] = strtotime($param['publish_time']);
            }

            $replay = DB::table('yz_appletslive_replay')->where('id', $id)->first();
            if (!$replay) {
                if (request()->ajax()) {
                    return $this->errorJson('无效的视频或直播ID');
                } else {
                    return $this->message('无效的视频或直播ID', Url::absoluteWeb(''), 'danger');
                }
            }
            if (DB::table('yz_appletslive_replay')->where('uniacid',$uniacid)->where('title', $upd_data['title'])->where('rid', $replay->rid)->where('id', '<>', $id)->first()) {
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

            if (request()->ajax()) {
                return $this->successJson('保存成功');
            } else {
                return $this->message('保存成功', Url::absoluteWeb('plugin.appletslive.admin.controllers.room.replaylist', ['rid' => $replay['rid']]));
            }
        }

        $id = request()->get('id', 0);
        $info = DB::table('yz_appletslive_replay')->where('id', $id)->first();
        if (!$info) {
            return $this->message('视频或直播不存在', Url::absoluteWeb(''), 'danger');
        }
        $info['minute'] = floor($info['time_long'] / 60);
        $info['second'] = $info['time_long'] % 60;

        $limit = 20;
        $liverooms = LiveRoom::whereIn('live_status', [
            APPLETSLIVE_ROOM_LIVESTATUS_101,
            APPLETSLIVE_ROOM_LIVESTATUS_102,
            APPLETSLIVE_ROOM_LIVESTATUS_105,
        ])->where('uniacid',$uniacid)->paginate($limit);
        $pager = PaginationHelper::show($liverooms->total(), $liverooms->currentPage(), $liverooms->perPage());

        return view('Yunshop\Appletslive::admin.replay_edit', [
            'id' => $id,
            'info' => $info,
            'liverooms' => $liverooms,
            'pager' => $pager,
        ])->render();
    }

    // 视频|直播添加
    public function replayadd()
    {
        $uniacid = \YunShop::app()->uniacid;
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
                'uniacid' => $uniacid,
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

            if (request()->ajax()) {
                return $this->successJson('添加成功');
            } else {
                return $this->message('添加成功', Url::absoluteWeb('plugin.appletslive.admin.controllers.room.replaylist', ['rid' => $rid]));
            }
        }

        $rid = request()->get('rid', 0);
        $room = DB::table('yz_appletslive_room')->where('uniacid', $uniacid)->where('id', $rid)->first();
        if (!$room) {
            return $this->message('数据不存在', Url::absoluteWeb(''), 'danger');
        }

        $limit = 20;
        $liverooms = LiveRoom::whereIn('live_status', [
            APPLETSLIVE_ROOM_LIVESTATUS_101,
            APPLETSLIVE_ROOM_LIVESTATUS_102,
            APPLETSLIVE_ROOM_LIVESTATUS_105,
        ])->where('uniacid', $uniacid)->paginate($limit);
        $pager = PaginationHelper::show($liverooms->total(), $liverooms->currentPage(), $liverooms->perPage());

        return view('Yunshop\Appletslive::admin.replay_add', [
            'rid' => $rid,
            'room' => $room,
            'liverooms' => $liverooms,
            'pager' => $pager,
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

    // fixBy-wk 评论列表 2020.9.29
    public function commentlist()
    {
        $uniacid = \YunShop::app()->uniacid;
        $rid = request()->get('rid', 0);
        $room = DB::table('yz_appletslive_room')->where('uniacid',$uniacid)->where('id', $rid)->first();
        $room_type = $room['type'];

        $input = \YunShop::request();
        $limit = 20;
        $where = [];
        if (isset($input->search)) {
            $search = $input->search;

            if (trim($search['del_sta']) !== '') {
                if ($search['del_sta'] === '0') {
                    $where[] = ['del_sta', '=', 0];
                } elseif ($search['del_sta'] === '1') {
                    $where[] = ['del_sta', '=', 1];
                } else {
                    $where[] = ['del_sta', '=', 2];
                }
            }
        }
        //评论列表
        $where[] = ['room_id', '=', $rid];
        $where[] = ['is_reply', '=', 0];
        $comment_list = RoomComment::where($where)
            ->orderBy('id', 'desc')
            ->paginate($limit);

        if ($comment_list->total() > 0) {
            foreach ($comment_list as $k => $comment_value) {
                //用户昵称 头像
                $user_info = DB::table('diagnostic_service_user')
                    ->where('ajy_uid', $comment_value['user_id'])
                    ->select('ajy_uid', 'nickname', 'avatarurl')
                    ->first();
                $comment_value['nickname'] = $user_info['nickname'];
                $comment_value['avatarurl'] = $user_info['avatarurl'];
                $comment_value['create_time'] = date('Y-m-d H:i',$comment_value['create_time']);
                $comment_value['comment_num'] = RoomComment::where([
                    ['room_id', '=', $comment_value['id']],
                    ['is_reply', '=', 1]
                ])->count();
                //回复的条数
                $comment_value['counts'] = RoomComment::where([
                    ['parent_id', '=', $comment_value['id']],
                    ['is_reply', '=', 1]
                ])->count();
            }
        }

        $pager = PaginationHelper::show($comment_list->total(), $comment_list->currentPage(), $comment_list->perPage());

        return view('Yunshop\Appletslive::admin.comment_list', [
            'rid' => $rid,
            'room_type' => $room_type,
            'comment_list' => $comment_list,
            'pager' => $pager,
            'request' => $input,
        ])->render();

    }
    // fixBy-wk 评论回复列表 2020.9.29
    public function commentreplylist()
    {
        $rid = request()->get('id', 0);
        $limit = 20;
        $input = \YunShop::request();

        $where = [];
        if (isset($input->search)) {
            $search = $input->search;

            if (trim($search['del_sta']) !== '') {
                if ($search['del_sta'] === '0') {
                    $where[] = ['del_sta', '=', 0];
                } elseif ($search['del_sta'] === '1') {
                    $where[] = ['del_sta', '=', 1];
                } else {
                    $where[] = ['del_sta', '=', 2];
                }
            }
        }
        //评论列表
        $comment_list = RoomComment::where([
                ['parent_id', '=', $rid],
                ['is_reply', '=', 1]
            ])->where($where)
            ->paginate($limit);

        if ($comment_list->total() > 0) {
            foreach ($comment_list as $k => &$comment_value) {
                //用户昵称 头像
                $user_info = DB::table('diagnostic_service_user')
                    ->where('ajy_uid', $comment_value['user_id'])
                    ->select('ajy_uid', 'nickname', 'avatarurl')
                    ->first();
                $comment_value['nickname'] = $user_info['nickname'];
                $comment_value['avatarurl'] = $user_info['avatarurl'];
                $comment_value['create_time'] = date('Y-m-d H:i',$comment_value['create_time']);
            }
        }
        $pager = PaginationHelper::show($comment_list->total(), $comment_list->currentPage(), $comment_list->perPage());

        return view('Yunshop\Appletslive::admin.comment_reply_list', [
            'id' => $rid,
            'comment_list' => $comment_list,
            'pager' => $pager,
            'request' => $input,
        ])->render();

    }

    // fixBy-wk 评论删除 2020.9.29
    public function commentdel()
    {
        $input = request()->all();
        $id_invalid = false;
        if (!array_key_exists('id', $input)) { // 房间id
            $id_invalid = true;
        }
        $replay = RoomComment::where('id', intval($input['id']))->first();
        if (empty($replay)) {
            $id_invalid = true;
        }
        if ($id_invalid) {
            return $this->message('数据不存在', Url::absoluteWeb(''), 'danger');
        }
        if($replay->parent_id == 0){
            //删除子评论
            RoomComment::where('parent_id', $replay->id)->delete();
        }
        $del_res = RoomComment::where('id', $replay->id)->delete();

        $cache_key = "api_live_room_comment|$replay->room_id";
        $cache_key_replay_comment = "api_live_replay_comment|$replay->room_id";

        // 刷新接口数据缓存
        if ($del_res) {
            Cache::forget(CacheService::$cache_keys[$cache_key]);
            Cache::forget(CacheService::$cache_keys[$cache_key_replay_comment]);

            Cache::forget(CacheService::$cache_keys['brandsale.albumcomment']);
            Cache::forget(CacheService::$cache_keys['brandsale.albumlist']);
            Cache::forget(CacheService::$cache_keys['brandsale.albuminfo']);
            Cache::forget(CacheService::$cache_keys['brandsale.albumliverooms']);
        } else {
            Cache::forget(CacheService::$cache_keys[$cache_key]);
            Cache::forget(CacheService::$cache_keys[$cache_key_replay_comment]);

            Cache::forget(CacheService::$cache_keys['brandsale.albumcomment']);
            Cache::forget(CacheService::$cache_keys['recorded.roomlist']);
            Cache::forget(CacheService::$cache_keys['recorded.roominfo']);
            Cache::forget(CacheService::$cache_keys['recorded.roomreplays']);
        }
        if($input['type'] == 'comment_list'){
            return $this->message('删除成功', Url::absoluteWeb('plugin.appletslive.admin.controllers.room.commentlist', ['rid' => $replay->room_id]));
        }else{
            return $this->message('删除成功', Url::absoluteWeb('plugin.appletslive.admin.controllers.room.commentreplylist', ['id' => $replay->parent_id]));
        }

    }
//  fixBy-wk-20201204  通过、拒绝审核
    public function commentverify()
    {
        $input = request()->all();
        $id_invalid = false;
        if (!array_key_exists('id', $input)) { // 房间id
            $id_invalid = true;
        }
        $replay = RoomComment::where('id', intval($input['id']))->first();
        if (empty($replay)) {
            $id_invalid = true;
        }
        if ($id_invalid) {
            return $this->message('数据不存在', Url::absoluteWeb(''), 'danger');
        }

        $del_res = RoomComment::where('id', $replay->id)->update(['del_sta' => $input['del_sta']]);

        $cache_key = "api_live_room_comment|$replay->room_id";
        $cache_key_replay_comment = "api_live_replay_comment|$replay->room_id";

        // 刷新接口数据缓存
        if ($del_res) {

            if ($input['del_sta'] == 0) {
                //审核通过 增加评论数量
                DB::table('yz_appletslive_room')->where('id', $replay->room_id)->decrement('comment_num');
            }
            if($input['del_sta'] == 2){
               //拒绝通过 查看是否超过三次，三次之后禁言或加入黑名单
                $delsta_count = RoomComment::where('user_id', $replay->user_id)->where('del_sta',2)->count();
                if($delsta_count >= 3){
                    DB::table('diagnostic_service_user')->where('ajy_uid', $replay->user_id)->update(['is_black' => 1, 'is_black_time' => time(),'black_end_time' => time() + 86400*365]);
                }
            }

            Cache::forget(CacheService::$cache_keys[$cache_key]);
            Cache::forget(CacheService::$cache_keys[$cache_key_replay_comment]);

            Cache::forget(CacheService::$cache_keys['brandsale.albumcomment']);
            Cache::forget(CacheService::$cache_keys['brandsale.albumlist']);
            Cache::forget(CacheService::$cache_keys['brandsale.albuminfo']);
            Cache::forget(CacheService::$cache_keys['brandsale.albumliverooms']);

            Cache::forget(CacheService::$cache_keys[$cache_key]);
            Cache::forget(CacheService::$cache_keys[$cache_key_replay_comment]);

            Cache::forget(CacheService::$cache_keys['brandsale.albumcomment']);
            Cache::forget(CacheService::$cache_keys['recorded.roomlist']);
            Cache::forget(CacheService::$cache_keys['recorded.roominfo']);
            Cache::forget(CacheService::$cache_keys['recorded.roomreplays']);
        }
        if($input['type'] == 'comment_list'){
            return $this->message('评论审核成功', Url::absoluteWeb('plugin.appletslive.admin.controllers.room.commentlist', ['rid' => $replay->room_id]));
        }else{
            return $this->message('评论审核成功', Url::absoluteWeb('plugin.appletslive.admin.controllers.room.commentreplylist', ['id' => $replay->parent_id]));
        }

    }
}
