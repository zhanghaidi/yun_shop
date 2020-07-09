<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CtrateImsYzPosterRecordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_poster_record')) {
            Schema::create('yz_poster_record', function (Blueprint $table) {
                $table->integer('id', true);
                $table->string('url')->nullable()->default('');
                $table->integer('poster_id')->unsigned()->index('poster_id');
                $table->integer('member_id')->unsigned()->index('member_id');
                $table->integer('created_at')->unsigned()->nullable();
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
        Schema::dropIfExists('yz_poster_record');
    }
}
