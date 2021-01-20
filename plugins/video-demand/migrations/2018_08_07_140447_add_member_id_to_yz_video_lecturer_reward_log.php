<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMemberIdToYzVideoLecturerRewardLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_video_lecturer_reward_log')) {
        	if (!Schema::hasColumn('yz_video_lecturer_reward_log', 'member_id')) {
        		Schema::table('yz_video_lecturer_reward_log', function (Blueprint $table) {
        			$table->integer('member_id')->nullable()->default(0)->comment('购买/打赏人ID');
        		});
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
        if (Schema::hasTable('yz_video_lecturer_reward_log')) {
            if (Schema::hasColumn('yz_video_lecturer_reward_log', 'member_id')) {
                Schema::table('yz_video_lecturer_reward_log', function (Blueprint $table) {
                    $table->dropColumn('member_id');
                });
            }
        }
    }
}
