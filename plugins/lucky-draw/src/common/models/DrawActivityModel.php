<?php
/**
 * Created by PhpStorm.
 * User: weifeng
 * Date: 2019-06-26
 * Time: 14:18
 */

namespace Yunshop\LuckyDraw\common\models;


use app\common\models\BaseModel;
use app\common\models\Coupon;
use Yunshop\LuckyDraw\admin\models\GoodsModel;

class DrawActivityModel extends BaseModel
{
    protected $table = 'yz_draw_activity';
    protected $guarded = [''];

    protected $casts = [
        'prize_id' => 'json',
        'countdown_time' => 'json',
    ];

    const ACTIVITY_QRCODE_URL = 'lottery';

    public static function getActivity($search)
    {
        $model = self::uniacid();

        if (!empty($search['id'])) {
            $model->where('id', $search['id']);
        }

        if (!empty($search['name'])) {
            $model->Where('name', 'like', '%' . $search['name'] . '%');
        }

        if (!empty($search['times'])) {
            $model->whereBetween('created_at', [$search['start_time'], $search['end_time']]);
        }

        return $model;
    }

    public function hasOneCoupon()
    {
        return $this->hasOne(Coupon::class, 'id', 'partake_coupon_id');
    }

    public function hasManyLog()
    {
        return $this->hasMany(DrawByMemberModel::class, 'activity_id', 'id');
    }

    public function hasManyRecord()
    {
        return $this->hasMany(DrawPrizeRecordModel::class, 'activity_id', 'id');
    }

    public function hasOneGoods()
    {
        return $this->hasOne(GoodsModel::class, 'id', 'goods_id');
    }
}