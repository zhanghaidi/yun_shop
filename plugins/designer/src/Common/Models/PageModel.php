<?php
/**
 * Created by PhpStorm.
 * User: king
 * Date: 2018/9/17
 * Time: 上午10:15
 */

namespace Yunshop\Designer\Common\Models;


use app\common\models\BaseModel;

class PageModel extends BaseModel
{
    protected $table = 'yz_designer';

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
