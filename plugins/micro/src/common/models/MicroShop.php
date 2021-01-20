<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/9
 * Time: 下午7:35
 */

namespace Yunshop\Micro\common\models;


use app\backend\modules\member\models\Member;
use app\common\models\BaseModel;

class MicroShop extends BaseModel
{
    protected $table = 'yz_micro_shop';
    protected $guarded = [''];

    /**
     * @name 通过微店等级id查询微店
     * @author 杨洋
     * @param $id
     * @return mixed
     */
    public static function getMicroShopByLevelId($id)
    {
        return self::builder()->byLevelId($id)->get();
    }

    public static function getMicroShopList($params)
    {
        return self::builder()->search($params);
    }

    public static function getMicroShopById($id)
    {
        return self::builder()->byId($id)->first();
    }

    public static function getMicroShopByMemberId($member_id)
    {
        return self::builder()->byMemberId($member_id)->first();
    }

    public static function builder()
    {
        return self::with([
            'hasOneMember',
            'hasOneMicroShopLevel'
        ]);
    }

    public function hasOneMember()
    {
        return $this->hasOne(Member::class, 'uid', 'member_id');
    }

    public function hasOneMicroShopLevel()
    {
        return $this->hasOne(MicroShopLevel::class, 'id', 'level_id');
    }

    public function scopeById($query, $id)
    {
        return $query->where('id', $id);
    }

    public function scopeByMemberId($query, $member_id)
    {
        return $query->where('member_id', $member_id);
    }

    public function scopeByLevelId($query, $level_id)
    {
        return $query->where('level_id', $level_id);
    }

    public function scopeSearch($query, $params)
    {
        $query->uniacid();
        if (!$params) {
            return $query;
        }
        if ($params['shop_name']) {
            $query->where('shop_name', 'like', '%' . $params['shop_name'] . '%');
        }
        if ($params['level_id']) {
            $query->where('level_id', $params['level_id']);
        }
        if ($params['member']) {
            $query->whereHas('hasOneMember', function($member)use($params) {
                $member = $member->select('uid', 'nickname','realname','mobile','avatar')
                    ->where('realname', 'like', '%' . $params['member'] . '%')
                    ->orWhere('mobile', 'like', '%' . $params['member'] . '%')
                    ->orWhere('nickname', 'like', '%' . $params['member'] . '%');
            });
        }
        return $query;
    }

    /**
     * 定义字段名
     *
     * @return array */
    public  function atributeNames() {
        return [
            'shop_avatar'       => '店铺头像',
            'shop_name'         => '微店名称',
            'signature'         => '个性签名',
            'shop_background'   => '背景图片'
        ];
    }

    /**
     * 字段规则
     *
     * @return array */
    public  function rules()
    {
        return [
            'shop_avatar'  => 'required',
            'shop_name'  => 'required',
            'signature'  => 'required',
            'shop_background'  => 'required'
        ];
    }
}