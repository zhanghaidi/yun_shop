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
namespace Yunshop\Appletslive\common\services;

use app\common\helpers\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Class NumService
 * @package Yunshop\Appletslive\common\services
 */
class NumService
{
    /**
     * 获取课程订阅数、观看数、评论数
     * @param int $room_id
     * @return mixed|null
     */
    public static function getRoomNum($room_id = 0)
    {
        $cache_key = 'api_live_room_num';
        $cache_val = Cache::get($cache_key);
        if ($cache_val && $room_id) {
            if (is_array($room_id)) {
                $result = [];
                foreach ($room_id as $v) {
                    $key = 'key_' . $v;
                    if (array_key_exists($key, $cache_val)) {
                        self::setRoomNum($room_id);
                        break;
                    }
                    $result[$key] = $cache_val[$key];
                }
                if (count($room_id) == count($result)) {
                    return $result;
                } else {
                    return self::getRoomNum($room_id);
                }
            } else {
                $key = 'key_' . $room_id;
                if (array_key_exists($key, $cache_val)) {
                    return $cache_val[$key];
                } else {
                    self::setRoomNum($room_id);
                    return self::getRoomNum($room_id);
                }
            }
        }
        return $cache_val;
    }

    /**
     * 设置课程订阅数、观看数、评论数（自增1）
     * @param $room_id
     * @param $field subscription_num|view_num|comment_num
     * @return mixed
     */
    public static function setRoomNum($room_id, $field = null)
    {
        if (is_array($room_id)) {
            DB::table('appletslive_room')->whereIn('id', $room_id)->increment($field);
            $record = DB::table('appletslive_room')->whereIn('id', $room_id)->get();
        } else {
            DB::table('appletslive_room')->where('id', $room_id)->increment($field);
            $record = DB::table('appletslive_room')->where('id', $room_id)->first();
        }

        $cache_key = 'api_live_room_num';
        $cache_val = Cache::get($cache_key);

        if (is_array($room_id)) {
            foreach ($record as $item) {
                $key = 'key_' . $item->id;
                $val = [
                    'hot_num' => $item->subscription_num + $item->view_num + $item->comment_num,
                    'subscription_num' => $item->subscription_num,
                    'view_num' => $item->view_num,
                    'comment_num' => $item->comment_num,
                ];
                if (!$cache_val) {
                    $cache_val = [$key => $val];
                } else {
                    $cache_val[$key] = $val;
                }
            }
            Cache::forever($cache_key, $cache_val);
        } else {
            $key = 'key_' . $room_id;
            $val = [
                'hot_num' => $record->subscription_num + $record->view_num + $record->comment_num,
                'subscription_num' => $record->subscription_num,
                'view_num' => $record->view_num,
                'comment_num' => $record->comment_num,
            ];
            if (!$cache_val) {
                Cache::forever($cache_key, [$key => $val]);
            } else {
                $cache_val[$key] = $val;
                Cache::forever($cache_key, $cache_val);
            }
        }
    }
}