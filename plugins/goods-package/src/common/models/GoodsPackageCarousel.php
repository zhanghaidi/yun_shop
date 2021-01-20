<?php
namespace Yunshop\GoodsPackage\common\models;
use Illuminate\Database\Eloquent\SoftDeletes;
use app\common\models\BaseModel;

class GoodsPackageCarousel extends BaseModel
{
    use SoftDeletes;

    public $table = 'yz_goods_package_carousel';

    protected $hidden = ['deleted_at','updated_at','created_at'];

    protected $guarded = ['deleted_at'];

    public  function rules(){
        return [
            'uniacid' => 'required',
            'carousel_sort' => 'required',
            'carousel_open_status' => 'required|integer',
        ];
    }
    public function atributeNames()
    {
        return [
            'uniacid' => '公众号ID',
            'carousel_sort' => '幻灯片排序',
            'carousel_open_status' => '幻灯片显示状态',
        ];
    }
}
