<?php

namespace Yunshop\Wechat\admin\staff\controller;

use app\common\components\BaseController;
use Yunshop\Wechat\common\model\ChatsRecord;
use Yunshop\Wechat\admin\fans\model\Fans;
use Yunshop\Wechat\common\model\WechatAttachment;
use app\common\modules\wechat\WechatApplication;
use Yunshop\Wechat\common\helper\Helper;
use app\common\helpers\Url;
use app\platform\modules\application\models\UniacidApp;
use Yunshop\Wechat\admin\material\model\WechatAttachmentImage;


class StaffController extends BaseController
{
    public function index()
    {
        $openid = request()->openid;
        // 获取粉丝信息
        $fans = Fans::uniacid()->with('hasOneMember')->where('follow','=',1)->where('openid',$openid)->first();
        if (empty($fans)) {
            return $this->message('粉丝不存在!',Url::absoluteWeb('plugin.wechat.admin.fans.controller.fans.index'),'danger');
        }
        $fans = $fans->toArray();
        // 获取与粉丝近20条回复信息
        $chatsRecords = ChatsRecord::uniacid()->where('openid','=',$openid)->orderBy('createtime','desc')->limit(20)->get();
        if (!empty($chatsRecords)) {
            $chatsRecords = $chatsRecords->toArray();
            foreach ($chatsRecords as &$chatsRecord) {
                switch ($chatsRecord['msgtype']) {
                    case ChatsRecord::IMAGE :
                    case ChatsRecord::VOICE :
                    case ChatsRecord::VIDEO :
                        $wechatAttachment = WechatAttachment::uniacid()->select('attachment')->where('media_id',$chatsRecord['content']['media_id'])->first();
                        if (empty($wechatAttachment)) {
                            $chatsRecord['content']['attachment'] = $chatsRecord['content']['picurl'];
                        } else {
                            $chatsRecord['content']['attachment'] = yz_tomedia($wechatAttachment->attachment,true);
                        }
                        break;
                    case ChatsRecord::NEWS :
                        $wechatAttachment = WechatAttachment::getWechatAttachmentAndNewsByMediaId($chatsRecord['content']['media_id']);
                        if (!empty($wechatAttachment)) {
                            $wechatAttachment = $wechatAttachment->toArray();
                            $chatsRecord['content']['attachment'] = $wechatAttachment['has_many_news'][0]['thumb_url'];
                        }
                        break;
                }
                // 头像和昵称设置设置
                if ($chatsRecord['flag'] == 1) {
                    $uniacidApp = UniacidApp::uniacid()->first();
                    if(!empty($uniacidApp)) {
                        $uniacidApp = $uniacidApp->toArray();
                        $chatsRecord['avatar'] = $uniacidApp['img'];
                        $chatsRecord['nickname'] = $uniacidApp['name'];
                    }
                } else {
                    $chatsRecord['avatar'] = $fans['has_one_member'][0]['avatar'];
                    $chatsRecord['nickname'] = $fans['has_one_member'][0]['nickname'];
                }
            }
        } else {
            $chatsRecords = [];
        }
        $thumb = WechatAttachmentImage::getAttachmentThumb()->count();
        $fans['thumb'] = $thumb;
        $fans['chatsRecords'] = $chatsRecords;
        return view('Yunshop\Wechat::admin.staff.index',['data'=>json_encode($fans)]);
    }

    // 作为客服发送消息，这里仅取页面传入的第一条进行发送
    public function sendMessage()
    {
        $messages = request()->messages;
        $openid = request()->openid;
        if (empty($openid)) {
            return $this->errorJson('openid不能为空');
        }
        $message = $messages[0];
        if (empty($message) || empty($message['msgtype']) || empty($message['content'])) {
            return $this->errorJson('消息不能为空');
        }
        \Log::info('-------客服回复粉丝消息------openid:'.$openid,$message);
        $record = [];// 记录本地的数组信息
        switch ($message['msgtype']) {
            case ChatsRecord::TEXT :
                $text = new  \EasyWeChat\Message\Text();
                $text->setAttribute('content', $message['content']['content']);
                $reply = $text;
                $record['content'] = $message['content']['content'];
                break;
            case ChatsRecord::IMAGE :
                $image = new \EasyWeChat\Message\Image();
                $image->setAttribute('media_id', $message['content']['media_id']);
                $reply = $image;
                $record['media_id'] = $message['content']['media_id'];
                break;
            case ChatsRecord::VOICE :
                $voice = new \EasyWeChat\Message\Voice();
                $voice->setAttribute('media_id', $message['content']['media_id']);
                $reply = $voice;
                $record['media_id'] = $message['content']['media_id'];
                break;
            case ChatsRecord::VIDEO :
                $video = new \EasyWeChat\Message\Video();
                $video->media_id = $message['content']['media_id'];
                $reply = $video;
                $record['media_id'] = $message['content']['media_id'];
                break;
            case ChatsRecord::MUSIC :
                $thumb = WechatAttachmentImage::getAttachmentThumb()->first();
                if ($thumb){
                    $music = new  \EasyWeChat\Message\Music();
                    $music->title = $message['content']['title'];
                    $music->description = $message['content']['description'];
                    $music->url = $message['content']['url'];
                    $music->hq_url = $message['content']['hqurl'];
                    $music->thumb_media_id =  $thumb->media_id;
                    $reply = $music;
                    $record['content'] = '音乐:'.$message['content']['title'].' 链接:'.$message['content']['url'];
                }else{
                    return $this->errorJson("请上传一张缩略图");
                }
                break;
            case ChatsRecord::NEWS :
                $newsAttachment = WechatAttachment::getWechatAttachmentAndNewsByMediaId($message['content']['media_id']);
                if (!empty($newsAttachment)) {//"CEpkcTwQNWB9QY5mwGqtzI502XjruimVstsVojCWdKc"
                    $newsAttachment = $newsAttachment->toArray();
                    $news = new  \EasyWeChat\Message\News([
                        'title'       => $newsAttachment['has_many_news'][0]['title'],
                        'description' => $newsAttachment['has_many_news'][0]['description'],
                        'url'         => $newsAttachment['has_many_news'][0]['url'],
                        'image'       => $newsAttachment['has_many_news'][0]['thumb'],
                    ]);
                    $reply =  $news;
                    $record['media_id'] = $message['content']['media_id'];
                } else {
                    return $this->errorJson("图文不存在");
                }
                break;
            default :
                $reply = null;
                break;
        }
        if (!empty($reply)) {
            $wechat = new WechatApplication();
            try {
                // 发送微信
                $result = $wechat->staff->message($reply)->to($openid)->send();
                \Log::info('-----微信客服消息-----openid:'.$openid,$reply);
            } catch (\Exception $exception) {
                return $this->errorJson(Helper::getErrorMessage($exception->getCode(),$exception->getMessage()));
            }
            // 存储本地
            $chatsRecord = new ChatsRecord();
            $chatsRecord->uniacid = \YunShop::app()->uniacid;
            $chatsRecord->flag = ChatsRecord::STAFF;
            $chatsRecord->openid = $openid;
            $chatsRecord->msgtype = $message['msgtype'];
            $chatsRecord->content = $record;
            $chatsRecord->createtime = time();
            $chatsRecord->save();
            return $this->successJson("发送成功");
        }
    }
}
