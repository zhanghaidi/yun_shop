<?php

namespace app\frontend\controllers;

use app\Jobs\SendTemplateMsgJob;
use Illuminate\Support\Facades\DB;
use app\common\components\BaseController;
use app\common\models\AccountWechats;
use app\Jobs\SendWeChatTplCreateJob;
use Illuminate\Support\Facades\Log;
use app\Jobs\DispatchesJobs;

/**
 * 公共服务接口类
 * Class OpenController
 * @package app\frontend\controllers
 */
class OpenController extends BaseController
{
    /**
     * 检测apikey是否正确
     * @param $api_key
     */
    private function checkAccess($api_key)
    {
        $access = [
            'jiushi' => '7PMDEkyGvdoD5o3vHmvxVMWV0UEiYprm',
            'ajy_service' => '6rj2ah0KsoZxS1Ks9blzt996rOe8fiys',
        ];
        if (!in_array($api_key, array_values($access))) {
            response()->json([
                'result' => 401,
                'msg' => 'invalid api key',
                'data' => false,
            ], 200, ['charset' => 'utf-8'])->send();
            exit;
        }
    }

    /**
     * 获取公众号配置
     * @param $type
     * @return array
     */
    private function getWeOptions($type, $uniacid = '')
    {
        if ($type == 'wechat') {
            $uniacid = $uniacid ? $uniacid : 39;
            $account = AccountWechats::getAccountByUniacid($uniacid);
            $options = [
                'app_id' => $account['key'],
                'secret' => $account['secret'],
            ];
        } elseif ($type == 'wxapp') {
            $uniacid = $uniacid ? $uniacid : 45;
            $account = DB::table('account_wxapp')->where('uniacid',$uniacid)->first();
            $options = $account ? [
                'app_id' => $account['key'],
                'secret' => $account['secret'],
            ] : [
                'app_id' => 'wxcaa8acf49f845662',
                'secret' => 'f627c835de1b4ba43fe2cbcb95236c52',
            ];
        }
        return $options;
    }

    /**
     * 发送模板消息
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendTemplateMsg()
    {
        $input = request()->all();
        $this->checkAccess($input['apikey']);
        if (!array_key_exists('type', $input) || !in_array($input['type'], ['wechat', 'wxapp'])) {
            return $this->errorJson('type参数有误');
        }
        if (!array_key_exists('template_id', $input) || !array_key_exists('notice_data', $input) || !array_key_exists('openid', $input)) {
            return $this->errorJson('缺少参数');
        }
        $options = $this->getWeOptions($input['type']);
        $url = array_key_exists('url', $input) ? $input['url'] : '';
        $page = array_key_exists('page', $input) ? $input['page'] : '';
        $rmat = array_key_exists('refresh_miniprogram_access_token', $input) ? $input['refresh_miniprogram_access_token'] : false;

        if ($input['type'] == 'wechat') {
            $job = new SendTemplateMsgJob('wechat', $options, $input['template_id'], $input['notice_data'], $input['openid'], $url, $page, $rmat);
            $dispatch = dispatch($job);
            Log::info("队列已添加:发送公众号模板消息");
        } elseif ($input['type'] == 'wxapp') {
            $job = new SendTemplateMsgJob('wxapp', $options, $input['template_id'], $input['notice_data'], $input['openid'], $url, $page, $rmat);
            $dispatch = dispatch($job);
            Log::info("队列已添加:发送小程序订阅模板消息");
        } else {
            $job = null;
            Log::info("队列未添加:无法识别的任务");
            return $this->errorJson('队列未添加:无法识别的任务', ['input' => $input]);
        }

        return $this->successJson('ok', ['input' => $input, 'job' => $job, 'dispatcht' => $dispatch]);
    }

    public function templateMsgSendWechat()
    {
        $input = request()->all();
        $options = $this->getWeOptions($input['type'], $input['weid']); //公众号参数

        $this->checkAccess($input['apikey']);
        if (!array_key_exists('type', $input) || !in_array($input['type'], ['wechat', 'wxapp'])) {
            return $this->errorJson('type参数有误');
        }
        if (!array_key_exists('template_id', $input) || !array_key_exists('notice_data', $input) || !array_key_exists('openid', $input)) {
            return $this->errorJson('缺少参数');
        }
        //触发 发送公众号模板消息队列
        $notify_id = intval($input['openid']);
        $notify_son = DB::table('qwx_notify_son_queue')->where('notify_id', $notify_id)->get();
        foreach ($notify_son as $queue){
            $job = new SendWeChatTplCreateJob($input['weid'], $queue, $options, $input['template_id'], $input['notice_data'],  $input['url'], $input['topcolor'], $input['miniprogram']);
            $dispatch = dispatch($job);
            Log::info("open方法创建队列完成:". $dispatch . ' '. json_encode($queue));
        }

        if($dispatch){
            return $this->successJson('ok', ['input' => $input, 'job' => $job, 'dispatcht' => $dispatch]);
        }else{
            return $this->errorJson('添加队列失败');
        }


    }
}