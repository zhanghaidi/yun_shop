<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/17
 * Time: 下午4:25
 */

namespace Yunshop\Supplier\common\models;



use app\backend\modules\member\models\Member;
use app\common\models\BaseModel;
use Yunshop\Supplier\common\Observer\WithdrawObserver;

class SupplierWithdraw extends BaseModel
{
    public $table = 'yz_supplier_withdraw';
    protected $guarded = [''];
    protected $search_fields = ['id', 'apply_sn'];
    protected $appends = ['type_name', 'status_obj'];

    /**
     * @name 获取提现列表
     * @author yangyang
     * @param $params
     * @param null $status
     * @return mixed
     */
    public static function getWithdrawList($params, $status = null)
    {
        $list = SupplierWithdraw::builder($params)->search($params)->status($status)->orderBy('id', 'desc');
        return $list;
    }

    public static function getWithdrawBySupplierIdAndWithdrawId($sid, $wid)
    {
        return self::select()->where('supplier_id', $sid)->whereId($wid);
    }

    /**
     * @name 通过提现id获取提现信息
     * @author yangyang
     * @param $withdraw_id
     * @return \Illuminate\Database\Eloquent\Model|null|static
     */
    public static function getWithdrawById($withdraw_id)
    {
        $withdraw = SupplierWithdraw::builder()->where('id', $withdraw_id)->first();
        return $withdraw;
    }

    /**
     * @name 构造器
     * @author yangyang
     * @param null $params
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function builder($params = null)
    {
        $builder = SupplierWithdraw::with(
            [
                'hasOneSupplier',
                'hasOneMember',
                'belongsToManyOrder'
            ]
        );
        return $builder;
    }

    public function getTypeNameAttribute()
    {
        $type_name = '';
        if ($this->type == 1) {
            $type_name = '手动提现';
        } else if ($this->type == 2) {
            $type_name = '微信提现';
        } else if ($this->type == 3) {
            $type_name = '支付宝提现';
        } else if ($this->type == 4) {
            $type_name = '易宝提现';
        } else if ($this->type == 5) {
            $type_name = '汇聚提现';
        }
        return $type_name;
    }

    public function getStatusObjAttribute()
    {
        $status = [];
        if ($this->status == 1) {
            $status = [
                'style' => 'label label-primary',
                'name' => '申请中'
            ];
        } else if ($this->status == 2) {
            $status = [
                'style' => 'label label-success',
                'name' => '待打款'
            ];
        } else if ($this->status == 4) {
            $status = [
                'style' => 'label label-success',
                'name' => '打款中'
            ];
        } else if ($this->status == 3) {
            $status = [
                'style' => 'label label-warning',
                'name' => '已打款'
            ];
        } else if ($this->status == -1) {
            $status = [
                'style' => 'label label-default',
                'name' => '驳回'
            ];
        }
        return $status;
    }

    /**
     * @name 关联订单表
     * @author yangyang
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function belongsToManyOrder()
    {
        return $this->belongsToMany(\app\common\models\Order::class, 'yz_withdraw_relation_order', 'withdraw_id', 'order_id');
    }

    /**
     * @name 关联供应商表
     * @author yangyang
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function hasOneSupplier()
    {
        return $this->hasOne(Supplier::class, 'id', 'supplier_id');
    }

    /**
     * @name 关联会员表
     * @author yangyang
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function hasOneMember()
    {
        return $this->hasOne(Member::class, 'uid', 'member_id');
    }

    /**
     * @name 状态检索
     * @author yangyang
     * @param $query
     * @param null $status
     */
    public function scopeStatus($query, $status = null)
    {
        if (isset($status)) {
            return $query->where('status', $status);
        }
        return;
    }

    /**
     * @name 检索
     * @author yangyang
     * @param $query
     * @param $params
     */
    public function scopeSearch($query, $params)
    {
        $query->uniacid();
        if (!$params) {
            return;
        }
        if ($params['member']) {
            $query->whereHas('hasOneMember', function ($member_query) use ($params) {
                $member_query->searchLike($params['member']);
            });
        }
        if ($params['apply']) {
            $query->searchLike($params['apply']);
        }
        if ($params['supplier']) {
            $query->whereHas('hasOneSupplier', function ($supplier_query) use ($params) {
                $supplier_query->searchLike($params['supplier']);
            });
        }
        if ($params['status']) {
            $query->whereStatus($params['status']);
        }
        if ($params['time_range']['field']) {
            $range = [strtotime($params['time_range']['start']), strtotime($params['time_range']['end'])];
            $query->whereBetween($params['time_range']['field'], $range);
        }
        return $query;
    }

    /**
     * @name 获取提现单号
     * @author yangyang
     * @return string
     */
    public static function ApplySn()
    {
        return 'AY' . date('YmdHis') . str_random(6);
    }

    public static function boot()
    {
        parent::boot();
        //static::$booted[get_class($this)] = true;
        // 开始事件的绑定...
        //creating, created, updating, updated, saving, saved,  deleting, deleted, restoring, restored.
        /*static::creating(function (Eloquent $model) {
            if ( ! $model->isValid()) {
                // Eloquent 事件监听器中返回的是 false ，将取消 save / update 操作
                return false;
            }
        });*/

        //注册观察者
        static::observe(new WithdrawObserver());
    }
}