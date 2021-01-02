<?php
namespace app\Console\Commands;

use app\Console\Commands\CourseReminderAloneMiniApp;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class NotPaidOrderNoticeAloneMiniApp extends Command
{
    protected $signature = 'command:notpaidordernotice-aloneminiapp';

    protected $description = '待支付订单提醒 - 多个小程序';

    public function handle()
    {
        // 1、获取所有的小程序、及其公众号APPID
        $miniAppRs = CourseReminderAloneMiniApp::getAloneMiniApp();

        $appSetRs = DB::table('yz_setting')->select('id', 'uniacid', 'key', 'value')
            ->where('group', 'shop')
            ->whereIn('key', ['trade', 'notice'])->get()->toArray();

        $messageTemplateRs = DB::table('yz_message_template')
            ->where('notice_type', 'order_not_paid')->get()->toArray();

        foreach ($miniAppRs as $v1) {
            // 待支付订单提醒时间设置为空
            $tempTradeSet = [];
            foreach ($appSetRs as $v2) {
                if ($v1['min_app']['uniacid'] != $v2['uniacid']) {
                    continue;
                }
                if ($v2['key'] != 'trade') {
                    continue;
                }
                $tempTradeSet = unserialize($v2['value']);
                break;
            }
            if (!isset($tempTradeSet['not_paid_notice_minutes']) || $tempTradeSet['not_paid_notice_minutes'] <= 0) {
                continue;
            }

            // 商城提醒未开启、待支付订单提醒未开启
            $tempNoticeSet = [];
            foreach ($appSetRs as $v3) {
                if ($v1['min_app']['uniacid'] != $v3['uniacid']) {
                    continue;
                }
                if ($v3['key'] != 'notice') {
                    continue;
                }
                $tempNoticeSet = unserialize($v3['value']);
                break;
            }
            if (!isset($tempNoticeSet['toggle']) || $tempNoticeSet['toggle'] != 1) {
                continue;
            }
            if (!isset($tempNoticeSet['order_not_paid']) || $tempNoticeSet['order_not_paid'] == '' ||
                $tempNoticeSet['order_not_paid'] <= 0
            ) {
                continue;
            }

            // 待支付订单提醒模板未配置
            $tempTemplateRs = [];
            foreach ($messageTemplateRs as $v4) {
                if ($v1['min_app']['uniacid'] != $v4['uniacid']) {
                    continue;
                }
                $tempTemplateRs = $v4;
                break;
            }
            if (!isset($tempTemplateRs['id'])) {
                continue;
            }

            $this->notPaidOrderNotice($v1, $tempTradeSet, $tempNoticeSet, $tempTemplateRs);
        }
    }

    private function notPaidOrderNotice($appRs, $tradeSetRs, $noticeSetRs, $messageTemplate)
    {
        // 模板消息内容
        $messageTemplate['data'] = json_decode($messageTemplate['data'], true);
        if ($messageTemplate['data'] == false) {
            return false;
        }

        $nowTime = time();
        $waitSeconds = $tradeSetRs['not_paid_notice_minutes'] * 60;
        $checkTimeRange = [$nowTime - $waitSeconds - 60, $nowTime - $waitSeconds];

        // 查询待支付订单（下单时间距离现在n~n+1分钟）
        $orderRs = DB::table('yz_order')->select('id', 'uid', 'order_sn', 'price', 'create_time')
            ->where('uniacid', $appRs['min_app']['uniacid'])
            ->whereBetween('create_time', $checkTimeRange)
            ->where('status', 0)->get()->toArray();
        $orderIds = array_column($orderRs, 'id');
        $orderIds = array_values(array_unique($orderIds));
        if (!isset($orderIds[0])) {
            return false;
        }
        $userIds = array_column($orderRs, 'uid');
        $userIds = array_values(array_unique($userIds));

        // 查询待支付订单关联的商品
        $goodsRs = DB::table('yz_order_goods')->select('id', 'order_id', 'title')
            ->whereIn('order_id', $orderIds)->get()->toArray();

        // 查询用户openid
        $wxappUser = DB::table('diagnostic_service_user')
            ->select('id', 'ajy_uid', 'uniacid', 'openid', 'shop_openid', 'unionid')
            ->whereIn('ajy_uid', $userIds)->get()->toArray();
        $wechatUser = DB::table('mc_mapping_fans')->select('uniacid', 'uid', 'unionid', 'openid')
            ->whereIn('uid', $userIds)
            ->where('follow', 1)->get()->toArray();

        foreach ($orderRs as $v1) {
            $tempGoods = [];
            foreach ($goodsRs as $v2) {
                if ($v1['id'] != $v2['order_id']) {
                    continue;
                }
                $tempGoods = $v2;
                break;
            }

            $tempWxapp = [];
            foreach ($wxappUser as $v3) {
                if ($v1['uid'] != $v3['ajy_uid']) {
                    continue;
                }
                $tempWxapp = $v3;
                break;
            }
            $tempWechat = [];
            foreach ($wechatUser as $v4) {
                if ($v1['uid'] != $v4['uid']) {
                    continue;
                }
                $tempWechat = $v4;
                break;
            }

            $tempOpenid = '';
            if (isset($tempWechat['uid'])) {
                $tempOpenid = $tempWechat['openid'];
                $type = 'wechat';
            } else {
                // if (isset($tempWxapp['shop_openid']) && $tempWxapp['shop_openid'] != '') {
                //     $tempOpenid = $tempWxapp['shop_openid'];
                // } else {
                //     $tempOpenid = $tempWxapp['openid'];
                // }
                $tempOpenid = $tempWxapp['openid'];
                $type = 'wxapp';
            }

            $tempUser = [];

        }
    }

    private function makeJobParam($type, $appRs, $order, $goods, $template, $tradeSet)
    {
        if (!in_array($type, ['wechat', 'wxapp'])) {
            return [];
        }

        $jumpPage = '/pages/template/rumours/index?share=1&shareUrl=';
        $jumpTail = '/pages/shopping/order_detail/index?id=' . $order['id'];
        $jumpTail = $jumpPage . urlencode($jumpTail);

        $param = [
            'order_sn' => $order['order_sn'],
            'amount' => $order['price'],
            'create_time' => date('Y-m-d H:i', $order['create_time']),
            'expire_time' => date('Y-m-d H:i', $order['create_time'] + intval($tradeSet['close_order_days']) * 86400),
            'goods_title' => $goods['title'],
        ];

        if ($type == 'wechat') {
            if (!isset($appRs['wechat']['value']['app_id'])) {
                return [];
            }
            $param['options'] = [
                'app_id' => $appRs['wechat']['value']['app_id'],
                'secret' => $appRs['wechat']['value']['app_secret'],
            ];
        } else {
            if (!isset($appRs['min_app']['value']['key'])) {
                return [];
            }
            $param['options'] = [
                'app_id' => $appRs['min_app']['value']['key'],
                'secret' => $appRs['min_app']['value']['secret'],
            ];
        }

        $param['template_id'] = $template['template_id'];
        $param['notice_data'] = [];
        $param['notice_data']['first'] = ['value' => $template['first'], 'color' => $template['first_color']];

    }
}
