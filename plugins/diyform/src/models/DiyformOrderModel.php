<?php

namespace Yunshop\Diyform\models;

use app\common\models\BaseModel;

/**
 * Author: lin
 * Date: 2020/02/25
 * Time: 上午9:59
 */
class DiyformOrderModel extends BaseModel
{
    public $table = 'yz_diyform_order';
    public $timestamps = true;
    protected $guarded = [''];

    public static function getDiyFormByGoodsId($goods_id)
    {
        $model = self::uniacid()->where('goods_id',$goods_id);

        return $model;
    }

    public function member()
    {
        return $this->hasOne('app\common\models\Member','uid','member_id');
    }

    public function diyform()
    {
        return $this->hasOne('Yunshop\Diyform\models\DiyformTypeModel','id','form_id');
    }


    public function getFormDataAttribute()
    {
        if (!isset($this->formData)) {
            $this->formData = iunserializer($this->data);
        }
        return $this->formData;
    }


    public function relationSave($goodsId, $data, $operate)
    {
        if (!$goodsId) {
            return false;
        }
        if (!$data) {
            return false;
        }

        $CourseModel = self::getDiyFormByGoodsId($goodsId)->first();

        if(!$CourseModel){
            $CourseModel = self::getModel($goodsId,$operate);
        }
        //判断deleted
        if ($operate == 'deleted') {
            return $CourseModel->delete();
        }

        $data['goods_id'] = $goodsId;
        $data['uniacid'] = \YunShop::app()->uniacid;
        //dd($data);
        $CourseModel->fill($data);

        $bool = $CourseModel->save();


        return $bool;
    }

}