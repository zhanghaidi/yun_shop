<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019/1/16
 * Time: 6:53 PM
 */

namespace Yunshop\Nominate\models;


use app\common\models\BaseModel;
use app\common\models\Income;
use app\common\models\Member;
use Illuminate\Database\Eloquent\Builder;
use Yunshop\Nominate\services\MessageService;

/**
 * Class NominateBonus
 * @package Yunshop\Nominate\models
 * @property int amount
 * @property int uniacid
 * @property int uid
 * @property int type
 * @property int level_id
 * @property int source_id
 * @property string type_name
 */
class NominateBonus extends BaseModel
{
    public $table = 'yz_nominate_bonus';
    public $timestamps = true;
    protected $guarded = [''];

    protected $appends = [
        'type_name', 'status_name'
    ];

    // 直推奖
    const NOMINATE_PRIZE = 0;
    // 直推极差奖
    const NOMINATE_POOR_PRIZE = 1;
    // 团队奖
    const TEAM_PRIZE = 2;

    const STATUS_SUCCESS = 1;

    public static function getList($search, $type)
    {
        return self::select()
            ->with([
                'member' => function ($member) {
                    $member->select(['uid', 'nickname', 'realname', 'avatar', 'mobile']);
                },
                'sourceMember' => function ($sourceMember) {
                    $sourceMember->select(['uid', 'nickname', 'realname', 'avatar', 'mobile']);
                },
                'memberLevel' => function ($memberLevel) {
                    $memberLevel->select('id', 'level_name');
                }
            ])
            ->search($search)
            ->byType($type);
    }

    public static function store($data)
    {
        $model = self::create($data);

        // 收入
        $class = get_class($model);
        $income_data = [
            'uniacid'           => $model->uniacid,
            'member_id'         => $model->uid,
            'incometable_type'  => $class,
            'incometable_id'    => $model->id,
            'type_name'         => $model->type_name,
            'amount'            => $model->amount,
            'status'            => 0,
            'pay_status'        => 0,
            'detail'            => '',
            'create_month'      => date('Y-m', time())
        ];
        Income::create($income_data);

        $typeAndAmount = $model->type_name . '-' . $model->amount;
        MessageService::awardMessage($model->uniacid, $model->uid, $typeAndAmount);
        return $model;
    }

    public function getTypeNameAttribute()
    {
        $set = \Setting::get('plugin.nominate');
        $typeName = $set['nominate_prize_name']?:'直推奖';
        if ($this->type == self::NOMINATE_POOR_PRIZE) {
            $typeName = $set['nominate_poor_prize_name']?:'直推极差奖';
        }
        if ($this->type == self::TEAM_PRIZE) {
            $typeName = $set['team_prize_name']?:'团队奖';
        }
        return $typeName;
    }

    public function getStatusNameAttribute()
    {
        $statusName = '未发放';
        if ($this->status == self::STATUS_SUCCESS) {
            $statusName = '已发放';
        }
        return $statusName;
    }

    public function scopeSearch($query, $search)
    {
        if ($search['uid']) {
            $query->where('uid', $search['uid']);
        }
        if ($search['member']) {
            $query->whereHas('member', function ($member) use ($search) {
                $member->select('uid', 'nickname', 'realname', 'mobile', 'avatar')
                    ->where('realname', 'like', '%' . $search['member'] . '%')
                    ->orWhere('mobile', 'like', '%' . $search['member'] . '%')
                    ->orWhere('nickname', 'like', '%' . $search['member'] . '%');
            });
        }
        if ($search['source_id']) {
            $query->where('source_id', $search['source_id']);
        }
        if ($search['source_member']) {
            $query->whereHas('sourceMember', function ($sourceMember) use ($search) {
                $sourceMember->select('uid', 'nickname', 'realname', 'mobile', 'avatar')
                    ->where('realname', 'like', '%' . $search['source_member'] . '%')
                    ->orWhere('mobile', 'like', '%' . $search['source_member'] . '%')
                    ->orWhere('nickname', 'like', '%' . $search['source_member'] . '%');
            });
        }
        if ($search['search_time']) {
            $query->whereBetween('created_at', [strtotime($search['time']['start']), strtotime($search['time']['end'])]);
        }
        return $query;
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * @name 用于前端获取列表
     * @author
     * @param $query
     * @param $status
     * @return mixed
     */
    public function scopeByStatusToApiList($query, $status)
    {
        if ($status == -1) {
            return $query;
        }
        return $query->where('status', $status);
    }

    public function member()
    {
        return $this->hasOne(Member::class, 'uid', 'uid');
    }

    public function sourceMember()
    {
        return $this->hasOne(Member::class, 'uid', 'source_id');
    }

    public function memberLevel()
    {
        return $this->hasOne(ShopMemberLevel::class, 'id', 'level_id');
    }

    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(function (Builder $builder) {
            $builder->uniacid();
        });
    }
}