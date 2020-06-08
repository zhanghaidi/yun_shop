<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShopYzMemberAlipayTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_member_alipay')) {
            Schema::create('yz_member_alipay', function (Blueprint $table) {
                $table->bigIncrements('alipay_id');
                $table->integer('uniacid');
                $table->integer('member_id');
                $table->string('user_id', 20); //支付宝用户的userId
                $table->string('avatar')->nullable();   //用户头像
                $table->string('province', 25)->nullable(); //省份名称
                $table->string('city', 25)->nullable();     //市名称
                $table->string('nick_name', 50)->nullable();    //用户昵称
                $table->string('is_student_certified', 10)->nullable(); //是否是学生
                $table->string('user_type', 10)->nullable();    //用户类型
                $table->string('user_status', 10)->nullable();    //用户状态
                $table->string('is_certified', 10)->nullable(); //是否通过实名认证
                $table->string('gender', 20)->nullable(); //性别
                $table->integer('created_at')->unsigned()->default(0);
                $table->integer('updated_at')->unsigned()->default(0);
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
        Schema::dropIfExists('yz_member_alipay');
        
    }
}
