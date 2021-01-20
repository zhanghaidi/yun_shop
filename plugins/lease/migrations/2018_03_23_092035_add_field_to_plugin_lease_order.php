<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldToPluginLeaseOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_plugin_lease_order')) {
            Schema::table('yz_plugin_lease_order',
                function (Blueprint $table) {
                    if (!Schema::hasColumn('yz_plugin_lease_order',
                        'be_damaged')) {
                        $table->decimal('be_damaged', 12, 2)->nullable()->default(0);
                    }
                    if (!Schema::hasColumn('yz_plugin_lease_order',
                        'be_overdue')) {
                        $table->decimal('be_overdue', 12, 2)->nullable()->default(0);
                    }
                    if (!Schema::hasColumn('yz_plugin_lease_order',
                        'return_status')) {
                        $table->tinyInteger('return_status')->nullable()->default(0);
                    }
                    if (!Schema::hasColumn('yz_plugin_lease_order',
                        'return_pay_type_id')) {
                        $table->tinyInteger('return_pay_type_id')->nullable()->default(0);
                    }
                    if (!Schema::hasColumn('yz_plugin_lease_order',
                        'explain')) {
                        $table->text('explain', 65535)->nullable();
                    }
                    if (!Schema::hasColumn('yz_plugin_lease_order',
                        'end_time')) {
                        $table->integer('end_time')->nullable()->comment('租赁到期时间');
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
