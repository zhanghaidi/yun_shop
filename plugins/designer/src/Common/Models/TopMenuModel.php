<?php
/**
 * Created by PhpStorm.
 * User: king
 * Date: 2018/10/29
 * Time: 4:24 PM
 */

namespace Yunshop\Designer\Common\Models;


use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class TopMenuModel extends BaseModel
{
    use SoftDeletes;

    protected $table = 'yz_designer_top_menu';

    protected $guarded = [''];


}
