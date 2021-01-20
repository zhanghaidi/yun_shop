<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/9/13
 * Time: 下午3:55
 */

namespace Yunshop\Mryt\store\models;


use app\common\exceptions\AppException;
use app\common\models\BaseModel;
use app\backend\modules\member\models\Member;
use Yunshop\Mryt\models\weiqing\WeiQingUsers;
use Illuminate\Database\Eloquent\Builder;


/**
 * Class StoreApply
 * @package Yunshop\Mryt\common\models
 * @property int validity
 * @property int order_id
 */
class StoreApply extends BaseModel
{
    public $table = 'yz_store_apply';
    public $timestamps = true;
    protected $guarded = [''];
    protected $search_fields = ['status_name'];
    protected $casts = [
        'information' => 'json'
    ];

    const PAGE_SIZE = 20;
    const ADOPT = 1;
    const REJECT = -1;

    public static function getStoreApplyList($search)
    {
        return self::select()->search($search);
    }

    public static function getStoreApplyById($id)
    {
        return self::select()
            ->with([
                'order' => function ($order) {
                    $order->select(['id', 'order_sn']);
                }
            ])
            ->whereId($id);
    }

    public static function getStoreApplyByUid($uid)
    {
        return self::select()->byUid($uid);
    }

    public static function getStoreApplyByUsername($username)
    {
        return self::select('id')->byUsername($username);
    }

    public function scopeByUsername($query, $username)
    {
        return $query->where('username', $username);
    }

    public function scopeByUid($query, $uid)
    {
        return $query->where('uid', $uid);
    }

    public function hasOneMember()
    {
        return $this->hasOne(Member::class, 'uid', 'uid');
    }

    public function order()
    {
        return $this->hasOne(Order::class, 'id', 'order_id');
    }

    public function scopeSearch($query, $search)
    {
        // 会员ID搜索
        if ($search['uid']) {
            $query->byUid($search['uid']);
        }
        // 会员昵称姓名电话搜索
        if ($search['member']) {
            $query->whereHas('hasOneMember', function($memberBuilder)use($search){
                $memberBuilder->select('uid', 'nickname', 'realname', 'mobile', 'avatar')
                    ->where('realname', 'like', '%' . $search['member'] . '%')
                    ->orWhere('mobile', 'like', '%' . $search['member'] . '%')
                    ->orWhere('nickname', 'like', '%' . $search['member'] . '%');
            });
        }
        // 申请时间
    }

    public function getStatusNameAttribute()
    {
        if ($this->status == 0) {
            return '待审核';
        } else if ($this->status == 1) {
            return '审核通过';
        } else {
            return '驳回审核';
        }
    }

    /**
     * @name 验证会员是否申请，以及申请的状态
     * @author
     * @param $uid
     * @throws AppException
     */
    public static function verifyMemberHisApplyStatus($uid)
    {
        $apply_store = self::getStoreApplyByUid($uid)->first();
        if ($apply_store) {
            if ($apply_store->status == 0) {
                throw new AppException('已提交申请,等待审核中');
            } else {
                throw new AppException('已通过申请');
            }
        }
    }

    /**
     * @name 验证用户名唯一
     * @author
     * @param $username
     * @throws AppException
     */
    public static function verifyUsernameSole($username)
    {
        $weiqing_user = WeiQingUsers::getUserByUserName($username)->first();
        $apply_user = StoreApply::getStoreApplyByUsername($username)->first();
        if ($apply_user || $weiqing_user) {
            throw new AppException('用户名重复');
        }
    }

    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(function (Builder $builder) {
            $builder->uniacid();
        });
    }
}