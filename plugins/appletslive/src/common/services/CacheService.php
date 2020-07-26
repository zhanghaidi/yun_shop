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
 * 接口数据缓存服务类
 * Class CacheService
 * @package Yunshop\Appletslive\common\services
 */
class CacheService
{
    protected $uniacid = 45;

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

    /**
     * 获取课程评论相关信息
     * @param int $room_id
     * @return mixed|null
     */
    public static function getRoomComment($room_id)
    {
        $cache_key = "api_live_room_comment|$room_id";
        $cache_val = Cache::get($cache_key);
        if ($cache_val) {
            self::setRoomComment($room_id);
            $cache_val = Cache::get($cache_key);
        }
        return $cache_val;
    }

    /**
     * 设置课程评论相关信息
     * @param $room_id
     * @return mixed
     */
    public static function setRoomComment($room_id)
    {
        $cache_key = "api_live_room_comment|$room_id";
        $comment = DB::table('appletslive_room_comment')
            ->where('uniacid', $this->uniacid)
            ->where('room_id', $room_id)
            ->get()->toArray();
        if (empty($comment)) {
            Cache::forever($cache_key, ['total' => 0, 'list' => []]);
        } else {
            $user = DB::table('diagnostic_service_user')
                ->where('ajy_uid', array_column($comment, 'user_id'))
                ->select('ajy_uid', 'nickname', 'avatarurl', 'province')
                ->get()->toArray();
            foreach ($user as $k => $v) {
                $user[100000 + $v['ajy_uid']] = $v;
                $user[$k] = null;
            }
            $user = array_filter($user);
            $reply = [];
            foreach ($comment as $k => $v) {
                if ($v['is_reply']) {
                    array_push($reply, $v);
                    $comment[$k] = null;
                }
            }
            $comment = array_filter($comment);
            array_walk($comment, function (&$item) use ($reply, $user) {
                $reply_for_this_comment = [];
                foreach ($reply as $v) {
                    if ($v['parent_id'] == $item['id']) {
                        $temp = array_merge($v, ['user' => $user[100000 + $v['user_id']]]);
                        array_push($reply_for_this_comment, $temp);
                    }
                }
                $item['user'] = $user[100000 + $item['user_id']];
                $item['reply'] = ['total' => count($reply_for_this_comment), 'list' => $reply_for_this_comment];
            });
            Cache::forever($cache_key, $comment);
        }
    }

    /**
     * 获取课程订阅相关信息
     * @param int $room_id
     * @return mixed|null
     */
    public static function getRoomSubscription($room_id = 0)
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
     * 设置课程订阅相关信息
     * @param $room_id
     * @return mixed
     */
    public static function setRoomSubscription($room_id)
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