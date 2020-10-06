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

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

/**
 * 接口数据缓存服务类
 * Class CacheService
 * @package Yunshop\Appletslive\common\services
 */
class CacheService
{
    protected static $uniacid = 45;

    public static $cache_keys = [
        'recorded.roomlist' => 'appletslive_api_recorded_roomlist',
        'recorded.roominfo' => 'appletslive_api_recorded_roominfo',
        'recorded.roomreplays' => 'appletslive_api_recorded_roomreplays',
        'recorded.roomreplayinfo' => 'appletslive_api_recorded_roomreplayinfo',
        'brandsale.albumlist' => 'appletslive_api_brandsale_albumlist',
        'brandsale.albuminfo' => 'appletslive_api_brandsale_albuminfo',
        'brandsale.albumnum' => 'appletslive_api_brandsale_albumnum',
        'brandsale.albumsubscription' => 'appletslive_api_brandsale_albumsubscription',
        'brandsale.albumusersubscription' => 'appletslive_api_brandsale_albumusersubscription',
        'brandsale.albumcomment' => 'appletslive_api_brandsale_albumcomment',
        'brandsale.albumliverooms' => 'appletslive_api_brandsale_albumliverooms',
        'brandsale.albumliveroomnum' => 'appletslive_api_brandsale_albumliveroomnum',
        'brandsale.albumliveroomwatch' => 'appletslive_api_brandsale_albumliveroomwatch',
    ];

    /************************ 课程/录播 相关缓存处理 BEGIN ************************/

    /**
     * 获取录播课程列表缓存数据
     * @param int $room_id
     * @return mixed|null
     */
    public static function getRecordedRoomList($page, $limit = 10)
    {
        $page_key = "$limit|$page";
        $cache_key = self::$cache_keys['recorded.roomlist'];
        $cache_val = Cache::get($cache_key);
        if (!$cache_val || !array_key_exists($page_key, $cache_val)) {
            self::setRecordedRoomList($page, $limit);
        }
        $cache_val = Cache::get($cache_key);
        return $cache_val[$page_key];
    }

    /**
     * 设置录播课程列表缓存数据
     *
     * @param $page
     * @param $limit
     */
    public static function setRecordedRoomList($page, $limit)
    {
        $page_key = "$limit|$page";
        $cache_key = self::$cache_keys['recorded.roomlist'];
        $cache_val = Cache::get($cache_key);

        $offset = ($page - 1) * $limit;
        $total = DB::table('yz_appletslive_room')
            ->where('type', 1)
            ->where('delete_time', 0)
            ->count();
        $list = DB::table('yz_appletslive_room')
            ->select('id', 'name', 'live_status', 'cover_img', 'subscription_num', 'view_num', 'comment_num')
            ->where('type', 1)
            ->where('delete_time', 0)
            ->orderBy('sort', 'desc')
            ->orderBy('id', 'asc')
            ->offset($offset)
            ->limit($limit)
            ->get();
        if (!$cache_val) {
            $cache_val = [];
        }
        $cache_val[$page_key] = [
            'total' => $total,
            'totalPage' => ceil($total / $limit),
            'list' => $list,
        ];

        Cache::forget($cache_key);
        Cache::add($cache_key, $cache_val, 1);
    }

    /**
     * 获取课程基本信息
     * @param int $room_id
     * @return mixed|null
     */
    public static function getRoomInfo($room_id)
    {
        $cache_key = self::$cache_keys['recorded.roominfo'];
        $cache_val = Cache::get($cache_key);
        if (!$cache_val || !array_key_exists($room_id, $cache_val)) {
            self::setRoomInfo($room_id);
        }
        $cache_val = Cache::get($cache_key);
        return $cache_val[$room_id];
    }

    /**
     * 设置课程基本信息
     * @param $room_id
     * @return mixed
     */
    public static function setRoomInfo($room_id)
    {
        $cache_key = self::$cache_keys['recorded.roominfo'];
        $cache_val = Cache::get($cache_key);
        $info = DB::table('yz_appletslive_room')
            ->select('id', 'type', 'roomid', 'name', 'anchor_name', 'cover_img', 'start_time', 'end_time', 'live_status', 'desc')
            ->where('id', $room_id)
            ->first();
        if (!$cache_val) {
            $cache_val = [];
        }
        $cache_val[$room_id] = $info;
        Cache::forget($cache_key);
        Cache::add($cache_key, $cache_val, 1);
    }

