<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImsYzMemberDesignerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_member_designer')) {
            Schema::create('yz_member_designer', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('uniacid')->default(0)->index('idx_uniacid');
                $table->string('page_name')->default('');
                $table->string('page_type', 45)->default(0);
                $table->boolean('shop_page_type')->default(0);
                $table->text('page_info', 65535);
                $table->string('keyword')->nullable()->default('');
                $table->boolean('is_default')->default(0);
                $table->longText('datas', 65535);
                $table->integer('created_at');
                $table->integer('updated_at');
                $table->integer('deleted_at')->nullable();
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
