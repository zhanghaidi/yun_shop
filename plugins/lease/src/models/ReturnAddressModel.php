<?php

namespace Yunshop\LeaseToy\models;

use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
* Author: 芸众商城 www.yunzshop.com
* Date: 2017/2/27
* Time: 15:06
*/
class ReturnAddressModel extends BaseModel
{
    use SoftDeletes;
    public $table = 'yz_lease_toy_return_address';

    protected $guarded = [''];

    protected $attributes = [''];

    public static function getAddressList($search = [])
    {   

        return self::uniacid()->orderBy('id', 'asc');
    }


    public static function getReturnAddressid($id)
    {
        return self::find($id);
    }

     /**
     * @param $id
     * @return mixed
     */
    public static function deletedReturnAddressid($id)
    {
        return self::where('id', $id)
            ->delete();
    }
    /**
     * 修改默认地址
     */
    public static function reviseDefault()
    {
        self::where('is_default', 1)->update(['is_default' => 0]);
    }


     /**
     * 定义字段名
     *
     * @return array */
    public  function atributeNames() {
        return [
            'contact_name'  => '联系人',
            'mobile'    => '联系方式',
            'province_id'  => '省份',
            'city_id'      => '城市',
            'district_id'  => '区域',
            'address'   => '详细地址',
        ];
    }

    /**
     * 字段规则
     *
     * @return array */
    public  function rules()
    {
        return [
            'contact_name'  => 'required|max:50',
            'mobile'    => ['required','regex:/^1\d{10}$/'],
            'province_id'  => 'required|integer',
            'city_id'      => 'required|integer',
            'district_id'  => 'required|integer',
            'address'  => 'required',
        ];
    }

}