<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/8/1
 * Time: 下午5:19
 */

namespace Yunshop\Mryt\store\models;


use app\common\exceptions\ShopException;
use app\common\helpers\QrCodeHelper;
use app\common\models\Address;
use app\common\models\BaseModel;
use app\frontend\modules\goods\models\Comment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use app\backend\modules\member\models\Member;
use Yunshop\Mryt\store\models\StoreOrder;
use Yunshop\Mryt\store\models\StoreSetting;
use Yunshop\Mryt\store\services\GetStoreStatisticService;
use Illuminate\Support\Facades\DB;

/**
 * Class Store
 * @package Yunshop\Mryt\common\models
 * @property Collection storeSettings
 * @property int uid
 * @property int validity
 * @property int validity_status
 * @property string store_name
 * @property Collection storeVerificationClerk
 */
class Store extends BaseModel
{
    public $table = 'yz_store';
    public $timestamps = true;
    static protected $needLog = true;
    protected $guarded = [''];
    protected $search_fields = ['store_name'];
    const PLUGIN_ID = 32;
    const CASHIER_PLUGIN_ID = 31;
    const PAGE_SIZE = 10;
    protected $appends = ['full_address', 'black_obj', 'dispatch', 'average_score', 'order_total', 'hide_obj', 'validity_status_name'];
    protected $hidden = [
        'member', 'hide_obj', 'black_obj'
    ];
    protected $with = ['member'];
    protected $casts = [
        'aptitude_imgs' => 'json',
        'dispatch_type' => 'json'
    ];
    protected $attributes = [
        'information' => ''
    ];

    public function getFullAddressAttribute()
    {
        return GetStoreStatisticService::getStoreAddress($this);
    }

    public function getBlackObjAttribute()
    {
        $obj = [];
        if ($this->is_black == 0) {
            $obj = [
                'style' => 'fa fa-minus-circle',
                'name'  => '设置黑名单'
            ];
        }
        if ($this->is_black == 1) {
            $obj = [
                'style' => 'fa fa-plus-circle',
                'name'  => '加入白名单'
            ];
        }
        return $obj;
    }

    public function getHideObjAttribute()
    {
        $obj = [];
        if ($this->is_hide == 0) {
            $obj = [
                'style' => 'fa fa-circle',
                'name'  => '设置不显示'
            ];
        }
        if ($this->is_hide == 1) {
            $obj = [
                'style' => 'fa fa-circle-o',
                'name'  => '设置显示'
            ];
        }
        return $obj;
    }

    /*public function getThumbAttribute()
    {
        return yz_tomedia($this->thumb);
    }*/

    public function getDispatchAttribute()
    {
        $dispatch = '支持';
        foreach ($this->dispatch_type as $value) {
            if ($value == 1) {
                $dispatch .= '快递、';
            }
            if ($value == 2) {
                $dispatch .= '自提、';
            }
            if ($value == 3) {
                $dispatch .= '核销、';
            }
        }
        return $dispatch;
    }

    public function getValidityStatusNameAttribute()
    {
        $name = '入驻中';
        if ($this->validity_status == 1) {
            $name = '已过期';
        }
        return $name;
    }

    public function getAverageScoreAttribute()
    {
        // todo 这考虑存到门店相关的表里面, 评价写事件,监听事件
        $goods_ids = \Yunshop\Mryt\store\models\StoreGoods::getGoodsIdsByStoreId($this->id);
        $build = Comment::select('id')->uniacid()
            ->whereIn('goods_id', $goods_ids)
            ->where('comment_id', 0);
        $score_total = $build->sum('level');
        $comment_total = $build->count();
        $ret = round($score_total / $comment_total, 1);
        if (is_int($ret)) {
            return $ret;
        }
        $arr = explode('.', $ret);
        if ($arr[1] > 5) {
            $ret = $arr[0] + 1;
        }
        if ($arr[1] < 5) {
            if ($arr[0] == 0) {
                return 0;
            }
            $ret = $arr[0] - 1;
        }
        return $ret;
    }

    public function getOrderTotalAttribute()
    {
        return StoreOrder::select('id')->where('store_id', $this->id)->count();
    }

    public static function getList($search)
    {
        return self::select()->search($search);
    }

    public static function getStoreById($id)
    {
        return self::builder()->byId($id);
    }

