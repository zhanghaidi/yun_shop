<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFormTypeToDiyformDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (\Schema::hasTable('yz_diyform_data')) {

            if (!Schema::hasColumn('yz_diyform_data', 'form_type')) {
                Schema::table('yz_diyform_data', function ($table) {
                    $table->string('form_type','200')->nullable()->default('');
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
        //
    }
}
