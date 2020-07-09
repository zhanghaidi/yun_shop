<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/29
 * Time: 14:18
 */

namespace Yunshop\Supplier\supplier\models;


use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Yunshop\Supplier\common\models\Supplier;

class Slide extends BaseModel
{
    use SoftDeletes;

    public $table = 'yz_supplier_slide';
    public $attributes = ['display_order' => 0];

    protected $fillable = [''];
    protected $guarded = [''];

    public static function getSlidesIsEnabled()
    {
        return self::uniacid()
            ->where('enabled','1');
    }

    public function hasOneSupplier()
    {
        return $this->hasOne(Supplier::class, 'uid', 'supplier_uid');
    }
    
    /**
     *  定义字段名
     * 可使
     * @return array */
    public  function atributeNames() {
        return [
            'slide_name'=> '幻灯片名称',
            'display_order'=> '排序',
            'thumb'=> '幻灯片图片',
        ];
    }

    /**
     * 字段规则
     * @return array */
    public  function rules() {
        return [
            'slide_name' => 'required',
            'display_order' => 'required',
            'thumb' => 'required',
        ];
    }

    public static function getSlides()
    {
        return self::uniacid()
            ->where('enabled', 1)
            ->orderBy('display_order', 'decs');

    }

    public static function getSlideByid($id)
    {
        return self::find($id);
    }

    public static function deletedSlide($id)
    {
        return self::where('id', $id)
            ->delete();
    }
}