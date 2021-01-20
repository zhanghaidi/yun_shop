<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/9/22
 * Time: 上午11:09
 */

namespace Yunshop\GoodsPackage\common\models;

use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoodsPackage extends BaseModel
{
    use SoftDeletes;

    const PAGE_SIZE  = 15;

    public $table = 'yz_goods_package';
    public $timestamps = true;
    public $attributes = [];

    protected $hidden = ['deleted_at','updated_at','created_at'];

    /**
     *  不可填充字段.
     *
     * @var array
     */
    protected $guarded = ['deleted_at'];

    public  function rules(){
        return [
            'uniacid' => 'required',
            'title' => 'required|max:50',
            'on_sale_price' => 'numeric',
            'other_package_status' => 'required|integer',
            'other_package_ids' => 'required_if:other_package_status,1',
        ];
    }
    public function atributeNames()
    {
        return [
            'uniacid' => '公众号ID',
            'title' => '套餐标题',
            'on_sale_price' => '优惠价格',
            'other_package_status' => '开启其他搭配套餐',
            'other_package_ids' => '其他套餐'
        ];
    }

    // 通过id查找套餐，本公众号的，未删除的，并且是开启状态的
    public static function getOpenGoodsPackageById($id){
        return self::uniacid()->where('id','=',$id)->where('status','=','1')
            ->with(['hasManyCategory' => function ($categoryQuery) {
                $categoryQuery->where('uniacid', '=', \YunShop::app()->uniacid);
            }])->with(['hasManyCarousel' => function ($carouselQuery) {
                $carouselQuery->where('uniacid', '=', \YunShop::app()->uniacid);
            }])
            ->first();
    }

    //通过id查询一个套餐的所有数据
    public static function getGoodsPackageById($id)
    {
        return self::uniacid()->where('id', '=', $id)
            ->with(['hasManyCategory' => function ($categoryQuery) {
                $categoryQuery->where('uniacid', '=', \YunShop::app()->uniacid);
            }])->with(['hasManyCarousel' => function ($carouselQuery) {
                $carouselQuery->where('uniacid', '=', \YunShop::app()->uniacid);
            }])
            ->first();
    }

    // 关联套餐栏目表，一对多
    public function hasManyCategory()
    {
        return $this->hasMany(GoodsPackageCategory::class, 'category_package_id', 'id');
    }

    // 关联套餐幻灯片表
    public function hasManyCarousel()
    {
        return $this->hasMany(GoodsPackageCarousel::class, 'carousel_package_id', 'id');
    }
}