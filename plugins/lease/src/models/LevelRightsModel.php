<?php

namespace Yunshop\LeaseToy\models;

use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
* Author: 芸众商城 www.yunzshop.com
* Date: 2018/3/1
* Time: 15:20
*/
class LevelRightsModel extends BaseModel
{
    use SoftDeletes;

    public $table = 'yz_lease_toy_level_rights';

    protected $guarded = [''];

    protected $attributes = [
        'rent_free' => 0,
        'deposit_free' => 0,
    ];

    public static function getRights($levelId)
    {

        return self::uniacid()->select('id','level_id', 'rent_free', 'deposit_free')->where('level_id', $levelId)->first();
    }

    /**
     * 获取对象模型
     * @param  [type] $levelId [description]
     * @return [type]          [description]
     */
    public static function getModel($levelId)
    {
        $model = false;
       
        $model = static::where(['level_id' => $levelId])->first();

        !$model && $model = new static;

        return $model;
    }

     /**
     * 定义字段名
     * @return [type] [description]
     */
    public function atributeNames() {
        return [
            'rent_free' => '免租金',
            'deposit_free' => '免押金',
        ];
    }

    /**
     * 字段规则
     * @return [type] [description]
     */
    public function rules()
    {
        return [
            'rent_free' => 'required|numeric',
            'deposit_free' => 'required|numeric',
        ];
    }
}
