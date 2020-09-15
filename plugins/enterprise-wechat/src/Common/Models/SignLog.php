<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/3/6 上午11:32
 * Email: livsyitian@163.com
 */

namespace Yunshop\Sign\Common\Models;


use app\common\models\BaseModel;

class SignLog extends BaseModel
{

    protected $table = 'yz_sign_log';

    protected $guarded = [];



    public function scopeRecords($query)
    {
        return $query;
    }



    public function scopeOfUid($query, $member_id)
    {
        return $query->where('member_id', $member_id);
    }







}
