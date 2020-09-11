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
use app\common\components\BaseController;
use app\common\helpers\Url;
use Yunshop\Appletslive\common\models\Replay;
use Yunshop\Appletslive\common\services\BaseService;
use Yunshop\Appletslive\common\services\CacheService;
use Yunshop\Appletslive\common\models\LiveRoom;
use Yunshop\Appletslive\common\models\Goods;
use app\common\helpers\PaginationHelper;
use Illuminate\Support\Facades\Log;


class LiveController extends BaseController
{
    // 直播间列表
    public function index()
    {
        $input = \YunShop::request();
        $limit = 20;
        $tag = request()->get('tag', '');

        // 同步房间列表
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
                foreach ($stored as $stk => $stv) {
                    if ($stv['roomid'] == $psv['roomid']) {
                        // 房间信息在数据库中存在，实时更新数据
                        $good_ids = '';
                        if (!empty($psv['goods'])) {
                            $good_ids = implode(',', array_column($psv['goods'], 'goods_id'));
                        }
                        if ($stv['name'] != $psv['name'] || $stv['cover_img'] != $psv['cover_img']
                            || $stv['share_img'] != $psv['share_img'] || $stv['live_status'] != $psv['live_status']
                            || $stv['start_time'] != $psv['start_time'] || $stv['end_time'] != $psv['end_time']
                            || $stv['live_status'] != $psv['live_status'] || $stv['goods_ids'] != $good_ids) {
                            array_push($update, [
                                'id' => $stv['id'],
                                'name' => $psv['name'],
                                'cover_img' => $psv['cover_img'],
                                'share_img' => $psv['share_img'],
                                'live_status' => $psv['live_status'],
                                'start_time' => $psv['start_time'],
                                'end_time' => $psv['end_time'],
                                'anchor_name' => $psv['anchor_name'],
                                'goods_ids' => $good_ids,
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
                    $id = $item['id'];
                    $temp = $item;
                    unset($temp['id']);
                    DB::table('yz_appletslive_liveroom')->where('id', $id)->update($temp);
                }
                Log::info('同步微信直播间数据:更新直播间信息', ['count' => count($update)]);
            }
            if ($insert) {
                DB::table('yz_appletslive_liveroom')->insert($insert);
                Log::info('同步微信直播间数据:新增直播间', ['count' => count($insert)]);
            }

            // 移除删掉的直播间
            $todel = [];
            foreach ($stored as $stk => $stv) {
                $match = false;
                foreach ($present as $psv) {
                    if ($stv['roomid'] == $psv['roomid']) {
                        $match = true;
                        break;
                    }
                }
                if (!$match) {
                    $todel[] = $stv['id'];
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

            if (request()->ajax()) {
                return $this->successJson('直播间同步成功', ['present' => $present]);
            }
        }

        // 清理已失效直播间
        if ($tag == 'clean') {

            $invalid_live_ids = LiveRoom::where('live_status', 108)->pluck('id');
            LiveRoom::where('live_status', 108)->delete();
            Replay::whereIn('room_id', $invalid_live_ids)->delete();

            Cache::forget(CacheService::$cache_keys['brandsale.albumlist']);
            Cache::forget(CacheService::$cache_keys['brandsale.albuminfo']);
            Cache::forget(CacheService::$cache_keys['brandsale.albumliverooms']);

            return $this->successJson('清理成功');
        }

        // 处理搜索条件
        $where = [];
        $where_between = ['start_time', [0, strtotime('20991231')]];
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
                $where_between[0] = $time_field;
                $where_between[1] =  [strtotime($search['date']['start']), strtotime($search['date']['end'] . ' 23:59:59')];
            }
            if (trim($search['live_status']) !== '') {
                $where[] = ['live_status', '=', $search['live_status']];
            }
        }

        $list = LiveRoom::where($where)
            ->whereBetween($where_between[0], $where_between[1])
            ->orderBy('id', 'desc')
            ->paginate($limit);
        $pager = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());

        return view('Yunshop\Appletslive::admin.live_index', [
            'list' => $list,
            'pager' => $pager,
            'request' => $input,
        ])->render();
    }

    // 添加直播间
    public function add()
    {
        if (request()->isMethod('post')) {

            $param = request()->all();
            $post_data = $param;
            unset($post_data['c']);
            unset($post_data['a']);
            unset($post_data['m']);
            unset($post_data['do']);
            unset($post_data['route']);

            // 必填项验证
            $field = ['name', 'coverImg', 'anchorName', 'anchorWechat', 'shareImg'];
            $field_name = ['直播间名称', '直播间背景图', '主播昵称', '主播微信号', '直播间分享图'];
            foreach ($field as $idx => $key) {
                if (!array_key_exists($key, $param) || $param[$key] == '') {
                    return $this->errorJson($field_name[$idx] . '不能为空');
                }
            }

            // 验证直播间名称长度
            $name_len = mb_strlen($param['name']);
            if ($name_len < 3 || $name_len > 17) {
                return $this->errorJson('直播间名称最短3个汉字，最长17个汉字', $param);
            }

            // 验证开播时间和结束时间
            $time = time();
            $start_time = strtotime($param['startTime']);
            $end_time = strtotime($param['endTime']);
            if (($start_time < ($time + 600)) || ($start_time > strtotime('+6 month'))) {
                return $this->errorJson('开播时间需要在当前时间的10分钟后 并且 开始时间不能在 6 个月后');
            }
            if (($end_time < ($start_time + 1800)) || ($end_time > ($start_time + 86400))) {
                return $this->errorJson('结束时间和开播时间间隔不得短于30分钟，不得超过24小时', ['']);
            }
            $post_data['startTime'] = $start_time;
            $post_data['endTime'] = $end_time;

            // 验证主播昵称长度
            $name_len = mb_strlen($param['name']);
            if ($name_len < 2 || $name_len > 15) {
                return $this->errorJson('主播昵称最短2个汉字，最长15个汉字', $param);
            }

            // 上传直播间背景图临时素材
            $cover_img_path = (new BaseService())->downloadImgFromCos($param['coverImg']);
            if ($cover_img_path['result_code'] != 0) {
                $msg = '背景图获取失败:' . $cover_img_path['data'];
                return $this->errorJson($msg);
            }
            $upload_media = (new BaseService())->uploadMedia($cover_img_path['data']);
            if (array_key_exists('errcode', $upload_media)) {
                return $this->errorJson('上传临时素材失败:' . $upload_media['errmsg']);
            }
            $post_data['coverImg'] = $upload_media['media_id'];

            // 上传直播间分享图临时素材
            $share_img_path = (new BaseService())->downloadImgFromCos($param['shareImg']);
            if ($share_img_path['result_code'] != 0) {
                $msg = '分享图获取失败:' . $share_img_path['data'];
                return $this->errorJson($msg);
            }
            $upload_media = (new BaseService())->uploadMedia($share_img_path['data']);
            if (array_key_exists('errcode', $upload_media)) {
                return $this->errorJson('上传临时素材失败:' . $upload_media['errmsg']);
            }
            $post_data['shareImg'] = $upload_media['media_id'];

            // 上传直播间购物频道封面图
            if ($param['feedsImg'] != '') {
                $feeds_img_path = (new BaseService())->downloadImgFromCos($param['feedsImg']);
                if ($feeds_img_path['result_code'] != 0) {
                    $msg = '购物频道封面图获取失败:' . $feeds_img_path['data'];
                    return $this->errorJson($msg);
                }
                $upload_media = (new BaseService())->uploadMedia($feeds_img_path['data']);
                if (array_key_exists('errcode', $upload_media)) {
                    return $this->errorJson('上传临时素材失败:' . $upload_media['errmsg']);
                }
                $post_data['feedsImg'] = $upload_media['media_id'];
            }

            // 调用小程序接口添加直播间
            $post_data['isFeedsPublic'] = intval($post_data['isFeedsPublic']);
            $post_data['type'] = intval($post_data['type']);
            $post_data['screenType'] = intval($post_data['screenType']);
            $post_data['closeLike'] = intval($post_data['closeLike']);
            $post_data['closeGoods'] = intval($post_data['closeGoods']);
            $post_data['closeComment'] = intval($post_data['closeComment']);
            $post_data['closeReplay'] = intval($post_data['closeReplay']);
            $post_data['closeShare'] = intval($post_data['closeShare']);
            $post_data['closeKf'] = intval($post_data['closeKf']);
            $result = (new BaseService())->createRoom($post_data);

            if ($result['errcode'] != 0) {
                return $this->errorJson($result['errmsg']);
            }

            $insert_data = [
                'name' => $post_data['name'],
                'roomid' => $result['roomId'],
                'live_status' => 100,
                'start_time' => $post_data['startTime'],
                'end_time' => $post_data['endTime'],
                'anchor_name' => $post_data['anchorName'],
                'anchor_wechat' => $post_data['anchorWechat'],
                'sub_anchor_wechat' => $post_data['subAnchorWechat'],
                'feeds_img' => $param['feedsImg'],
                'is_feeds_public' => $post_data['isFeedsPublic'],
                'type' => $post_data['type'],
                'screen_type' => $post_data['screenType'],
                'close_like' => $post_data['closeLike'],
                'close_goods' => $post_data['closeGoods'],
                'close_comment' => $post_data['closeComment'],
                'close_replay' => $post_data['closeReplay'],
                'close_share' => $post_data['closeShare'],
                'close_kf' => $post_data['closeKf'],
            ];
            LiveRoom::insert($insert_data);
            return $this->successJson('直播间添加成功');
        }

        return view('Yunshop\Appletslive::admin.live_add')->render();
    }

    // 直播间导入商品
    public function import()
    {
        $id = request()->get('id', 0);
        $info = LiveRoom::where('id', $id)->first();

        if (!$info) {
            return $this->message('无效的直播间ID', Url::absoluteWeb(''), 'danger');
        }

        $goods = Goods::whereNotIn('id', explode($info['goods_ids']))->get();

        return view('Yunshop\Appletslive::admin.goods_edit', [
            'id' => $id,
            'info' => $info,
            'goods' => $goods,
        ])->render();
    }
}
