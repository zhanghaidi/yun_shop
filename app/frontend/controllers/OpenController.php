<?php

namespace app\frontend\controllers;

use app\common\components\BaseController;
use app\common\models\AccountWechats;
use app\Jobs\SendTemplateMsgJob;
use Illuminate\Support\Facades\Log;

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
            'jiqshi' => '7PMDEkyGvdoD5o3vHmvxVMWV0UEiYprm',
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
    private function getWeOptions($type)
    {
        if ($type == 'wechat') {
            $account = AccountWechats::getAccountByUniacid(39);
            $options = [
                'app_id' => $account['key'],
                'secret' => $account['secret'],
            ];
        } elseif ($type == 'wxapp') {
            $options = [];
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

        if ($input['type'] == 'wechat') {
            $job = new SendTemplateMsgJob('wechat', $options, $input['template_id'], $input['notice_data'], $input['openid'], $url, $page);
            $dispatch = dispatch($job);
            Log::info("队列已添加:发送公众号模板消息");
        } elseif ($input['type'] == 'wxapp') {
            $job = new SendTemplateMsgJob('wxapp', $options, $input['template_id'], $input['notice_data'], $input['openid'], $url, $page);
            $dispatch = dispatch($job);
            Log::info("队列已添加:发送小程序订阅模板消息");
        } else {
            $job = null;
            Log::info("队列未添加:无法识别的任务");
            return $this->errorJson('队列未添加:无法识别的任务', ['input' => $input]);
        }

        return $this->successJson('ok', ['input' => $input, 'job' => $job, 'dispatcht' => $dispatch]);
    }
}