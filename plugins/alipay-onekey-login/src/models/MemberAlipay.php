<?php
namespace Yunshop\AlipayOnekeyLogin\models;

use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\Builder;


/**
* 
*/
class MemberAlipay extends BaseModel
{
	public $table = 'yz_member_alipay';
	protected $primaryKey = 'alipay_id';

    public $attributes = [];

	/**
	* 获取会员ID
	* @param $user_id
    * @return mixed
	*/
	public static function getUid($user_id)
	{
		return self::select('member_id')
				->uniacid()
				->where('user_id', $user_id)
				->first();
	}

	/**
     * 删除会员信息
     *
     * @param $id
     */
    public static function  deleteMemberInfoById($id)
    {
        return self::uniacid()
            ->where('member_id', $id)
            ->delete();
    }

    public static function insertData($userInfo, $yz)
    {
        $model = self::where('user_id', $userInfo['user_id'])->where('member_id', $yz['member_id'])->first();


        $model = $model ? $model : new static;

    	$data = [
    		'uniacid' => $yz['uniacid'],
    		'member_id' => $yz['member_id'],
    		'user_id'	=> $userInfo['user_id'],
    		'avatar'	=> $userInfo['avatar'],
    		'nick_name'	=> $userInfo['nick_name'],
    		'province'	=> $userInfo['province'],
    		'city'	=> $userInfo['city'],
    		'is_student_certified'	=> $userInfo['is_student_certified'],
    		'user_type'	=> $userInfo['user_type'],
    		'user_status'	=> $userInfo['user_status'],
    		'is_certified'	=> $userInfo['is_certified'],
    		'gender'	=> $userInfo['gender'],

    	];
        $model->setRawAttributes($data);

        if ($model->save()) {
            return $model->member_id;
        }
    	return false;
    }
}