<?php


namespace Yunshop\Poster\Listener;

use Illuminate\Contracts\Events\Dispatcher;
use Yunshop\Poster\services\ResponseEventService;
use Yunshop\Poster\services\ResponseTextService;

class WechatMessageListener
{
    public function subscribe(Dispatcher $events)
    {
        if (config('APP_Framework') == 'platform') {
            // 海报，都是关键字触发，所以要先找出关键字，拿到模块名，判断模块名是否是yun_shop
            $events->listen(\app\common\events\WechatMessage::class, function (\app\common\events\WechatMessage $event) {
                // 标志位，为true时才向微信发送数据
                $replyStatus = false;
                $message = $event->getMessage();
                $server = $event->getServer();
                switch ($message['MsgType']) {
                    case 'event':
                        $reply = $this->eventMessage($message, $replyStatus);
                        break;
                    case 'text':
                        $reply = $this->keywordMessage($message, $replyStatus);
                        break;
                    case 'image':
                        $reply = '收到图片消息';
                        break;
                    case 'voice':
                        $reply = '收到语音消息';
                        break;
                    case 'video':
                        $reply = '收到视频消息';
                        break;
                    case 'location':
                        $reply = '收到坐标消息';
                        break;
                    case 'link':
                        $reply = '收到链接消息';
                        break;
                    // ... 其它消息
                    default:
                        $reply = '收到其它消息';
                        break;
                }
                if ($replyStatus && !empty($reply)) {
                    $server->setMessageHandler(function ($message) use ($reply) {
                        return $reply;
                    });
                    $server->serve()->send();
                }
            });
        }

    }

    // 消息转换，原本是走微擎的消息回复，现在使用easywechat的
    public function transformResponse($response)
    {
        if ($response['type'] == 'news'){ //图文消息
            $reply = new  \EasyWeChat\Message\News([
                'title'       => $response['content']['title'],
                'description' => $response['content']['description'],
                'url'         => $response['content']['url'],
                'image'       => yz_tomedia($response['content']['picurl']),
            ]);
        } elseif($response['type'] == 'image'){ //图片消息
            $reply = new \EasyWeChat\Message\Image();
            $reply->setAttribute('media_id', $response['mediaid']);
        } else{ //文本消息
            $reply = new  \EasyWeChat\Message\Text();
            $reply->setAttribute('content', $response['content']);
        }
        return $reply;
    }

    public function eventMessage($message,&$replyStatus)
    {
        $reply = "感谢您的关注!";
        switch ($message['Event']) {
            case 'subscribe':
                if (!empty($message['Ticket'])) {//事件必须是关注，并且必须有Ticket
                    $msg['event'] = $message['Event'];
                    $msg['eventkey'] = $message['EventKey'];
                    $msg['fromusername'] = $message['FromUserName'];
                    $response = ResponseEventService::index($msg);
                    $reply = $this->transformResponse($response);
                    $replyStatus = true;
                }
                break;
            case 'unsubscribe':
                break;
            case 'CLICK': // 菜单点击事件，关键字就是EventKey，找出该点击事件的关键字，是海报模块的才执行
                $ruleKeyword = \app\common\modules\wechat\models\RuleKeyword::getRuleKeywordByKeywords($message['EventKey']);
                if (!empty($ruleKeyword)) {
                    if ($ruleKeyword->module == 'yun_shop') { // 是海报插件的回复内容
                        $msg['content'] = $message['EventKey'];
                        $msg['fromusername'] = $message['FromUserName'];
                        try {
                            $response = ResponseTextService::index($msg);
                            $reply = $this->transformResponse($response);
                        } catch (\Exception $e) {
                            $reply = $e->getMessage();
                        }
                        $replyStatus = true;
                    }
                }
                break;
            case 'VIEW':
                break;
            case 'SCAN':
                if (!empty($message['Ticket'])) {//事件必须是扫码，并且必须有Ticket。后续需要考虑先去判断场景是不是海报场景，再执行
                    $msg['event'] = $message['Event'];
                    $msg['eventkey'] = $message['EventKey'];
                    $msg['fromusername'] = $message['FromUserName'];
                    $response = ResponseEventService::index($msg);
                    $reply = $this->transformResponse($response);
                    $replyStatus = true;
                }
                break;
            default:
                break;
        }
        return $reply;
    }

    // 关键字回复
    public function keywordMessage($message,&$replyStatus)
    {
        $reply = "感谢您的关注!";
        $ruleKeyword = \app\common\modules\wechat\models\RuleKeyword::getRuleKeywordByKeywords($message['Content']);
        // 找不到关键字，则默认回复
        if (!empty($ruleKeyword)) {
            // 是海报插件的回复内容
            if ($ruleKeyword->module == 'yun_shop') {
                $msg['content'] = $message['Content'];
                $msg['fromusername'] = $message['FromUserName'];
                try {
                    $response = ResponseTextService::index($msg);
                    $reply = $this->transformResponse($response);
                } catch (\Exception $e) {
                    $reply = $e->getMessage();
                }
                $replyStatus = true;
            }
        }
        return $reply;
    }
}
