<?php
/**
 * Created by PhpStorm.
 * User: zlt
 * Date: 2020/10/22
 * Time: 10:00
 */

namespace app\frontend\modules\live\controllers;

use app\common\components\BaseController;
use app\common\facades\Setting;
use app\common\services\tencentlive\IMService;
use app\common\models\live\ImCallbackLog;
use app\common\services\tencentlive\LiveSetService;
use Yunshop\Appletslive\common\services\BaseService;
use app\common\models\live\CloudLiveRoomLike;
use app\common\models\live\CloudLiveRoomMessage;

class IMCallbackController extends BaseController
{
    /**
     * 腾讯直播统一回调接口
     * @return \Illuminate\Http\JsonResponse
     */

    public function index()
    {
        //接收的全部数据带uil参数?SdkAppid=888888&CallbackCommand=Group.CallbackAfterNewMemberJoin&contenttype=json&ClientIP=$ClientIP&OptPlatform=$OptPlatform HTTP/1.1
        $requestData = request()->input(); //接收的包含query参数的所有数据

        $requestBody = request()->getContent(); //接收的post内容数据不包含网址参数

        \Log::info('========IM回调数据Start========' . json_encode($requestData, 320));

        if ($requestData['SdkAppid'] != LiveSetService::getIMSetting('sdk_appid')) {
            return $this->responJson(4002, 'error', 'illegal SdkAppid!');
        }

        if(empty($requestBody)){
            return $this->responJson(4001, 'error', 'body empty!');
        }

        //根据回调Json格式内容处理数据
        $contentArr = json_decode($requestBody, true);

        $logModel = new ImCallbackLog();

        $data = [
            'uniacid' => \YunShop::app()->uniacid,
            'sdk_appid' => $requestData['SdkAppid'],
            'client_iP' => $requestData['ClientIP'],
            'type' => $logModel->getType($contentArr['CallbackCommand']),
            'callback_command' => $contentArr['CallbackCommand'],
            'callback_data' => json_encode($requestData, 320),
            'group_id' => $contentArr['GroupId'],
            'from_account' => $contentArr['From_Account'],
            'Operator_Account' => $contentArr['Operator_Account'],
            'msg_time' => $contentArr['MsgTime'],
            'msg_type' => $logModel->getMsgType($contentArr['MsgBody'][0]['MsgType']), //回调内容消息类型
            'msg_content' => $contentArr['MsgBody'][0]['MsgContent']['Text'] ? json_encode(json_decode($contentArr['MsgBody'][0]['MsgContent']['Text'], true), 320) : json_encode($contentArr['MsgBody'][0]['MsgContent'], 320) ,
        ];

        $extra = [];
        $logModel->fill($data)->save();

        if($contentArr['CallbackCommand'] == 'Group.CallbackBeforeSendMsg'){
            //处理群内发言之前回调 文本过滤 自定义消息处理 ···
            /*
             * {
            "MsgType": "TIMTextElem", // 文本
            "MsgContent": {
                "Text": "red packet"
            }
        },
        {
            "MsgType": "TIMCustomElem", // 自定义消息
            "MsgContent": {
                "Desc": "CustomElement.MemberLevel", // 描述
                "Data": "LV1" // 数据
            }
        }
             * */

            $msgData = [
                'uniacid' => \YunShop::app()->uniacid,
                'sdk_appid' => $requestData['SdkAppid'],
                'group_id' => $contentArr['GroupId'],
                'client_iP' => $requestData['ClientIP'],
                'user_id' => $contentArr['From_Account'],
            ];
            $msgBody = $this->messageHandling($contentArr['MsgBody'][0], $msgData);
            $contentArr['MsgBody'][0] = $msgBody;
        }

        return $this->responJson(0, 'OK', '', $contentArr);
    }

    //消息统一处理方法
    protected function messageHandling($msgBody ,$msgData)
    {
        if($msgBody['MsgType'] == 'TIMTextElem'){
            //文本类型
            $messageModel = new CloudLiveRoomMessage();
            $msgData['msg_content'] = $msgBody['MsgContent']['Text'];
            $messageModel->fill($msgData)->save();
            $msg_id = $messageModel->id;
            $msgBody['MsgContent']['Text'] = json_decode($msgBody['MsgContent']['Text'], true);

            $msgBody['MsgContent']['Text']['text'] = $this->filterMsg($msgBody['MsgContent']['Text']['text']);
            $msgBody['MsgContent']['Text']['msg_id'] = $msg_id;
            $msgBody['MsgContent']['Text'] = json_encode($msgBody['MsgContent']['Text'], 320);
            $messageModel->msg_content = $msgBody['MsgContent']['Text'];
            $messageModel->save();
            \Log::info('========处理文本消息========' . $msgBody['MsgContent']['Text']);
        }elseif ($msgBody['MsgType'] == 'TIMCustomElem'){
            //自定义类型 删除消息、直播间点赞
            if($msgBody['MsgContent']['Data'] == 'REMOVE_MSG'){
                //删除消息
                \Log::info('========处理自定义删除========' . $msgBody['MsgContent']['Ext']);
                CloudLiveRoomMessage::destroy($msgBody['MsgContent']['Ext']);

            }elseif ($msgBody['MsgContent']['Data'] == 'LIKE_LIVE'){
                //点赞处理

                \Log::info('========处理自定义点赞========' . $msgBody['MsgContent']['Ext']);
                $ext = json_decode($msgBody['MsgContent']['Ext'], true);
                CloudLiveRoomLike::create([
                    'uniacid' => \YunShop::app()->uniacid,
                    'user_id' => $ext['uid'],
                    'room_id'=> $ext['room_id']
                ]);
            }
        }

        \Log::info('========IM消息处理完毕结果========' . json_encode($msgBody, 320));

        return $msgBody;
    }

    protected function responJson($ErrorCode = 0, $ActionStatus = 'OK', $ErrorInfo = '', $extra = [])
    {
        $res_data = [
            "ActionStatus" => $ActionStatus,
            "ErrorInfo" => $ErrorInfo,
            "ErrorCode" => $ErrorCode
        ];
        if($extra){
            $resp_data = array_merge($res_data, $extra);
        }
        return response()->json($resp_data, 200, ['charset' => 'utf-8']);
    }

    //过滤文本类型内容
    protected function filterMsg($message)
    {
        return (new BaseService())->textCheck($message,false);
    }

}