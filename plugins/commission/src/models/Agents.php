<?php
/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/3/14
 * Time: 下午5:30
 */

namespace Yunshop\Commission\models;


use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Agents
 * @package Yunshop\Commission\models
 * @property AgentLevel agentLevel
 * @property int agent_not_upgrade;
 * @property int member_id;
 * @property int parent_id;
 */
class Agents extends BaseModel
{
    use SoftDeletes;

    /**
     * @var string
     */
    protected $table = 'yz_agents';

    /**
     * @var array
     */
    protected $guarded = [''];

    /**
     * @var array
     */
    protected $appends = ['agent_level'];

    public $AgentLevel;

    public function getAgentLevelAttribute()
    {
        $set = \Setting::get('plugin.commission');
        if (!isset($this->AgentLevel)) {
            $this->AgentLevel = AgentLevel::getAgentLevelByid($this->agent_level_id);
            if (!isset($this->AgentLevel)) {
                $this->AgentLevel['name'] = AgentLevel::getDefaultLevelName();
                if ($set['level'] >= 1) {
                    $this->AgentLevel['first_level'] = $set['first_level'];
                } elseif ($set['level'] >= 2) {
                    $this->AgentLevel['second_level'] = $set['second_level'];
                } elseif ($set['level'] >= 3) {
                    $this->AgentLevel['third_level'] = $set['third_level'];
                }
            }
        }
        return $this->AgentLevel;
    }


    /**
     * @return mixed
     */
    public static function getAgentsMemberId()
    {
        return self::select('member_id');
    }

    /**
     * @param $memberId
     */
    public static function updatedAt($memberId)
    {
        self::whereIn('member_id', $memberId)
            ->update(['created_at' => time(), 'updated_at' => time()]);
    }


    /**
     * @param $search
     * @return mixed
     */
    public static function getAgents($search)
    {
        $agentModel = self::uniacid();
        //与商城会员同步
        $agentModel->whereHas('Member', function ($query4) {
            return $query4;
        });
        if (!empty($search['member'])) {
            $agentModel->whereHas('Member', function ($query) use ($search) {
                return $query->searchLike($search['member']);
            });
        }
        $agentModel->with(['Member' => function ($query1) {
            return $query1->select(['uid', 'avatar', 'nickname', 'realname', 'mobile']);
        }]);
        //绑定yz_member 不然软删除的会员还会被查询出来
        $agentModel->whereHas('yzMember', function ($query) {
            return $query->whereNull('deleted_at');
        });

        $agentModel->with(['toParent' => function ($query2) {
            return $query2->select(['uid', 'avatar', 'nickname', 'realname', 'mobile', 'createtime']);
        }]);
        $agentModel->with('agentLevel');

        if ($search['follow'] >= '0') {
            if ($search['follow'] == 2) {
                $agentModel->whereDoesntHave('fans');
            } else {
                $agentModel->whereHas('fans', function ($query3) use ($search) {
                    return $query3->where('follow', $search['follow']);
                });
            }
        }
        $agentModel->with('fans');
        if ($search['parent_id'] == '0') {
            $agentModel->where('parent_id', $search['parent_id']);
        } else {
            if (!empty($search['parent_name'])) {
                $agentModel->whereHas('toParent', function ($query4) use ($search) {
                    return $query4->searchLike($search['parent_name']);
                });
            }
        }
        if ($search['level']) {
            $agentModel->where('agent_level_id', $search['level']);
        }
        if ($search['black'] >= '0') {
            $agentModel->where('is_black', $search['black']);
        }

        if ($search['is_time']) {
            if ($search['time']) {
                $range = [strtotime($search['time']['start']), strtotime($search['time']['end'])];
                $agentModel->whereBetween('created_at', $range);
            }
        }
        return $agentModel;
    }

    /**
     * @param $id
     * @return mixed
     */
    public static function getAgentById($id)
    {
        return self::where('id', $id)
            ->with(['Member' => function ($query) {
                return $query->select(['uid', 'avatar', 'nickname', 'realname', 'mobile', 'createtime']);
            }]);
    }

    /**
     * @param $memberId
     * @return $this
     */
    public static function getAgentByMemberId($memberId)
    {
        return self::uniacid()
            ->with('agentLevel')
            ->where('member_id', $memberId);
    }

    /**
     * @param $memberId
     * @param int $level
     * @return mixed
     */
    public static function getAgentCount($memberId, $level = 0)
    {
        if ($level) {
            $data = [$memberId, $level];
        } else {
            $data = [$memberId];
        }
        return self::uniacid()->whereRaw('FIND_IN_SET(?,parent)' . ($level != 0 ? ' = ?' : ''), $data)
            ->count();
    }

    /**
     * @param $id
     * @param $is_black
     * @return mixed
     */
    public static function black($id, $is_black)
    {
        return self::where('id', $id)
            ->update(['is_black' => $is_black, 'updated_at' => time()]);
    }

    /**
     * @param $id
     * @return mixed
     */
    public static function deletedAgent($id)
    {
        return self::where('id', $id)
            ->delete();
    }

