<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/12/5
 * Time: 14:51
 */

namespace Yunshop\Designer\models;


use app\common\models\BaseModel;

class GoodsGroupGoods extends BaseModel
{

    protected $table = 'yz_goods_group_goods';

    /*
     * 搜索商品组的商品
     */
    public function GetGroupGoodsId($group_id){
        return self::uniacid()->where('group_id',$group_id)->get();
    }


    /*
     * 删除商品组的商品
     */
    public function DelGroupGoods($group_id){
        return self::uniacid()->where('group_id', $group_id)->delete();
    }

    /*
     * 查询首页商品组商品
     */
    public function GetGroupGoods($group_id){
        return self::uniacid()->where('group_id',$group_id)->paginate(10);
    }

    /*
     * 分页
     */
    public function GetPageGoods($group_id){
        return self::select(['goods','Identification'])
            ->uniacid()
            ->where('group_id',$group_id);

    }

    public function scopeWherePageType($query, $page_type)
    {
        return $query->whereRaw('FIND_IN_SET(?,group_type)', [(int)$page_type]);
    }

    /*
     * 获取当前页的所有商品组
     */
    public function GetGroup(){
        return self::uniacid()//查询当前页的所有商品组
        ->wherePageType($this->page_type)
            ->DISTINCT(['group_id'])
            ->get(['group_id']);
    }
}