<?php
namespace Yunshop\Diyform\models;

use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/9/23
 * Time: 上午9:59
 */
class DiyformTypeModel extends BaseModel
{
    use SoftDeletes;
    public $table = 'yz_diyform_type';
    public $timestamps = true;
    protected $guarded = [''];
    public $formType;
    protected $appends = ['form_type'];

    public static function getDiyformList()
    {
        $model = self::uniacid();
        return $model;
    }

    public function getFormTypeAttribute()
    {
        if (!isset($this->formType)) {
            $this->formType = iunserializer($this->fields);
        }
        return $this->formType;
    }

    public function hasOneDiyformTypeMemberData()
    {
        return $this->hasOne('Yunshop\Diyform\models\DiyformTypeMemberDataModel', 'form_id', 'id');
    }
}