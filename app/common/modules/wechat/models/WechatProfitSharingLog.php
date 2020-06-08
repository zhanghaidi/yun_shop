<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2019/11/28
 * Time: 16:44
 */

namespace app\common\modules\wechat\models;


use app\common\models\BaseModel;
use app\framework\Database\Eloquent\Builder;

/**
 * @property string transaction_id
 * @property string out_order_no
 * @property number amount
 * Class WechatProfitSharingLog
 * @package app\common\modules\wechat\models
 */
class WechatProfitSharingLog extends BaseModel
{
    public $table = 'yz_wechat_profit_sharing_log';
    public $guarded = [''];

    public function scopeSearch($query, $search)
    {
        if ($search['transaction_id']) {
            $query->where('transaction_id', $search['transaction_id']);
        }
        if ($search['order_sn']) {
            $query->whereHas('hasOneWechatOrder', function ($q) use ($search) {
                $q->whereHas('hasOneOrder',function ($q2) use ($search) {
                    $q2->where('order_sn', $search['order_sn']);
                });
            });
        }
        if ($search['is_time']) {
            $query->whereBetween('created_at', [strtotime($search['time']['start']), strtotime($search['time']['end'])]);
        }
        return $query;
    }

    public function hasOneWechatOrder()
    {
        return $this->hasOne(WechatPayOrder::class, 'transaction_id','transaction_id');
    }

}