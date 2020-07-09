<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/7/15 下午2:07
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace Yunshop\Poster\Jobs;


use app\common\facades\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Yunshop\Commission\models\Log;
use Yunshop\Poster\models\MemberPoster;
use Yunshop\Poster\models\Poster;
use Yunshop\Poster\services\CreatePosterService;

class MemberPosterCreateJob implements ShouldQueue
{

    use InteractsWithQueue, Queueable, SerializesModels;
    protected $uniacid;
    protected $memberId;
    protected $host;
    protected $type;

    public function __construct($memberId, $uniacid, $host, $type)
    {
        $this->uniacid = $uniacid;
        $this->memberId = $memberId;
        $this->host = $host;
        $this->type = $type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \YunShop::app()->uniacid = $this->uniacid;
        Setting::$uniqueAccountId = $this->uniacid;
        $posterModel = Poster::where('uniacid', $this->uniacid)->select('id', 'is_open')->where('center_show', 1)->first();
        \Log::error('$posterModel', $posterModel);
        \Log::error('$this->uniacid', $this->uniacid);
        \Log::error('$this->memberId', $this->memberId);
        \Log::error('$this->type', $this->type);
        $file = (new CreatePosterService($this->memberId, $posterModel->id, $this->type))->createMemberPoster($this->host);
        MemberPoster::where('uid', $this->memberId)->update([
            'status' => 2
        ]);
    }


}