<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2018/10/25
 * Time: 下午2:29
 */

namespace Yunshop\Mryt\models;


use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\Builder;

class MrytLevelModel extends BaseModel
{
    public $table = 'yz_mryt_level';

    protected $guarded = [''];
    protected $attributes = [
        'current_md' => '0'
    ];

    /**
     * 升级条件1:1关系
     *
     * @return mixed
     */
    public function hasOneUpgradeSet()
    {
        return $this->hasOne(MrytLevelUpgradeModel::class, 'level_id', 'id');
    }

    public static function getList()
    {
        return self::uniacid()
            ->orderBy('level_weight', 'desc')
            ->orderBy('id', 'desc');
    }

    /**
     * @param $id
     * @return self
     */
    public static function getLevelById($id)
    {
        return self::where('id', $id)
            ->first();
    }

    public static function getAutoWithdrawLevel($day)
    {
        return self::where('auto_withdraw',1)
            ->where('withdraw_time','=',$day)
            ->with('hasManyMrytMember');
    }

    /**
     * 删除
     *
     * @param \Closure|string $id
     * @return mixed
     */
    public static function deletedLevel($id)
    {
        return self::uniacid()
            ->where('id', $id)
            ->delete();
    }



    /**
     * 定义字段名
     *
     * @return array
     */
    public function atributeNames()
    {
        return [
            'level_name' => '等级名称',
            'level_weight' => '等级权重',
            'team_manage_ratio' => '团队管理奖比例',
            'team' => '团队奖',
            'thankful' => '感恩奖',
            'train_ratio' => '育人奖比例',
            'direct' => '直推奖',
        ];
    }


    /**
     * 字段规则
     *
     * @return array
     */
    public function rules()
    {
        return [
            'level_name' => 'required',
            'level_weight' => 'required|integer|min:1',
            'team_manage_ratio' => 'required',
            'team' => 'required',
            'thankful' => 'required',
            'train_ratio' => 'required',
            'direct' => 'required',
        ];
    }

    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(function (Builder $builder) {
            $builder->uniacid();
        });
    }

    public function hasManyMrytMember()
    {
        return $this->hasMany(MrytMemberModel::class, 'level', 'id');
    }
}