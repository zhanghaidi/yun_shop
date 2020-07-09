<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/6/6
 * Time: 下午9:09
 */

namespace Yunshop\Wechat\admin\material\model;


class WechatAttachmentVoice extends \Yunshop\Wechat\common\model\WechatAttachment
{
// 获取本地和微信语音
    public static function getAttachmentVoices($page)
    {
        return static::uniacid()
            ->where('type','=',self::ATTACHMENT_TYPE_VOICE)
            ->orderBy('id','desc')
            ->paginate(static::PAGE_SIZE,['*'],'page',$page);
    }

    // 只获取微信语音
    public static function getAttachmentWechatVoices($page)
    {
        return static::uniacid()
            ->where('type','=',self::ATTACHMENT_TYPE_VOICE)
            ->where('model','=',self::ATTACHMENT_MODEL_WECHAT)
            ->orderBy('id','desc')
            ->paginate(static::PAGE_SIZE,['*'],'page',$page);
    }

    // 只获取服务器语音
    public static function getAttachmentLocalVoices($page)
    {
        return static::uniacid()
            ->where('type','=',self::ATTACHMENT_TYPE_VOICE)
            ->where('model','=',self::ATTACHMENT_MODEL_LOCAL)
            ->orderBy('id','desc')
            ->paginate(static::PAGE_SIZE,['*'],'page',$page);
    }
}