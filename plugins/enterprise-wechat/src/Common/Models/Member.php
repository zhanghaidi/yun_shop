<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/3/6 上午11:29
 * Email: livsyitian@163.com
 */

namespace Yunshop\Sign\Common\Models;


class Member extends \app\common\models\Member
{


    public function sign()
    {
        return $this->hasOne('Yunshop\Sign\Common\Models\Sign', 'member_id', 'uid');
    }


    public function signLog()
    {
        return $this->hasMany('Yunshop\Sign\Common\Models\SignLog', 'member_id', 'uid');
    }


    public function scopeRecords($query)
    {
        return $query->select('uid', 'avatar', 'nickname', 'realname', 'mobile', 'createtime');
            /*->with(['yzMember' => function($query) {
                $query->select('member_id', 'group_id', 'level_id', 'is_black')
                    ->with([
                        'group' => function($query) {
                            $query->select('id', 'group_name')->uniacid();
                        },
                        'level' => function($query) {
                            $query->select('id', 'level_name')->uniacid();
                        }
                    ]);
                }
            ]);*/
    }


}
