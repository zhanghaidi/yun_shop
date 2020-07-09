<?php
/**
 * Created by PhpStorm.
 * User: 芸众网
 * Date: 2019/6/17
 * Time: 10:14
 */

namespace Yunshop\Designer\models;


use app\common\models\BaseModel;
use app\framework\Database\Eloquent\Builder;

class DiyMarketSync extends BaseModel
{
    protected $table = 'diy_market_sync';

    public $timestamps = true;

    protected $guarded = [''];

    /**
     * 检索
     * @param Builder $query
     * @param $params
     * @return Builder
     */
    public function scopeSearch(Builder $query, $params)
    {
        if($params['type_name'] && $params['type_name'] != '全部'){
            $query = $query->where('type',$params['type_name']);
        }
        if($params['page_name'] && $params['page_name'] != '全部'){
            $query = $query->where('page',$params['page_name']);
        }
        if($params['category_name'] && $params['category_name'] != '全部'){
            $query = $query->where('category',$params['category_name']);
        }
        return $query;
    }

}