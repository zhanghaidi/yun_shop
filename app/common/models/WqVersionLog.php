<?php
/**
 * Created by PhpStorm.
 * User: blank
 * Date: 2020/4/30
 * Time: 9:19
 */

namespace app\common\models;


class WqVersionLog extends BaseModel
{
    protected $table = 'yz_wq_version_log';

    protected $guarded = [];

    protected $attributes = [];


    /**
     * 保存版本号
     * @param $version
     * @return static
     */
    static public function createLog($version)
    {
        return self::create(['version' => $version]);
    }

    /**
     * 是否存在
     * @param $version string 版本号
     * @return bool false:不存在 true:存在
     */
    static public function verifyExist($version)
    {
        $log = self::where('version', $version)->first();


        return is_null($log)?false:true;
    }
}