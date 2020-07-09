<?php

namespace Yunshop\Article\api;

use app\common\components\ApiController;
use app\common\models\MemberShopInfo;
use Illuminate\Support\Facades\DB;
use Yunshop\Article\models\Article;
use Yunshop\Article\services\ArticleService;
use Yunshop\Article\models\Category;
use app\common\facades\Setting;
use app\backend\modules\member\models\MemberLevel;
use Yunshop\Article\models\Report;
use app\common\exceptions\AppException;
use app\common\models\AccountWechats;
use EasyWeChat\Foundation\Application;
use Yunshop\ArticlePay\models\ArticleModel;
use Yunshop\ArticlePay\models\RecordModel;
use app\common\services\Utils;

class ArticleController extends ApiController
{

    public function getArticleSet()
    {
        $setting = Setting::get('plugin.article');

        $setting['center'] = $setting['center'] ? $setting['center'] : '文章中心';

        if ($setting) {
            $setting['banner'] = yz_tomedia($setting['banner']);
            return $this->successJson('成功', $setting);
        }
        return $this->errorJson('失败');

    }

    /**
     * 获取(全部或者指定分类的)文章概述的列表(不包含文章正文内容) & 所有文章分类 & "文章营销"的基础设置
     * @return mixed
     */
    public function getArticles()
    {
        $setting = setting::get('plugin.article');
        if ($setting['enabled'] == 0) {
            return $this->errorjson('文章已经关闭!');
        }
        /**
         * todo 太慢
         */
//        $areaauthority = articleservice::authorizearealimit();
//        if (!$areaauthority) {
//            return $this->errorjson('对不起, 您所在的地理区域暂无阅读文章的权限!');
//        }

        $pagesize = $setting['num_per_page'] ?: 10;

        $member_id = \YunShop::app()->getMemberId();

        $category_id = intval(\yunshop::request()->category_id) ?: 0;
        if (!empty($category_id)) { //获取指定分类的文章的概述
            $lists = article::getarticleoverviewsbycategory($category_id,$member_id)->paginate($pagesize);
        } else { //获取全部文章的概述
            $lists = article::getarticleoverviews($member_id)->paginate($pagesize);
        }

        $lists->map(function ($article) {
            if ($article->thumb) {
                $article->thumb = yz_tomedia($article->thumb);
            }
        });

        if (app('plugins')->isEnabled('article-pay')) {
            $article_pay = 1;
        } else {
            $article_pay = 0;
        }

        $articles = $lists->toarray();

        if ($articles['total'] < 1) {
            return $this->errorjson('没有文章!');
        }

        $articles = $lists->toarray();
        $categories = category::getcategorys()->get()->toarray();

        return $this->successJson('获取文章和分类数据成功',
            array(
                'title' => $setting['title'] ? html_entity_decode($setting['title']) : '文章列表',
                'banner' => yz_tomedia($setting['banner']),
                'template_type' => $setting['template_type'],
                'categories' => $categories, //所有分类
                'articles' => $articles, //所有文章,
                'article_pay' => $article_pay
            )
        );

    }

