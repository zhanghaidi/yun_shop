<?php

namespace app\Console\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RecommendArticle extends Command
{
    protected $signature = 'command:recommendarticle';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '推荐文章命令';


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        Log::info(data('Y-m-d H:i:s').'------------------------ 开始推荐文章 BEGIN -------------------------------');

        $res = DB::table("diagnostic_service_article")->where('is_hot', 1)->update(['is_hot' => 0]);

        $articles = DB::table("diagnostic_service_article")->select('uniacid')->where('status', 1)->groupBy('uniacid')->get()->toArray();

        $uniacidArr = array_column($articles,'uniacid');

        foreach ($uniacidArr as $uniacid){
            $articleIdArr = DB::table("diagnostic_service_article")
                ->select('id','uniacid')
                ->where(['uniacid'=> $uniacid ,'status' => 1])
                ->inRandomOrder()
                ->take(30)
                ->update(['is_hot' => 1]);

        }

        Log::info("------------------------ 推荐文章任务 END -------------------------------\n");

    }
}
