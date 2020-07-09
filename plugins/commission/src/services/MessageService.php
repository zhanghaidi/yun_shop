<?php
/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/4/15
 * Time: 下午2:49
 */

namespace Yunshop\Commission\services;


use app\common\facades\Setting;
use app\common\models\notice\MessageTemp;
use Illuminate\Support\Facades\Log;
use EasyWeChat\Foundation\Application;

class MessageService
{
    public static function notice($templateId, $data, $openId)
    {
        $app = app('wechat');
        $notice = $app->notice;
        $notice->uses($templateId)->andData($data)->andReceiver($openId)->send();
    }

    public static function becomeAgent($member)
    {
        $commissionNotice = Setting::get('plugin.commission_notice');
        $temp_id = $commissionNotice['become_agent'];
        if (!$temp_id) {
            return;
        }
        static::messageNotice($temp_id, $member);
    }

    public static function createdOrder($noticeData)
    {
        if (!\YunShop::notice()->getNotSend('commission.commission_order_title')) {
            return;
        }
        $commissionNotice = Setting::get('plugin.commission_notice');
        $temp_id = $commissionNotice['commission_order'];
        if (!$temp_id) {
            return;
        }
        $member = [
            'nickname' => $noticeData['agent']['nickname'],
            'uid' => $noticeData['agent']['uid'],
        ];
        $goodsData = '';
        foreach ($noticeData['goods'] as $goods) {
            $goodsData .= '商品名称:' . $goods['title'];
            $goodsData .= "\r\n";
            $goodsData .= '价格:' . $goods['price'];
            $goodsData .= ' × ' . $goods['total'];
            $goodsData .= "\r\n";
        }
        $noticeData['goodsData'] = $goodsData;
        $noticeData['amount'] = $noticeData['commission'];
        static::messageNotice($temp_id, $member, $noticeData);
    }

    public static function receiveOrder($noticeData)
    {
        if (!\YunShop::notice()->getNotSend('commission.commission_order_finish_title')) {
            return;
        }
        $commissionNotice = Setting::get('plugin.commission_notice');
        $temp_id = $commissionNotice['commission_order_finish'];
        if (!$temp_id) {
            return;
        }
        $member = [
            'nickname' => $noticeData['agent']['nickname'],
            'uid' => $noticeData['agent']['uid'],
        ];
        $goodsData = '';
        foreach ($noticeData['goods'] as $goods) {
            $goodsData .= '商品名称:' . $goods['title'];
            $goodsData .= "\r\n";
            // $goodsData .= '价格:' . $goods['price'];
            $goodsData .= '价格:' . \app\common\models\Goods::uniacid()->where('id', $goods['goods_id'])->select('price')->first()->price;
            $goodsData .= ' × ' . $goods['total'];
            $goodsData .= "\r\n";
        }
        $noticeData['goodsData'] = $goodsData;
        $noticeData['amount'] = $noticeData['commissinOrder']['commission'];
        $noticeData['hierarchy'] = $noticeData['commissinOrder']['hierarchy'];

        static::messageNotice($temp_id, $member, $noticeData);
    }

    public static function upgrade($levels)
    {
        if (!\YunShop::notice()->getNotSend('commission.commission_upgrade_title')) {
            return;
        }
        $commissionNotice = Setting::get('plugin.commission_notice');
        $temp_id = $commissionNotice['commission_upgrade'];
        if (!$temp_id) {
            return;
        }
        $member = [
            'nickname' => $levels['memberFans']['nickname'],
            'uid' => $levels['memberFans']['uid'],
        ];

        static::messageNotice($temp_id, $member, $levels);
    }

    public static function statement($noticeData)
    {
        if (!\YunShop::notice()->getNotSend('commission.statement_title')) {
            return;
        }
        $commissionNotice = Setting::get('plugin.commission_notice');
        $temp_id = $commissionNotice['statement'];
        if (!$temp_id) {
            return;
        }
        $member = [
            'nickname' => $noticeData['agent']['nickname'],
            'uid' => $noticeData['agent']['uid'],
        ];

        static::messageNotice($temp_id, $member, $noticeData);
    }

    public static function messageNotice($temp_id, $member, $data = [], $uniacid = '')
    {

        $params = [
            ['name' => '昵称', 'value' => $member['nickname']],
            ['name' => '时间', 'value' => date('Y-m-d H:i:s', time())],
            ['name' => '奖励金额', 'value' => $data['dividend_amount']],
            ['name' => '下级昵称', 'value' => $data['buy']['nickname']],
            ['name' => '订单编号', 'value' => $data['order']['order_sn']],
            ['name' => '订单金额', 'value' => $data['order']['price']],
            ['name' => '商品详情', 'value' => $data['goodsData']],
//            ['name' => '佣金金额', 'value' => $data['commission']],
            ['name' => '层级', 'value' => $data['hierarchy']],
            ['name' => '旧等级', 'value' => $data['oldLevel']['name']],
            ['name' => '旧一级分销比例', 'value' => $data['oldLevel']['first_level']],
            ['name' => '旧二级分销比例', 'value' => $data['oldLevel']['second_level']],
            ['name' => '旧三级分销比例', 'value' => $data['oldLevel']['third_level']],
            ['name' => '新等级', 'value' => $data['newLevel']['name']],
            ['name' => '新一级分销比例', 'value' => $data['newLevel']['first_level']],
            ['name' => '新二级分销比例', 'value' => $data['newLevel']['second_level']],
            ['name' => '新三级分销比例', 'value' => $data['newLevel']['third_level']],
            ['name' => '结算时间', 'value' => date('Y-m-d H:i:s', time())],
            ['name' => '佣金金额', 'value' => $data['amount']],

        ];

        $news_link = MessageTemp::find($temp_id)->news_link;

        $msg = MessageTemp::getSendMsg($temp_id, $params);
        if (!$msg) {
            return;
        }
        \app\common\services\MessageService::notice(MessageTemp::$template_id, $msg, $member['uid'], $uniacid,$news_link);
    }
}