    /**
     * 阅读文章(包括文章分享奖励)
     * @return mixed
     */
    public function getArticle()
    {
            $setting = Setting::get('plugin.article');
        if ($setting['enabled'] == 0) {
            return $this->errorJson('文章已经关闭!');
        }

        $clickUid = \YunShop::app()->getMemberId(); //阅读者的用户id
        if (empty($clickUid)) {
            return;
        }

        $articleId = intval(\YunShop::request()->article_id);
        if (empty($articleId)) {
            $this->errorJson('请传入正确参数!');
        }

        $is_article = Article::getArticle($articleId,$clickUid);
        if (!$is_article) {
            $this->errorJson('文章不存在!');
        }

        if (app('plugins')->isEnabled('article-pay')) {
            $articleMoney = ArticleModel::uniacid()->where("status",0)->where("article_id",$articleId)->value("money");

            if ($articleMoney > 0) {
                $record = RecordModel::uniacid()->where("article_id",$articleId)->where("member_id",$clickUid)->count();

                if ($record < 1) {
                    return $this->errorJson("您尚未购买该文章内容,无法观看");
                }
            }
        }

        $member = MemberShopInfo::uniacid()->ofMemberId(\YunShop::app()->getMemberId())->withLevel()->first();
        $this->articleMemberLevelLimit($is_article, $member);

        /**
         * todo 太慢
         */
        //检查是否满足地区限制
//        $areaAuthority = ArticleService::authorizeAreaLimit();
//        if (!$areaAuthority) {
//            return $this->errorJson('对不起, 您所在的地理区域暂无阅读文章的权限!');
//        }

        // todo 新商城暂时没有等级限制功能
        //检查是否满足用户等级限制
//        $levelAuthority = ArticleService::authorizeMemberLevel($clickUid, $articleId);
//        if (!$levelAuthority) { //todo 查询待优化
//            $articleInfo = Article::getArticle($articleId);
//            $categoryInfo = Category::getCategory($articleInfo['category_id']);
//            $memberLevelLimitId = $categoryInfo['member_level_id_limit'];
//            $level = MemberLevel::uniacid()->where('id', $memberLevelLimitId)->first();
//            return $this->errorJson('您当前还没获得阅读权限, 本篇文章仅限"' . $level->level_name . '"等级的会员阅读!');
//        }

        //写入阅读记录
        ArticleService::setRead($clickUid, $articleId);

        //重新获取，解决前端阅读记录与后端阅读记录不同
        $article = Article::getArticle($articleId,$clickUid);

        //记录分享并奖励
        $shareUid = intval(\YunShop::request()->mid) ?: 0;//分享者用户ID
        if ($shareUid && ($shareUid != $clickUid) && ($article->point || $article->credit)) { //有分享者, 而且不是自己点击自己的分享文章, 而且该文章的奖励不为0
            ArticleService::setShare($article, $shareUid, $clickUid);
        }

        if ($article->advs_img) { //todo 待优化
            $article->advs_img = yz_tomedia($article->advs_img);
        }

        $article->thumb = yz_tomedia($article->thumb);
        $article->content = html_entity_decode($article->content);

        $article->wxJsSdkConfig = $this->wxJsSdkConfig();

        $article->miQrCodeUrl = self::getSmallQrCode($articleId);
        $article->qr = $setting['qr'];

        return $this->successJson('获取文章成功', $article);
    }
    public function wxJsSdkConfig($message = '')
    {
        $url = \YunShop::request()->url;

        if (\YunShop::request()->type == 2) {
            $pay = \Setting::get('plugin.min_app');

            if (!empty($pay['key']) && !empty($pay['secret'])) {
                $app_id = $pay['key'];
                $secret = $pay['secret'];
            }
        } else {
            $pay = \Setting::get('shop.pay');

            if (!empty($pay['weixin_appid']) && !empty($pay['weixin_secret'])) {
                $app_id = $pay['weixin_appid'];
                $secret = $pay['weixin_secret'];
            } else {
                $account = AccountWechats::getAccountByUniacid(\YunShop::app()->uniacid);

                $app_id = $account->key;
                $secret = $account->secret;
            }
        }

        $options = [
            'app_id' => $app_id,
            'secret' => $secret
        ];
        $app = new Application($options);

        $js = $app->js;
        $js->setUrl($url);

        $config = $js->config(array(
            'onMenuShareTimeline',
            'onMenuShareAppMessage',
            'showOptionMenu',
            'scanQRCode',
            'updateAppMessageShareData',
            'updateTimelineShareData',
            'startRecord',
            'stopRecord',
            'playVoice',
            'pauseVoice',
            'stopVoice',
            'uploadVoice',
            'downloadVoice'
        ));
       return  $config;
    }

    public function articleMemberLevelLimit($article, $member)
    {
        if (empty($article->show_levels) && $article->show_levels !== '0') {
            return $this->successJson('成功');
        }
        $show_levels = explode(',', $article->show_levels);
        if ($article->show_levels !== '0') {
            $level_names = MemberLevel::select(DB::raw('group_concat(level_name) as level_name'))->whereIn('id', $show_levels)->value('level_name');
            if (empty($level_names)) {
                return $this->errorJson('会员等级不足');
            }
        }
        if (!in_array($member->level_id, $show_levels)) {
            $ordinaryMember = in_array('0', $show_levels)? '普通会员 ':'';

            throw new AppException('文章(' . $article->title . ')仅限' . $ordinaryMember.$level_names . '浏览');
        }
    }

    /**
     * 点赞
     * @return mixed
     */
    public function like()
    {
        $uid = \YunShop::app()->getMemberId(); //阅读者
        \Log::debug('article_get_uid: ' . $uid);
        $articleId = intval(\YunShop::request()->article_id) ?: 0;

        if (!$uid) {
            return $this->errorJson('未获取到会员信息!');
        }

        if (!$articleId) {
            return $this->errorJson('未获取到文章信息!');
        }

        $like = ArticleService::setLike($uid, $articleId);
        if ($like['log']['liked'] == 0) {
            return $this->successJson('取消点赞成功', array('like_num' => $like['article']['like_num'],'liked'=>0));
        } else {
            return $this->successJson('点赞成功', array('like_num' => $like['article']['like_num'],'liked'=>1));
        }
    }

