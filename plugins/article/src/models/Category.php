<?php
namespace Yunshop\Article\models;

use app\common\models\BaseModel;

class Category extends BaseModel
{
    /*
     * 注意: category 表中保存的 member_level_id_limit 不是 level 等级, 而是 level 等级在数据表 member_level 中的 id 值
     */

    public $table = "yz_plugin_article_category";
    public $timestamps = false;
    protected $guarded = [''];

    /**
     * 自定义字段名
     * @return array
     */
    public function atributeNames()
    {
        return [
            'name'=> '分类名称',
            'member_level_id_limit'=>'会员等级的ID'
        ];
    }

    /**
     * 字段规则
     * @return array
     */
    public function rules()
    {

        return [
            'name' => 'required|string|max:255',
            'member_level_id_limit' => 'required|integer',
        ];
    }

    public static function getCategorys()
    {
        return self::uniacid()->select('id', 'name', 'member_level_id_limit');
    }

    public static function getCategory($id)
    {
        return self::where('id', $id)
        ->first();
    }

    public static function deletedCategory($id)
    {
        return self::where('id', $id)
            ->delete();
    }

    public static function getCategorysByKeyword($keyword)
    {
        return self::uniacid()->where('name','like','%'.$keyword.'%');
    }
}
