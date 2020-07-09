<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019/11/14
 * Time: 16:01
 */
namespace Yunshop\Poster\models;

use app\common\models\BaseModel;

class PosterRecord extends BaseModel
{
    protected $table = 'yz_poster_record';
    public $timestamps = false;

    /*通过psoter_id获取海报*/
    public static function  getPosterByPosterId($poster_id)
    {
        return self::where('poster_id','=',$poster_id)->paginate(15);
    }


    public function getPosterById($id)
    {
        return self::find($id);
    }



}