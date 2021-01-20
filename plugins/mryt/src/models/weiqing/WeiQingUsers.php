<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/6/12
 * Time: 下午2:01
 */

namespace Yunshop\Mryt\models\weiqing;


use app\common\models\BaseModel;

class WeiQingUsers extends BaseModel
{
    public $table = 'users';
    protected $guarded = [''];
    protected $primaryKey = 'uid';
    public $timestamps = false;

    public static function getUserByUserName($username)
    {
        return self::select()->byUserName($username);
    }

    public static function getUserByUid($uid)
    {
        return self::select()->byUid($uid);
    }

    public function scopeByUserName($query, $username)
    {
        return $query->where('username', $username);
    }

    public function scopeByUid($query, $uid)
    {
        return $query->where('uid', $uid);
    }

    public static function updateType($uid)
    {
        $user = self::getUserByUid($uid)->first();
        if ($user) {
            $user->type = 3;
            $user->save();
        }
    }
}