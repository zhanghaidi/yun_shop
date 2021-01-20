<?php
/**
 * Created by PhpStorm.
 * User: weifeng
 * Date: 2019-06-26
 * Time: 14:19
 */

namespace Yunshop\LuckyDraw\common\models;


use app\common\models\BaseModel;
use app\common\models\Coupon;
use app\common\models\Member;

class DrawPrizeRecordModel extends BaseModel
{
    protected $table = 'yz_draw_prize_record';
    protected $guarded = [''];

    public static function builder()
    {
        return self::uniacid()
            ->with([
                'hasOnePrize' => function ($q) {
                    $q->with(['hasOneCoupon' => function ($q) {
                        $q->select(['id', 'name']);
                    }]);
                },
                'Member' => function ($q) {
                    $q->select(['uid', 'mobile', 'nickname', 'avatar']);
                },
                'hasOneActivity' => function ($q) {
                    $q->select(['id', 'name', 'countdown_time']);
                }
            ]);
    }

    public function Member()
    {
        return $this->hasOne(Member::class, 'uid', 'member_id');
    }

    public function hasOnePrize()
    {
        return $this->hasOne(DrawPrizeModel::class, 'id', 'prize_id');
    }

    public function hasOneActivity()
    {
        return $this->hasOne(DrawActivityModel::class, 'id', 'activity_id');

    }

    public function hasOneCoupon()
    {
        return $this->hasOne(Coupon::class, 'id', 'coupon_id');
    }

    public static function getRecord($search)
    {
        $model = self::uniacid()
            ->with([
                'hasOnePrize' => function ($q) {
                    $q->with(['hasOneCoupon' => function ($q) {
                        $q->select(['id', 'name']);
                    }]);
                },
                'Member' => function ($q) {
                    $q->select(['uid', 'mobile', 'nickname', 'avatar']);
                },
                'hasOneActivity' => function ($q) {
                    $q->select(['id', 'name', 'countdown_time']);
                }
            ]);

        if ($search['name'] && $search['name'] != 'null') {
            $model->whereHas('Member', function ($q) use ($search) {
                $q->where('nickname', 'like', '%' . $search['name'] . '%');
            });
        }

        if ($search['times'] && $search['start_time'] != 'null' && $search['end_time']) {
            $model->whereBetween('created_at', [$search['start_time'], $search['end_time']]);
        }

        return $model;
    }
}