<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRemindToAdminUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_admin_users')) {
            Schema::table('yz_admin_users', function (Blueprint $table) {
                $table->integer('change_password_at')->nullable();
                $table->tinyInteger('change_remind')->default(0);
                $table->string('login_token')->nullable();
            });
        }

        if (Schema::hasTable('yz_admin_users')) {
            if (Schema::hasColumn('yz_admin_users', 'change_password_at') && Schema::hasColumn('yz_admin_users', 'change_remind')) {
                \app\platform\modules\user\models\AdminUser::where('deleted_at',null)
                    ->update(['change_password_at'=>time(),'change_remind'=>1]);
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
