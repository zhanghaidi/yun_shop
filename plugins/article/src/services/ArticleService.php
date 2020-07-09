<?php
namespace Yunshop\Article\services;

use app\backend\modules\member\models\Member;
use app\common\facades\Setting;
use Yunshop\Article\models\Article;
use Yunshop\Article\models\Category;
use Yunshop\Article\models\Log;
use Yunshop\Article\models\Share;
use Yunshop\Article\models\Report;
use Illuminate\Support\Facades\Log as DebugLog; //todo
use app\common\services\finance\BalanceChange;
use app\common\services\credit\ConstService;
use app\common\services\finance\PointService;
use app\backend\modules\member\models\MemberLevel;

class ArticleService
{
    /**
     * 判断用户是否符合文章的"可读地区"限制
     * @return bool
     */
    public static function authorizeAreaLimit()
    {
        $articleSet = Setting::get('plugin.article');
        $city = self::getCity();

        $in_area = FALSE; //标识是否符合"可读地区"限制, 初始化成不符合限制
        if (is_array($city) && sizeof($city)){
            $province = $city['region'];
            $city = $city['city'];

            //判断是否在设定的地理范围内：
            foreach ($articleSet['area'] as $key=>$area){
                if (trim($area['province']) == trim($province)){
                    if (trim($area['city'])){
                        //如果有城市限制：
                        if (trim($area['city']) == trim($city)){
                            $in_area = TRUE;
                            break;
                        }
                    }else{
                        $in_area = TRUE;
                        break;
                    }
                }
            }
        }
        return $in_area;
    }


    /**
     * 判断用户是否满足文章的会员等级要求
     * @param $uid
     * @param $articleId
     * @return bool
     */
    public static function authorizeMemberLevel($uid, $articleId)
    {
        $articleInfo = Article::getArticle($articleId);
        $categoryId = $articleInfo['category_id'];
        $categoryInfo = Category::getCategory($categoryId);
        if (empty($categoryInfo)){
            return FALSE;
        }

        $memberLevelLimitId = $categoryInfo['member_level_id_limit'];
        if(!$memberLevelLimitId){ //所有人都可以阅读
            return TRUE;
        }

        $level = MemberLevel::uniacid()->where('id', $memberLevelLimitId)->first();
        $levelLimit = $level->level;
        $memberInfo = Member::getMemberInfoById($uid);
        if ($memberInfo['yzMember']['level']['level'] != $levelLimit){
            return FALSE;
        }

        return TRUE;
    }

    public static function setJson($array)
    {
        $json = '';
        if (!empty($array)) {
            $json = json_decode($array);
        }
        return $json;
    }

    public static function getJson($json)
    {
        $array = [];
        if (!empty($json)) {
            $array = json_encode($json);
        }
        return $array;
    }

    public static function getContentByCollect($url)
    {
        $contents = file_get_contents($url);
        if (!$contents) {
            return;
        }
        preg_match_all('/<\s*img\s+[^>]*?src\s*=\s*(\'|\")(.*?)\\1[^>]*?\/?\s*>/i', $contents, $match);
        $pic = $match['0'];
        $img = $match['2'];
        array_pop($pic);
        array_pop($img);
        foreach ($pic as $key => $value) {
            $url = $value;
            if (!empty($img[$key])) {
                $imgarr = getimagesize($img[$key]);
            } else {
                $imgarr = '';
            }
            if ($imgarr > 60) {
                $fileurl = "<img src='$img[$key]' width='100%'/>";
            } else {
                $fileurl = "<img src='$img[$key]' width=$imgarr />";
            }
            $contents = str_replace("$url", $fileurl, $contents);
        }
        $contents = str_replace("mmbiz.qpic.cn/mmbiz_jpg", "gjb.antbiz.cn/tutu", $contents);
        $contents = str_replace("mmbiz.qpic.cn/mmbiz_png", "gjb.antbiz.cn/tutu2", $contents);
        $contents = str_replace("mmbiz.qpic.cn/mmbiz_gif", "gjb.antbiz.cn/tutu3", $contents);
        $contents = str_replace("mmbiz.qpic.cn/mmbiz", "gjb.antbiz.cn/tutu4", $contents);
        $contents = str_replace("mmsns.qpic.cn/mmsns", "gjb.antbiz.cn/tutu5", $contents);
        $title = explode('var msg_title = "', $contents);
        $title = explode('";', $title['1']);
        $desc = explode('var msg_desc = "', $contents);
        $desc = explode('";', $desc['1']);
        $thumb = explode('var msg_cdn_url = "', $contents);
        $thumb = explode('";', $thumb['1']);
        $contents = explode('js_content', $contents);
        $contents = $contents[1];
        $contents = explode('<script ', $contents);
        $contents = $contents[0];
        $contents = '<div id="js_content' . $contents;
        $data = array(
            'title' => $title['0'],
            'contents' => $contents,
            'desc' => $desc['0'],
            'thumb' => $thumb['0']
        );
        return $data;
    }

    /**
     * 点赞
     * @param $uid
     * @param $articleId
     * @return array
     */
    public static function setLike($uid, $articleId)
    {
        $articleModel = Article::getArticle($articleId);
        $logModel = Log::getLogByUid($uid, $articleId)->first(); //因为点赞前需要阅读, 所以在 log 上确定会有记录 //todo 有没有可能没有记录?
        if (empty($logModel->liked)) {
            $articleModel->like_num += 1;
            $logModel->liked = 1;
        } else { //取消点赞
            $articleModel->like_num -= 1;
            $logModel->liked = 0;
        }
        $articleModel->save();
        $logModel->save();
        return [
            'log' => $logModel,
            'article' => $articleModel,
        ];
    }

