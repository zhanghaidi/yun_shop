<?php

namespace Yunshop\LeaseToy\models;

use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use app\common\exceptions\AppException;

/**
* Author: 芸众商城 www.yunzshop.com
* Date: 2018/2/28
* Time: 17:13
*/
class LeaseTermModel extends BaseModel
{
    use SoftDeletes;

    public $table = 'yz_lease_toy_term_set';

    protected $guarded = [''];

    protected $attributes = [
        'sequence' => 0,
        'term_days' => 0,
        'term_discount' => 0,
    ];

    public static function getList()
    {
        return self::uniacid()->orderBy('sequence', 'desc')->get();
    }

    /**
     * 获取天数最接近的一条数据
     * @param  [type] $day [description]
     * @return [type]      [description]
     */
    public static function getDays($day = 0)
    {

        $term_days = self::uniacid()->select('term_days')->orderBy('term_days')->first();

        if ($day < $term_days->term_days) {
            return [
                'days' => $day,
                'term_discount' => 0
            ];
        }

        $max = self::uniacid()->where('term_days', '>=', $day)->orderBy('term_days')->first();
        $min = self::uniacid()->where('term_days', '<=', $day)->orderBy('term_days', 'desc')->first();

        if (empty($max) || empty($min)) {
            return empty($max)? $min->toArray() : $max->toArray();
        }

        $result =  ($day - $min->term_days) > ($max->term_days - $day) ? $max : $min;
        
        return $result->toArray();
    }
    public static function deletedTerm($id)
    {
        return self::where('id', $id)
            ->delete();
    }

    public static function apiList()
    {
        return self::uniacid()->select('id', 'term_name', 'term_days', 'term_discount')->orderBy('sequence', 'desc');
    }

    /**
     * 定义字段名
     * @return [type] [description]
     */
    public function atributeNames() {
        return [
            'sequence' => '排序',
            'term_name' => '名称',
            'term_days' => '天数',
            'term_discount' => '优惠',
        ];
    }

    /**
     * 字段规则
     * @return [type] [description]
     */
    public function rules()
    {
        return [
            'sequence' => 'required|numeric',
            'term_name' => 'required|max:50',
            'term_days' => 'required|numeric',
            'term_discount' => 'required|numeric|between:0,100',
        ];
    }
}