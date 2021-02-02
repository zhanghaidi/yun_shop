<?php

namespace Yunshop\MinappContent\models;

use app\common\models\BaseModel;

class ArticleModel extends BaseModel
{
    const CREATED_AT = 'create_time';
    const UPDATED_AT = null;

    public $table = 'diagnostic_service_article';

    public static function getImageFromHtml(string $content)
    {
        $pattern = "/<img.*?src=[\'|\"](.*?)[\'|\"].*?[\/]?>/";
        preg_match_all($pattern, htmlspecialchars_decode($content), $match);
        if (!empty($match[1])) {
            return $match[1];
        }
        return [];
    }
}
