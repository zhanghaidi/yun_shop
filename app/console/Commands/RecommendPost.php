<?php

namespace app\Console\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RecommendPost extends Command
{
    protected $signature = 'command:recommendpost';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '推荐达人命令';


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        Log::info(date('Y-m-d H:i:s').'------------------------ 开始推荐达人 BEGIN -------------------------------');

        $service_uniacid = 45;
        $res = DB::table("diagnostic_service_post")->where('is_recommend', 1)->where('uniacid', '!=', $service_uniacid)->update(['is_recommend' => 0]);

        $posts = DB::table("diagnostic_service_post")->select('uniacid')->where('status', 1)->groupBy('uniacid')->get()->toArray();

        $uniacidArr = array_column($posts,'uniacid');

        foreach ($uniacidArr as $uniacid){
            if($uniacid == $service_uniacid){
                continue;
            }
            $postIdArr = DB::table("diagnostic_service_post")
                ->select('id','uniacid')
                ->where(['uniacid'=> $uniacid ,'status' => 1])
                ->inRandomOrder()
                ->take(30)
                ->update(['is_recommend' => 1]);

            Log::info("------------------------ uniacid -------------------------------".$uniacid);
        }

        Log::info("------------------------ 推荐达人任务 END -------------------------------\n");

    }
}
