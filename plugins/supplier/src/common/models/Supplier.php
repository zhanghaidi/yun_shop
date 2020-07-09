<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/17
 * Time: 下午4:21
 */
namespace Yunshop\Supplier\common\models;

use app\common\models\Address;
use app\common\models\BaseModel;
use app\common\models\Street;
use Yunshop\Supplier\common\Observer\SupplierObserver;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Supplier
 * @package Yunshop\Supplier\common\models
 * @property int member_id
 * @property string username
 * @property string password
 * @property string realname
 * @property string mobile
 * @property int status
 * @property int uniacid
 * @property string salt
 * @property string product
 * @property string remark
 * @property int uid
 * @property string logo
 * @property string company_bank
 * @property string company_ali
 * @property string ali
 * @property string wechat
 * @property int diyform_data_id
 * @property string bank_username
 * @property string bank_of_accounts
 * @property string opening_branch
 * @property string company_ali_username
 * @property string ali_username
 * @property string province_name
 * @property string city_name
 * @property string district_name
 * @property int grade
 * @property string store_name
 */
class Supplier extends BaseModel
{
    public $table = 'yz_supplier';
    protected $guarded = [''];
    protected $search_fields = ['id', 'username'];
    protected $appends = ['supplier_id'];

    const PLUGIN_ID = 92;

    public function getSupplierIdAttribute()
    {
        return $this->attributes['supplier_id'] = $this->attributes['id'];
    }

    //此方法是获取地址，有用别修改
    public function getFullAddressAttribute()
    {
        $areaList = Address::whereIn('id', [$this->province_id, $this->city_id, $this->district_id])->pluck('areaname');
        $street_name = Street::select('areaname')->where('id',$this->street_id)->value('areaname');
        $areaList->push($street_name);
        return $areaList->push($this->address)->implode(' ');
    }

    /**
     * @name 获取供应商列表
     * @author yangyang
     * @param null $params
     * @param null $status
     * @return mixed
     */
    public static function getSupplierList($params = null, $status = null)
    {
        $list = Supplier::builder()->search($params)->status($status)->orderBy('id', 'desc');
        return $list;
    }

    /**
     * @name 通过供应商id获取供应商信息
     * @author yangyang
     * @param $supplier_id
     * @param null $status
     * @return mixed
     */
    public static function getSupplierById($supplier_id, $status = null)
    {
        $supplier = Supplier::builder()->supplierId($supplier_id)->status($status)->first();
        return $supplier;
    }

    /**
     * @name 通过会员id获取供应商信息
     * @author yangyang
     * @param $member_id
     * @return mixed
     */
    public static function getSupplierByMemberId($member_id, $status = null)
    {
        $supplier = Supplier::builder()->memberId($member_id)->status($status)->first();
        return $supplier;
    }

    public static function getSupplierByUid($uid)
    {
        return self::builder()->byUid($uid);
    }

    public function scopeByUid($query, $uid)
    {
        return $query->where('uid', $uid);
    }

    /**
     * @name 通过账号获取供应商信息
     * @author yangyang
     * @param $username
     * @return mixed
     */
    public static function getSupplierByUsername($username)
    {
        $supplier = Supplier::builder()->uniacid()->username($username)->status(1)->first();
        return $supplier;
    }

    public static function getSupplierListByMemberIds($member_ids)
    {
        return Supplier::builder()->status(1)->uniacid()->whereIn('member_id', $member_ids->toArray())->orderBy('id', 'desc')->get();
    }

    /**
     * @name 构造器
     * @author yangyang
     * @param null $params
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public static function builder()
    {
        $builder = Supplier::with(
            [
                'hasOneMember',
                'hasOneWqUser' => self::wqUserBuilder()
            ]
        );
        return $builder;
    }

    /**
     * @name 关联会员表
     * @author yangyang
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function hasOneMember()
    {
        return $this->hasOne(\app\backend\modules\member\models\Member::class, 'uid', 'member_id');
    }

    public function hasOneWqUser()
    {
        return $this->hasOne(WeiQingUsers::class, 'uid', 'uid');
    }

    private static function wqUserBuilder()
    {
        return function ($query) {
            return $query->select('uid', 'username');
        };
    }

    /**
     * @name 供应商id查询
     * @author yangyang
     * @param $query
     * @param $supplier_id
     * @return mixed
     */
    public function scopeSupplierId($query, $supplier_id)
    {
        return $query->where('id', $supplier_id);
    }

