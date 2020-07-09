<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/6/27 上午10:50
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace Yunshop\Love\Common\Models;


use app\common\scopes\UniacidScope;


/**
 * Class Member
 * @package Yunshop\Love\Common\Models
 *
 * @method static ofUid($uid)
 * @method records()
 * @method withLove()
 *
 */
class Member extends \app\common\models\Member
{
    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(new UniacidScope);
    }

    /**
     * 关联爱心值会员表，1：1
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function love()
    {
        return $this->hasOne('Yunshop\Love\Common\Models\MemberLove','member_id','uid');
    }

    /**
     * 会员 uid 检索
     *
     * @param $query
     * @param $Uid
     * @return mixed
     */
    public function scopeOfUid($query,$Uid)
    {
        return $query->where('uid',$Uid);
    }

    /**
     * 查询记录
     * @param $query
     */
    public function scopeRecords($query)
    {
        $query->select('uid', 'avatar', 'nickname', 'realname', 'mobile', 'createtime')
            ->whereHas('yzMember', function ($query) {
                $query->whereNull('deleted_at');
            })
            ->with('love')
            ->with([
                'yzMember' => function($query) {
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
            ]);
    }


    /**
     * 会员信息条件搜索
     * @param $query
     * @param array $search
     * @return mixed
     */
    public function scopeSearch($query,$search)
    {
        if ($search['member_id']) {
            $query->where('uid',$search['member_id']);
        }
        if ($search['realname']) {
            $query->searchLike($search['realname']);
        }
        if ($search['member_level'] || $search['member_group']) {
            $query->whereHas('yzMember', function ($yzMember) use ($search) {

                if ($search['member_level']) {
                    $yzMember->where('level_id', $search['member_level']);

                }
                if ($search['member_group']) {
                    $yzMember->where('group_id', $search['member_group']);
                }

            });
        }
        return $query;
    }


    /**
     * 关联会员爱心值检索
     * @param $query
     * @return mixed
     */
    public function scopeWithLove($query)
    {
        return $query->with(['love' => function($query) {
            /**
             * @var $query MemberLove
             */
            $query->records();
        }]);
    }


    /**
     * 会员爱心值数据搜索
     * @param $query
     * @param array $search
     * @return mixed
     */
    public function scopeSearchLove($query,array $search)
    {
        if ($search['min_love'] || $search['max_love']) {
            return $query->whereHas('love', function ($query) use ($search) {
                /**
                 * @var $query MemberLove
                 */
                $query->search($search);
            });
        }
        return $query;
    }


}
