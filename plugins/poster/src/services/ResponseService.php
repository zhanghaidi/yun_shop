<?php

namespace Yunshop\Poster\services;

use Yunshop\Poster\models\Poster;
use app\common\models\AccountWechats;

//对微信的默认回复
class ResponseService
{

    public function __construct()
    {
        $this->setCookie();
    }

    /*
     * 判断请求的时间是否在活动海报的有效时间内
     */
    protected static function checkTime($timeStart, $timeEnd)
    {
        $now = time();

        if ($now < $timeStart){
            $status = Poster::NOT_YET_START; //活动还未开始
        } else if ($now > $timeEnd){
            $status = Poster::ALREADY_FINISHED; //活动已结束
        } else {
            $status = Poster::IN_TIME;
        }

        return $status;
    }

    /*
     * 动态显示时间
     */
    protected static function dynamicTime($poster, $notice)
    {
        if(preg_match('/\[.+time\]/', $notice)){
            $notice = str_replace('[starttime]', date('Y年m月d日 H:i', $poster->time_start), $notice);
            $notice = str_replace('[endtime]', date('Y年m月d日 H:i', $poster->time_end), $notice);
        }
        return $notice;
    }

    //动态显示昵称
    protected static function dynamicName($userName, $notice)
    {
        if (preg_match('/\[nickname\]/', $notice)){
            $notice = str_replace('[nickname]', $userName, $notice);
        }
        return $notice;
    }

    //动态显示奖励数量
    //$subject 表示替换的是给recommender的奖励通知, 还是给subscriber的奖励通知
    protected static function dynamicAward(Poster $poster, $notice, $forWho){
        if($forWho == 'recommender'){
            $notice = str_replace('[credit]', $poster->supplement->recommender_credit, $notice);
            $notice = str_replace('[money]', $poster->supplement->recommender_bonus, $notice);
            $notice = str_replace('[couponname]', $poster->supplement->recommender_coupon_name, $notice);
            $notice = str_replace('[couponnum]', $poster->supplement->recommender_coupon_num, $notice);
        } else if ($forWho == 'subscriber'){
            $notice = str_replace('[credit]', $poster->supplement->subscriber_credit, $notice);
            $notice = str_replace('[money]', $poster->supplement->subscriber_bonus, $notice);
            $notice = str_replace('[couponname]', $poster->supplement->subscriber_coupon_name, $notice);
            $notice = str_replace('[couponnum]', $poster->supplement->subscriber_coupon_num, $notice);
        }

        return $notice;
    }

    /**
     * 设置Cookie存储
     *
     * @return void
     */
    private function setCookie()
    {
        $session_id = '';
        if (isset(\YunShop::request()->state) && !empty(\YunShop::request()->state) && strpos(\YunShop::request()->state, 'yz-')) {
            $pieces = explode('-', \YunShop::request()->state);
            $session_id = $pieces[1];
            unset($pieces);
        }

        if (empty($session_id) && \YunShop::request()->session_id &&
            \YunShop::request()->session_id != 'undefined') {
            $session_id = \YunShop::request()->session_id;
        }

        if (!empty($session_id)) {
            session_id($session_id);
        }

        session_start();
    }

    /**
     * 获取公众号的 appID 和 appsecret
     * @return array
     */
    public static function wechatConfig()
    {
        $account = AccountWechats::getAccountByUniacid(\YunShop::app()->uniacid);
        $options = [
            'app_id'  => $account->key,
            'secret'  => $account->secret,
        ];

        return $options;
    }

}
