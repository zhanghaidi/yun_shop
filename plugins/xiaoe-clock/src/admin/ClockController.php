<?php

namespace Yunshop\XiaoeClock\admin;

use app\common\components\BaseController;
use app\common\exceptions\AppException;
use app\common\facades\Setting;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Yunshop\Appletslive\common\models\Room;
use Yunshop\Appletslive\common\models\RoomComment;
use Yunshop\Appletslive\common\services\CacheService;

/**
 * 打卡任务管理控制器
 */
class ClockController extends BaseController
{

    //增加打卡活动
    public function clock_index()
    {
        $type = request()->get('type', 1);
        if (!in_array($type, [1, 2])) {
            throw new AppException('房间类型有误');
        }

        $input = \YunShop::request();
        $limit = 20;

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

        return view('Yunshop\XiaoeClock::admin.clock_index', [
            'type' => $type,
            'room_list' => $list,
            'pager' => $pager,
            'request' => $input,
        ])->render();
    }
//增加打卡活动
    public function clock_add()
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

            return $this->message('保存成功', Url::absoluteWeb('plugin.xiaoe-clock.admin.controllers.room.index', ['type' => $param['type']]));
        }

        $type = request()->get('type', 0);
        if (!$type) {
            return $this->message('无效的类型', Url::absoluteWeb(''), 'danger');
        }
        return view('Yunshop\XiaoeClock::admin.clock_add', ['type' => $type])->render();
    }

//增加打卡活动任务
    public function addClockTask()
    {

    }

//编辑打卡活动
    public function editClock()

    {

    }

//编辑打卡活动
    public function editClockTask()
    {

    }
}