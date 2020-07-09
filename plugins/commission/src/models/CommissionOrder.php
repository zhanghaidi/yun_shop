<?php
/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/3/14
 * Time: 下午5:30
 */

namespace Yunshop\Commission\models;


use app\common\models\BaseModel;
use app\common\models\OrderAddress;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use app\backend\modules\member\models\Member;
use Yunshop\Commission\models\ErrorCommissionOrder;

class CommissionOrder extends BaseModel
{
    use SoftDeletes;
    public $table = 'yz_commission_order';
    public $timestamps = true;
    protected $guarded = [''];

    /**
     * @param $search
     * @return mixed
     */
    public static function getOrder($search)
    {
        $query = self::uniacid();
        if (!empty($search['order'])) {
            $query->whereHas('order', function ($query1) use ($search) {
                return $query1->searchLike($search['order']);
            });
        }
        if($search['is_pay'] == 1){
            $query->whereHas('order',function($query){
                return $query->where('status','>=',1);
            });
        }

        if($search['is_pay'] == 2){
            $query->whereHas('order',function($query){
                return $query->where('status','<',1);
            });
        }

        $query->with(['order' => function ($query2) {
            $query2->with('belongsToMember')->get();
        }]);

        if (!empty($search['level'])) {
            $query->whereHas('agent', function ($query3) use ($search) {
                return $query3->where('agent_level_id', $search['level']);//searchLike($search['keyword']);
            });
        }
        $query->with(['agent' => function ($query4) {
            $query4->with('agentLevel');
        }]);

        if (!empty($search['member'])) {
            $query->whereHas('parentMember', function ($query5) use ($search) {
                return $query5->searchLike($search['member']);
            });
        }
        $query->with('parentMember');

        if ($search['status'] >= '0' || $search['status'] == '-1') {
            $query->where('status', $search['status']);
        }

        if ($search['withdraw'] >= '0') {
            $query->where('status', '2');
            $query->where('withdraw', $search['withdraw']);
        }

        if (!empty($search['member_id'])) {
            $query->where('member_id', $search['member_id']);
        }

        if (!empty($search['hierarchy'])) {
            $query->where('hierarchy', $search['hierarchy']);
        }
        if ($search['is_time']) {
            if ($search['time']) {
                $range = [strtotime($search['time']['start']), strtotime($search['time']['end'])];
                $query->whereBetween('created_at', $range);
            }
        }
        return $query;
    }

    /**
     * @param $id
     * @param array $status
     * @return mixed
     */
    public static function getOrderById($id, $status = [])
    {
        return self::where('id', $id)
            ->whereIn('status', $status)
            ->first();
    }

    public static function getOrderByOrderId($type, $order_id)
    {
        return self::uniacid()
            ->where('ordertable_id', $order_id)
            ->where('ordertable_type', $type);
    }

    /**
     * @param $commission
     * @param $id
     */
    public static function edit($commission, $id)
    {
        return self::where('id', $id)->update($commission);
    }

    /**
     * @param $type
     * @param $typeId
     * @param $status
     * @return mixed
     */
    public static function getOrderByTypeId($type, $typeId, $status)
    {
        return self::uniacid()
            ->where('ordertable_type', $type)
            ->where('ordertable_id', $typeId)
            ->where('status', $status);
    }

    /**
     * @param $type
     * @param $typeId
     * @param $data
     * @return mixed
     */
    public static function updatedOrderStatus($type, $typeId, $data)
    {

        return self::where('ordertable_type', $type)
            ->where('ordertable_id', $typeId)
            ->update($data);
    }

    public static function getCommissiomOrders($type, $typeId)
    {
        return self::where('ordertable_type', $type)
            ->where('ordertable_id', $typeId)
            ->where('buy_id', '<>', DB::raw('member_id'))
            ->with('hasOneFans');
    }

    public function hasOneFans()
    {
        return $this->hasOne('app\common\models\McMappingFans', 'uid', 'member_id');
    }

    public function hasOneMember()
    {
        return $this->hasOne(Member::class, 'uid', 'buy_id');
    }

