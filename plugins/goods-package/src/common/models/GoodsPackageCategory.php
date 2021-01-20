<?php
namespace Yunshop\GoodsPackage\common\models;
use Illuminate\Database\Eloquent\SoftDeletes;
use app\common\models\BaseModel;

class GoodsPackageCategory extends BaseModel
{
    use SoftDeletes;
    public $table = 'yz_goods_package_category';

    protected $hidden = ['deleted_at','updated_at','created_at'];

    protected $guarded = ['deleted_at'];

    public  function rules(){
        return [
            'uniacid' => 'required|integer',
            'category_sort' => 'required|integer',
            'category_name' => 'required|max:50',
            'category_goods_ids' => 'required',
        ];
    }
    public function atributeNames()
    {
        return [
            'uniacid' => '公众号ID',
            'category_sort' => '栏目排序',
            'category_name' => '栏目名称',
            'category_goods_ids' => '栏目商品',
        ];
    }

}
