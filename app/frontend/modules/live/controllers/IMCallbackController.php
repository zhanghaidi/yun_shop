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

class IMCallbackController extends BaseController
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $req_data = request()->input();
        $input_data = request()->getContent();
        \Log::debug('IMCallback req_data:' . json_encode($req_data, 320) . ' input_data:' . $input_data);
        if($req_data['SdkAppid'] != LiveSetService::getIMSetting('sdk_appid')){
            return $this->responJson(4002, 'error', 'illegal SdkAppid!');
        }

        if (!empty($input_data)) {
            $callback_data = $input_data;
            $input_data = json_decode($input_data,true);
            $_model = new ImCallbackLog();
            $type_str = substr($input_data['CallbackCommand'],0,strpos($input_data['CallbackCommand'],'.'));
            $data = [
                'uniacid'   => \YunShop::app()->uniacid,
                'sdk_appid' =>  $req_data['SdkAppid'],
                'type'      =>  $_model->getType($type_str),
                'callback_command' =>  $input_data['CallbackCommand'],
                'callback_data' =>  $callback_data,
                'client_iP' =>  $req_data['ClientIP'],
                'created_at' => time()
            ];
            if(in_array($input_data['CallbackCommand'],['Group.CallbackAfterSendMsg'])){
                $data = array_merge($data,$this->getMsgData($input_data,$_model));
            }
            $_model->fill($data)->save();
            return $this->responJson();
        } else {
            return $this->responJson(4001, 'error', 'body empty!');
        }

    }

    protected function getMsgData($input_data,$_model){
        $msg_data = [
            'group_id'  =>  $input_data['GroupId'],
            'from_account'  =>  $input_data['From_Account'],
            'Operator_Account'  =>  empty($input_data['Operator_Account']) ? '' : $input_data['Operator_Account'],
            'msg_time'  =>  $input_data['MsgTime'],
            'msg_type'  =>  $_model->getMsgType($input_data['MsgBody'][0]['MsgType']),
            'msg_content'  =>  $input_data['MsgBody'][0]['MsgContent']['Text'],
        ];
        return $msg_data;
    }

    protected function responJson($ErrorCode = 0, $ActionStatus = 'OK', $ErrorInfo = '')
    {
        return response()->json([
            "ActionStatus" => $ActionStatus,
            "ErrorInfo" => $ErrorInfo,
            "ErrorCode" => $ErrorCode
        ], 200, ['charset' => 'utf-8']);
    }

}