    /**
     * @name 会员id查询
     * @author yangyang
     * @param $query
     * @param $member_id
     * @return mixed
     */
    public function scopeMemberId($query, $member_id)
    {
        return $query->where('member_id', $member_id);
    }

    /**
     * @name 账号查询
     * @author yangyang
     * @param $query
     * @param $username
     * @return mixed
     */
    public function scopeUsername($query, $username)
    {
        return $query->where('username', $username);
    }

    /**
     * @name 状态查询
     * @author yangyang
     * @param $query
     * @param null $status
     * @return mixed
     */
    public function scopeStatus($query, $status = null)
    {
        if (isset($status)) {
            return $query->where('status', $status);
        }
        return $query;
    }

    /**
     * @name 检索条件
     * @author yangyang
     * @param $query
     * @param $params
     * @return mixed
     */
    public function scopeSearch($query, $params)
    {

        $query->uniacid();
        if (!$params) {
            return $query;
        }
        if ($params['member_id']) {
            $query->where('member_id', 'like', '%' . $params['member_id'] . '%');
        }
        if ($params['supplier']) {
            $query->where('username', 'like', '%' . $params['supplier'] . '%');
        }
        if ($params['supplier_id']) {
            $query->where('id', $params['supplier_id']);
        }
        if ($params['member']) {
            $query->whereHas('hasOneMember', function ($member) use ($params) {
                $member = $member->select('uid', 'nickname', 'realname', 'mobile', 'avatar')
                    ->where('realname', 'like', '%' . $params['member'] . '%')
                    ->orWhere('mobile', 'like', '%' . $params['member'] . '%')
                    ->orWhere('nickname', 'like', '%' . $params['member'] . '%')
                    ->orWhere('uid', 'like', '%' . $params['member'] . '%');
            });
        }
        return $query;
    }

    /**
     * 定义字段名
     *
     * @return array */
    public  function atributeNames() {
        return [
            'username'  => '用户名',
            'member_id'  => '微信号',
            'password'  => '密码',
        ];
    }

    /**
     * 字段规则
     *
     * @return array */
    public  function rules()
    {
        return [
            'username'  => [
                'alpha_num',
                Rule::unique($this->table)->where('uniacid', \YunShop::app()->uniacid)->where('status',1)->ignore($this->id)],
            'member_id'  => [
                Rule::unique($this->table)->where('status',1)->ignore($this->id)
            ],
            'password'  => 'required'
        ];
    }

    public function validationMessages()
    {
        return array_merge(parent::validationMessages(),[
            'alpha'=>'用户名必须是中文、数字、字母',
        ]);
    }

    /**
     * @name 供应商密码加密
     * @author yangyang
     * @param $password
     * @param $salt
     * @return string
     */
    public static function user_hash($password, $salt)
    {
        //$config = \YunShop::app()['config']['setting']['authkey'];
        //$password = "{$password}-{$salt}-{$config}";
        $password = "{$password}-{$salt}";
        return sha1($password);
    }

    public static function boot()
    {
        parent::boot();

        static::observe(new SupplierObserver());
        static::addGlobalScope(function (Builder $builder) {
            $builder->uniacid();
        });
    }

    public static function addWeiqingTables($username, $password)
    {
        $uid = user_register(array('username' => $username, 'password' => $password), '');
        if (is_array($uid) || $uid == 0) {
            return $uid;
        }

        UniAccountUser::AddUniAccountUser($uid);

        WeiQingUsers::updateType($uid);

        (new UsersPermission())->addUsersPermission($uid);
        return $uid;
    }
}