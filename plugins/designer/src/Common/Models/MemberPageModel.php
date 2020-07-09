<?php
/**
 * Created by PhpStorm.
 * User: win 10
 * Date: 2019/7/10
 * Time: 9:11
 */

namespace Yunshop\Designer\Common\Models;


use app\common\models\BaseModel;

class MemberPageModel extends BaseModel
{
    protected $table = 'yz_member_designer';

    protected $guarded = [''];

    public $widgets =[];


    /**
     * @param static $query
     * @param int $page_type
     * @return static mixed
     */
    public function scopeWherePageType($query, $page_type)
    {
        return $query->whereRaw('FIND_IN_SET(?,page_type)', [(int)$page_type]);
    }
}