    public static function getStoreByUid($uid)
    {
        return self::select()->byUid($uid);
    }

    public static function getStoreByCashierId($cashier_id)
    {
        return self::select()->byCashierId($cashier_id);
    }

    public static function getStoreByCategoryId($category_id)
    {
        return self::select()->byCategoryId($category_id);
    }

    public static function getStoreByUserUid($user_uid)
    {
        //return self::select()->byUserUid($user_uid);
        if ((new static())->hasColumn('user_uid')) {
            return self::select()->byUserUid($user_uid);
        }
        return collect([]);
    }

    public function conversionFormat(self $store_model)
    {
        $store_model->salers = unserialize($store_model->salers);
        $store_model->hasOneCashier->hasOneCashierGoods->plugins = unserialize($store_model->hasOneCashier->hasOneCashierGoods->plugins);
        $store_model->hasOneCashier->hasOneCashierGoods->profit = unserialize($store_model->hasOneCashier->hasOneCashierGoods->profit);
        $store_model->hasOneCashier->hasOneCashierGoods->coupon = unserialize($store_model->hasOneCashier->hasOneCashierGoods->coupon);
        return $store_model;
    }

    public static function builder()
    {
        return self::with([
            'hasOneMember',
            'hasOneCategory',
            'hasOneCashier',
            'hasManyCashierOrder',
            'hasOneBossStoreMember' => function ($member) {
                $member->select(['uid', 'realname', 'nickname', 'avatar']);
            }
        ]);
    }

    public function scopeIsHide($query)
    {
        return $query->where('is_hide', 0);
    }

    public function scopeByCategoryId($query, $category_id)
    {
        return $query->where('category_id', $category_id);
    }

    public function scopeByCashierId($query, $cashier_id)
    {
        return $query->where('cashier_id', $cashier_id);
    }

    public function scopeById($query, $id)
    {
        return $query->where('id', $id);
    }

    public function scopeByUid($query, $uid)
    {
        return $query->where('uid', $uid);
    }

    public function scopeByUserUid($query, $user_uid)
    {
        return $query->where('user_uid', $user_uid);
    }

    public function hasOneBossStoreMember()
    {
        return $this->hasOne(Member::class, 'uid', 'boss_uid');
    }

    public function hasOneMember()
    {
        return $this->hasOne(Member::class, 'uid', 'uid');
    }

    public function hasOneCategory()
    {
        return $this->hasOne(StoreCategory::class, 'id', 'category_id');
    }

    public function hasOneCashier()
    {
        return $this->hasOne(Goods::class, 'id', 'cashier_id');
    }

    public function hasManyCashierOrder()
    {
        return $this->hasMany(CashierOrder::class, 'cashier_id', 'cashier_id');
    }

    public function hasManyStoreOrder()
    {
        return $this->hasMany(StoreOrder::class, 'store_id', 'id');
    }

    public function scopeSearch($query, $params)
    {
        if (!$params) {
            return $query;
        }
        if ($params['store_id']) {
            $query->where('id', $params['store_id']);
        }
        // todo 门店省市区搜索
        if ($params['province_id']) {
            $query->where('province_id', $params['province_id']);
        }
        if ($params['city_id']) {
            $query->where('city_id', $params['city_id']);
        }
        if ($params['district_id']) {
            $query->where('district_id', $params['district_id']);
        }
        if ($params['street_id']) {
            $query->where('street_id', $params['street_id']);
        }
        // todo 门店名称搜索
        if ($params['store_name']) {
            $query->where('store_name', 'like', '%' . $params['store_name'] . '%');
        }
        // todo 门店分类搜索
        if ($params['category']) {
            $query->where('category_id', $params['category']);
        }
        // todo 门店微信搜索
        if ($params['member']) {
            $query->whereHas('hasOneMember', function ($member) use ($params) {
                $member = $member->select('uid', 'nickname', 'realname', 'mobile', 'avatar')
                    ->where('realname', 'like', '%' . $params['member'] . '%')
                    ->orWhere('mobile', 'like', '%' . $params['member'] . '%')
                    ->orWhere('nickname', 'like', '%' . $params['member'] . '%');
            });
        }

        // 会员id
        if ($params['uid']) {
            $query->where('uid', $params['uid']);
        }

        // 入驻状态
        if (in_array($params['enter_status'], [0, 1]) && $params['enter_status'] != null) {
            $query->where('validity_status', $params['enter_status']);
        }

        return $query;
    }

