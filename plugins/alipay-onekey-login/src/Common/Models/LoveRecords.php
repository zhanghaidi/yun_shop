<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/6/27 上午10:57
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace Yunshop\Love\Common\Models;


use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Yunshop\Love\Common\Services\CommonService;
use Yunshop\Love\Common\Services\ConstService;

class LoveRecords extends BaseModel
{
    protected $table = 'yz_love';

    protected $guarded = [''];

    protected $appends = ['type_name','source_name','value_type_name'];

    /**
     * 关联会员数据表， N:1
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function member()
    {
        return $this->belongsTo('Yunshop\Love\Common\Models\Member','member_id','uid');
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
     * 通过字段 value_type 输出 value_type_name ;
     * @return mixed|string
     */
    public function getValueTypeNameAttribute()
    {
        return static::getValueTypeNameComment($this->attributes['value_type']);
    }

    /**
     * 通过字段 service_type 输出 service_type_name ;
     * @return string
     * @Author yitian */
    public function getSourceNameAttribute()
    {
        return static::getSourceNameComment($this->attributes['source']);
    }

    /**
     * 通过字段 type 输出 type_name ;
     * @return string
     */
    public function getTypeNameAttribute()
    {
        return static::getTypeNameComment($this->attributes['type']);
    }


    /**
     * 输出 type_name 附加字段名称
     * @param $valueType
     * @return mixed|string
     */
    public function getValueTypeNameComment($valueType)
    {
        return isset(static::getValueTypeComment()[$valueType]) ? static::getValueTypeComment()[$valueType]: '';
    }

    /**
     * 输出 source_name 附加字段名称
     * @param $source
     * @return string
     */
    public static function getSourceNameComment($source)
    {
        return isset(static::getSourceComment()[$source]) ? static::getSourceComment()[$source]: '';
    }

    /**
     * 输出 type_name 附加字段名称
     * @param $type
     * @return string
     */
    public static function getTypeNameComment($type)
    {
        return isset(static::getTypeComment()[$type]) ? static::getTypeComment()[$type]: '';
    }

    /**
     * 记录信息搜索
     * @param $query
     * @param $search
     */
    public function scopeSearch($query,$search)
    {
        if ($search['order_sn']) {
            $query->where('relation','like',$search['order_sn'].'%');
        }
        if ($search['source']) {
            $query->ofSource($search['source']);
        }
        if ($search['type']) {
            $query->whereType($search['type']);
        }
        if ($search['search_time']) {
            $query->whereBetween('created_at', [strtotime($search['time']['start']), strtotime($search['time']['end'])]);
        }
    }

    /**
     * 关联 member 搜索
     * @param $query
     * @param $search
     * @return mixed
     */
    public function scopeSearchMember($query,$search)
    {
        return $query->whereHas('member',function($query)use($search) {
            return $query->search($search);
        });
    }


    /**
     * id 主键检索
     * @param $query
     * @param $recordId
     * @return mixed
     */
    public function scopeOfRecordId($query,$recordId)
    {
        return $query->where('id',$recordId);
    }

    /**
     * 订单号检索
     * @param $query
     * @param $orderSn
     * @return mixed
     */
    public function scopeOfOrderSn($query,$orderSn)
    {
        return $query->where('relation',$orderSn);
    }

    /**
     * 业务类型检索
     * @param $query
     * @param $source
     * @return mixed
     */
    public function scopeOfSource($query,$source)
    {
        return $query->where('source',$source);
    }

    /**
     * 变动值类型检索
     * @param $query
     * @param $valueType
     * @return mixed
     */
    public function scopeOfValueType($query,$valueType)
    {
        return $query->where('value_type',$valueType);
    }

    /**
     * 会员ID检索
     * @param $query
     * @param $memberId
     * @return mixed
     */
    public function scopeOfMemberId($query,$memberId)
    {
        return $query->where('member_id',$memberId);
    }

    /**
     * @param $query
     * @param $operator
     * @return mixed
     */
    public function scopeOfOperator($query,$operator)
    {
        return $query->where('operator',$operator);
    }

    /**
     * 获取 value_type 对应的名称数组
     * @return array
     */
    private static function getValueTypeComment()
    {
        return (new ConstService(static::getLoveName()))->valueTypeComment();
    }

    /**
     * 获取 type 字段对应的名称数组
     * @return array
     */
    private static function getTypeComment()
    {
        return (new ConstService(static::getLoveName()))->typeComment();
    }

    /**
     * 获取 source 字段对应的名称数组
     * @return array
     */
    private static function getSourceComment()
    {
        return (new ConstService(static::getLoveName()))->sourceComment();
    }

    /**
     * 获取爱心值自定义名称
     * @return mixed|string
     */
    private static function getLoveName()
    {
        return CommonService::getLoveName();
    }

}