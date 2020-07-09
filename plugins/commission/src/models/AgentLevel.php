<?php
/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/3/14
 * Time: 下午5:30
 */

namespace Yunshop\Commission\models;


use app\common\models\BaseModel;
use Illuminate\Validation\Rule;

/**
 * Class AgentLevel
 * @package Yunshop\Commission\models\
 * @property int level
 */
class AgentLevel extends BaseModel
{
    public $table = 'yz_agent_level';
    static protected $needLog = true;
    public $attributes = [
        'name' => '',
        'level' => '',
        'first_level' => '',
        'second_level' => '',
        'third_level' => '',
        'upgrade_type' => '',
        'upgrade_value' => ''
    ];

    protected $guarded = [''];

    /**
     * @return mixed
     */
    public static function getLevels()
    {
        return self::uniacid()
            ->withCount(['agent']);
    }
    public static function WeightDetermine($weight){
        return self::uniacid()
            ->where('level',$weight)
            ->first();
    }

    /**
     * @param $id
     * @return mixed
     */
    public static function getAgentLevelByid($id)
    {
        return self::uniacid()->find($id);
    }

    /**
     * @param $id
     * @return mixed
     */
    public static function daletedLevel($id)
    {
        return self::where('id', $id)
            ->delete();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function agent()
    {
        return $this->belongsTo('Yunshop\Commission\models\Agents', 'id', 'agent_level_id');
    }

    /**
     *  定义字段名
     * 可使
     * @return array
     */
    public function atributeNames()
    {
        return [
            'name' => '分销商等级名称',
            'level' => '分销商等级权重',
            'first_level' => '分销商一级比例',
            'second_level' => '分销商二级比例',
            'third_level' => '分销商三级比例',
            'additional_ratio' => '额外分红比例',
        ];
    }

    /**
     * 字段规则
     * @return array
     */
    public function rules()
    {
        $arr = [
            'name' => 'required',
            //'level' => 'required|unique:yz_agent_level',
            'level' => ['required', Rule::unique($this->table)
                ->ignore($this->id)
                ->where('uniacid', \YunShop::app()->uniacid)]
        ];
        $arr += static::getOpenLevel();
        return $arr;

    }

    public static function getOpenLevel()
    {
        $set = \Setting::get('plugin.commission');
        $arr = [];
        if ($set['level'] >= '1') {
            $arr['first_level'] = ['required', 'numeric'];
        }
        if ($set['level'] >= '2') {
            $arr['second_level'] = ['required', 'numeric'];
        }
        if ($set['level'] >= '3') {
            $arr['third_level'] = ['required', 'numeric'];
        }
        $arr['additional_ratio'] = ['required', 'numeric'];
        return $arr;
    }

    public static function getDefaultLevelName()
    {
        $setting = \Setting::get('shop.lang', ['lang' => 'zh_cn']);

        return $setting['zh_cn']['commission']['commission_name'] ?  : '默认等级';
    }    
}