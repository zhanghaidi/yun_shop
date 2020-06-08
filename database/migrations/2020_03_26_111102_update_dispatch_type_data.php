<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateDispatchTypeData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_dispatch_type')) {
            $dispatchTypes = \app\common\models\DispatchType::get();
            $data = [
                1 => [
                    'code' => 'dispatch',
                    'enable' => 1,
                ], 2 => [
                    'code' => 'self_deliver',
                    'plugin' => 32,
                    'enable' => 1,

                ], 3 => [
                    'code' => 'store_deliver',
                    'plugin' => 32,
                    'enable' => 1,

                ], 4 => [
                    'code' => 'store_deliver',
                    'plugin' => 33,
                    'enable' => 1,

                ], 5 => [
                    'code' => 'deliver_station_self',
                    'plugin' => 1,
                    'enable' => 1,

                ], 6 => [
                    'code' => 'deliver_station_send',
                    'plugin' => 1,
                    'enable' => 1,

                ],
                8 => [
                    'code' => 'package_deliver',
                    'enable' => 1,

                ],
            ];
            foreach ($dispatchTypes as $dispatchType) {
                if($dispatchType['id'] !== 7) { //司机配送暂时不更新
                    $dispatchType->fill($data[$dispatchType['id']]);
                    $dispatchType->save();
                }
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
