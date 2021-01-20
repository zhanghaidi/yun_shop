<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYzAreaLeaseLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_area_lease_log')) {
            Schema::create('yz_area_lease_log', function (Blueprint $table) {
                    $table->integer('id', true);
                    $table->integer('uniacid')->nullable()->default(0)->index('idx_uniacid');
                    $table->integer('order_id')->nullable()->default(0)->comment('订单id');
                    $table->integer('as_id')->nullable()->default(0)->comment('分站id');
                    $table->decimal('return_deposit', 12, 2)->default(0)->comment('退还押金总和');
                    $table->decimal('be_overdue', 12, 2)->nullable()->default(0);
                    $table->decimal('be_damaged', 12, 2)->nullable()->default(0);
                    $table->string('as_name', 50)->nullable()->default('');
                    $table->text('explain', 65535)->nullable();
                    $table->integer('updated_at')->nullable();
                    $table->integer('created_at')->nullable();
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
        Schema::dropIfExists('yz_area_lease_log');
    }
}