    /**
     * @param $id
     * @param int $level
     * @param array $search
     * @return mixed
     */
    public static function getLower($id, $level = 0, $search = [])
    {

        $lowerModel = self::uniacid();
        if (!empty($search) && $search['lower']) {
            $level = $search['lower'];
        }
        if (!empty($search) && $search['level']) {
            $lowerModel->where('agent_level_id', $search['level']);
        }
        if (!empty($search) && $search['black'] >= '0') {
            $lowerModel->where('is_black', $search['black']);
        }
        if (!empty($search['member'])) {
            $lowerModel->whereHas('Member', function ($query) use ($search) {
                return $query->searchLike($search['member']);
            });
        }
        if ($search['is_time']) {
            if ($search['time']) {
                $range = [strtotime($search['time']['start']), strtotime($search['time']['end'])];
                $lowerModel->whereBetween('created_at', $range);
            }
        }

        /*$array      = $level ? [$id,$level] : [$id];
        $condition  = $level ? ' = ?' : '';
        $lowerModel->whereRaw('FIND_IN_SET(?,parent)' . $condition, $array);*/

        $lowerModel->whereRaw('FIND_IN_SET(?,parent)' . ($level ? ' = ? ' : ''), $level ? [$id, $level] : [$id]);

        $lowerModel->whereHas('Member', function ($query) {
            return $query->select(['uid']);
        });
        $lowerModel->with(['Member' => function ($query) {
            return $query->select(['uid', 'avatar', 'nickname', 'realname', 'mobile', 'createtime']);
        }]);

        $lowerModel->with(['toParent' => function ($query) {
            return $query->select(['uid', 'avatar', 'nickname', 'realname', 'mobile', 'createtime']);
        }]);

        $lowerModel->with(['agentLevel' => function ($query) {

        }]);

        if (!empty($search) && $search['follow'] >= '0') {
            if ($search['follow'] == 2) {
                $lowerModel->whereDoesntHave('fans');
            } else {
                $lowerModel->whereHas('fans', function ($query) use ($search) {
                    return $query->where('follow', $search['follow']);
                });
            }
        }
        $lowerModel->with('fans');
        if (!empty($search)) {
            if (!empty($search['parent_name'])) {
                $lowerModel->whereHas('toParent', function ($query) use ($search) {
                    return $query->searchLike($search['parent_name']);
                });
            }
        }

        return $lowerModel;
    }

    /**
     * @param $agentData
     * @param $memberId
     * @param $type : 'plus' 'minus'
     * @return mixed
     */
    public static function updateCommission($commission, $memberId, $type)
    {
        $model = self::uniacid();
        $model->where('member_id', $memberId);

        if ($type == 'plus') {
            $model->update(['commission_total' => DB::raw('`commission_total` + ' . $commission)]);
        } elseif ($type == 'minus') {
            $model->update(['commission_total' => DB::raw('`commission_total` - ' . $commission)]);
        }

        return $model;
    }

    public static function getLevelByMemberId()
    {
        return self::uniacid()
            ->with(['agentLevel' => function ($qurey) {
                $qurey->select('id', 'name', 'first_level', 'second_level', 'third_level');
            }]);
    }

    /**
     * @param $memberId
     * @param $commission
     * @return mixed
     */
    public static function addPayCommission($memberId, $commission)
    {
        $model = self::uniacid();
        $model->where('member_id', $memberId);
        $model->update(['commission_pay' => DB::raw('`commission_pay` + ' . $commission)]);
        return $model;
    }

    /**
     * @param $levelId
     * @param $memberID
     * @return mixed
     * 修改分销商等级
     */
    public static function updatedLevelByMemberId($levelId, $memberID)
    {

        return self::where('member_id', $memberID)
            ->update(['agent_level_id' => $levelId, 'updated_at' => time()]);
    }


    /***
     * @param $memberParent
     * @return mixed
     * 上三级数据
     */
    public static function getPraents($memberParent)
    {
        $memberModel = self::uniacid();
        $memberModel->whereIn('member_id', explode(',', $memberParent));
        return $memberModel;
    }

    /**
     * @param $memberId
     * @param $level
     * @return mixed
     * 下线数据
     */
    public static function getLowerData($memberId, $level = 0)
    {
        if ($level) {
            $data = [$memberId, $level];
        } else {
            $data = [$memberId];
        }
        $memberModel = self::uniacid();
        $memberModel->whereRaw('FIND_IN_SET(?,parent)' . ($level != 0 ? ' = ?' : ''), $data);
        return $memberModel;
    }

    public static function getMemberIdByLevelId($levelId)
    {
        return self::select('member_id', 'agent_level_id')
            ->uniacid()
            ->where('agent_level_id', $levelId);
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function toParent()
    {
        return $this->belongsTo('app\common\models\Member', 'parent_id', 'uid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Member()
    {
        return $this->belongsTo('app\common\models\Member', 'member_id', 'uid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function yzMember()
    {
        return $this->belongsTo('app\common\models\MemberShopInfo', 'member_id', 'member_id');
    }

//    /**
//     * @return \Illuminate\Database\Eloquent\Relations\HasOne
//     */
//    public function yzMember()
//    {
//        return $this->hasOne('Yunshop\Commission\models\yzMember', 'member_id', 'member_id');
//    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function fans()
    {
        return $this->hasOne('app\common\models\McMappingFans', 'uid', 'member_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function agentLevel()
    {
        return $this->belongsTo('Yunshop\Commission\models\AgentLevel', 'agent_level_id', 'id');
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

    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(function (Builder $builder) {
            $builder->uniacid();
        });
    }
}