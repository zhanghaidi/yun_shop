<?php

namespace app\Console\Commands;

use Illuminate\Console\Command;
use Yunshop\Appletslive\common\services\BaseService;
use Yunshop\Appletslive\common\services\CacheService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SyncWxappLiveRoom extends Command
{

    protected $signature = 'command:syncwxappliveroom';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步微信小程序直播间';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        Log::getMonolog()->popHandler();
        Log::useFiles(storage_path('logs/schedule.run.log'), 'info');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $room_from_weixin = (new BaseService())->getRooms();
        $present = $room_from_weixin['room_info'];

        $need_update = false;
        $cache_key = 'appletslive_liverooms';
        $cache_val = Cache::get($cache_key);

        if (!empty($cache_val)) {
            // 对比缓存数据和最新数据是否存在不同
            foreach ($present as $psk => $psv) {
                $exist = false;
                foreach ($cache_val as $cvk => $cvv) {
                    $exist = true;
                    if ($cvv['roomid'] == $psv['roomid']) {
                        if ($cvv['name'] != $psv['name'] || $cvv['anchor_name'] != $psv['anchor_name']
                            || $cvv['live_status'] != $psv['live_status'] || $cvv['start_time'] != $psv['start_time']) {
                            $need_update = true;
                            break;
                        }
                        break;
                    }
                }
                if (!$exist) {
                    $need_update = true;
                    break;
                }
            }

            // 扫描是否有已删除的直播间
            foreach ($cache_val as $cvk => $cvv) {
                $match = false;
                foreach ($present as $psv) {
                    if ($cvv['roomid'] == $psv['roomid']) {
                        $match = true;
                        break;
                    }
                }
                if (!$match) {
                    $need_update = true;
                    break;
                }
            }
        } else {
            $need_update = true;
        }

        Cache::forget($cache_key);
        Cache::add($cache_key, $present, 1);

        if ($need_update) {

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
    }
}
