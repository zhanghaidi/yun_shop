<?php

namespace Yunshop\XiaoeClock\admin;

use app\backend\modules\goods\models\Goods;
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
            if (array_key_exists('name', $param)) { // 打卡名称
                $ist_data['name'] = $param['name'] ? trim($param['name']) : '';
            }
            if (DB::table('yz_xiaoe_clock')->where('name', $ist_data['name'])->first()) {
                return $this->message('打卡名称已存在', Url::absoluteWeb(''), 'danger');
            }
            if (array_key_exists('cover_img', $param)) { // 打卡封面
                $ist_data['cover_img'] = $param['cover_img'] ? $param['cover_img'] : '';
            }
            if (array_key_exists('text_desc', $param)) { // 打卡图文介绍
                $ist_data['text_desc'] = $param['text_desc'] ? $param['text_desc'] : '';
            }
            if (array_key_exists('video_desc', $param)) { // 打卡视频介绍
                $ist_data['video_desc'] = $param['video_desc'] ? $param['video_desc'] : '';
            }
            if (array_key_exists('audio_desc', $param)) { // 打卡音频介绍
                $ist_data['audio_desc'] = $param['audio_desc'] ? $param['audio_desc'] : '';
            }
            if (array_key_exists('join_type', $param)) { // 打卡方式
                $ist_data['join_type'] = $param['join_type'] ? $param['join_type'] : 0;
            }
            if (array_key_exists('course_id', $param)) { // 管理课程id
                $ist_data['course_id'] = $param['course_id'] ? $param['course_id'] : 0;
            }
            if (array_key_exists('start_time', $param)) { //有效期 开始是日期
                $start_time = $param['start_time'] ? $param['start_time'] : 0;
                if($start_time != 0){
                    $ist_data['start_time'] = strtotime($start_time);
                } else {
                    return $this->message('请选择开始日期', Url::absoluteWeb(''), 'danger');
                }
            }
            if (array_key_exists('end_time', $param)) { //有效期 结束是日期
                $end_time = $param['end_time'] ? $param['end_time'] : 0;
                if($end_time != 0){
                    $ist_data['end_time'] = strtotime($end_time);
                } else {
                    return $this->message('请选择结束日期', Url::absoluteWeb(''), 'danger');
                }
            }

            if (array_key_exists('text_length', $param)) { //图文长度
                $ist_data['text_length'] = $param['text_length'] ? $param['text_length'] : 5;
            }
            if (array_key_exists('image_length', $param)) { //音频长度
                $ist_data['image_length'] = $param['image_length'] ? $param['image_length'] : 0;
            }
            if (array_key_exists('video_length', $param)) { //视频长度
                $ist_data['video_length'] = $param['video_length'] ? $param['video_length'] : 0;
            }
            if (array_key_exists('display_status', $param)) { //显示状态
                $ist_data['display_status'] = $param['display_status'] ? $param['display_status'] : 1;
            }
            if (array_key_exists('helper_nickname', $param)) { // 助手昵称
                $ist_data['helper_nickname'] = $param['helper_nickname'] ? $param['helper_nickname'] : '';
            }
            if (array_key_exists('helper_avatar', $param)) { //助手头像
                $ist_data['helper_avatar'] = $param['helper_avatar'] ? $param['helper_avatar'] : '';
            }
            if (array_key_exists('helper_wechat', $param)) { //助手微信
                $ist_data['helper_wechat'] = $param['helper_wechat'] ? $param['helper_wechat'] : '';
            }
            if ($param['type'] == 1) {//日历打卡
                if (array_key_exists('valid_time_start', $param)) { //有效时段
                    $ist_data['valid_time_start'] = $param['valid_time_start'] ? $param['valid_time_start'] : 0;
                }
                if (array_key_exists('valid_time_end', $param)) { // 有效时段
                    $ist_data['valid_time_end'] = $param['valid_time_end'] ? $param['valid_time_end'] : 0;
                }
            }
            if ($param['type'] == 2) { //作业打卡
                if (array_key_exists('is_cheat_mode', $param)) { //防作弊
                    $ist_data['is_cheat_mode'] = $param['is_cheat_mode'] ? $param['is_cheat_mode'] : 0;
                }
                if (array_key_exists('is_resubmit', $param)) { //删除原来，重复提交新的 是否允许重新打卡
                    $ist_data['is_resubmit'] = $param['is_resubmit'] ? $param['is_resubmit'] : 0;
                }
            }

            DB::beginTransaction();//开启事务
            $insert_res =DB::table('yz_xiaoe_clock')->insert($ist_data);
            if (!$insert_res) {
                DB::rollBack();//事务回滚
                return $this->message('创建失败', Url::absoluteWeb('plugin.xiaoe-clock.admin.clock.clock_index', ['type' => $param['type']]));
            }
            DB::commit();//事务提交
            return $this->message('创建成功', Url::absoluteWeb('plugin.xiaoe-clock.admin.clock.clock_index', ['type' => $param['type']]));
        }
        $type = request()->get('type', 0);
        if (!$type) {
            return $this->message('无效的类型', Url::absoluteWeb(''), 'danger');
        }
        return view('Yunshop\XiaoeClock::admin.clock_add', ['type' => $type])->render();
    }

    /**
     * 获取搜索课程
     * @return html
     */
    public function get_search_course()
    {
        $keyword = \YunShop::request()->keyword;
        $where[] = ['type', '=', 1];
        if (trim($keyword) !== '') {
            $where[] = ['name', 'like', '%' . trim($keyword) . '%'];
        }
        $list = Room::select('id', 'name as title', 'cover_img as thumb')->where($where)->get();

        if (!$list->isEmpty()) {
            $goods = set_medias($list->toArray(), array('thumb','share_icon'));

        }
        return view('goods.query', [
            'goods' => $goods,
            'exchange' => \YunShop::request()->exchange,
        ])->render();

    }
}