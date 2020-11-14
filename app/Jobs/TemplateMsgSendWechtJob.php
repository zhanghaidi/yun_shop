<?php

namespace app\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use EasyWeChat\Foundation\Application;
use EasyWeChat\Core\Exceptions\HttpException;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use app\Jobs\DispatchesJobs;
use \app\Jobs\SendTemplateMsgJob;

class TemplateMsgSendWechtJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 120;

    protected $config;

    /**
     * SendTemplateMsgJob constructor.
     * @param $type
     * @param $options
     * @param $template_id
     * @param $notice_data
     * @param $openid
     * @param string $url
     * @param string $page
     * @param bool $refresh_miniprogram_access_token
     */
    public function __construct($is_open = 0, $options, $template_id, $notice_data, $openid, $url = '', $page = '', $refresh_miniprogram_access_token = false)
    {
        $this->config = [
            'type' => 'wechat',
            'is_open' => $is_open,
            'options' => $options,
            'template_id' => $template_id,
            'notice_data' => $notice_data,
            'openid' => $openid,
            'url' => $url,
            'page' => $page,
            'refresh_miniprogram_access_token' => $refresh_miniprogram_access_token,
        ];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('TemplateMsgSendWechtJob队列开始执行');


        if ($this->config['is_open'] == 1) {

            //查询公众号粉丝 发送模板消息
            DB::table('mc_mapping_fans')->whereIn('uid',[125519,114685,129411,129419,125310, 129415, 114545])->where('follow', 1)->orderBy('fanid')
                ->chunk(1000, function ($mapping_fans_list) {
                    foreach ($mapping_fans_list as $mapping_fans) {
                        $job = new SendTemplateMsgJob($this->config['type'], $this->config['options'], $this->config['template_id'], $this->config['notice_data'],
                            $mapping_fans['openid'], '', $this->config['page']);
                        dispatch($job);
                    }

                });
        }
    }
}
