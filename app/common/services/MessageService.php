<?php

namespace app\common\services;

use app\backend\modules\tracking\models\DiagnosticServiceUser;
use app\common\events\message\SendMessageEvent;
use app\common\models\AccountWechats;
use app\common\models\Member;
use app\common\models\notice\MessageTemp;
use app\common\models\TemplateMessageRecord;
use app\Jobs\DispatchesJobs;
use app\Jobs\MessageNoticeJob;
use app\Jobs\SendTemplateMsgJob;
use Exception;
use EasyWeChat\Message\News;
use EasyWeChat\Message\Text;
use EasyWeChat\Foundation\Application;
use app\Jobs\MiniMessageNoticeJob;
use app\common\models\MemberMiniAppModel;
use app\common\models\FormId;
use app\common\models\TemplateMsgLog;
use Illuminate\Support\Facades\Log;

class MessageService
{

    /**
     * 消息推送，暂时使用，需要优化
     *
     * @param int $member_id
     * @param int $template_id
     * @param array $params
     * @param string $url
     * @return bool
     */
    public function push($member_id, $temp_id, array $params, $url='', $uniacid='', $pagepath='pages/template/rumours/index')
    {
        if ($uniacid) {
            \Setting::$uniqueAccountId = \YunShop::app()->uniacid = $uniacid;
        } else{
            \Setting::$uniqueAccountId = \YunShop::app()->uniacid;
        }
        
        if(\Setting::get('shop.notice.toggle') == false){
            return false;
        }

        if (!$member_id || !$temp_id) {
            return false;
        }

        $temp = MessageTemp::withoutGlobalScopes(['uniacid'])->whereId($temp_id)->first();

        if (!$temp) {
            return false;
        }
        $template_id = $temp->template_id;

        if (!$template_id) {
            \Log::error("微信消息推送：MessageTemp::template_id参数不存在");
            return false;
        }

//        $send_msg = $this->getSendMsg($temp, $params);
        $send_msg = MessageTemp::getSendMsg($temp_id, $params);

        $memberModel = $this->getMemberModel($member_id);

        if (!$memberModel) {
            return false;
        }
        if(!empty($send_msg['miniprogram'])){  //接收自定义的小程序路径
            $miniApp = ['miniprogram' => $send_msg['miniprogram']];
            unset($send_msg['miniprogram']);
        }elseif(!empty($url)){
            $miniApp = [];
        }else{
            $miniApp = ['miniprogram' => ['appid' => 'wxcaa8acf49f845662', 'pagepath' => $pagepath]];
        }
        $config = $this->getConfiguration($uniacid);

        try {

            $app = new Application($config);
            $app = $app->notice;
            $app = $app->uses($template_id);
            $app = $app->andData($send_msg);
            $app = $app->andReceiver($memberModel->hasOneFans->openid);
            $app = $app->andUrl($url);
            //$app = $app->andminiApp($miniApp);
            //$miniApp = ['miniprogram' => ['appid' => 'wxcaa8acf49f845662', 'pagepath' => 'pages/template/rumours/index']]
            $res = $app->send($miniApp);

            $log_data = [
                'uniacid' => $uniacid,
                'member_id' => intval($memberModel->hasOneFans->uid),
                'template_id' => $template_id,
                'openid' => $memberModel->hasOneFans->openid,
                'message' => json_encode($send_msg,320),
                'weapp_appid' => !empty($miniApp['miniprogram']['appid']) ? $miniApp['miniprogram']['appid'] :  '',
                'weapp_pagepath' => !empty($miniApp['miniprogram']['pagepath']) ? $miniApp['miniprogram']['pagepath'] : '',
                'news_link' => $url,
                'respon_code' => $res->errcode,
                'respon_data' => json_encode($res),
                'remark' => '公众号消息模板推送',
                'created_at' => time()
            ];
            TemplateMsgLog::insert($log_data);

        } catch (Exception $error) {

            TemplateMessageRecord::create([
                'uniacid' => \YunShop::app()->uniacid,
                'member_id' => $member_id,
                'template_id' => $template_id,
                'url' => $url,
                'openid' => $memberModel->hasOneFans->openid ?: 0,
                'data' => json_encode($send_msg),
                'send_time' => time(),
                'status' => -1,
                'extend_data' => $error->getMessage(),
            ]);

            return true;
        }

        return true;
    }


