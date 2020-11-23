<?php

namespace app\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use app\Jobs\DispatchesJobs;
use \app\Jobs\SendWeChatTplNoticeJob;

class SendWeChatTplCreateJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 120;

    protected $config;

    /**
     * TemplateMsgSendWechtJob constructor.
     * @param $openid
     * @param $options
     * @param $template_id
     * @param $notice_data
     * @param string $url
     * @param $topcolor
     * @param array $miniprogram
     */
    public function __construct($weid, $queue, $options, $template_id, $notice_data, $url = '', $topcolor, $miniprogram = array())
    {
        $this->config = [
            'weid' => $weid,
            'queue' => $queue,
            'options' => $options,
            'template_id' => $template_id,
            'notice_data' => $notice_data,
            'url' => $url,
            'topcolor' => $topcolor,
            'miniprogram' => $miniprogram
        ];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $begin = time();
        Log::info('*** SendWeChatTplCreateJob队列开始执行' . date('Y-m-d H:i:s', $begin));
        //查询公众号粉丝 发送模板消息
        $weid = intval($this->config['weid']);

        $openid_arr = json_decode($this->config['queue']['openid_arr'], true);

        foreach ($openid_arr as $k => $openid){
            $job = new SendWeChatTplNoticeJob($openid, $this->config['options'], $this->config['template_id'], $this->config['notice_data'], $this->config['url'], $this->config['topcolor'], $this->config['miniprogram']);
            $job_dispatch = dispatch($job);
            Log::info('----- JobID:'.$job_dispatch.' : '.$k.' : '.$weid.' '.$openid.'');
        }

        $end = time();
        $totalSecond = $end-$begin;
        Log::info('*** SendWeChatTplCreateJob队列执行结束'.date('Y-m-d H:i:s',$end).' /耗时 '.$totalSecond.'秒\n');
    }
}
