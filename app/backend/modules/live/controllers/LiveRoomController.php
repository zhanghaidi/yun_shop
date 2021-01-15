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

        $roomList = $room->orderBy(['sort','desc'],['id','desc'])->paginate();
        $page = PaginationHelper::show($roomList->total(),$roomList->currentPage(),$roomList->perPage());

        return view('live.index',[
            'roomList'      => $roomList,
            'page'           => $page,
            'search'         => $search,
        ])->render();
    }

    public function create()
    {
        $liveModel = new CloudLiveRoom();
        //获取表单提交的值
        $liveRequest = \YunShop::request()->live;
        if($liveRequest){
            $liveRequest = CloudLiveRoom::handleArray($liveRequest);
            $liveModel->fill($liveRequest);
            $validator = $liveModel->validator();
            if($validator->fails()){
                $this->error($validator->messages());
            }else{
                if($liveModel->save()){
                    $liveModel->stream_name = LiveSetService::getSetting('stream_name_pre') .$liveModel->id;
                    $liveModel->push_url = LiveService::getPushUrl($liveModel->id, $liveRequest['time']['end']);
                    $liveModel->pull_url = LiveService::getPullUrl($liveModel->id);

                    //创建直播对应的群聊IM群组
                    $im_service = new IMService();
                    $im_res = $im_service->createGroup($liveModel->id, $liveModel->name);
                    if($im_res->ErrorCode != 0){
                        $this->message('创建直播群聊失败','','error');
                    }
                    $liveModel->group_id = $im_res->GroupId;
                    $liveModel->group_name = $liveModel->name.'群聊';
                    $liveModel->save();

                    return $this->message('添加成功', Url::absoluteWeb('live.live-room.index'));

                }else{
                    $this->message('活码创建失败','','error');
                }
            }
        }

        return view('live.edit', [
            'live' => $liveModel
        ])->render();
    }

    /*
     * 编辑直播间
     */
    public function edit()
    {
        $id = intval(\YunShop::request()->id);

        $liveModel = $this->verifyRoom($id);

        $liveRequest = \YunShop::request()->live;
        if ($liveRequest) {
            $liveRequest = CloudLiveRoom::handleArray($liveRequest);

            $liveModel->fill($liveRequest);
            $validator = $liveModel->validator();
            if ($validator->fails()) {
                $this->error($validator->messages());
            } else {
                if ($liveModel->save()) {
                    $liveModel->stream_name = LiveSetService::getSetting('stream_name_pre') .$liveModel->id;
                    $liveModel->push_url = LiveService::getPushUrl($liveModel->id, $liveRequest['time']['end']);
                    $liveModel->pull_url = LiveService::getPullUrl($liveModel->id);
                    //创建直播对应的群聊IM群组
                    if(empty($liveModel->group_id)){
                        //创建直播对应的群聊IM群组
                        $im_service = new IMService();
                        $im_res = $im_service->createGroup($liveModel->id, $liveModel->name);
                        if($im_res->ErrorCode != 0){
                            $this->message('创建直播群聊失败','','error');
                        }
                        $liveModel->group_id = $im_res->GroupId;
                        $liveModel->group_name = $liveModel->name.'群聊';
                    }

                    $liveModel->save();
                    return $this->message('保存直播间成功', Url::absoluteWeb('live.live-room.index'));
                }else{
                    return $this->message('保存直播间失败','', 'error');
                }
            }
        }

        return view('live.edit', [
            'live' => $liveModel
        ])->render();
    }

    public function start(){
        $id = intval(request()->id);
        $liveModel = $this->verifyRoom($id);
        $liveModel->live_status = 101;
        if($liveModel->save()){
            (new LiveService())->resumeLiveStream($liveModel->stream_name);
            return $this->message('开始直播成功', Url::absoluteWeb('live.live-room.index'));
        }else{
            return $this->message('开始直播失败,请检查状态', '','error');
        }
    }

    public function stop(){

        $id = intval(request()->id);
        $liveModel = $this->verifyRoom($id);
        $liveModel->live_status = 103;

        if($liveModel->save()){
            $live_service = new LiveService();
            if($live_service->getDescribeLiveStreamState($liveModel->stream_name) == 'active'){
                $live_service->dropLiveStream($liveModel->stream_name);
            }
            return $this->message('结束直播成功', Url::absoluteWeb('live.live-room.index'));
        }else{
            return $this->message('结束直播失败', '','error');
        }
    }

    private function verifyRoom($id)
    {
        if (!$id) {
            return $this->message('直播间id不能为空', '', 'error');
        }
        $liveModel = CloudLiveRoom::getRoomById($id);
        if (!$liveModel) {
            return $this->message('未找到直播间数据', '', 'error');
        }
        return $liveModel;
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