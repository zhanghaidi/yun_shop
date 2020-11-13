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
        if ($this->config['is_open'] == 1) {
            global $template_id;
            global $jump_page;
            global $notice_data;

            $jump_page = $this->config['page'] ? $this->config['page'] : '/pages/template/rumours/index?share=1&shareUrl=';
            $template_id = $this->config['template_id'] ? $this->config['template_id'] :'c-tYzcbVnoqT33trwq6ckW_lquLDPmqySXvntFJEMhE';
            $notice_data = $this->config['notice_data'];
               /* [
                'first' => ['value' => '尊敬的用户,您订阅的课程有新视频要发布啦~', 'color' => '#173177'],
                'keyword1' => ['value' => '【和大师一起学艾灸】', 'color' => '#173177'],
                'keyword2' => ['value' => '长期有效', 'color' => '#173177'],
                'keyword3' => ['value' => '更新中', 'color' => '#173177'],
                'remark' => [
                    'value' => '最新视频【每次艾灸几个穴位合适】将于' . date('Y-m-d H:i', strtotime('+15 minutes')) . '震撼发布!',
                    'color' => '#173177',
                ],
            ];*/

            //查询公众号粉丝 发送模板消息
            DB::table('mc_mapping_fans')->whereIn('uid',[125519,114685,129411,129419,125310, 129415, 114545])->where('follow', 1)->orderBy('fanid')
                ->chunk(1000, function ($mapping_fans_list) {
                    foreach ($mapping_fans_list as $mapping_fans) {
                        $job = new SendTemplateMsgJob('wechat', $this->options['wechat'], $GLOBALS['template_id'], $GLOBALS['notice_data'],
                            $mapping_fans['openid'], '', $GLOBALS['jump_page']);
                        $dispatch = dispatch($job);
                    }

                });
        }
    }
}
