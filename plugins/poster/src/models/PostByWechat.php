<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/8/21
 * Time: 下午5:56
 */

namespace Yunshop\Poster\models;


use app\common\models\BaseModel;

class PostByWechat extends BaseModel
{
    public $table = 'yz_post_by_wechat';

    public $guarded = [];

    public static function hasFile($file)
    {
        return self::where('file_path', $file)->count();
    }

    public static function getfile($file)
    {
        return self::where('file_path', $file)
            ->orderBy('id', 'desc')
            ->first();
    }
}