    /**
     * 获取课程订阅数、观看数、评论数
     * @param int $room_id
     * @return mixed|null
     */
    public static function getRoomNum($room_id = 0)
    {
        $cache_key = 'api_live_room_num';
        $cache_val = Cache::get($cache_key);

        if (!$cache_val) {
            self::setRoomNum($room_id);
            $cache_val = Cache::get($cache_key);
        }

        if (!$room_id) {
            return $cache_val;
        }

        if (is_array($room_id)) {
            $result = [];
            foreach ($room_id as $v) {
                $key = 'key_' . $v;
                if (!array_key_exists($key, $cache_val)) {
                    self::setRoomNum($room_id);
                    return self::getRoomNum($room_id);
                }
                $result[$key] = $cache_val[$key];
            }
            return $result;
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

    /**
     * 设置课程订阅数、观看数、评论数（自增1）
     * @param $room_id
     * @param $field subscription_num|view_num|comment_num
     * @param bool $is_dec
     */
    public static function setRoomNum($room_id, $field = null, $is_dec = false)
    {
        if (is_array($room_id)) {
            $record = DB::table('yz_appletslive_room')->whereIn('id', $room_id)->get();
        } else {
            if ($field !== null) {
                if ($is_dec) {
                    DB::table('yz_appletslive_room')->where('id', $room_id)->decrement($field);
                } else {
                    DB::table('yz_appletslive_room')->where('id', $room_id)->increment($field);
                }
            }
            $record = DB::table('yz_appletslive_room')->where('id', $room_id)->first();
        }

        $cache_key = 'api_live_room_num';
        $cache_val = Cache::get($cache_key);

        if (is_array($room_id)) {
            foreach ($record as $item) {
                $key = 'key_' . $item['id'];
                $val = [
                    'hot_num' => $item['subscription_num'] + $item['view_num'] + $item['comment_num'],
                    'subscription_num' => $item['subscription_num'],
                    'view_num' => $item['view_num'],
                    'comment_num' => $item['comment_num'],
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
                'hot_num' => $record['subscription_num'] + $record['view_num'] + $record['comment_num'],
                'subscription_num' => $record['subscription_num'],
                'view_num' => $record['view_num'],
                'comment_num' => $record['comment_num'],
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
     * 获取课程订阅相关信息
     * @param int $room_id
     * @return mixed|null
     */
    public static function getRoomSubscription($room_id = 0)
    {
        $cache_key = "api_live_room_subscription|$room_id";
        $cache_val = Cache::get($cache_key);
        if (!$cache_val) {
            self::setRoomSubscription($room_id);
            $cache_val = Cache::get($cache_key);
        }
        return $cache_val;
    }

    /**
     * 设置课程订阅相关信息
     * @param $room_id
     * @param $user_id
     * @return mixed
     */
    public static function setRoomSubscription($room_id, $user_id = 0)
    {
        $cache_key = "api_live_room_subscription|$room_id";
        $cache_val = Cache::get($cache_key);
        $cache_refresh = false;
        if (!$cache_val) {
            $cache_refresh = true;
            $cache_val = DB::table('yz_appletslive_room_subscription')
                ->where('uniacid', self::$uniacid)
                ->where('room_id', $room_id)
                ->where('status', 1) // 0 取消订阅 1 订阅 fixby-wk-20201005 订阅状态
                ->orderBy('id', 'desc')
                ->pluck('create_time', 'user_id')
                ->toArray();
        } else {
            if ($user_id && array_search($user_id, $cache_val) === false) {
                $cache_refresh = true;
                $cache_val[$user_id] = time();
            }
        }
        if ($cache_refresh) {
            if (empty($cache_val)) {
                Cache::forever($cache_key, ['total' => 0, 'list' => []]);
            } else {
                $user = DB::table('diagnostic_service_user')
                    ->whereIn('ajy_uid', array_keys($cache_val))
                    ->select('ajy_uid', 'nickname', 'avatarurl', 'province')
                    ->get()->toArray();
                foreach ($user as $k => $v) {
                    $user['ajy_uid_' . $v['ajy_uid']] = $v;
                    $user[$k] = null;
                }
                $user = array_filter($user);
                array_walk($cache_val, function (&$item, $key) use ($user) {
                    $item = ['user' => $user['ajy_uid_' . $key], 'create_time' => date('Y-m-d H:i:s', $item)];
                });
                Cache::forever($cache_key, $cache_val);
            }
        }
    }

    /**
     * 获取用户订阅的课程列表
     * @param $user_id
     * @return array
     */
    public static function getUserSubscription($user_id)
    {
        $cache_key = "api_live_user_subscription|$user_id";
        $cache_val = Cache::get($cache_key);
        if (!$cache_val) {
            self::setUserSubscription($user_id);
            $cache_val = Cache::get($cache_key);
        }
        return $cache_val;
    }

    /**
     * 设置用户订阅的课程列表
     * @param $user_id
     * @param $room_id
     */
    public static function setUserSubscription($user_id, $room_id = 0)
    {
        $cache_key = "api_live_user_subscription|$user_id";
        $cache_val = Cache::get($cache_key);
        $cache_refresh = false;
        if (!$cache_val) {
            $cache_refresh = true;
            $cache_val = DB::table('yz_appletslive_room_subscription')
                ->where('uniacid', self::$uniacid)
                ->where('user_id', $user_id)
                ->where('type', 1)
                ->where('status', 1) //0 取消订阅 1 订阅 fixby-wk-20201005 订阅状态
                ->pluck('room_id')->toArray();
        } else {
            if ($room_id && array_search($room_id, $cache_val) === false) {
                $cache_refresh = true;
                $cache_val[] = $room_id;
            }
        }
        if ($cache_refresh) {
            Cache::forever($cache_key, $cache_val);
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
        if (!$cache_val) {
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
        $comment = DB::table('yz_appletslive_room_comment')
            ->select('id', 'user_id', 'content', 'create_time', 'parent_id', 'is_reply')
            ->where('uniacid', self::$uniacid)
            ->where('room_id', $room_id)
            ->orderBy('id', 'desc')
            ->get()->toArray();
        if (empty($comment)) {
            Cache::forever($cache_key, ['total' => 0, 'list' => []]);
        } else {
            $user = DB::table('diagnostic_service_user')
                ->whereIn('ajy_uid', array_unique(array_column($comment, 'user_id')))
                ->select('ajy_uid', 'nickname', 'avatarurl', 'province')
                ->get()->toArray();
            foreach ($user as $k => $v) {
                $user['ajy_uid_' . $v['ajy_uid']] = $v;
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
            $comment = array_values(array_filter($comment));
            for ($i = 0; $i < count($comment); $i++) {
                $item = $comment[$i];
                $reply_for_this_comment = [];
                foreach ($reply as $k => $v) {
                    if ($v['parent_id'] == $item['id']) {
                        $temp = array_merge($v, ['user' => $user['ajy_uid_' . $v['user_id']]]);
                        $temp['create_time'] = date('Y-m-d H:i:s', $temp['create_time']);
                        unset($temp['user_id']);
                        unset($temp['parent_id']);
                        unset($temp['is_reply']);
                        array_push($reply_for_this_comment, $temp);
                        $reply[$k] = null;
                    }
                }
                $reply = array_filter($reply);
                $item['create_time'] = date('Y-m-d H:i:s', $item['create_time']);
                $item['user'] = $user['ajy_uid_' . $item['user_id']];
                $item['reply'] = ['total' => count($reply_for_this_comment), 'list' => $reply_for_this_comment];
                unset($item['user_id']);
                unset($item['parent_id']);
                unset($item['is_reply']);
                $comment[$i] = $item;
            }
            Cache::forever($cache_key, ['total' => count($comment), 'list' => $comment]);
        }
    }

    /**
     * 获取课程下属视频列表
     * @param int $room_id
     * @return mixed|null
     */
    public static function getRoomReplays($room_id)
    {
        $cache_key = self::$cache_keys['recorded.roomreplays'];
        $cache_val = Cache::get($cache_key);
        if (!$cache_val || !array_key_exists($room_id, $cache_val)) {
            self::setRoomReplays($room_id);
        }
        $cache_val = Cache::get($cache_key);
        return $cache_val[$room_id];
    }

    /**
     * 设置课程下属视频列表
     * @param $room_id
     * @return mixed
     */
    public static function setRoomReplays($room_id)
    {
        $cache_key = self::$cache_keys['recorded.roomreplays'];
        $cache_val = Cache::get($cache_key);
        $list = DB::table('yz_appletslive_replay')
            ->select('id', 'type', 'title', 'cover_img', 'publish_time', 'media_url', 'time_long')
            ->where('rid', $room_id)
            ->where('delete_time', 0)
            ->orderBy('sort', 'desc')
            ->orderBy('id', 'asc')
            ->get()->toArray();
        array_walk($list, function (&$item) {
            $item['publish_status'] = 1;
            if ($item['publish_time'] > time()) {
                $item['publish_status'] = 0;
                $item['media_url'] = '';
            }
            $item['minute'] = floor($item['time_long'] / 60);
            $item['second'] = $item['time_long'] % 60;
            $item['publish_time'] = date('Y-m-d H:i:s', $item['publish_time']);
        });
        if ($cache_val) {
            $cache_val = [];
        }
        $cache_val[$room_id] = ['total' => count($list), 'list' => $list];
        Cache::forget($cache_key);
        Cache::add($cache_key, $cache_val, 1);
    }

    /**
     * 获取视频信息
     * @param int $replay_id
     * @return mixed|null
     */
    public function getReplayInfo($replay_id)
    {
        $cache_key = self::$cache_keys['recorded.roomreplayinfo'];
        $cache_val = Cache::get($cache_key);
        if (!$cache_val || !array_key_exists($replay_id, $cache_val)) {
            self::setReplayInfo($replay_id);
        }
        $cache_val = Cache::get($cache_key);
        return $cache_val[$replay_id];
    }

    /**
     * 设置视频信息
     * @param int $replay_id
     * @return mixed|null
     */
    public function setReplayInfo($replay_id)
    {
        $cache_key = self::$cache_keys['recorded.roomreplayinfo'];
        $cache_val = Cache::get($cache_key);
        $info = DB::table('yz_appletslive_replay')
            ->select('id', 'rid', 'type', 'title', 'intro', 'cover_img', 'media_url', 'publish_time', 'time_long')
            ->where('id', $replay_id)
            ->first();
        $info['minute'] = floor($info['time_long'] / 60);
        $info['second'] = $info['time_long'] % 60;
        $info['publish_time'] = date('Y-m-d H:i:s', $info['publish_time']);
        if ($cache_val) {
            $cache_val = [];
        }
        $cache_val[$replay_id] = $info;
        Cache::forget($cache_key);
        Cache::add($cache_key, $cache_val, 1);
    }

    /**
     * 获取录播视频观看数、评论数
     * @param int $replay_id
     * @return mixed|null
     */
    public static function getReplayNum($replay_id = 0)
    {
        $cache_key = 'api_live_replay_num';
        $cache_val = Cache::get($cache_key);

        if (!$cache_val) {
            self::setReplayNum($replay_id);
            $cache_val = Cache::get($cache_key);
        }

        if (!$replay_id) {
            return $cache_val;
        }

        if (is_array($replay_id)) {
            $result = [];
            foreach ($replay_id as $v) {
                $key = 'key_' . $v;
                if (!array_key_exists($key, $cache_val)) {
                    self::setReplayNum($replay_id);
                    return self::getReplayNum($replay_id);
                }
                $result[$key] = $cache_val[$key];
            }
            return $result;
        } else {
            $key = 'key_' . $replay_id;
            if (array_key_exists($key, $cache_val)) {
                return $cache_val[$key];
            } else {
                self::setReplayNum($replay_id);
                return self::getReplayNum($replay_id);
            }
        }
    }

    /**
     * 设置录播视频观看数、评论数（自增1）
     * @param $replay_id
     * @param null $field
     * @param int $user_id
     * @param bool $is_dec
     */
    public static function setReplayNum($replay_id, $field = null, $user_id = 0, $is_dec = false)
    {
        $watch_num = 0;
        $num_table = 'yz_appletslive_replay';
        if (is_array($replay_id)) {
            $num_record = DB::table($num_table)->whereIn('id', $replay_id)->get();
            $watch_record = DB::table('yz_appletslive_replay_watch')
                ->select('replay_id', DB::raw('COUNT(user_id) as watch_num'))
                ->whereIn('replay_id', $replay_id)
                ->groupBy('replay_id')
                ->get()->toArray();
        } else {
            $watch_table = 'yz_appletslive_replay_watch';
            if ($field !== null) {
                if ($field == 'watch_num') {
                    if (!DB::table($watch_table)->where('replay_id', $replay_id)->where('user_id', $user_id)->first()) {
                        DB::table($watch_table)->insert([
                            'uniacid' => self::$uniacid,
                            'replay_id' => $replay_id,
                            'user_id' => $user_id,
                            'create_time' => time(),
                        ]);
                    }
                } else {
                    if ($is_dec) {
                        DB::table($num_table)->where('id', $replay_id)->decrement($field);
                    } else {
                        DB::table($num_table)->where('id', $replay_id)->increment($field);
                    }
                }
            }
            $num_record = DB::table($num_table)->where('id', $replay_id)->first();
            $watch_num = DB::table($watch_table)->where('replay_id', $replay_id)->count();
        }

        $cache_key = 'api_live_replay_num';
        $cache_val = Cache::get($cache_key);

        if (is_array($replay_id)) {
            foreach ($num_record as $item) {
                $key = 'key_' . $item['id'];
                $val = [
                    'hot_num' => $item['view_num'] + $item['comment_num'],
                    'view_num' => $item['view_num'],
                    'comment_num' => $item['comment_num'],
                    'watch_num' => 0,
                ];
                foreach ($watch_record as $wrv) {
                    if ($wrv['replay_id'] == $item['id']) {
                        $val['watch_num'] = $wrv['watch_num'];
                        break;
                    }
                }
                if (!$cache_val) {
                    $cache_val = [$key => $val];
                } else {
                    $cache_val[$key] = $val;
                }
            }
            Cache::forever($cache_key, $cache_val);
        } else {
            $key = 'key_' . $replay_id;
            $val = [
                'hot_num' => $num_record['view_num'] + $num_record['comment_num'],
                'view_num' => $num_record['view_num'],
                'comment_num' => $num_record['comment_num'],
                'watch_num' => $watch_num,
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
     * 获取用户看过的视频列表
     * @param $user_id
     * @return array
     */
    public static function getUserWatch($user_id)
    {
        $cache_key = "api_live_user_watch|$user_id";
        $cache_val = Cache::get($cache_key);
        if (!$cache_val) {
            self::setUserWatch($user_id);
            $cache_val = Cache::get($cache_key);
        }
        return $cache_val;
    }

    /**
     * 设置用户看过的视频列表
     * @param $user_id
     * @param $replay_id
     */
    public static function setUserWatch($user_id, $replay_id = 0)
    {
        $cache_key = "api_live_user_watch|$user_id";
        $cache_val = Cache::get($cache_key);
        $cache_refresh = false;
        if (!$cache_val) {
            $cache_refresh = true;
            $cache_val = DB::table('yz_appletslive_replay_watch')
                ->where('uniacid', self::$uniacid)
                ->where('user_id', $user_id)
                ->pluck('replay_id')->toArray();
        } else {
            if ($replay_id && array_search($replay_id, $cache_val) === false) {
                $cache_refresh = true;
                $cache_val[] = $replay_id;
            }
        }
        if ($cache_refresh) {
            Cache::forever($cache_key, $cache_val);
        }
    }

    /**
     * 获取录播视频评论相关信息
     * @param int $replay_id
     * @return mixed|null
     */
    public static function getReplayComment($replay_id)
    {
        $cache_key = "api_live_replay_comment|$replay_id";
        $cache_val = Cache::get($cache_key);
        if (!$cache_val) {
            self::setReplayComment($replay_id);
            $cache_val = Cache::get($cache_key);
        }
        return $cache_val;
    }

    /**
     * 设置录播视频评论相关信息
     * @param $replay_id
     * @return mixed
     */
    public static function setReplayComment($replay_id)
    {
        $cache_key = "api_live_replay_comment|$replay_id";
        $comment = DB::table('yz_appletslive_replay_comment')
            ->select('id', 'user_id', 'content', 'create_time', 'parent_id', 'is_reply')
            ->where('uniacid', self::$uniacid)
            ->where('replay_id', $replay_id)
            ->orderBy('id', 'desc')
            ->get()->toArray();
        if (empty($comment)) {
            Cache::forever($cache_key, ['total' => 0, 'list' => []]);
        } else {
            $user = DB::table('diagnostic_service_user')
                ->whereIn('ajy_uid', array_unique(array_column($comment, 'user_id')))
                ->select('ajy_uid', 'nickname', 'avatarurl', 'province')
                ->get()->toArray();
            foreach ($user as $k => $v) {
                $user['ajy_uid_' . $v['ajy_uid']] = $v;
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
            $comment = array_values(array_filter($comment));
            for ($i = 0; $i < count($comment); $i++) {
                $item = $comment[$i];
                $reply_for_this_comment = [];
                foreach ($reply as $k => $v) {
                    if ($v['parent_id'] == $item['id']) {
                        $temp = array_merge($v, ['user' => $user['ajy_uid_' . $v['user_id']]]);
                        $temp['create_time'] = date('Y-m-d H:i:s', $temp['create_time']);
                        unset($temp['user_id']);
                        unset($temp['parent_id']);
                        unset($temp['is_reply']);
                        array_push($reply_for_this_comment, $temp);
                        $reply[$k] = null;
                    }
                }
                $reply = array_filter($reply);
                $item['create_time'] = date('Y-m-d H:i:s', $item['create_time']);
                $item['user'] = $user['ajy_uid_' . $item['user_id']];
                $item['reply'] = ['total' => count($reply_for_this_comment), 'list' => $reply_for_this_comment];
                unset($item['user_id']);
                unset($item['parent_id']);
                unset($item['is_reply']);
                $comment[$i] = $item;
            }
            Cache::forever($cache_key, ['total' => count($comment), 'list' => $comment]);
        }
    }

    /************************ 课程/录播 相关缓存处理 END ************************/


    /************************ 课程/品牌特卖 相关缓存处理 BEGIN ************************/

    /**
     * 获取品牌特卖专辑列表缓存数据
     * @param int $room_id
     * @return mixed|null
     */
    public static function getBrandSaleAlbumList($page, $limit = 10)
    {
        $page_key = "$limit|$page";
        $cache_key = self::$cache_keys['brandsale.albumlist'];
        // Cache::forget($cache_key);
        $cache_val = Cache::get($cache_key);
        if (!$cache_val || !array_key_exists($page_key, $cache_val)) {
            self::setBrandSaleAlbumList($page, $limit);
            $cache_val = Cache::get($cache_key);
        }
        return $cache_val[$page_key];
    }

    /**
     * 设置品牌特卖专辑列表缓存数据
     *
     * @param $page
     * @param $limit
     */
    public static function setBrandSaleAlbumList($page, $limit)
    {
        $page_key = "$limit|$page";
        $cache_key = self::$cache_keys['brandsale.albumlist'];
        $cache_val = Cache::get($cache_key);

        $offset = ($page - 1) * $limit;
        $total = DB::table('yz_appletslive_room')
            ->where('type', 2)
            ->where('delete_time', 0)
            ->count();
        $list = DB::table('yz_appletslive_room')
            ->select('id', 'name', 'cover_img', 'subscription_num', 'view_num', 'comment_num')
            ->where('type', 2)
            ->where('delete_time', 0)
            ->orderBy('sort', 'desc')
            ->orderBy('id', 'asc')
            ->offset($offset)
            ->limit($limit)
            ->get()->toArray();
        if (!empty($list)) {
            foreach ($list as $k => $v) {
                $list[$k] = self::getBrandSaleAlbumInfo($v['id']);
            }
        }
        if (!$cache_val) {
            $cache_val = [];
        }
        $cache_val[$page_key] = [
            'total' => $total,
            'totalPage' => ceil($total / $limit),
            'list' => $list,
        ];
        Cache::forget($cache_key);
        Cache::add($cache_key, $cache_val, 1);
    }

    /**
     * 获取牌特卖专辑基本信息
     * @param int $album_id
     * @return mixed|null
     */
    public static function getBrandSaleAlbumInfo($album_id)
    {
        $cache_key = self::$cache_keys['brandsale.albuminfo'];
        $cache_val = Cache::get($cache_key);
        // Cache::forget($cache_key);
        if (!$cache_val || ($album_id && !array_key_exists($album_id, $cache_val))) {
            self::setBrandSaleAlbumInfo($album_id);
            $cache_val = Cache::get($cache_key);
        }
        return $cache_val[$album_id];
    }

    /**
     * 设置牌特卖专辑基本信息
     * @param $album_id
     * @return mixed
     */
    public static function setBrandSaleAlbumInfo($album_id)
    {
        $cache_key = self::$cache_keys['brandsale.albuminfo'];
        $cache_val = Cache::get($cache_key);
        $info = DB::table('yz_appletslive_room')
            ->select('id', 'name', 'desc', 'cover_img', 'subscription_num', 'view_num', 'comment_num')
            ->where('id', $album_id)
            ->first();
        $liverooms = self::getBrandSaleAlbumLiveRooms($album_id);
        $has_live_status_101 = false;
        $room_live_room_101 = null;
        $has_live_status_102 = false;
        $room_live_room_102 = null;
        foreach ($liverooms['list'] as $room) {
            if ($room['live_status'] == 101) {
                $has_live_status_101 = true;
                $room_live_room_101 = $room;
            }
            if ($room['live_status'] == 102) {
                $has_live_status_102 = true;
                if (empty($room_live_room_102) ||
                    (!empty($room_live_room_102) && $room_live_room_102['start_time'] > $room['start_time'])) {
                    $room_live_room_102 = $room;
                }
            }
        }
        if (!$has_live_status_101 && !$has_live_status_102) {
            $info['live_status'] = 103;
        }
        if ($has_live_status_101) {
            $info['live_status'] = 101;
            $info['start_time'] = $room_live_room_101['start_time'];
            $info['end_time'] = $room_live_room_101['end_time'];
        } else {
            if ($has_live_status_102) {
                $info['live_status'] = 102;
                $info['start_time'] = $room_live_room_102['start_time'];
                $info['end_time'] = $room_live_room_102['end_time'];
            }
        }
        if (!$cache_val) {
            $cache_val = [];
        }
        $cache_val[$album_id] = $info;
        Cache::forget($cache_key);
        Cache::add($cache_key, $cache_val, 1);
    }

    /**
     * 获取品牌特卖专辑下属直播间
     * @param int $album_id
     * @return mixed|null
     */
    public static function getBrandSaleAlbumLiveRooms($album_id)
    {
        $cache_key = self::$cache_keys['brandsale.albumliverooms'];
        $cache_val = Cache::get($cache_key);
        // Cache::forget($cache_key);
        if (!$cache_val || !array_key_exists($album_id, $cache_val)) {
            self::setBrandSaleAlbumLiveRooms($album_id);
            $cache_val = Cache::get($cache_key);
        }
        return $cache_val[$album_id];
    }

    /**
     * 设置品牌特卖专辑下属直播间
     * @param $album_id
     */
    public static function setBrandSaleAlbumLiveRooms($album_id)
    {
        $cache_key = self::$cache_keys['brandsale.albumliverooms'];
        $cache_val = Cache::get($cache_key);
        $replay_list = DB::table('yz_appletslive_replay')
            ->join('yz_appletslive_liveroom', 'yz_appletslive_replay.room_id', '=', 'yz_appletslive_liveroom.id')
            ->select('yz_appletslive_replay.id', 'yz_appletslive_replay.room_id', 'yz_appletslive_replay.view_num')
            ->where('yz_appletslive_replay.rid', $album_id)
            ->where('yz_appletslive_replay.delete_time', 0)
            ->whereIn('yz_appletslive_liveroom.live_status', [101, 102, 103, 105, 107])
            ->orderBy('yz_appletslive_liveroom.start_time', 'desc')
            ->orderBy('yz_appletslive_replay.id', 'desc')
            ->get()->toArray();
        if (!empty($replay_list)) {
            $liverooms = DB::table('yz_appletslive_liveroom')
                ->whereIn('id', array_column($replay_list, 'room_id'))
                ->get()->toArray();
            foreach ($replay_list as $rk => $rv) {
                foreach ($liverooms as $lrk => $lrv) {
                    if ($rv['room_id'] == $lrv['id']) {
                        unset($replay_list[$rk]['room_id']);
                        $replay_list[$rk]['name'] = $lrv['name'];
                        $replay_list[$rk]['roomid'] = $lrv['roomid'];
                        $replay_list[$rk]['cover_img'] = $lrv['cover_img'];
                        $replay_list[$rk]['start_time'] = $lrv['start_time'];
                        $replay_list[$rk]['end_time'] = $lrv['end_time'];
                        if (($lrv['start_time'] < time() && $lrv['end_time'] > time())
                            || ($lrv['live_status'] == 101 || $lrv['live_status'] == 105)) {
                            $replay_list[$rk]['live_status'] = 101;
                        }
                        if ($lrv['start_time'] > time() || $lrv['live_status'] == 102) {
                            $replay_list[$rk]['live_status'] = 102;
                        }
                        if ($lrv['end_time'] < time() || $lrv['live_status'] == 103) {
                            $replay_list[$rk]['live_status'] = 103;
                        }
                        $replay_list[$rk]['start_time'] = date('Y-m-d H:i:s', $lrv['start_time']);
                        $replay_list[$rk]['end_time'] = date('Y-m-d H:i:s', $lrv['end_time']);
                        $replay_list[$rk]['anchor_name'] = $lrv['anchor_name'];
                    }
                }
            }
        }
        if (!$cache_val) {
            $cache_val = [];
        }
        $cache_val[$album_id] = ['total' => count($replay_list), 'list' => $replay_list];
        Cache::forget($cache_key);
        Cache::add($cache_key, $cache_val, 1);
    }

    /**
     * 获取品牌特卖专辑订阅数、观看数、评论数
     * @param int $album_id
     * @return mixed|null
     */
    public static function getBrandSaleAlbumNum($album_id = 0)
    {
        $cache_key = self::$cache_keys['brandsale.albumnum'];
        $cache_val = Cache::get($cache_key);
        Cache::forget($cache_key);

        if (!$cache_val) {
            self::setBrandSaleAlbumNum($album_id);
            $cache_val = Cache::get($cache_key);
        }

        if (!$album_id) {
            return $cache_val;
        }

        if (is_array($album_id)) {
            $result = [];
            foreach ($album_id as $v) {
                $key = 'key_' . $v;
                if (!array_key_exists($key, $cache_val)) {
                    self::setBrandSaleAlbumNum($album_id);
                    return self::getBrandSaleAlbumNum($album_id);
                }
                $result[$key] = $cache_val[$key];
            }
            return $result;
        } else {
            $key = 'key_' . $album_id;
            if (array_key_exists($key, $cache_val)) {
                return $cache_val[$key];
            } else {
                self::setBrandSaleAlbumNum($album_id);
                return self::getBrandSaleAlbumNum($album_id);
            }
        }
    }

    /**
     * 设置品牌特卖专辑订阅数、观看数、评论数（自增1）
     * @param $album_id
     * @param $field subscription_num|view_num|comment_num
     * @param bool $is_dec
     */
    public static function setBrandSaleAlbumNum($album_id, $field = null, $is_dec = false)
    {
        if (is_array($album_id)) {
            $record = DB::table('yz_appletslive_room')->whereIn('id', $album_id)->get();
        } else {
            if ($field !== null) {
                if ($is_dec) {
                    DB::table('yz_appletslive_room')->where('id', $album_id)->decrement($field);
                } else {
                    DB::table('yz_appletslive_room')->where('id', $album_id)->increment($field);
                }
            }
            $record = DB::table('yz_appletslive_room')->where('id', $album_id)->first();
        }

        $cache_key = self::$cache_keys['brandsale.albumnum'];
        $cache_val = Cache::get($cache_key);

        if (is_array($album_id)) {
            foreach ($record as $item) {
                $key = 'key_' . $item['id'];
                $val = [
                    'hot_num' => $item['subscription_num'] + $item['view_num'] + $item['comment_num'],
                    'subscription_num' => $item['subscription_num'],
                    'view_num' => $item['view_num'],
                    'comment_num' => $item['comment_num'],
                ];
                if (!$cache_val) {
                    $cache_val = [$key => $val];
                } else {
                    $cache_val[$key] = $val;
                }
            }
        } else {
            $key = 'key_' . $album_id;
            $val = [
                'hot_num' => $record['subscription_num'] + $record['view_num'] + $record['comment_num'],
                'subscription_num' => $record['subscription_num'],
                'view_num' => $record['view_num'],
                'comment_num' => $record['comment_num'],
            ];
            if (!$cache_val) {
                $cache_val = [$key => $val];
            } else {
                $cache_val[$key] = $val;
            }
        }

        Cache::forget($cache_key);
        Cache::add($cache_key, $cache_val, 10);
    }

    /**
     * 获取品牌特卖专辑订阅相关信息
     * @param int $album_id
     * @return mixed|null
     */
    public static function getBrandSaleAlbumSubscription($album_id = 0)
    {
        $cache_key = self::$cache_keys['brandsale.albumsubscription'];
        $cache_val = Cache::get($cache_key);
        Cache::forget($cache_key);
        if (!$cache_val || ($album_id && !array_key_exists($album_id, $cache_val))) {
            self::setBrandSaleAlbumSubscription($album_id);
            $cache_val = Cache::get($cache_key);
        }
        return $album_id ? $cache_val[$album_id] : $cache_val;
    }

    /**
     * 设置品牌特卖专辑订阅相关信息
     * @param $album_id
     * @param $user_id
     * @return mixed
     */
    public static function setBrandSaleAlbumSubscription($album_id, $user_id = 0)
    {
        $cache_key = self::$cache_keys['brandsale.albumsubscription'];
        $cache_val = Cache::get($cache_key);
        $cache_refresh = false;
        if (!$cache_val || !array_key_exists($album_id, $cache_val)) {
            if (empty($cache_val)) {
                $cache_val = [];
            }
            $list = DB::table('yz_appletslive_room_subscription')
                ->where('uniacid', self::$uniacid)
                ->where('room_id', $album_id)
                ->where('status', 1) //0 取消订阅 1 订阅 fixby-wk-20201005 订阅状态
                ->orderBy('id', 'desc')
                ->pluck('create_time', 'user_id')
                ->toArray();
            $cache_val[$album_id] = $list;
            $cache_refresh = true;
        } else {
            if ($user_id && array_search($user_id, $cache_val[$album_id]) === false) {
                $cache_val[$album_id][$user_id] = time();
                $cache_refresh = true;
            }
        }
        if ($cache_refresh) {
            if (empty($cache_val[$album_id])) {
                $cache_val[$album_id] = ['total' => 0, 'list' => []];
            } else {
                $user = DB::table('diagnostic_service_user')
                    ->whereIn('ajy_uid', array_keys($cache_val[$album_id]))
                    ->select('ajy_uid', 'nickname', 'avatarurl', 'province')
                    ->get()->toArray();
                foreach ($user as $k => $v) {
                    $user['ajy_uid_' . $v['ajy_uid']] = $v;
                    $user[$k] = null;
                }
                $user = array_filter($user);
                array_walk($cache_val[$album_id], function (&$item, $key) use ($user) {
                    $item = ['user' => $user['ajy_uid_' . $key], 'create_time' => date('Y-m-d H:i:s', $item)];
                });
            }
            Cache::forget($cache_key);
            Cache::add($cache_key, $cache_val, 10);
        }
    }

    /**
     * 获取用户订阅的品牌特卖专辑列表
     * @param $user_id
     * @return array
     */
    public static function getUserBrandSaleAlbumSubscription($user_id)
    {
        $cache_key = self::$cache_keys['brandsale.albumusersubscription'];
        $cache_val = Cache::get($cache_key);
        Cache::forget($cache_key);
        if (!$cache_val || ($user_id && !array_key_exists($user_id, $cache_val))) {
            self::setUserBrandSaleAlbumSubscription($user_id);
            $cache_val = Cache::get($cache_key);
        }
        return $cache_val[$user_id];
    }

    /**
     * 设置用户订阅的品牌特卖专辑
     * @param $user_id
     * @param $album_id
     */
    public static function setUserBrandSaleAlbumSubscription($user_id, $album_id = 0)
    {
        $cache_key = self::$cache_keys['brandsale.albumusersubscription'];
        $cache_val = Cache::get($cache_key);
        $cache_refresh = false;
        if (!$cache_val || !array_key_exists($user_id, $cache_val)) {
            if (empty($cache_val)) {
                $cache_val = [];
            }
            $list = DB::table('yz_appletslive_room_subscription')
                ->where('uniacid', self::$uniacid)
                ->where('user_id', $user_id)
                ->where('type', 2)
                ->where('status', 1) // 0 取消订阅 1 订阅 fixby-wk-20201005 订阅状态
                ->pluck('room_id')
                ->toArray();
            $cache_val[$user_id] = $list;
            $cache_refresh = true;
        } else {
            if ($album_id && array_search($album_id, $cache_val[$user_id]) === false) {
                $cache_val[$user_id][] = $album_id;
                $cache_refresh = true;
            }
        }
        if ($cache_refresh) {
            Cache::forget($cache_key);
            Cache::add($cache_key, $cache_val, 10);
        }
    }

    /**
     * 获取课程评论相关信息
     * @param int $album_id
     * @return mixed|null
     */
    public static function getBrandSaleAlbumComment($album_id)
    {
        $cache_key = self::$cache_keys['brandsale.albumcomment'];
        $cache_val = Cache::get($cache_key);
        Cache::forget($cache_key);
        if (!$cache_val || ($album_id && !array_key_exists($album_id, $cache_val))) {
            self::setBrandSaleAlbumComment($album_id);
            $cache_val = Cache::get($cache_key);
        }
        return $cache_val[$album_id];
    }

    /**
     * 设置课程评论相关信息
     * @param $album_id
     * @return mixed
     */
    public static function setBrandSaleAlbumComment($album_id)
    {
        $cache_key = self::$cache_keys['brandsale.albumcomment'];
        $cache_val = Cache::get($cache_key);

        $comment = DB::table('yz_appletslive_room_comment')
            ->select('id', 'user_id', 'content', 'create_time', 'parent_id', 'is_reply')
            ->where('uniacid', self::$uniacid)
            ->where('room_id', $album_id)
            ->orderBy('id', 'desc')
            ->get()->toArray();
        if (empty($comment)) {
            $cache_item = ['total' => 0, 'list' => []];
        } else {
            $user = DB::table('diagnostic_service_user')
                ->whereIn('ajy_uid', array_unique(array_column($comment, 'user_id')))
                ->select('ajy_uid', 'nickname', 'avatarurl', 'province')
                ->get()->toArray();
            foreach ($user as $k => $v) {
                $user['ajy_uid_' . $v['ajy_uid']] = $v;
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
            $comment = array_values(array_filter($comment));
            for ($i = 0; $i < count($comment); $i++) {
                $item = $comment[$i];
                $reply_for_this_comment = [];
                foreach ($reply as $k => $v) {
                    if ($v['parent_id'] == $item['id']) {
                        $temp = array_merge($v, ['user' => $user['ajy_uid_' . $v['user_id']]]);
                        $temp['create_time'] = date('Y-m-d H:i:s', $temp['create_time']);
                        unset($temp['user_id']);
                        unset($temp['parent_id']);
                        unset($temp['is_reply']);
                        array_push($reply_for_this_comment, $temp);
                        $reply[$k] = null;
                    }
                }
                $reply = array_filter($reply);
                $item['create_time'] = date('Y-m-d H:i:s', $item['create_time']);
                $item['user'] = $user['ajy_uid_' . $item['user_id']];
                $item['reply'] = ['total' => count($reply_for_this_comment), 'list' => $reply_for_this_comment];
                unset($item['user_id']);
                unset($item['parent_id']);
                unset($item['is_reply']);
                $comment[$i] = $item;
            }
            $cache_item = ['total' => count($comment), 'list' => $comment];
        }

        if (!$cache_val) {
            $cache_val = [];
        }
        $cache_val[$album_id] = $cache_item;

        Cache::forget($cache_key);
        Cache::forever($cache_key, $cache_val);
    }

    /**
     * 获取品牌特卖直播间浏览量、观看数
     * @param int $live_room_id
     * @return mixed|null
     */
    public static function getBrandSaleLiveRoomNum($live_room_id = 0)
    {
        $cache_key = self::$cache_keys['brandsale.albumliveroomnum'];
        $cache_val = Cache::get($cache_key);
        Cache::forget($cache_key);

        if (!$cache_val) {
            self::setBrandSaleLiveRoomNum($live_room_id);
            $cache_val = Cache::get($cache_key);
        }

        if (!$live_room_id) {
            return $cache_val;
        }

        if (is_array($live_room_id)) {
            $result = [];
            foreach ($live_room_id as $v) {
                $key = 'key_' . $v;
                if (!array_key_exists($key, $cache_val)) {
                    self::setBrandSaleLiveRoomNum($live_room_id);
                    return self::getBrandSaleLiveRoomNum($live_room_id);
                }
                $result[$key] = $cache_val[$key];
            }
            return $result;
        } else {
            $key = 'key_' . $live_room_id;
            if (array_key_exists($key, $cache_val)) {
                return $cache_val[$key];
            } else {
                self::setBrandSaleLiveRoomNum($live_room_id);
                return self::getBrandSaleLiveRoomNum($live_room_id);
            }
        }
    }

    /**
     * 设置品牌特卖直播间浏览量
     * @param int $live_room_id
     * @param string $field view_num|watch_num
     * @param int $user_id
     */
    public static function setBrandSaleLiveRoomNum($live_room_id, $field = null)
    {
        $num_table = 'yz_appletslive_replay';
        if (is_array($live_room_id)) {
            $num_record = DB::table($num_table)->whereIn('id', $live_room_id)->get();
        } else {
            if ($field !== null) {
                DB::table($num_table)->where('id', $live_room_id)->increment($field);
            }
            $num_record = DB::table($num_table)->where('id', $live_room_id)->first();
        }

        $cache_key = self::$cache_keys['brandsale.albumliveroomnum'];
        $cache_val = Cache::get($cache_key);

        if (is_array($live_room_id)) {
            foreach ($num_record as $item) {
                $key = 'key_' . $item['id'];
                $val = [
                    'view_num' => $item['view_num'],
                ];
                if (!$cache_val) {
                    $cache_val = [];
                }
                $cache_val[$key] = $val;
            }
        } else {
            $key = 'key_' . $live_room_id;
            $val = [
                'view_num' => $num_record['view_num'],
            ];
            if (!$cache_val) {
                $cache_val = [];
            }
            $cache_val[$key] = $val;
        }

        Cache::forget($cache_key);
        Cache::add($cache_key, $cache_val, 10);
    }

    /**
     * 获取用户看过的品牌特卖直播间列表
     * @param $user_id
     * @return array
     */
    public static function getUserLiveRoomWatch($user_id)
    {
        $cache_key = self::$cache_keys['brandsale.albumliveroomnum'];
        $cache_val = Cache::get($cache_key);
        Cache::forget($cache_key);
        if (!$cache_val || ($user_id && !array_key_exists($user_id, $cache_val))) {
            self::setUserLiveRoomWatch($user_id);
            $cache_val = Cache::get($cache_key);
        }
        return $cache_val[$user_id];
    }

    /**
     * 设置用户看过的品牌特卖直播间列表
     * @param $user_id
     * @param $live_room_id
     */
    public static function setUserLiveRoomWatch($user_id, $live_room_id = 0)
    {
        $cache_key = self::$cache_keys['brandsale.albumliveroomnum'];
        $cache_val = Cache::get($cache_key);
        if (!$cache_val) {
            $cache_refresh = true;
            $cache_val = DB::table('yz_appletslive_replay_watch')
                ->where('uniacid', self::$uniacid)
                ->where('user_id', $user_id)
                ->where('type', 2)
                ->pluck('replay_id')
                ->toArray();
        } else {
            if ($live_room_id && array_search($live_room_id, $cache_val) === false) {
                $cache_refresh = true;
                $cache_val[] = $live_room_id;
            }
        }
        if ($cache_refresh) {
            Cache::forget($cache_key);
            Cache::forever($cache_key, $cache_val);
        }
    }

    /************************ 课程/品牌特卖 相关缓存处理 END ************************/
}