    /**
     * 会员信息
     *
     * @param $member_id
     * @return bool
     */
    private function getMemberModel($member_id)
    {
        if (!$member_id) {
            \Log::error("微信消息推送：uid参数不存在");
            return false;
        }

        $memberModel = Member::whereUid($member_id)->with([
            'hasOneFans' => function($q) {
                $q->uniacid();
            }])->first();

        if (!isset($memberModel)) {
            \Log::error("微信消息推送：未找到uid:{$member_id}的用户");
            return false;
        }
        if (!$memberModel->isFollow()) {
            \Log::error("微信消息推送：会员uid:{$member_id}未关注公众号");
            return false;
        }
        if (!$memberModel->hasOneFans->openid) {
            \Log::error("微信消息推送：会员uid:{$member_id} 没有openid");
            return false;
        }

        return $memberModel;
    }


    /**
     * 获取公众号配置信息
     *
     * @return array|bool
     */
    public function getConfiguration($uniacid)
    {
        if ($uniacid) {
            \Setting::$uniqueAccountId = \YunShop::app()->uniacid = $uniacid;
        } else{
            \Setting::$uniqueAccountId = \YunShop::app()->uniacid;
        }

        $accountWechat = AccountWechats::getAccountByUniacid(\YunShop::app()->uniacid);

        if (!isset($accountWechat)) {
            \Log::error("微信消息推送：未找到uniacid:{$uniacid}的配置信息");
            return false;
        }

        return ['app_id' => $accountWechat->key, 'secret' => $accountWechat->secret];
    }

    private function getSendMsg($temp, $params)
    {
        $msg = [
            'first' => [
                'value' => $this->replaceTemplate($temp->first, $params),
                'color' => $temp->first_color
            ],
            'remark' => [
                'value' => $this->replaceTemplate($temp->remark, $params),
                'color' => $temp->remark_color
            ]
        ];
        foreach ($temp->data as $row) {
            $msg[$row['keywords']] = [
                'value' => $this->replaceTemplate($row['value'], $params),
                'color' => $row['color']
            ];
        }

        return $msg;
    }

    private function replaceTemplate($str, $datas = array())
    {
        foreach ($datas as $row ) {
            $str = str_replace('[' . $row['name'] . ']', $row['value'], $str);
        }
        return $str;
    }




    /*todo 一下代码需要重构，重新分化类功能 2018-03-23 yitian*/

    /**
     * 发送微信模板消息
     *
     * @param $templateId
     * @param $data
     * @param $uid
     * @param string $uniacid
     * @param string $url
     * @return bool
     */
    public static function MiniNotice($templateId, $data, $uid, $uniacid = '', $url = '',$scene = '')
    {
        \Log::debug('==============miniApp===================',[$templateId,$data,$uid]);

       // $res = AccountWechats::getAccountByUniacid(\YunShop::app()->uniacid);
        $res = \Setting::get('plugin.min_app');
        $options = [
            'app_id' => $res['key'],
            'secret' => $res['secret'],
        ];
        $member = Member::whereUid($uid)->first();
        if (!isset($member)) {
            \Log::error("小程序消息推送失败,未找到uid:{$uid}的用户");
            return false;
        }

        if(empty($scene)){
            $createTime = $member->hasOneMiniApp->formId_create_time;
            $time=strtotime (date("Y-m-d H:i:s")); //当前时间
            $minute=floor(($time-$createTime) % (86400/60));
            \Log::info('小程序消息推送时间',$createTime."----".$time."----".$minute);
            if ($minute > 10080 && empty($createTime)){ 
                \Log::error("小程序消息推送失败,formId失效");
                return false;
            }
            // $scene = $member->hasOneMiniApp->formId;
            // $pieces = explode("#", $scene);
            // $scene = array_shift($pieces);//获取首个元素并删除
            // $str = implode("#", $pieces);
            // MemberMiniAppModel::where('member_id',$uid)->uniacid()->update(['formId'=>$str]);
            $scene = FormId::orderBy('addtime','desc')->first();
            $scene = empty($scene) ? array() : $scene->toArray();
            \Log::info('小程序scene',$scene);
        }
        \Log::info('小程序消息scene',$scene);
        if(empty($scene)){
            \Log::error("小程序消息推送失败,formId失效");
            return false;
        }
        $job = new MiniMessageNoticeJob($options, $templateId, $data,$member->hasOneMiniApp->openid, $url,$scene['formid'] );
        DispatchesJobs::dispatch($job,DispatchesJobs::LOW);
        FormId::where('id',$scene['id'])->delete();//删除formid减少消耗
    }