    public function member()
    {
        return $this->belongsTo(\app\common\models\Member::class, 'uid', 'uid');
    }

    public function atributeNames()
    {
        return [
            'store_name' => '门店名称',
            'thumb' => '门店图片',
            'banner_thumb' => '门店banner图',
            'uid' => '店长微信',
            'category_id' => '门店分类',
            'province_id' => '省',
            'city_id' => '市',
            'district_id' => '区',
            'street_id' => '街道',
            'address' => '详细地址',
            'longitude' => '地理位置经度',
            'latitude' => '地理位置维度',
            'mobile' => '店铺电话',
            'store_introduce' => '门店介绍',
            'username' => '登录账号',
            'password' => '登录密码',
            'password_again' => '确认密码'
        ];
    }

    public function rules()
    {
        $rules = [
            'store_name' => 'required',
            'thumb' => 'required',
            'banner_thumb' => 'required',
            'uid' => [
                Rule::unique($this->table)->where('uniacid', \YunShop::app()->uniacid)->ignore($this->id),
                'required'
            ],
            'category_id' => 'required',
            'province_id' => 'required',
            'city_id' => 'required',
            'district_id' => 'required',
            'street_id' => 'required',
            'address' => 'required',
            'longitude' => 'required',
            'latitude' => 'required',
            //'mobile'            => 'regex:/^1[34578]{1}\d{9}$/',
            'mobile' => 'required',
            'store_introduce' => 'required',
            'cashier_id' => 'required',
            'username' => [
                Rule::unique($this->table)->where('uniacid', \YunShop::app()->uniacid)->ignore($this->id)
            ]
        ];
        return $rules;
    }

