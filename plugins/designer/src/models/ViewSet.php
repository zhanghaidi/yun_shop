<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2019/1/24
 * Time: 11:30
 */

namespace Yunshop\Designer\models;

use app\common\models\BaseModel;
use app\common\scopes\UniacidScope;
use Illuminate\Validation\Rule;

class ViewSet extends BaseModel
{
    protected $table = 'yz_designer_view_set';
    protected $search_fields = ['names', 'type'];

    public static function boot()
    {
        parent::boot();
        self::observe(IncreaseRecordsObserver::class);
        self::addGlobalScope(new UniacidScope());
    }


    public function rules()
    {
        return [
            'names' => 'required',
            'type' => 'required',
            'path' => ''

        ];
    }

    public function atributeNames()
    {
        return [
            'names' => '页面名称',
            'type' => '页面类型',
            'path' => ''
        ];
    }

}