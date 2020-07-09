<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddYzInsurancePolicyAdditionalGlassRisk extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_insurance_policy')) {
            Schema::table('yz_insurance_policy',
                function (Blueprint $table) {
                    if (!Schema::hasColumn('yz_insurance_policy', 'insurance_coverage')) {
                        $table->string('insurance_coverage', 255)->nullable()->comment('投保险种');
                    }

                    if (!Schema::hasColumn('yz_insurance_policy', 'additional_glass_risk')) {
                        $table->integer('additional_glass_risk')->nullable()->comment('附加玻璃险');
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
