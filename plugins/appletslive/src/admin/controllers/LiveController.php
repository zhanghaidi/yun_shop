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
use app\common\helpers\PaginationHelper;
use Illuminate\Support\Facades\Log;
use app\common\services\qcloud\Api;

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

            if (request()->ajax()) {
                return $this->successJson('直播间同步成功');
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
            $cover_img_path = $this->downloadImgFromCos($param['coverImg']);
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
            $share_img_path = $this->downloadImgFromCos($param['shareImg']);
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
                $feeds_img_path = $this->downloadImgFromCos($param['feedsImg']);
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
            $result = (new BaseService())->createRoom($post_data);

            if ($result['errcode'] != 0) {
                return $this->errorJson($result['errmsg']);
            }
            return $this->successJson('直播间添加成功');
        }

        return view('Yunshop\Appletslive::admin.live_add')->render();
    }

    // 从云存储下载图片
    private function downloadImgFromCos($filepath)
    {
        global $_W;

        $fileinfo = explode('/', $filepath);
        $filename = $fileinfo[count($fileinfo) - 1];

        // 获取云存储配置信息
        $uni_setting = app('WqUniSetting')->get()->toArray();
        if (!empty($uni_setting['remote']) && iunserializer($uni_setting['remote'])['type'] != 0) {
            $setting['remote'] = iunserializer($uni_setting['remote']);
            $remote = $setting['remote']['cos'];
        } else {
            $remote = $_W['setting']['remote']['cos'];
        }

        try {

            $uniqid = uniqid();
            $localpath = ATTACHMENT_ROOT . 'image/' . $uniqid . $filename;

            $config = [
                'app_id' => $remote['appid'],
                'secret_id' => $remote['secretid'],
                'secret_key' => $remote['secretkey'],
                'region' => $remote['local'],
                'timeout' => 60,
            ];
            $cosApi = new Api($config);
            $ret = $cosApi->download($remote['bucket'], $filepath, $localpath);

            $message = $localpath;
            if ($ret['code'] != 0) {
                switch ($ret['code']) {
                    case -62:
                        $message = '输入的appid有误';
                        break;
                    case -79:
                        $message = '输入的SecretID有误';
                        break;
                    case -97:
                        $message = '输入的SecretKEY有误';
                        break;
                    case -166:
                        $message = '输入的bucket有误';
                        break;
                }
            }

            return ['result_code' => $ret['code'], 'data' => $message, 'ret' => $ret];

        } catch (\Exception $e) {
            return ['result_code' => 1, 'data' => $e->getMessage()];
        }
    }

    // 直播间导入商品
    public function import()
    {
        $id = request()->get('id', 0);
        $info = DB::table('yz_appletslive_room')->where('id', $id)->first();

        if (!$info) {
            return $this->message('无效的直播间ID', Url::absoluteWeb(''), 'danger');
        }

        return view('Yunshop\Appletslive::admin.room_edit', [
            'id' => $id,
            'info' => $info,
        ])->render();
    }
}
