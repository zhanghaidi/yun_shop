<?php
/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/3/17
 * Time: 下午12:04
 */

namespace Yunshop\Commission\models;


use app\common\models\MemberShopInfo;
use Illuminate\Support\Facades\DB;

class YzMember extends MemberShopInfo
{
    /**
     * @return mixed
     */
    public static function getMemberAgent()
    {
        return self::uniacid()
            ->select('parent_id')
            ->with(['belongsToParent' => function ($query) {
                $query->select('member_id', 'parent_id')
                    ->with(['belongsToParent' => function ($query) {
                        $query->select('member_id', 'parent_id')
                            ->with(['belongsToParent' => function ($query) {
                                $query->select('member_id', 'parent_id');
                            }]);
                    }])
                    ->with('hasOneFans');
            }])
            ->whereDoesntHave('existsAgent', function ($query) {
                return $query->withTrashed();
            })
            ->where('parent_id', '>', '0')
            ->groupBy(['parent_id']);
    }

    /**
     * @param $memberId
     * @param $selfBuy
     * @return mixed
     * 获取上级关系链 (三级)
     */
    public static function getParentAgents($memberId, $selfBuy)
    {
        $agentModel = self::uniacid();
        //上一级
        $agentModel->with(['belongsToParent' => function ($query) use ($selfBuy) {
            //上上级
            $query->with(['belongsToParent' => function ($query) use ($selfBuy) {
                if (!$selfBuy) {
                    //上三级
//                    $query->with(['belongsToParent' => function ($query) use ($selfBuy) {
//                        $query->with(['Agent' => function ($query) {
//                            return $query->with('agentLevel');
//                        }])->with('hasOneFans');
//                        return $query;
//                    }])->with('hasOneFans');
                }
                $query->with(['Agent' => function ($query) {
                    return $query->with('agentLevel');
                }])->with('hasOneFans');
                return $query;
            }])->with('hasOneFans');
            $query->with(['Agent' => function ($query) {
                return $query->with('agentLevel');
            }]);
            return $query;
        }]);

        $agentModel->with(['Agent' => function ($query) {
            return $query->with('agentLevel');
        }]);
        $agentModel->with('hasOneFans');
        $agentModel->where('member_id', $memberId);
        return $agentModel;

    }

    /**
     * @param $memberId
     * @return mixed
     *
     */
    public static function getMemberByMemberId($memberId)
    {
        return self::where('member_id', $memberId)
            ->with('hasOneFans');
    }


    /**
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
     *
     */
    public static function getLowerData($memberId, $level = 0)
    {
        if ($level) {
            $data = [$memberId, $level];
        } else {
            $data = [$memberId];
        }
        $memberModel = self::uniacid();
        $memberModel->whereRaw('FIND_IN_SET(?,relation)' . ($level != 0 ? ' = ?' : ''), $data);
        return $memberModel;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function parent()
    {
        return $this->hasOne(get_class($this), $this->getKeyName(), 'parent_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function belongsToParent()
    {
        return $this->belongsTo(self::class, "parent_id", "member_id");
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function Agent()
    {
        return $this->hasOne('Yunshop\Commission\models\Agents', 'member_id', 'member_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function existsAgent()
    {
        return $this->hasOne('Yunshop\Commission\models\Agents', 'member_id', 'parent_id');
    }

    public function hasOneFans()
    {
        return $this->hasOne('app\common\models\McMappingFans', 'uid', 'member_id');
    }

}