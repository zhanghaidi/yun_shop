<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateFileTypeToYzDesigner extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (\Schema::hasTable('yz_designer')) {
            //is_default 字段值未改变，可以记录原默认页面是哪一个
            \Illuminate\Support\Facades\DB::table('yz_designer')->where('is_default', '<>', 1)->update(['page_type' => 0]);
            \Illuminate\Support\Facades\DB::table('yz_designer')->where('is_default', 1)->where('page_type', 9)->update(['page_type' => 2]);
            Schema::table('yz_designer', function (Blueprint $table) {
                if (Schema::hasColumn('yz_designer', 'page_type')) {
                    $table->string('page_type', 45)->change();
                }
            });
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
