<?php
namespace Yunshop\Article\models;

use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends BaseModel
{
    use SoftDeletes;
    public $table = "yz_plugin_article_report";
    protected $guarded = [''];

    //关联模型
    public function belongsToMember()
    {
        return $this->belongsTo('app\common\models\Member', 'uid', 'uid');
    }

    //关联模型
    public function belongsToArticle()
    {
        return $this->belongsTo('Yunshop\Article\models\Article', 'article_id', 'id');
    }

    public static function getReports()
    {
        return self::with('belongsToMember')
            ->with('belongsToArticle')
            ->uniacid();
    }

    public static function getReportByUidAndArticleId($uid, $articleId)
    {
        return self::uniacid()
            ->where('uid', $uid)
            ->where('article_id', $articleId);
    }

}