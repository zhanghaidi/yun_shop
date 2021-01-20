<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2018/10/26
 * Time: 上午7:15
 */

namespace Yunshop\Mryt\models;


use app\common\models\BaseModel;

class MrytLevelUpgradeModel extends BaseModel
{
    public $table = 'yz_mryt_level_upgrade';

    public static function getUpgradeByLevelId($level_id)
    {
        return self::uniacid()
            ->where('level_id', $level_id)
            ->first();
    }

    public static function deleteUpgradeByLevelId($level_id)
    {
        return self::uniacid()
            ->where('level_id', $level_id)
            ->delete();

    }
}