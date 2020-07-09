<?php


namespace Yunshop\Poster\Listener;

use Illuminate\Contracts\Events\Dispatcher;
use Yunshop\Poster\services\ResponseEventService;
use Yunshop\Poster\services\ResponseTextService;
use Yunshop\Poster\services\ResponseDefaultService;

class WechatProcessorListener
{
    public function subscribe(Dispatcher $events)
    {
        //处理微信关键字和二维码扫码
        $events->listen(\app\common\events\WechatProcessor::class, function($event) {

            if($event->getPluginName() == 'poster'){
                $processor = $event->getProcessor();
                $msg = $processor->message;
                $msgType = strtolower($msg['msgtype']);
                $msgEvent = strtolower($msg['event']);

                \Log::debug('------海报消息-----', $msg);

                if ($msgType == 'text' || ($msgType == 'event' && $msgEvent == 'click')) { //推荐人, 生成海报
                    $response = ResponseTextService::index($msg);
                } elseif ($msgType == 'event' && ($msgEvent == 'scan' || $msgEvent == 'subscribe')) { //扫码人, 获取海报
                    $response = ResponseEventService::index($msg);
                } else {
                    $response = ResponseDefaultService::index();
                }

                if ($response['type'] == 'news'){ //图文消息
                    $event->setResponse($processor->news($response['content']));
                } elseif($response['type'] == 'image'){ //图片消息
                    $event->setResponse($processor->image($response['mediaid']));
                } else{ //文本消息
                    $event->setResponse($processor->text($response['content']));
                }
            };

        });
    }
}
