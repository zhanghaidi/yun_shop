<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImsYzAgentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_agents')) {
            Schema::create('yz_agents', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid')->nullable();
                $table->integer('member_id')->nullable();
                $table->integer('parent_id')->nullable()->default(0);
                $table->integer('agent_level_id')->nullable()->default(0);
                $table->boolean('is_black')->nullable()->default(0);
                $table->decimal('commission_total', 14)->nullable()->default(0.00);
                $table->decimal('commission_pay', 14)->nullable();
                $table->boolean('agent_not_upgrade')->nullable()->default(0);
                $table->text('content', 65535)->nullable();
                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
                $table->integer('deleted_at')->nullable();
                $table->string('parent', 20)->nullable();
                $table->index(['uniacid', 'parent'], 'idx_uniacid_parent');
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
		Schema::dropIfExists('yz_agents');
	}

}
