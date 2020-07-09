<?php
/**
 * Created by PhpStorm.
 * User: CHUWU
 * Date: 2019/2/27
 * Time: 10:38
 */
namespace Yunshop\Wechat\Listener;

use Illuminate\Contracts\Events\Dispatcher;
use Yunshop\Wechat\common\model\Rule;
use app\common\facades\Setting;
use Yunshop\Wechat\common\model\RuleKeyword;
use Yunshop\Wechat\common\model\WechatAttachment;
use app\frontend\modules\member\models\McMappingFansModel;
use app\common\modules\wechat\WechatApplication;
use Yunshop\Wechat\admin\fans\model\Member;
use Yunshop\Wechat\common\model\Fans;
use Yunshop\Wechat\common\model\ChatsRecord;
use Yunshop\Wechat\service\FansService;
use Yunshop\Wechat\admin\material\model\WechatAttachmentImage;

class WechatMessageListener
{
    public function subscribe(Dispatcher $events)
    {
        //处理微信关键字和二维码扫码
        $events->listen(\app\common\events\WechatMessage::class, function(\app\common\events\WechatMessage $event) {
            // 标志位，为true时才向微信发送数据
            $replyStatus = false;
            $wechatApp = $event->getWechatApp();
            $message = $event->getMessage();
            $server = $event->getServer();
            \Log::info('-------微信公众号消息-------',$message);
            $this->messageRecord($wechatApp,$message);
            switch ($message['MsgType']) {
                case 'event':
                    $reply = $this->eventMessage($message,$replyStatus);
                    break;
                case 'text':
                    $reply = $this->keywordMessage($message['Content'],$replyStatus);
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
            \Log::info('----wechat:to_wechat_message----replyStatus:'.$replyStatus,$reply);
            if ($replyStatus && !empty($reply)) {
                $server->setMessageHandler(function ($message) use ($reply) {
                    return $reply;
                });
                $server->serve()->send();
                \Log::info('-------公众号消息回复-------',$reply);
            }
        });
    }

    // 记录用户消息,只记录文本，图片消息
    public function messageRecord($wechatApp,$message)
    {
        if ($message['MsgType'] == 'text' || $message['MsgType'] == 'image') {
            $chatsRecord = new ChatsRecord();
            $chatsRecord->uniacid = \YunShop::app()->uniacid;
            $chatsRecord->flag = ChatsRecord::USER;
            $chatsRecord->openid = $message['FromUserName'];
            $chatsRecord->msgtype = $message['MsgType'];
            if ($message['MsgType'] == 'image') {
                $chatsRecord->content = ['media_id' => $message['MediaId'],'picurl' => $message['PicUrl'],'attachment' => $message['PicUrl']];//图片PicUrl可能是临时文件，如果过时后访问不到，可考虑将其存储本地
            } else if ($message['MsgType'] == 'voice') {
                /*
                // 调用临时素材接口获取url
                $temporary = $wechatApp->material_temporary;
                $content = $temporary->getStream($message['MediaId']);
                // 保存本地
                */
            } else {
                $chatsRecord->content = ['content' => $message['Content']];
            }
            $chatsRecord->createtime = time();
            $chatsRecord->save();
        }
    }

    // 微信事件，包含菜单事件，关注公众号事件(首次访问自动回复)
    public function eventMessage($message,&$replyStatus)
    {
        switch ($message['Event']) {
            case 'subscribe':// 关注公众号
                $this->subscriber($message);//不管是海报的还是公众号的，只要关注就要修改关注状态
                if (empty($message['Ticket'])) {//事件必须是关注，并且没有Ticket，公众号插件才进行回复。有Ticket是属于海报的关注，让海报处理
                    // 关注事件，记录到粉丝表和会员主表
                    $this->updateFansAndMember($message);
                    $reply = $this->getWelcomeReply(0);
                    $replyStatus = true;
                }
                break;
            case 'unsubscribe':
                $reply = $this->unSubscribe($message);
                break;
            case 'CLICK': // 点击事件，根据EventKey获取关键字。
                $reply = $this->keywordMessage($message['EventKey'],$replyStatus);
                break;
            case 'VIEW':
                break;
            default:

                break;
        }
        return $reply;
    }

    public function getWelcomeReply($type)
    {
        $keywordsId = Setting::get('wechat.reply.welcome_keywords_id');
        $ruleKeyword = RuleKeyword::getRuleKeywordById($keywordsId);
        // 用户可能删除了该关键字，如果关键字为空，则走默认回复，如果默认为空，则回复空字符串
        if (!empty($ruleKeyword)) {
            $reply = $this->getReplyByRuleId($ruleKeyword->rid);
        } else {
            $reply = $this->getDefaultReply($type);
        }
        return $reply;
    }
    //会员关注公众号
    public function subscriber($message){
        $uid = McMappingFansModel::getUId($message['FromUserName'])->uid;
        if(empty($uid)){
            \Log::info('该用户不是公众号会员');
            return;
        }
        $data = [
            'follow' => 1,
            'followtime' => $message['CreateTime'],
            'unfollowtime' => 0
        ];
        $fansModel =McMappingFansModel::updateData($uid,$data);

    }

    //会员取消关注公众号
    public function unSubscribe($message){
        $uid = McMappingFansModel::getUId($message['FromUserName'])->uid;
        if(empty($uid)){
            \Log::info('该用户不是公众号会员');
            return;
        }
        $data = [
            'follow' => 0,
            'unfollowtime' => time()
        ];
        $fansModel =McMappingFansModel::updateData($uid,$data);

        $text = new  \EasyWeChat\Message\Text();
        $text->setAttribute('content', '');
        $reply = $text;

        return $reply;
    }

    public function getDefaultReply($type)
    {
        $keywordsId = Setting::get('wechat.reply.default_keywords_id');
        $ruleKeyword = RuleKeyword::getRuleKeywordById($keywordsId);
        if (!empty($ruleKeyword)) {
            $reply = $this->getReplyByRuleId($ruleKeyword->rid);
        } else {
            $text = new  \EasyWeChat\Message\Text();
            if ($type){
                $text->setAttribute('content', '请输入正确关键字!');
            }else{
                $set = \Setting::get('shop.shop');
                $text->setAttribute('content', '欢迎您关注'.$set['name'].'!');
            }

            $reply = $text;
        }
        return $reply;
    }

    // 关键字回复
    public function keywordMessage($message,&$replyStatus)
    {
        $ruleKeyword = RuleKeyword::getRuleKeywordByKeywords($message);
        // 找不到关键字,包括其他插件的关键字，则默认回复
        if (empty($ruleKeyword)) {
            $reply = $this->getDefaultReply(1);
            $replyStatus = true;
        } else {
            // 是微信插件的回复内容
            if ($ruleKeyword->module == Rule::WECHAT_MODULE) {
                $reply = $this->getReplyByRuleId($ruleKeyword->rid);
                $replyStatus = true;
            }
        }

//        if (!empty($ruleKeyword)) {
//            // 是微信插件的回复内容
//            if ($ruleKeyword->module == Rule::WECHAT_MODULE) {
//                $reply = $this->getReplyByRuleId($ruleKeyword->rid);
//                $replyStatus = true;
//            }
//        } 
        return $reply;
    }

    // 通过规则id，随机获取一种回复
    public function getReplyByRuleId($ruleId)
    {
        // 获取规则及其所有回复
        $rule = Rule::getRuleAndKeywordsAndRepliesByRuleId($ruleId);
        $arr = explode(',',$rule['containtype']);
        $replyName = $arr[array_rand($arr)];
        switch ($replyName) {
            case Rule::REPLY_TYPE_NEWS :
                // 重新获取图文数据，进行多图文的回复
                $attachment = WechatAttachment::getWechatAttachmentAndNewsById($rule['has_many_news_reply'][0]['media_id']);

                if (!empty($attachment)) {
                    $attachment = $attachment->toArray();
                    if (!empty($attachment['has_many_news'])) {
                        foreach ($attachment['has_many_news'] as $news) {
                            $tempNews = new  \EasyWeChat\Message\News([
                                'title'       => $news['title'],
                                'description' => empty($news['digest']) ? '' : $news['digest'],
                                'url'         => !empty($news['content_source_url']) ? $news['content_source_url'] : $news['url'],
                                'image'       => $news['thumb_url'],
                            ]);
                            $newsList[] = $tempNews;
                        }
                        /* 尝试过文章，但是也不对
                            $tempArticle = new  \EasyWeChat\Message\Article([
                                'title'       => $news['title'],
                                'author'  => !empty($news['author']) ? $news['author'] : '',
                                'thumb_media_id' => $news['thumb_media_id'],
                                'content'         => !empty($news['content']) ? $news['content'] : ' ',
                                'digest'       => !empty($news['digest']) ? $news['digest'] : '',
                                'source_url'       => !empty($news['content_source_url']) ? $news['content_source_url'] : $news['url'],
                                'show_cover'       => $news['show_cover_pic'],
                            ]);
                            $newsList[] = $tempArticle;
                        */
                        $reply =  $newsList;
                    }
                }
                break;
            case Rule::REPLY_TYPE_BASIC :
                $text = new  \EasyWeChat\Message\Text();
                $text->setAttribute('content', $rule['has_many_basic_reply'][0]['content']);
                $reply = $text;
                break;
            case Rule::REPLY_TYPE_IMAGE :
                $image = new \EasyWeChat\Message\Image();
                $image->setAttribute('media_id', $rule['has_many_image_reply'][0]['mediaid']);
                $reply = $image;
                break;
            case Rule::REPLY_TYPE_MUSIC :
                // media_id必须,所以查找一张微信图片作为封面，否则微信会报服务器故障的错误

                $thumb = WechatAttachmentImage::getAttachmentThumb()->first();
                if ($thumb){
                    $music = new  \EasyWeChat\Message\Music();
                    $music->title = $rule['has_many_music_reply'][0]['title'];
                    $music->description = $rule['has_many_music_reply'][0]['description'];
                    $music->url = $rule['has_many_music_reply'][0]['url'];
                    $music->hq_url = $rule['has_many_music_reply'][0]['hqurl'];
                    $music->thumb_media_id = $thumb->media_id;
                    $reply = $music;
                }else{
                    return $this->errorJson("请上传一张缩略图");
                }
//                $image = WechatAttachment::uniacid()->where('model','=',WechatAttachment::ATTACHMENT_MODEL_WECHAT)->where('type','=',WechatAttachment::ATTACHMENT_TYPE_IMAGE)->first();
//                if (empty($image)) {
//                    $text = new  \EasyWeChat\Message\Text();
//                    $text->setAttribute('content', '缺少图片素材,请至少上传一张!');
//                    $reply = $text;
//                } else {
//                    $music = new  \EasyWeChat\Message\Music();
//                    $music->title = $rule['has_many_music_reply'][0]['title'];
//                    $music->description = $rule['has_many_music_reply'][0]['description'];
//                    $music->url = $rule['has_many_music_reply'][0]['url'];
//                    $music->hq_url = $rule['has_many_music_reply'][0]['hqurl'];
//                    $music->thumb_media_id = $image->media_id;
//                    $reply = $music;
//                }
                break;
            case Rule::REPLY_TYPE_USERAPI :
                $text = new  \EasyWeChat\Message\Text();
                $text->setAttribute('content', $rule['has_many_userapi_reply'][0]['description']);
                $reply = $text;
                break;
            case Rule::REPLY_TYPE_VIDEO :
                $video = new \EasyWeChat\Message\Video();
                $video->title = $rule['has_many_video_reply'][0]['title'];
                $video->description = $rule['has_many_video_reply'][0]['description'];
                $video->media_id = $rule['has_many_video_reply'][0]['mediaid'];
                $reply = $video;
                break;
            case Rule::REPLY_TYPE_VOICE :
                $voice = new \EasyWeChat\Message\Voice();
                $voice->setAttribute('media_id', $rule['has_many_voice_reply'][0]['mediaid']);
                $reply = $voice;
                break;
            default:
                $text = new  \EasyWeChat\Message\Text();
                $text->setAttribute('content', '请输入正确关键字!');
                $reply = $text;
                break;
        }
        return $reply;
    }

    public function updateFansAndMember($message)
    {
        // 通过openid获取用户详细信息，插入或更新mapping_fans和mc_members
        $openidList = [$message['FromUserName']];
        $fansService = new FansService();
        $fansService->updateFansInfo($openidList);
    }
}