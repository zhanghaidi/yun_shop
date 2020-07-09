<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddThumbDescShareImsYzDiyformTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (\Schema::hasTable('yz_diyform_type')) {

            if (!Schema::hasColumn('yz_diyform_type', 'thumb')) {
                Schema::table('yz_diyform_type', function ($table) {
                    $table->string('thumb',255)->nullable()->default('');
                });
            }
            if (!Schema::hasColumn('yz_diyform_type', 'description')) {
                Schema::table('yz_diyform_type', function ($table) {
                    $table->text('description',65535)->nullable();
                });
            }
            if (!Schema::hasColumn('yz_diyform_type', 'share_description')) {
                Schema::table('yz_diyform_type', function ($table) {
                    $table->string('share_description',255)->nullable()->default('');
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
        if (Schema::hasTable('yz_diyform_type')) {
            if (Schema::hasColumn('yz_diyform_type', 'thumb')) {
                Schema::table('yz_diyform_type', function (Blueprint $table) {
                    $table->dropColumn('thumb');
                });
            }
            if (Schema::hasColumn('yz_diyform_type', 'description')) {
                Schema::table('yz_diyform_type', function (Blueprint $table) {
                    $table->dropColumn('description');
                });
            }
            if (Schema::hasColumn('yz_diyform_type', 'share_description')) {
                Schema::table('yz_diyform_type', function (Blueprint $table) {
                    $table->dropColumn('share_description');
                });
            }
        }
    }
}
