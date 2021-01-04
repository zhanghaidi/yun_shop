<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImsYzCustomAppArticleTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('yz_custom_app_article')) {
            Schema::create('yz_custom_app_article', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid', false, true)->default(0)->comment('公众号ID');
                $table->mediumInteger('sort_id', false, true)->default(0)->comment('分类ID');
                $table->mediumText('content')->nullable()->comment('内容');
                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
            });
        }
    }

    public function down()
    {
        Schema::drop('yz_custom_app_article');
    }
}
