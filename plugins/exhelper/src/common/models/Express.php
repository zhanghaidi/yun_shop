<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/6/16
 * Time: 下午5:09
 */

namespace Yunshop\Exhelper\common\models;


use app\common\models\BaseModel;

class Express extends BaseModel
{
    public $table = 'yz_exhelper_express';
    protected $guarded = [''];
    public $timestamps = false;
    protected $casts = [
        'datas' => 'json'
    ];

    const DEFAULT_SUCCESS = 1;
    const DEFAULT_ERROR = 0;
    const EXPRESS_TYPE = 1;
    const EXPRESS_INDEX_URL = 'plugin.exhelper.admin.express.index';
    const EXPRESS_EDIT_URL = 'plugin.exhelper.admin.express.edit';
    const EXPRESS_ADD_URL = 'plugin.exhelper.admin.express.add';
    const EXPRESS_DEL_URL = 'plugin.exhelper.admin.express.delete';
    const EXPRESS_DEFAULT_URL = 'plugin.exhelper.admin.express.isDefault';

    const SEND_TYPE = 2;
    const SEND_INDEX_URL = 'plugin.exhelper.admin.send.index';
    const SEND_EDIT_URL = 'plugin.exhelper.admin.send.edit';
    const SEND_ADD_URL = 'plugin.exhelper.admin.send.add';
    const SEND_DEL_URL = 'plugin.exhelper.admin.send.delete';
    const SEND_DEFAULT_URL = 'plugin.exhelper.admin.send.isDefault';

    public static function getList($type)
    {
        return self::select()->uniacid()->byType($type);
    }

    public static function getDefault($type)
    {
        return self::select()->uniacid()->byType($type)->byDefault();
    }

    public function scopeByDefault($query)
    {
        return $query->where('isdefault', 1);
    }

    public function scopeByType($query, $type)
    {
        // todo 1 快递单 2 发货单
        return $query->where('type', $type);
    }
}