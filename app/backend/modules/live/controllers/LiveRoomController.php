<?php

namespace app\backend\modules\live\controllers;

use app\common\components\BaseController;
use app\common\services\tencentlive\LiveSetService;
use app\common\services\tencentlive\LiveService;
use app\common\models\live\CloudLiveRoom;
use app\common\facades\Setting;
use app\common\helpers\Url;
use app\framework\Support\Facades\Log;
use app\common\helpers\PaginationHelper;
use app\common\services\tencentlive\IMService;
use app\common\models\live\CloudLiveRoomGoods;
use app\common\models\live\CloudLiveRoomMessage;

class LiveRoomController extends BaseController
{

    const PAGE_SIZE = 20;

    protected $room_model;
    
    /**
     * 查看云直播房间列表
     */
    public function index()
    {
        $search = \YunShop::request()->search;
        $room = new CloudLiveRoom();

        if ($search) {
            $room = $room->search($search);
        }

        $roomList = $room->orderBy('sort','desc')->orderBy('id','desc')->paginate(static::PAGE_SIZE);
        $page = PaginationHelper::show($roomList->total(),$roomList->currentPage(),$roomList->perPage());

        return view('live.index',[
            'roomList'      => $roomList,
            'page'           => $page,
            'search'         => $search,
        ])->render();
    }

    /*
     * 编辑直播间
     */
    public function edit()
    {
        $id = intval(request()->id);

        if($id){
            $this->verifyRoom($id);
        }else{
            $this->room_model = new CloudLiveRoom();
        }

        if (request()->live) {
            $request_data = request()->live;

            $goods_sort = $request_data['goods_sort'];
            unset($request_data['goods_sort']);
            $this->room_model = $this->room_model->fill(CloudLiveRoom::handleArray($request_data, $id));
            $validator = $this->room_model->validator();
            if ($validator->fails()) {
                $this->error($validator->messages());
            } else {
                if (is_array($this->room_model['goods_sort'])) {
                    unset($this->room_model['goods_sort']);
                }
                $ret = $this->room_model->save();
                if (!$ret) {
                    return $this->message('保存直播间失败', Url::absoluteWeb('live.live-room.edit',['id'=>$id]), 'error');
                }

//                fixBy-wk-20201217 云直播管理商品自定义排序
                if (!empty($this->room_model->goods_ids)) {
                    $cloud_live_room_goods = new CloudLiveRoomGoods();
                    $goods_id = explode(',',$this->room_model->goods_ids);
                    foreach ($goods_id as $k => $value) {
                        $updata_live_room_goods = [
                            'uniacid' => $this->room_model->uniacid,
                            'room_id' => $this->room_model->id,
                            'goods_ids' => $value,
                            'sort' => $goods_sort[$k]
                        ];

                        $live_room_goods_model = $cloud_live_room_goods::where([
                            'uniacid' => $this->room_model->uniacid,
                            'room_id' => $this->room_model->id,
                            'goods_ids' => $value,
                        ])->first();
                        if (!$live_room_goods_model) { //没有数据，新增
                            $updata_live_room_goods['created_at'] = time();
                            $cloud_live_room_goods::create($updata_live_room_goods);
                        } else { // 有数据 更新
                            $updata_live_room_goods['updated_at'] = time();
                            $cloud_live_room_goods::where('id', $live_room_goods_model->id)->update($updata_live_room_goods);
                        }
                    }
                }

                if($this->room_model->id){//编辑更新
                    $upd_data = [
                        'stream_name' => LiveSetService::getSetting('stream_name_pre') . $this->room_model->id,
                        'push_url' => LiveService::getPushUrl($this->room_model->id,request()->live['time']['end']),
                        'pull_url' => LiveService::getPullUrl($this->room_model->id),
                    ];
                    //创建直播对应的群聊IM群组
                    if(empty($this->room_model->group_id)){
                        $im_service = new IMService();
                        $res = $im_service->createGroup($this->room_model->id,$this->room_model->name);
                        if($res->ErrorCode == 0){
                            $upd_data['group_id'] = $res->GroupId;
                            $upd_data['group_name'] = $this->room_model->name;
                        }
                    }
                    CloudLiveRoom::where('id',$this->room_model->id)->update($upd_data);
                }
                return $this->message('保存直播间成功', Url::absoluteWeb('live.live-room.index'));
            }
        }
        return view('live.edit', [
            'live' => $this->room_model
        ])->render();
    }

