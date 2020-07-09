<?php

namespace Yunshop\GoodsAssistant\models;

use app\common\models\BaseModel;

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/24
 * Time: 上午11:20
 */
class Import extends  BaseModel
{
    public $table = 'yz_plugin_goods_assistant';
    public $timestamps = false;
    public $attributes = [
        'itemid' => '',
        'source' => '',
        'url' => '',
    ];
    /**
     *  不可填充字段.
     *
     * @var array
     */
    protected $guarded = [''];

    /**
     * 自定义字段名
     * 可使用
     * @return array
     */
    public function atributeNames()
    {
        return [
            'uniacid' => '公众号ID',
            'goods_id' => '商品ID',
            'itemid' => '导入商品itemID',
            'source' => '导入资源',
            'url' => '导入商品URL',
        ];
    }

    /**
     * 字段规则
     * @return array
     */
    public function rules()
    {

        return [
            'uniacid' => 'required|integer',
            'category_id' => 'required|integer|min:1',
            'title' => 'required|max:50',
            'desc' => 'max:255',
            'thumb' => 'string',
        ];
    }

    public static function getInfo($itemId, $source)
    {
        return self::uniacid()->where('itemid', $itemId)->where('source', $source);
    }
}