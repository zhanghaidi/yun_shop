<?php

namespace Yunshop\Diyform\models;

use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/9/23
 * Time: 上午9:59
 */
class DiyformOrderContentModel extends BaseModel
{
    public $table = 'yz_diyform_order_content';
    public $timestamps = true;
    protected $guarded = [''];


    public static function getDiyFormDataByFormId($goods_id)
    {
        $model = self::uniacid()->where('goods_id',$goods_id)->where('member_id',\YunShop::app()->getMemberId());

        return $model;
    }

    public function member()
    {
        return $this->hasOne('app\common\models\Member','uid','member_id');
    }

    public function form()
    {
        return $this->hasOne('Yunshop\Diyform\models\DiyformTypeModel','id','form_id');
    }


}