<?php

namespace app\Jobs;

use app\common\models\AccountWechats;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use EasyWeChat\Foundation\Application;
use app\common\models\TemplateMsgLog;
use app\common\models\McMappingFans;
use Illuminate\Support\Facades\Log;

class MessageNoticeJob implements  ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;


    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 5;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 120;

    protected $templateId;
    protected $noticeData;
    protected $openId;
    protected $url;
    protected $uniacid;
    protected $pagepath; //小程序页面路径
    protected $miniApp; //消息发送小程序参数

    /**
     * MessageNoticeJob constructor.
     * @param $templateId
     * @param $noticeData
     * @param $openId
     * @param $url
     * @param $uniacid
     */
    public function __construct($templateId, $noticeData, $openId, $url, $pagepath)
    {
        $this->templateId = $templateId;
        $this->noticeData = $noticeData;
        $this->openId = $openId;
        $this->url = $url;
        $this->uniacid = \YunShop::app()->uniacid;

        //fixby-zlt-miniprogram 2020-10-11 优化小程序路径
        if(!empty($noticeData['miniprogram'])){  //接收自定义的小程序路径
            $this->miniApp = ['miniprogram' => $noticeData['miniprogram']];
            unset($this->noticeData['miniprogram']);
        }elseif(!empty($noticeData['news_link'])){
            $this->url = $noticeData['news_link'];
            $this->miniApp = [];
            unset($this->noticeData['news_link']);
        }else{
            $this->pagepath = $pagepath ?:'pages/template/user/user'; //默认小程序用户中心路径
            $this->miniApp = ['miniprogram' => ['appid' => 'wxcaa8acf49f845662', 'pagepath' => $this->pagepath]]; //封装成小程序参数
        }
    }

    /**
     * Execute the job.
     *
     * @return bool
     */
    public function handle()
    {
        if ($this->attempts() > 1) {
            Log::info('消息通知测试，执行大于两次终止');
            return true;
        }
        $res = AccountWechats::getAccountByUniacid($this->uniacid);
        $options = [
            'app_id' => $res['key'],
            'secret' => $res['secret'],
        ];
        $app = new Application($options);
        $app = $app->notice;
        try{
            $res = $app->uses($this->templateId)->andData($this->noticeData)->andReceiver($this->openId)->andUrl($this->url)->send($this->miniApp);
            $log_data = [
                'uniacid' => $this->uniacid,
                'member_id' => intval(McMappingFans::select('uid')->where('openid', $this->openId)->first()->uid),
                'template_id' => $this->templateId,
                'openid' => $this->openId,
                'message' => json_encode($this->noticeData,320),
                'weapp_appid' => !empty($this->miniApp['miniprogram']['appid']) ? $this->miniApp['miniprogram']['appid'] : '',
                'weapp_pagepath' => !empty($this->miniApp['miniprogram']['pagepath']) ? $this->miniApp['miniprogram']['pagepath'] : '',
                'news_link' => $this->url,
                'respon_code' => $res->errcode,
                'respon_data' => json_encode($res),
                'remark' => '公众号消息模板推送',
                'created_at' => time()
            ];
            TemplateMsgLog::insert($log_data);
        }catch (\ErrorException $e){
            Log::info('记录消息发送日志报错，error：' . $e->getMessage());
        }
        return true;
    }
}