    /**
     * @return mixed
     * 获取可结算数据
     */
    public static function getStatement()
    {
        $time = time();
        return self::uniacid()
            ->with(['order' => function ($query) {
                $query->select('id', 'order_sn', 'status');
            }])
            ->with(['OrderGoods' => function ($query) {
                $query->select('order_id', 'title', 'goods_price');
            }])
            ->with(['agent' => function ($query) {
                $query->select('member_id', 'agent_level_id');
                $query->with(['agentLevel' => function ($query) {
                    $query->select('id', 'name');
                }]);
            }])
            ->where(function ($query) {
                return $query->where(DB::raw('ifnull(`recrive_at`, 0) + (`settle_days` * 86400)'), '<=', time())
                    ->orWhere('settle_days', '=', '0');
            })
            ->where('status', '1')
            ->get();
    }

    /*
     * 手动结算：可结算的数据
     * */
    public static function getNotSettleInfo($where)
    {
        $model = self::uniacid()
            ->where($where)
            ->with(['order' => function ($query) {
                $query->select('id', 'order_sn', 'status');
            }])
            ->with(['OrderGoods' => function ($query) {
                $query->select('order_id', 'title', 'goods_price');
            }])
            ->with(['agent' => function ($query) {
                $query->select('member_id', 'agent_level_id');
                $query->with(['agentLevel' => function ($query) {
                    $query->select('id', 'name');
                }]);
            }])
            ->where('status', '1');

        return $model;
    }

    /*
    * 手动结算：佣金结算处理
    * */
    public static function updatedManualStatement($id, $times)
    {
        return self::uniacid()
            ->where('id', $id)
            ->where('status', '1')
            ->update(['status' => '2', 'statement_at' => $times]);
    }

    /*
    * 获取未结算总金额
    * */
    public static function getNotSettleAmount($uid)
    {
        return self::uniacid()
            ->where('member_id', $uid)
            ->where('status', '1')
            ->whereNotNull('recrive_at')
            ->sum('commission');
    }

    /**
     * @param $times
     * @return mixed
     * 佣金结算处理
     */
    public static function updatedStatement($times)
    {
        return self::uniacid()
            ->where('status', '1')
            ->where(function ($query) {
                return $query->where(DB::raw('ifnull(`recrive_at`, 0) + (`settle_days` * 86400)'), '<=', time())
                    ->orWhere('settle_days', '=', '0');
            })
            ->update(['status' => '2', 'statement_at' => $times]);
    }

    public static function getCommissionByMemberId($status = '', $withdraw = '')
    {
        $model = self::uniacid();
        $model->where('member_id', \YunShop::app()->getMemberId());
        $model->whereHas('order',function($query){
            
             $query->where('pay_time','>',0);
        });
        if ($status != '') {
            $model->whereIn('status', $status);
        }
        if ($withdraw != '') {
            $model->where('status', '2');
            $model->where('withdraw', $withdraw);
        }
        return $model;
    }

    public static function getCommissionOrderByMemberId($memberId, $status = '')
    {
        $model = self::select('id', 'ordertable_id', 'member_id', 'buy_id', 'commission_amount', 'formula', 'hierarchy', 'commission')
            ->uniacid();
        $model->where('member_id', $memberId);
        if ($status >= '0') {
            $model->whereHas('order', function ($query) use ($status) {
                return $query->where('status', $status);
            });
        }

        $model->with(['order' => function ($qurey) {
            $qurey->select('id', 'price', 'order_sn', 'created_at', 'create_time', 'status');
        }])->with(['order.address']);
        $model->with(['orderGoods' => function ($query) {
            $query->select('id', 'goods_id', 'order_id', 'thumb', 'title', 'total', 'goods_price');
        }]);

        $model->with(['member' => function ($query) {
            $query->select('uid', 'realname', 'nickname', 'avatar');
        }])->with('member.yzMember' );


        $model->orderBy('id', 'desc');
        return $model;
    }

    public static function updatedWithdraw($data, $where)
    {
        return self::uniacid()
            ->where($where)
            ->update($data);
    }

    public static function updatedCommissionOrderWithdraw($type, $typeId, $withdraw)
    {
        return self::where('member_id', \YunShop::app()->getMemberId())
            ->whereIn('id', explode(',', $typeId))
            ->update(['withdraw' => $withdraw]);
    }

