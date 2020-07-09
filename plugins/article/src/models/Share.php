<?php
namespace Yunshop\Article\models;

use app\common\models\BaseModel;

class Share extends BaseModel
{
    public $table = "yz_plugin_article_share";
    public $timestamps = false;
    protected $guarded = [''];

    //关联模型
    public function belongsToShareMemeber()
    {
        return $this->belongsTo('app\common\models\Member', 'share_uid', 'uid');
    }

    //关联模型
    public function belongsToClickMemeber()
    {
        return $this->belongsTo('app\common\models\Member', 'click_uid', 'uid');
    }

    /**
     * 根据文章id获取数据
     * @param $articleId
     * @return mixed
     */
    public static function getSharesById($articleId)
    {
        return self::with('belongsToShareMemeber')->with('belongsToClickMemeber')->uniacid()->where('article_id', $articleId);
    }

    /**
     * 获取该阅读者的记录
     * @param $articleId
     * @param
     * @return mixed
     */
    public static function getLogByClickUid($articleId, $clickUid)
    {
        return self::uniacid()->where('article_id', $articleId)->where('click_uid', $clickUid)->orderBy('id', 'desc')->first();
    }

    /**
     * 获取该分享者的阅读者的记录
     * @param $articleId
     * @param $clickUid
     * @param $shareUid
     * @return mixed
     */
    public static function getLogByClickUidAndShareUid($articleId, $clickUid, $shareUid)
    {
        return self::uniacid()->where('article_id', $articleId)->where('click_uid', $clickUid)->where('share_uid', $shareUid)->orderBy('id', 'desc')->first();
    }

    /**
     * 获取指定分享者在指定文章的分享中, 获得的奖励总次数
     * @param $articleId
     * @param $shareUid
     * @return mixed
     */
    public static function getSomeoneTotalAwardCount($articleId, $shareUid)
    {
        return self::uniacid()->where('article_id', $articleId)->where('share_uid', $shareUid)->count();
    }

    /**
     * 获取分享者在指定时间内的累计被奖励次数
     * @param $articleId
     * @param $shareUid
     * @param $dayBegin
     * @param $dayEnd
     * @return mixed
     */
    public static function getAwardCountInTimeRange($articleId, $shareUid, $dayBegin, $dayEnd)
    {
        return self::uniacid()->where('article_id', $articleId)->where('share_uid', $shareUid)->whereBetween('click_time', [
            $dayBegin, $dayEnd])->count();
    }

    /**
     * 获取指定文章的总奖励金额
     * @param $articleId
     * @return mixed
     */
    public static function getBonusSum($articleId)
    {
        return self::uniacid()->where('article_id', $articleId)->sum('credit');
    }

    /**
     * 获取指定文章的积分奖励总数
     * @param $articleId
     * @return mixed
     */
    public static function getPointSum($articleId)
    {
        return self::uniacid()->where('article_id', $articleId)->sum('point');
    }

}