    /**
     * 举报
     * @return mixed
     */
    public function report()
    {
        $articleId = intval(\YunShop::request()->article_id);
//        $uid = intval(\YunShop::request()->uid);
        $uid = \YunShop::app()->getMemberId(); //阅读者
        $desc = trim(\YunShop::request()->desc);

        if (!$uid) {
            return $this->errorJson('未获取到会员信息!');
        }

        if (!$articleId) {
            return $this->errorJson('未获取到文章信息!');
        }

        if (!$desc) {
            return $this->errorJson('未获取到举报内容!');
        }

        $articleModel = Article::getArticle($articleId);
        if (empty($articleModel)) {
            return $this->errorJson('文章不存在!');
        }

        $reportModel = Report::getReportByUidAndArticleId($uid, $articleId)->first();
        if ($reportModel) {
            return $this->errorJson('您已经举报过该文章, 正在处理!');
        }

        $report = ArticleService::setReport($uid, $articleId, $desc);
        if ($report) {
            return $this->successJson('举报成功');
        } else {
            return $this->errorJson('举报失败');
        }
    }

    public function audioArticle()
    {
        $setting = Setting::get('plugin.article');
        $pageSize = $setting['num_per_page'] ?: 10;
        $display_order = request()->display_order;

        $list = Article::getAudioArticle($display_order)->paginate($pageSize);

        return $this->successJson('获取音频文章成功!', $list);
    }


    /**
     *生成小程序二维码
     */

//    public function getCode(){
//        $id = \YunShop::request()->article_id;
//        $miQrCodeUrl = self::getSmallQrCode($id);
//        if($miQrCodeUrl){
//            return $this->successJson($msg = '成功', ['miQrCodeUrl'=>$miQrCodeUrl]);
//        }else{
//            return $this->errorJson($msg = '失败', $miQrCodeUrl);
//        }
//    }
    public function getSmallQrCode($id)
    {
        \Log::info('接收',$id);
        $member_id = \YunShop::app()->getMemberId();
        $small_url = "pages/member/index_v2/index_v2";
        // $small_url = "pages/index/index";
        $url = "https://api.weixin.qq.com/wxa/getwxacodeunlimit?";
        $token = $this->getToken();
        \Log::debug('===========access_token===========', $token);
        $url .= "access_token=" . $token;

        $postdata = [
            "page"  => $small_url,
            "scene" => 'mid=' . $member_id

        ];
        \Log::info('接收',$postdata);
        $path = storage_path('static/article/images/' . \YunShop::app()->uniacid);

        if (!is_dir($path)) {
            Utils::mkdirs($path);
        }
        \Log::debug('=====地址信息=======', $postdata);

        $res = $this->curl_post($url, json_encode($postdata), $options = array());

        $erroe = json_decode($res);
        \Log::info('接收',$postdata);
        \Log::info('接收',json_encode($postdata));
        $data['message'] = "";
        $data['file_path'] = "";
        $data['code'] = 0;

        if (isset($erroe->errcode)) {
            $data['message'] = '错误码' . $erroe->errcode . ';错误信息' . $erroe->errmsg;
            $data['code'] = 1;
            return $data;
        }
        \Log::debug('===========生成二维码===========', $res);
        $file = date('YmdHis').'-'.$member_id . '-' .'.png';
        file_put_contents($path . '/' . $file, $res);
        $img = imagecreatefromstring(file_get_contents($path . '/' . $file));
        $qrCodeUrl = $path.'/'.$file;
        if (config('app.framework') == 'platform') {
            $urlPath = request()->getSchemeAndHttpHost() . '/' . substr($qrCodeUrl, strpos($qrCodeUrl, 'storage'));
        } else {
            $urlPath = request()->getSchemeAndHttpHost() . '/' . substr($qrCodeUrl, strpos($qrCodeUrl, 'addons'));
        }
        return $urlPath;
    }

    public function curl_post($url = '', $postdata = '', $options = array())
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($options)) {
            curl_setopt_array($ch, $options);
        }
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    //发送获取token请求,获取token(2小时)
    public function getToken()
    {
        $url = $this->getTokenUrlStr();
        $res = $this->curl_post($url, $postdata = '', $options = array());

        $data = json_decode($res, JSON_FORCE_OBJECT);
        return $data['access_token'];
    }

    //获取token的url参数拼接
    public function getTokenUrlStr()
    {
        $set = \Setting::get('plugin.min_app');

        $getTokenUrl = "https://api.weixin.qq.com/cgi-bin/token?"; //获取token的url
        $WXappid = $set['key']; //APPID
        $WXsecret = $set['secret']; //secret
        $str = $getTokenUrl;
        $str .= "grant_type=client_credential&";
        $str .= "appid=" . $WXappid . "&";
        $str .= "secret=" . $WXsecret;
        return $str;
    }

}