    public function member()
    {
        return $this->hasOne('\app\common\models\Member', 'uid', 'buy_id');
    }

    public function OrderGoods()
    {
        return $this->hasMany('\app\common\models\OrderGoods', 'order_id', 'ordertable_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function order()
    {
        return $this->hasOne('\app\common\models\Order', 'id', 'ordertable_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parentMember()
    {
        return $this->belongsTo('\app\common\models\Member', 'member_id', 'uid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function agent()
    {
        return $this->belongsTo('Yunshop\Commission\models\Agents', 'member_id', 'member_id');
    }

    public function incomes()
    {
        return $this->morphMany('app\common\models\Income', 'incomeTable');
    }

    public function ordertable()
    {
        return $this->morphTo();//->with('hasManyOrderGoods')
    }

    /**
     *  定义字段名
     * 可使
     * @return array
     */
    public function atributeNames()
    {
        return [];
    }

    /**
     * 字段规则
     * @return array
     */
    public function rules()
    {
        return [];
    }

    //新增
    public function hasOneRecharge()
    {
        return $this->hasOne('Yunshop\Recharge\models\RechargeOrderModel', 'order_id', 'ordertable_id');
    }

    public static function getCommissionModel()
    {
        $model = new CommissionOrder();
        return $model->with('hasOneRecharge')->orderBy('id', 'desc')->whereHas('hasOneRecharge')->delete();
    }

    public function income()
    {
        return $this->hasOne(Income::class, 'incometable_id', 'id')->where('incometable_type', self::class);
    }

    public function logError($msg)
    {
        \Illuminate\Support\Facades\Log::info("订单{$this->order_id}用户{$this->member_id}:", [$msg]);
    }

    /**
     * 删除并操作对应的收入记录和提现记录
     * @throws \Exception
     */
    public function rollback()
    {

        // 将区域代理记录 减去分红记录的金额
        $this->agent->commission_total -= $this->commission;
        if ($this->status == 1) {
            $this->agent->commission_pay -= $this->commission;
        }
        $this->agent->save();

        //已结算
        if ($this->status == 2) {
            if ($this->income && $this->income->status == 1) {
                // 收入已提现
                if (!$this->income->withdraw()) {
                    $this->logError('提现记录未找到');
                } else {
                    if ($this->income->withdraw()->status == 2) {
                        $this->logError('分红已提现,无法处理');
                        ErrorCommissionOrder::create(['commission_order_id' => $this->id, 'order_id' => $this->ordertable_id, 'member_id' => $this->member_id, 'commission_amount' => $this->commission, 'note' => "用户{$this->member_id}的分红已提现,无法处理"]);
                    }
                    $this->income->withdraw()->amounts -= $this->commission;
                    if ($this->income->withdraw()->amounts <= 0) {
                        // 提现记录的对应收入记录全部删除,则提现记录也删除
                        $this->income->withdraw()->delete();
                    } else {
                        // 还有其他收入记录的时候,修改时删除对应id并保存 todo 表结构不合理
                        $ids = explode(',', $this->income->withdraw()->type_id);
                        unset($ids[array_search($this->id, $ids)]);
                        $ids = implode(',', $ids);
                        $this->income->withdraw()->type_id = $ids;
                        $this->income->withdraw()->save();
                        $this->logError("分红记录{$this->order->order_sn}的分红提现状态为{$this->income->withdraw()->status},已修改");
                    }
                }


                $this->logError("分红记录{$this->order->order_sn}的收入状态为{$this->status},收入记录已删除");
                $this->income->delete();
            }
        }
        $this->logError("订单{$this->order->order_sn}的分红记录{$this->id},已修复");
        // 删除对应分红日志
        $this->delete();

    }
    public function scopeRepetition(Builder $query){
        $commissionOrders = self::selectRaw('group_concat(id) as ids,count(1) as count_num')
            ->groupBy(['ordertable_id','member_id','hierarchy'])
            ->having('count_num','>',1)->get();
        $errorCommissionOrderIds = $commissionOrders->reduce(function ($result,$commissionOrder){
            $a = explode(',',$commissionOrder->ids);
            array_shift($a);
            return array_merge($result,$a);
        },[]);
        return $query->whereIn('id',$errorCommissionOrderIds);
    }
}