    /**
     * 记录阅读行为到 log 和 article
     * @param $uid
     * @param $articleId
     */
    public static function setRead($uid, $articleId)
    {
        $articleModel = Article::getArticle($articleId);
        $logModel = Log::getLogByUid($uid, $articleId)->first();
        if (empty($logModel)) {
            $logModel = new Log();
            $logModel->uniacid = \YunShop::app()->uniacid;
            $logModel->article_id = $articleId;
            $logModel->read_num = 1;
            $logModel->liked = 0;
            $logModel->uid = $uid;

            $articleModel->read_num += 1;
        } else {
            $articleModel->read_num += 1;
            $logModel->read_num += 1;
        }
        $articleModel->save();
        $logModel->save();
    }

    /**
     * 举报
     * @param $uid
     * @param $articleId
     * @param $desc
     * @return bool
     */
    public static function setReport($uid, $articleId, $desc)
    {
        $reportModel = new Report();
        $reportModel->uid = $uid;
        $reportModel->uniacid = \YunShop::app()->uniacid;
        $reportModel->article_id = $articleId;
        $reportModel->desc = $desc;
        if($reportModel->save()){
            return TRUE;
        } else{
            return FALSE;
        }
    }

    /**
     * 对"阅读分享文章"进行奖励
     * @param $article
     * @param $shareUid
     * @param $clickUid
     */
    public static function setShare($article, $shareUid, $clickUid)
    {
        if ($article->reward_mode) {//奖励方式：按天
            $clickLog = Share::getLogByClickUidAndShareUid($article->id, $clickUid, $shareUid);
            //判断该阅读者的阅读行为在今天是否被奖励过（局限于$shareUid）
            if ($clickLog && $clickLog->click_time > strtotime('today')) {
                return;
            }
        } else {//奖励方式：按次
            //判断该阅读者的阅读行为是否已经被奖励过 (包括阅读任意分享者分享的该文章, 而不局限于 $shareUid)
            $clickLog = Share::getLogByClickUid($article->id, $clickUid);
            if($clickLog){
                return;
            }
        }


        //判断分享者今天的奖励总数是否超过该分享者的每天可奖励次数
        $todayBegin = strtotime('today');
        $awardCount = Share::getAwardCountInTimeRange($article->id, $shareUid, $todayBegin, $todayBegin+3600*24);
        if($awardCount >= $article->per_person_per_day){
            return;
        }

        //判断分享者的累计奖励总数是否超过单个分享者的累计可奖励次数
        $totalAwardCount = Share::getSomeoneTotalAwardCount($article->id, $shareUid);
        if($totalAwardCount >= $article->total_per_person){
            return;
        }

        //判断所有分享者的奖励中金额是否超过该文章的累计奖励总金额限制
        $totalBonus = Share::getBonusSum($article->id);
        if($totalBonus >= $article->bonus_total){
            $article->credit = 0;
        } else {
            //发放奖励, 写入 ims_yz_plugin_article_share 数据表
            //奖励余额
            if($article->credit > 0){
                $creditData = [
                    'member_id' => $shareUid,
                    'remark' => '文章营销: 用户 '.$shareUid.' 推荐用户 '.$clickUid.' 阅读文章 '.$article->id.', 获得余额奖励 '.$article->credit.' 元',
                    'source' => ConstService::SOURCE_AWARD,
                    'relation' => '',
                    'operator' => 1,
                    'operator_id' => $article->id,
                    'change_value' => $article->credit,
                ];
                $balanceChange = new BalanceChange();
                $balanceChange->award($creditData);
            }
        }

        //奖励积分
        if($article->point > 0){
            $pointData = array(
                'point_income_type' => 1,
                'member_id' => $shareUid,
                'point_mode' => 4,
                'point' => $article->point,
                'remark' => '文章营销: 用户 '.$shareUid.' 推荐用户 '.$clickUid.' 阅读文章 '.$article->id.', 获得积分奖励 '.$article->point.' 个',
            );
            $pointService = new PointService($pointData);
            $pointService->changePoint();
        }

        //写入分享奖励记录
        $shareData = [
            'uniacid' => \YunShop::app()->uniacid,
            'article_id' => $article->id,
            'share_uid' => $shareUid,
            'click_uid' => $clickUid,
            'click_time' => time(),
            'point' => $article->point,
            'credit' => $article->credit,
        ];
        $shareModel = Share::create($shareData);
    }
    
    /**
     * 获取用户真实 IP
     */
    public static function get_real_ip()
    {
        $ip = false;
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(', ', $_SERVER['HTTP_X_FORWARDED_FOR']);
            if ($ip) {
                array_unshift($ips, $ip);
                $ip = false;
            }
            for ($i = 0; $i < count($ips); $i++) {
                if (!eregi('^(10│172.16│192.168).', $ips[$i])) {
                    $ip = $ips[$i];
                    break;
                }
            }
        }
        return ($ip ? $ip : $_SERVER['REMOTE_ADDR']);
    }

    /**
     * 获取IP地理位置
     * 淘宝IP接口
     * @Return: array
     */
    function getCity()
    {
        $ip =  self::get_real_ip();
        $url = 'http://ip.taobao.com/service/getIpInfo.php?ip=' . $ip;
        $response = file_get_contents($url);
        $ip = json_decode($response, TRUE);
        if ((string)$ip['code'] == '1') {//失败
            return false;
        }
//        $data = (array)$ip->data; //todo
        $data = $ip['data'];
        DebugLog::debug('article_city: ', $data); //todo
        return $data;
    }

}