    public function cashierGoods()
    {
        return $this->hasOne(CashierGoods::class, 'goods_id', 'cashier_id');
    }

    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(function (Builder $builder) {
            $builder->uniacid();
        });
    }

    public static function getQrCodeUrl($store_id, $mid)
    {
        if (!$store_id) {
            throw new ShopException('未找到所属门店');
        }
        $url = yzAppFullUrl('cashier_pay/' . $store_id, ['mid' => $mid]);
        return (new QrCodeHelper($url, 'app/public/qr/cashier'))->url();
    }

    public static function getGoodsQrCodeUrl($goods_id, $store_id, $mid)
    {
        if (!$store_id) {
            throw new ShopException('未找到所属门店');
        }
        $url = yzAppFullUrl('goods/' . $goods_id . '/o2o/' . $store_id, ['mid' => $mid]);
        \Log::info('---store_goods_qrcode_url----', $url);

        $result = [
           'url'  => (new QrCodeHelper($url, 'app/public/qr/cashier'))->url(),
           'name' => md5($url) . '.png'
        ];
        \Log::info('-----result------', $result);
        return $result;
    }


    public static function getStoreData($goods_model, $store_data)
    {
        $store_data['username'] = trim($store_data['username']);
        $store_data['uniacid'] = \YunShop::app()->uniacid;
        $store_data['salers'] = serialize($store_data['salers']);
        if(!$store_data['longitude']){
            $store_data['longitude'] = trim($store_data['baidumap']['lng']);
        }

        if(!$store_data['latitude']){
           $store_data['latitude'] = trim($store_data['baidumap']['lat']);
        }

        $store_data['cashier_id'] = $goods_model->id;
        unset($store_data['baidumap']);
        unset($store_data['password_again']);
        return $store_data;
    }

    public static function verifyStore($store_data, $store_model = '')
    {
        if (!$store_model) {
            $store_model = new self();
        }
        if ($store_data['province_id'] == 0) {
            $store_data['province_id'] = '';
        }
        if ($store_data['city_id'] == 0) {
            $store_data['city_id'] = '';
        }
        if ($store_data['district_id'] == 0) {
            $store_data['district_id'] = '';
        }
        if ($store_data['street_id'] == 0) {
            $store_data['street_id'] = '';
        }
        if ($store_data['validity'] > 0) {
            $store_data['validity_status'] = 0;
        }
        $store_model->fill($store_data);

        $address = Address::where('id', $store_data['city_id'])->first();
        if ($address) {
            $store_model->initials = self::getFirstCharter($address->areaname);
        }

        $validator = $store_model->validator();
        return [
            'validator' => $validator,
            'store_model' => $store_model
        ];
    }

    public function storeVerificationClerk()
    {
        return $this->hasMany(StoreVerificationClerk::class, 'store_id', 'id');
    }

    public function hasManyStoreSetting()
    {
        return $this->hasMany(StoreSetting::class, 'store_id', 'id');
    }

    public function storeSettings()
    {
        return $this->hasMany(StoreSetting::class);
    }
    public function getStoreSetting($key = ''){
        if(!$key){
            return $this->storeSettings;
        }
        $result = $this->storeSettings->where('key',$key)->first();
        if(!isset($result)){
            return null;
        }
        return $result['value'];
    }
    //通过两个经纬度信息获取距离
    public static function getDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6367000; //approximate radius of earth in meters

        /*
          Convert these degrees to radians
          to work with the formula
        */

        $lat1 = ($lat1 * pi()) / 180;
        $lng1 = ($lng1 * pi()) / 180;

        $lat2 = ($lat2 * pi()) / 180;
        $lng2 = ($lng2 * pi()) / 180;

        /*
          Using the
          Haversine formula

          http://en.wikipedia.org/wiki/Haversine_formula

          calculate the distance
        */

        $calcLongitude = $lng2 - $lng1;
        $calcLatitude = $lat2 - $lat1;
        $stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);
        $stepTwo = 2 * asin(min(1, sqrt($stepOne)));
        $calculatedDistance = $earthRadius * $stepTwo;

        return round($calculatedDistance);
    }

    public static function getFirstCharter($str)
    {
        if (empty($str)) {
            return '';
        }

        $fchar = ord($str{0});

        if ($fchar >= ord('A') && $fchar <= ord('z')) {
            return strtoupper($str{0});
        }

        $s1 = iconv('UTF-8', 'gb2312', $str);

        $s2 = iconv('gb2312', 'UTF-8', $s1);

        $s = $s2 == $str ? $s1 : $str;

        $asc = ord($s{0}) * 256 + ord($s{1}) - 65536;

        if ($asc >= -20319 && $asc <= -20284)
        {
            return 'A';
        }

        if ($asc >= -20283 && $asc <= -19776)
        {
            return 'B';
        }

        if ($asc >= -19775 && $asc <= -19219)
        {
            return 'C';
        }

        if ($asc >= -19218 && $asc <= -18711)
        {
            return 'D';
        }

        if ($asc >= -18710 && $asc <= -18527)
        {
            return 'E';
        }

        if ($asc >= -18526 && $asc <= -18240)
        {
            return 'F';
        }

        if ($asc >= -18239 && $asc <= -17923)
        {
            return 'G';
        }

        if ($asc >= -17922 && $asc <= -17418)
        {
            return 'H';
        }

        if ($asc >= -17417 && $asc <= -16475)
        {
            return 'J';
        }

        if ($asc >= -16474 && $asc <= -16213)
        {
            return 'K';
        }

        if ($asc >= -16212 && $asc <= -15641)
        {
            return 'L';
        }

        if ($asc >= -15640 && $asc <= -15166)
        {
            return 'M';
        }

        if ($asc >= -15165 && $asc <= -14923)
        {
            return 'N';
        }

        if ($asc >= -14922 && $asc <= -14915)
        {
            return 'O';
        }

        if ($asc >= -14914 && $asc <= -14631)
        {
            return 'P';
        }

        if ($asc >= -14630 && $asc <= -14150)
        {
            return 'Q';
        }

        if ($asc >= -14149 && $asc <= -14091)
        {
            return 'R';
        }

        if ($asc >= -14090 && $asc <= -13319)
        {
            return 'S';
        }

        if ($asc >= -13318 && $asc <= -12839)
        {
            return 'T';
        }

        if ($asc >= -12838 && $asc <= -12557)
        {
            return 'W';
        }

        if ($asc >= -12556 && $asc <= -11848)
        {
            return 'X';
        }

        if ($asc >= -11847 && $asc <= -11056)
        {
            return 'Y';
        }

        if ($asc >= -11055 && $asc <= -10247)
        {
            return 'Z';
        }
        if ('衢州市' === $str) {
            return 'Q';
        }

        return 0;

    }
}