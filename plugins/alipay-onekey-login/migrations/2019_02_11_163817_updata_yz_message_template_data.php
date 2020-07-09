<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdataYzMessageTemplateData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*备注：修改爱心值变动消息模板，修改内容如下
         * [{"keywords":"keyword1","value":"[变动值类型]","color":"#000000"},
         * {"keywords":"keyword2","value":"[业务类型]","color":"#000000"},
         * {"keywords":"keyword3","value":"尊敬的[昵称]，您于[时间]发生一笔[爱心值]变动，变动类型为[变动值类型]，
         * 变动数值为[变动数量]，变动后[爱心值]余值为[当前剩余值]。","color":"#000000"}]
         */
        if (Schema::hasTable('yz_message_template')) {
            $list = \Illuminate\Support\Facades\DB::table('yz_message_template')->where(['notice_type' => 'change_temp_id' , 'is_default' => 1])->get();
            foreach ($list as $itme){
                \Illuminate\Support\Facades\DB::table('yz_message_template')->where('id',$itme['id'])->update([
                    "data" => '[{"keywords":"keyword1","value":"[\u53d8\u52a8\u503c\u7c7b\u578b]","color":"#000000"},
                {"keywords":"keyword2","value":"[\u4e1a\u52a1\u7c7b\u578b]","color":"#000000"},{"keywords":"keyword3",
                "value":"\u5c0a\u656c\u7684[\u6635\u79f0]\uff0c\u60a8\u4e8e[\u65f6\u95f4]\u53d1\u751f\u4e00\u7b14[\u7231\u5fc3\u503c]\u53d8\u52a8\uff0c\u53d8\u52a8\u7c7b\u578b\u4e3a[\u53d8\u52a8\u503c\u7c7b\u578b]\uff0c\u53d8\u52a8\u6570\u503c\u4e3a[\u53d8\u52a8\u6570\u91cf]\uff0c\u53d8\u52a8\u540e[\u7231\u5fc3\u503c]\u4f59\u503c\u4e3a[\u5f53\u524d\u5269\u4f59\u503c]\u3002","color":"#000000"}]',
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
