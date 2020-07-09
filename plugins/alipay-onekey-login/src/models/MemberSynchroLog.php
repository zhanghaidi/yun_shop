<?php
namespace Yunshop\AlipayOnekeyLogin\models;

use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\Builder;


/**
* 
*/
class MemberSynchroLog extends BaseModel
{
	public $table = 'yz_member_synchro_log';
	// protected $primaryKey = '';

    protected $guarded = [''];

    public $attributes = [];
	
}