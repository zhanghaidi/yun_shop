<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/6/14
 * Time: 下午3:57
 */

namespace Yunshop\Exhelper\common\models;


class Goods extends \app\common\models\Goods
{
    public function hasOneShort()
    {
        return $this->hasOne(Short::class, 'goods_id', 'id');
    }

    public function scopeSearch($query, $filters)
    {
        $query->uniacid();

        if (!$filters) {
            return;
        }

        foreach ($filters as $key => $value) {
            switch ($key) {
                case 'keyword':
                    $query->where('title', 'LIKE', "%{$value}%");
                    break;
                case 'status':
                    $query->where('status', $value);
                    break;
                case 'category':
                    if(array_key_exists('parentid', $value) || array_key_exists('childid', $value) || array_key_exists('thirdid', $value)){
                        $id = $value['parentid'] ? $value['parentid'] : '';
                        $id = $value['childid'] ? $value['childid'] : $id;
                        $id = $value['thirdid'] ? $value['thirdid'] : $id;

                        $query->select([
                            'yz_goods.*',
                            'yz_goods_category.id as goods_category_id',
                            'yz_goods_category.goods_id as goods_id',
                            'yz_goods_category.category_id as category_id',
                            'yz_goods_category.category_ids as category_ids'
                        ])->join('yz_goods_category', 'yz_goods_category.goods_id', '=', 'yz_goods.id')->whereRaw('FIND_IN_SET(?,category_ids)', [$id]);
                    } elseif(strpos($value, ',')){
                        $scope = explode(',', $value);
                        $query->select([
                            'yz_goods.*',
                            'yz_goods_category.id as goods_category_id',
                            'yz_goods_category.goods_id as goods_id',
                            'yz_goods_category.category_id as category_id',
                            'yz_goods_category.category_ids as category_ids'
                        ])->join('yz_goods_category', function($join) use ($scope){
                            $join->on('yz_goods_category.goods_id', '=', 'yz_goods.id')
                                ->whereIn('yz_goods_category.category_id', $scope);
                        });
                    } else{
                        $query->select([
                            'yz_goods.*',
                            'yz_goods_category.id as goods_category_id',
                            'yz_goods_category.goods_id as goods_id',
                            'yz_goods_category.category_id as category_id',
                            'yz_goods_category.category_ids as category_ids'
                        ])->join('yz_goods_category', function($join) use ($value){
                            $join->on('yz_goods_category.goods_id', '=', 'yz_goods.id')
                                ->whereRaw('FIND_IN_SET(?,category_ids)', [$value]);
                        });
                    }
                    break;
                case 'short':
                    if ($value == 1) {
                        $query->whereHas('hasOneShort', function($short)use($value) {
                            $short->select();
                        });
                    }
                    break;
                default:
                    break;
            }
        }
    }
}