<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2019/4/4
 * Time: 11:02
 */

namespace Yunshop\Supplier\common\models;


use app\common\models\BaseModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class Insurance extends BaseModel
{
    use SoftDeletes;

    public $table = 'yz_insurance_policy';
    protected $guarded = [''];
    protected $dates = ['delete_at'];
    public $appends = ['lost_time'];

    public function rules()
    {

        return [
            'id'                        => '',
            'uniacid'                   => '',
            'supplier_id'               => '',
            'serial_number'             => '',
            'shop_name'                 => 'required|between:2,30',
            'insured'                   => '',
            'identification_number'     => '',
            'phone'                     => 'regex:/^1\d{10}$/',
            'province_id'               => '',
            'city_id'                   => '',
            'district_id'               => '',
            'street_id'                 => '',
            'address'                   => '',
            'insured_property'          => '',
            'customer_type'             => '',
            'insured_amount'            => '',
            'guarantee_period'          => '',
            'premium'                   => '',
            'insurance_coverage'        => '',
            'additional_glass_risk'     => '',
            'insurance_company'         => '',
            'note'                      => '',
            'company_id'                => '',
            'pay_type'                  => '',
            'pay_time'                  => '',
        ];
    }

    /**
     * 定义字段名
     *
     * @return array
     */
    public function atributeNames()
    {
        return [
            'shop_name'                 => '店面名称',
            'insured'                   => '被保人',
            'identification_number'     => '序号',
            'phone'                     => '联系方式',
            'province_id'               => '省',
            'city_id'                   => '市',
            'district_id'               => '区',
            'street_id'                 => '街',
            'address'                   => '详细地址',
            'insured_property'          => '保险财产',
            'customer_type'             => '客户类型',
            'insured_amount'            => '保证金额',
            'guarantee_period'          => '保证期',
            'premium'                   => '保费',
            'additional_glass_risk'     => '附加玻璃险（份）',
            'insurance_coverage'        => '投保险种',
            'insurance_company'         => '保险公司',
            'note'                      => '备注',
            'company_id'                => '保险公司id',
            'pay_type'                  => '支付类型',
            'pay_time'                  => '支付时间',
        ];
    }

    public function getLostTimeAttribute()
    {
        return $this->attributes['lost_time'] = Carbon::createFromFormat('Y-m-d', date('Y-m-d', $this->attributes['pay_time']))->addYear($this->attributes['guarantee_period'])->timestamp;
    }

    public function queryData($data)
    {
        $insurance = self::uniacid()->with(['supplier', 'hasOneCompany']);

        if (!empty($data['supplier_id'])){
            $insurance->where('supplier_id',$data['supplier_id']);
        }
        if (!empty($data['supplier_number'])){
            $insurance->when('supplier_number', function ($query) use ($data) {
                return $query->whereHas('supplier', function ($query) use ($data) {
                    return $query->where('username','like','%'.$data['supplier_number'].'%');
                });
            })->get();
        }
        if (!empty($data['member_id'])){
            $insurance->when('member_id', function ($query) use ($data) {
                return $query->whereHas('supplier', function ($query) use ($data) {
                    return $query->where('member_id',$data['member_id']);
                });
            })->get();
        }
        if (!empty($data['shop_name'])){
            $insurance->where('shop_name',$data['shop_name']);
        }
        if (!empty($data['insured_person'])){
            $insurance->where('insured','like','%'.$data['insured_person'].'%')
            ->orWhere('phone','like','%'.$data['insured_person'].'%');
        }
        if (!empty($data['member_name'])){
            $insurance->when('member_name', function ($query) use ($data) {
                return $query->whereHas('supplier.hasOneMember', function ($query) use ($data) {
                    return $query->where('nickname','like','%'.$data['member_name'].'%')
                        ->orWhere('realname','like','%'.$data['member_name'].'%')
                        ->orWhere('mobile',$data['member_name']);
                });
            })->get();
        }
        if (!empty($data['serial_number'])){
            $insurance->where('serial_number',$data['serial_number']);
        }
        if (!empty($data['province_id'])){
            $insurance->where('province_id',$data['province_id']);
        }
        if (!empty($data['city_id'])){
            $insurance->where('city_id',$data['city_id']);
        }
        if (!empty($data['district_id'])){
            $insurance->where('district_id',$data['district_id']);
        }
        if (!empty($data['street_id'])){
            $insurance->where('street_id',$data['street_id']);
        }
        if (!empty($data['time_range']['field']) && $data['time_range']['field']){
            $insurance->whereBetween('created_at',[strtotime($data['time_range']['start']),strtotime($data['time_range']['end'])]);
        }

        return $insurance;
    }

    public function memberInsurance($member_id){
       return self::when('supplier_id', function ($query) use ($member_id) {
            return $query->whereHas('supplier', function ($query) use ($member_id) {
                return $query->where('member_id', $member_id);
            });
        })->orderBy('id', 'desc')->get();
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }

    public function hasOneCompany()
    {
        return $this->hasOne(InsuranceCompany::class, 'id', 'company_id');
    }

    /**
     * @name 构造器
     * @author yangyang
     * @param null $params
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public static function builder($params = null)
    {
        $builder = Insurance::with(
            [
                'supplier',
//                'supplier.hasOneMember'
            ]
        );
        return $builder;
    }


}