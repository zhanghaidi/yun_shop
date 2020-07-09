<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/11/29
 * Time: 下午5:49
 */

namespace Yunshop\RechargeCode\common\models;


use app\backend\modules\member\models\Member;
use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Yunshop\RechargeCode\common\services\QrCode;

class RechargeCode extends BaseModel
{
    public $table = 'yz_recharge_code';
    public $timestamps = true;
    protected $guarded = [''];
    protected $appends = ['status_name', 'time', 'type_name', 'bind_name', 'qr_code'];
    protected $attributes = [
        'status' => 0,
        'is_bind' => 0
    ];
    protected $hidden = [
        'created_at', 'updated_at', 'deleted_at'
    ];
    static protected $needLog = true;

    public static function fetchCodes($search)
    {
        return RechargeCode::select()->search($search);
    }

    public static function fetchCodesByEndTimeAndStatus($time, $status)
    {
        return RechargeCode::select('id')->status($status)->endTime($time);
    }

    public static function updateStatusByIds($ids)
    {
        RechargeCode::whereIn('id', $ids)->update(['status' => 1]);
    }

    public static function getCodeByKey($code_key)
    {
        return RechargeCode::select()->byKey($code_key);
    }

    public function scopeByKey($query, $code_key)
    {
        return $query->whereCodeKey($code_key);
    }

    public function scopeSearch($query, $search)
    {
        if ($search['status'] != '') {
            $query->whereStatus($search['status']);
        }
        if ($search['is_bind'] != '') {
            $query->whereIsBind($search['is_bind']);
        }
        if (in_array($search['type'], [1, 2, 3, 4])) {
            $query->whereType($search['type']);
        }
        if ($search['name'] != '') {
            $query->whereHas('hasOneMember',function ($member) use($search){
                $member->where('nickname','like','%'.$search['name'].'%');
            });
        }
        if ($search['is_time'] == 1) {
            $query->whereBetween('created_at',[strtotime($search['time']['start']), strtotime($search['time']['end'])]);
        }
        return $query;
    }

    public function scopeStatus($query, $status)
    {
        return $query->whereStatus($status);
    }

    public function scopeEndTime($query, $now)
    {
        return $query->where('end_time', '<=', $now);
    }

    public function getQrCodeAttribute()
    {
        return self::getQrCode($this->code_key, $this->uid);
    }

    public function getStatusNameAttribute()
    {
        $status_name = '';
        if ($this->status == 0) {
            $status_name = '未过期';
        }
        if ($this->status == 1) {
            $status_name = '已过期';
        }
        return $status_name;
    }

    public function getTimeAttribute()
    {
        return date('Y-m-d H:i', $this->end_time);
    }

    public function getBindNameAttribute()
    {
        $bind_name = '';
        if ($this->is_bind == 0) {
            $bind_name = '未充值';
        }
        if ($this->is_bind == 1) {
            $bind_name = '已充值';
        }
        return $bind_name;
    }

    public function getTypeNameAttribute()
    {
        $type_name = '';
        if ($this->type == 1) {
            $type_name = '积分';
        }
        if ($this->type == 2) {
            $type_name = '余额';
        }
        if ($this->type == 3) {
            $type_name = '可用'.trans('Yunshop\Love::love.name');
        }
        if ($this->type == 4) {
            $type_name = '冻结'.trans('Yunshop\Love::love.name');
        }
        return $type_name;
    }

    public function hasOneMember()
    {
        return $this->hasOne(Member::class, 'uid', 'uid');
    }

    public static function setRechargeCodes($code_data)
    {
        $code_datas = [];
        for ($i = 0; $i < $code_data['total']; $i++) {
            $code_datas[] = [
                'uniacid' => \YunShop::app()->uniacid,
                'type'      => $code_data['type'],
                'price'     => $code_data['price'],
                'end_time'  => strtotime($code_data['end_time']),
                'is_bind'   => 0,
                'status'    => 0,
                'code_key'  => RechargeCode::getCodeKey(),
                'uid'       => $code_data['uid'],
                'created_at'=> time(),
            ];
        }
        return $code_datas;
    }

    public static function getCodeKey($len = 16){
        $chars = array(
            "A", "B", "C", "D", "E", "F", "G",
            "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",
            "S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2",
            "3", "4", "5", "6", "7", "8", "9"
        );
        $charsLen = count($chars) - 1;
        shuffle($chars);
        $output = "";
        for ($i=0; $i<$len; $i++) {
            $output .= $chars[mt_rand(0, $charsLen)];
        }
        return $output;
    }

    public static function getQrCode($code_key, $uid)
    {
        $url = yzAppFullUrl('rechargeCodeByQrCode/' . $code_key, ['mid' => $uid]);
        return (new QrCode($url, 'app/public/qr/recharge'))->url();
    }

    public static function setQrCodeToInterim($code_key, $uid)
    {
        $url = yzAppFullUrl('rechargeCodeByQrCode/' . $code_key, ['mid' => $uid]);
        (new QrCode($url, 'app/public/interimqr'))->url();
    }

    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(function (Builder $builder) {
            $builder->uniacid();
        });
    }
}