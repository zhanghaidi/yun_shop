<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/30 0030
 * Time: 下午 1:33
 */

namespace Yunshop\HelpCenter\models;


use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class HelpCenterAddModel extends BaseModel
{
    use SoftDeletes;

    public $table = 'yz_help_center_content';

    //数据库里的字段允许被修改,空数组表示都能被修改
    public $guarded = [''];

    public static function getList()
    {
        $model = self::uniacid();

        return $model;
    }

    public function rules()
    {
        return [
            'title' => 'filled',
            'sort' => 'integer',
            'content' => 'filled',
        ];
    }
}