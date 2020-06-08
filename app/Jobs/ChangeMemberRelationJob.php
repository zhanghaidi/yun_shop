<?php
/**
 * Created by PhpStorm.
 * User: 马赛克
 * Date: 2020/3/31
 * Time: 下午3:38
 */

namespace app\Jobs;


use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use app\common\services\member\MemberRelation;

class ChangeMemberRelationJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $member_id = 0;
    public $parent_id = 0;

    public function __construct($member_id, $parent_id)
    {
        $this->member_id = $member_id;
        $this->parent_id = $parent_id;
    }

    public function handle()
    {
        if (intval($this->member_id) > 0 && intval($this->parent_id) >= 0) {
            \Log::debug('修改会员关系---job', [$this->member_id, $this->parent_id]);
            $member_relation = new MemberRelation();

            return $member_relation->change($this->member_id, $this->parent_id);
        }
    }
}