    //微信消息推送方法，关联小程序路径
    public static function notice($templateId, $data, $uid, $uniacid = '', $url = '', $pagepath = '', $sync = false)
    {
        if (\Setting::get('shop.notice.toggle') == false) {
            return false;
        }

        $member = Member::whereUid($uid)->first();
        if (!isset($member)) {
            \Log::error("微信消息推送失败,未找到uid:{$uid}的用户");
            return false;
        }

        if (!$member->isFollow()) {
            return false;
        }

        $job = new MessageNoticeJob($templateId, $data, $member->hasOneFans->openid, $url, $pagepath);

        if($sync){
            DispatchesJobs::dispatchNow($job);
        }else{
            DispatchesJobs::dispatch($job,DispatchesJobs::LOW);
        }
    }

    /**
     * 小程序订阅消息发送
     *
     * @param int $member_id
     * @param int $template_id
     * @param array $params
     * @return bool
     */
    public function subMsgPush($member_id, $temp_id, array $params = [], $sync = false)
    {

        if(\Setting::get('shop.miniNotice.toggle') == false){
            return false;
        }

        if (!$member_id || !$temp_id) {
            return false;
        }

        $temp = MessageTemp::withoutGlobalScopes(['uniacid'])->whereId($temp_id)->first();

        if (!$temp) {
            return false;
        }
        $template_id = $temp->template_id;

        if (!$template_id) {
            \Log::error("小程序订阅消息推送：MessageTemp::template_id参数不存在");
            return false;
        }

        $send_msg = MessageTemp::getSubMsg($temp_id, $params);

        $memberModel = DiagnosticServiceUser::where('ajy_uid', $member_id)->first();

        if (!$memberModel) {
            Log::debug("subMsgPush 获取member信息失败，member_id:" . $member_id);
            return false;
        }

        $options = ['app_id'=>$send_msg['miniprogram']['appid'], 'secret'=>'secret'];
        Log::debug("subMsgPush:{$temp_id},msg:" . json_encode($send_msg) . ',options:' . json_encode($options) . ",template_id:{$template_id},openid:{$memberModel->openid}");
        $job = new SendTemplateMsgJob('wxapp', $options, $template_id, $send_msg, $memberModel->openid);

        if($sync){
            DispatchesJobs::dispatchNow($job);
        }else{
            DispatchesJobs::dispatch($job,DispatchesJobs::LOW);
        }

    }

    public static function getWechatTemplates()
    {
        $app = app('wechat');
        $notice = $app->notice;
        return $notice->getPrivateTemplates();
    }

    /**
     * 验证"模板消息ID" 是否有效
     * @param $template_id
     * @return array
     */
    public static function verifyTemplateId($template_id)
    {
        $templates = self::getWechatTemplates()->get('template_list');
        if (!isset($templates)) {
            return [
                'status' => -1,
                'msg' => '任务处理通知模板id错误'
            ];
        }
        $template = collect($templates)->where('template_id', $template_id)->first();
        if (!isset($template)) {
            return [
                'status' => -1,
                'msg' => '任务处理通知模板id错误'
            ];
        }
        return [
            'status' => 1
        ];
    }

    /**
     * 发送微信"客服消息"
     * @param $openid
     * @param $data
     * 文本消息: $data = new Text(['content' => 'Hello']);
     * 图文消息:
     * $data = new News([
     * 'title' => 'your_title',
     * 'image' => 'your_image',
     * 'description' => 'your_description',
     * 'url' => 'your_url',
     * ]);
     */
    public static function sendCustomerServiceNotice($openid, $data)
    {
        $app = app('wechat');
        if (array_key_exists('content', $data)) {
            $data = new Text($data); //发送文本消息
        } else {
            $data = new News($data); //发送图文消息
        }
        $app->staff->message($data)->to($openid)->send();
    }
}