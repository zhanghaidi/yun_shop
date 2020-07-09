<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/7/11 下午4:05
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace Yunshop\Love\Common\Models;


use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Yunshop\Love\Common\Services\CommonService;

class LoveActivationRecords extends BaseModel
{
    protected $table = 'yz_love_activation';

    protected $guarded = [''];

    public function getDates()
    {
        return ['created_at'];
    }
    /**
     * 设置全局作用域 拼接 uniacid()
     */
    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(
            function(Builder $builder){
                return $builder->uniacid();
            }
        );
    }

    /**
     * 关联会员数据表， N:1
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function member()
    {
        return $this->belongsTo('Yunshop\Love\Common\Models\Member','member_id','uid');
    }

    /**
     * 检索条件
     * @param $query
     * @param $id
     * @return mixed
     */
    public function scopeOfId($query,$id)
    {
        return $query->where('id',$id);
    }

    public function scopeOfOrderSn($query,$orderSn)
    {
        return $query->where('order_sn',$orderSn);
    }

    public function scopeSearch($query,$search)
    {
        if ($search['id']) {
            $query->where('id',$search['id']);
        }
        if ($search['order_sn']) {
            $query->where('order_sn','like',$search['order_sn'].'%');
        }
        if ($search['min_love']) {
            $query->where('actual_activation_love', '>=', $search['min_love']);
        }
        if ($search['max_love']) {
            $query->where('actual_activation_love', '<=', $search['max_love']);
        }
        if ($search['is_time'] == 1) {
            $query->where('created_at', '>', strtotime($search['time']['start']))->where('created_at', '<', strtotime($search['time']['end']));
        }
        return $query;
    }

    public function scopeSearchMember($query,$search)
    {
        return $query->whereHas('member',function($query)use($search) {
            return $query->search($search);
        });
    }


    /**
     * 字段验证规则
     * @return array
     */
    public function rules()
    {
        return [
            'uniacid'                       => 'numeric|integer',
            'member_id'                     => 'numeric|integer',
            'first_order_money'             => 'numeric',
            'first_proportion'              => 'numeric',
            'first_activation_love'         => 'numeric',
            'second_three_order_money'      => 'numeric',
            'second_three_proportion'       => 'numeric',
            'last_upgrade_team_leve_award'  => 'numeric',
            'second_three_fetter_proportion'=> 'numeric',
            'second_three_activation_love'  => 'numeric',
            'sum_activation_love'           => 'numeric',
        ];
    }


    /**
     * 字段名称
     * @return array
     */
    public function atributeNames()
    {
        $love = CommonService::getLoveName();
        return [
            'uniacid'                       => '公众号ID',
            'member_id'                     => '会员ID',
            'first_order_money'             => '一级下线订单总金额',
            'first_proportion'              => '一级下线激活比例',
            'first_activation_love'         => '一级下线激活' . $love,
            'second_three_order_money'      => '二、三级下线订单总金额',
            'second_three_proportion'       => '二、三级下线激活比例',
            'last_upgrade_team_leve_award'  => '最后等级升级奖励' . $love,
            'second_three_fetter_proportion'=> '二、三级下线激活最高束缚比例',
            'second_three_activation_love'  => '二、三级下线激活' . $love,
            'sum_activation_love'           => '激活'. $love . '总和',
        ];
    }

}
