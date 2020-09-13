<?php
/**
 * Created by PhpStorm.
 * User: weifeng
 * Date: 2020-02-28
 * Time: 12:45
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
namespace Yunshop\Appletslive\common\models;

use app\common\models\BaseModel;
use Yunshop\Appletslive\common\services\BaseService;
use Illuminate\Support\Facades\Log;

class LiveRoom extends BaseModel
{
    public $table = "yz_appletslive_liveroom";

    public $timestamps = false;

    // 同步直播间列表(与小程序官方后台同步数据)
    public static function refresh($print_log = false)
    {
        // 重新查询并同步直播间列表
        $room_from_weixin = (new BaseService())->getRooms();
        $present = $room_from_weixin['room_info'];
        $ids = array_column($present, 'roomid');
        sort($ids);

        $sort_present = [];
        foreach ($ids as $id) {
            foreach ($present as $item) {
                if ($item['roomid'] == $id) {
                    $sort_present[$id] = $item;
                    break;
                }
            }
        }

        // 查询数据库中已存在的直播间列表
        $stored = self::orderBy('id', 'desc')->limit(100)->get();

        $insert = [];
        $update = [];
        foreach ($sort_present as $spk => $spv) {
            $exist = false;
            foreach ($stored as $stk => $stv) {
                if ($stv['roomid'] == $spv['roomid']) {
                    // 房间信息在数据库中存在，实时更新数据
                    $good_ids = '';
                    if (!empty($spv['goods'])) {
                        $good_ids = implode(',', array_column($spv['goods'], 'goods_id'));
                    }
                    if ($stv['name'] != $spv['name'] || $stv['cover_img'] != $spv['cover_img']
                        || $stv['share_img'] != $spv['share_img'] || $stv['start_time'] != $spv['start_time']
                        || $stv['end_time'] != $spv['end_time'] || $stv['goods_ids'] != $good_ids
                        || ($stv['live_status'] != 108 && $stv['live_status'] != $spv['live_status'])) {
                        array_push($update, [
                            'id' => $stv['id'],
                            'name' => $spv['name'],
                            'cover_img' => $spv['cover_img'],
                            'share_img' => $spv['share_img'],
                            'live_status' => $spv['live_status'],
                            'start_time' => $spv['start_time'],
                            'end_time' => $spv['end_time'],
                            'anchor_name' => $spv['anchor_name'],
                            'goods_ids' => $good_ids,
                            'goods' => json_encode($spv['goods']),
                        ]);
                    }
                    $exist = true;
                    break;
                }
            }
            // 房间信息在数据库中不存在，实时记录数据
            if (!$exist) {
                array_push($insert, [
                    'name' => $spv['name'],
                    'roomid' => $spv['roomid'],
                    'cover_img' => $spv['cover_img'],
                    'share_img' => $spv['share_img'],
                    'live_status' => $spv['live_status'],
                    'start_time' => $spv['start_time'],
                    'end_time' => $spv['end_time'],
                    'anchor_name' => $spv['anchor_name'],
                    'goods' => json_encode($spv['goods']),
                ]);
            }
        }

        // 移除删掉的直播间
        $delete = [];
        foreach ($stored as $stk => $stv) {
            if (empty($sort_present[$stv['roomid']]) && $stv['live_status'] != 108) {
                array_push($delete, $stv['id']);
            }
        }

        if ($insert) {
            self::insert($insert);
            if ($print_log) {
                Log::info('同步微信直播间数据:新增直播间', ['count' => count($insert)]);
            }
        }
        if ($update) {
            foreach ($update as $item) {
                $id = $item['id'];
                $temp = $item;
                unset($temp['id']);
                self::where('id', $id)->update($temp);
            }
            if ($print_log) {
                Log::info('同步微信直播间数据:更新直播间信息', ['count' => count($update)]);
            }
        }
        if ($delete) {
            self::whereIn('id', $delete)->update(['live_status' => 108]);
            Replay::whereIn('room_id', $delete)->update(['delete_time' => time()]);
            if ($print_log) {
                Log::info('同步微信直播间数据:移除直播间', ['count' => count($delete)]);
            }
        }

        return [
            'stored' => $stored,
            'present' => $sort_present,
            'insert' => $insert,
            'update' => $update,
            'delete' => $delete,
        ];
    }
}
