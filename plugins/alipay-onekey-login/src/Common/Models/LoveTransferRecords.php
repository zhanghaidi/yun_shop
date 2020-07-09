<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/7/8 下午1:11
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace Yunshop\Love\Common\Models;


use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Yunshop\Love\Common\Services\CommonService;

class LoveTransferRecords extends BaseModel
{
    protected $table = 'yz_love_transfer';

    protected $guarded = [''];


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
     * 转让单号检索条件
     * @param $query
     * @param $orderSn
     * @return mixed
     */
    public function scopeOfOrderSn($query,$orderSn)
    {
        return $query->where('order_sn',$orderSn);
    }



    /**
     * 字段验证规则
     * @return array
     */
    public function rules()
    {
        return [
            'uniacid'           => 'numeric|integer',
            'transfer'          => 'numeric|integer',
            'recipient'         => 'numeric|integer',
            'change_value'      => 'numeric|min:0',
            'status'            => 'numeric|integer',
            'order_sn'          => 'max:45',
            'poundage'          => 'numeric|min:0',
            'proportion'        => 'numeric|min:0|max:100',
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
            'uniacid'           => '公众号ID',
            'transfer'          => '转让者',
            'recipient'         => '被转让者',
            'change_value'      => '转让'. $love,
            'status'            => '转让状态',
            'order_sn'          => '转让单号',
            'poundage'          => '转让手续费',
            'proportion'        => '转让手续费比例',
        ];
    }

}
