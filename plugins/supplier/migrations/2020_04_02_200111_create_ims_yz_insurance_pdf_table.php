<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzInsurancePdfTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_insurance_pdf')) {
            Schema::create('yz_insurance_pdf',
                function (Blueprint $table) {
                    $table->increments('id');
                    $table->integer('supplier_id');
                    $table->text('pdf');
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

	}

}
