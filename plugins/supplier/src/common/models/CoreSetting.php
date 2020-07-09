<?php


namespace Yunshop\Supplier\common\models;


use app\common\models\BaseModel;

class CoreSetting extends BaseModel
{
    public $table = 'core_settings';
    protected $guarded = [''];
    public $timestamps = false;
}