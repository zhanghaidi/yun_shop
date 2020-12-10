<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzSignTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_plugin_circle')) {
            Schema::create('yz_plugin_circle', function (Blueprint $table) {
                $table->increments('id');
                $table->tinyInteger('uniacid');
                $table->tinyInteger('category_id');
                $table->string('title')->default('');
                $table->string('desc')->nullable();
                $table->string('thumb')->nullable();
                $table->longText('content');
                $table->integer('virtual_created_at')->nullable()->comment('虚拟发布时间');
                $table->string('author', 20)->default('');
                $table->integer('virtual_read_num')->nullable()->comment('虚拟阅读量');
                $table->integer('read_num')->default(0);
                $table->integer('virtual_like_num')->nullable()->comment('虚拟点赞数');
                $table->integer('like_num')->default(0);
                $table->string('link')->nullable();
                $table->integer('per_person_per_day')->nullable()->comment('每人每天奖励次数');
                $table->integer('total_per_person')->nullable()->comment('每人总共的奖励次数');
                $table->integer('point')->nullable()->comment('每次分享可以获得的积分奖励');
                $table->integer('credit')->nullable()->comment('每次分享可以获得的余额奖励');
                $table->integer('bonus_total')->nullable()->comment('最高累计余额奖励总量');
                $table->boolean('no_copy_url')->nullable();
                $table->boolean('no_share')->nullable();
                $table->boolean('no_share_to_friend')->nullable();
                $table->string('keyword')->default('');
                $table->boolean('report_enabled')->nullable();
                $table->boolean('advs_type')->nullable();
                $table->string('advs_title')->nullable();
                $table->string('advs_img')->nullable();
                $table->string('advs_title_footer')->nullable();
                $table->string('advs_link')->nullable();
                $table->text('advs', 65535)->nullable();
                $table->boolean('state')->nullable();
                $table->boolean('state_wechat')->nullable();
                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();

                $table->index('uniacid');
                $table->index('category_id');
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
		Schema::drop('yz_plugin_circle');
	}

}
