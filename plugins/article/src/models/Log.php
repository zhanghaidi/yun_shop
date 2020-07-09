<?php
namespace Yunshop\Article\models;

use app\common\models\BaseModel;

class Log extends BaseModel
{
    public $table = "yz_plugin_article_log";
    public $timestamps = false;
    protected $guarded = [''];

    //关联模型
    public function belongsToMember()
    {
        return $this->belongsTo('app\common\models\Member', 'uid', 'uid');
    }

    /**
     * 获取指定文章的记录
     * @param $articleId
     * @return mixed
     */
    public static function getLogsById($articleId)
    {
        return self::uniacid()->with('belongsToMember')->where('article_id', $articleId);
    }

    /**
     * 获取指定文章和指定阅读者的记录
     * @param $uid
     * @param $articleId
     * @return mixed
     */
    public static function getLogByUid($uid, $articleId)
    {
       return self::uniacid()->where('uid', $uid)->where('article_id', $articleId);
    }

}