    public function start(){
        $id = intval(request()->id);
        if($id){
            $this->verifyRoom($id);
            $this->room_model->live_status = 101;
            if (is_array($this->room_model['goods_sort'])) {
                unset($this->room_model['goods_sort']);
            }
            if($this->room_model->save() !== $this->room_model){
                (new LiveService())->resumeLiveStream($this->room_model->stream_name);
                return $this->message('开始直播成功', Url::absoluteWeb('live.live-room.index'));
            }else{
                return $this->message('开始直播失败', Url::absoluteWeb('live.live-room.index'));
            }
        }else{
            return $this->message('直播间id为空', Url::absoluteWeb('live.live-room.index'));
        }
    }

    public function stop(){
        $id = intval(request()->id);
        if($id){
            $this->verifyRoom($id);
            $this->room_model->live_status = 103;
            if (is_array($this->room_model['goods_sort'])) {
                unset($this->room_model['goods_sort']);
            }
            if($this->room_model->save() !== false){
                $live_service = new LiveService();
                if($live_service->getDescribeLiveStreamState($this->room_model->stream_name) == 'active'){
                    $live_service->dropLiveStream($this->room_model->stream_name);
                }
                return $this->message('结束直播成功', Url::absoluteWeb('live.live-room.index'));
            }else{
                return $this->message('结束直播失败', Url::absoluteWeb('live.live-room.index'));
            }
        }else{
            return $this->message('直播间id为空', Url::absoluteWeb('live.live-room.index'));
        }
    }

    private function verifyRoom($id)
    {
        if (!$id) {
            return $this->message('参数错误', Url::absoluteWeb('live.live-room.index'), 'error');
        }
        $room_model = CloudLiveRoom::getRoomById($id)->first();
        if (!$room_model) {
            return $this->message('未找到数据', Url::absoluteWeb('live.live-room.index'), 'error');
        }

//        fixBy-wk-20201217 管理商品自定义排序
        $goods_sort = [];
        if (!empty($room_model->goods_ids)) {
            $cloud_live_room_goods = new CloudLiveRoomGoods();
            $goods_id = explode(',',$room_model->goods_ids);
            foreach ($goods_id as $k => $value) {
                $live_room_goods_model = $cloud_live_room_goods::where([
                    'uniacid' => $room_model['uniacid'],
                    'room_id' => $room_model['id'],
                    'goods_ids' => $value,
                ])->first();
                if (!$live_room_goods_model) { //没有数据，默认值0
                    $goods_sort[$k] = 0;
                } else { // 有数据 更新
                    $goods_sort[$k] = $live_room_goods_model['sort'];
                }
            }
        }
        $room_model['goods_sort'] = $goods_sort;

        $this->room_model = $room_model;
    }


    //直播间消息列表
    public function roomMessage()
    {
        $records = CloudLiveRoomMessage::records();

        $search = \YunShop::request()->search;
        if ($search) {

            $records = $records->search($search);

        }

        $recordList = $records->orderBy('id', 'desc')->paginate();

        $pager = PaginationHelper::show($recordList->total(), $recordList->currentPage(), $recordList->perPage());

        return view('live.room-message', [
            'pageList'    => $recordList,
            'page'          => $pager,
            'search'        => $search

        ])->render();
    }

    //删除直播间消息
    public function roomMessageDel(){

        $id = \YunShop::request()->id;
        if(empty($id)){
            return $this->message('Id不能为空', '', 'error');
        }
        $res = CloudLiveRoomMessage::destroy($id);

        if(!$res){
            return $this->message('删除失败', '', 'error');
        }

        return $this->message('删除成功', Url::absoluteWeb('live.live-room.room-message'));
    }
    
}