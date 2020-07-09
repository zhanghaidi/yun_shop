<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/6/6
 * Time: 下午9:09
 */

namespace Yunshop\Wechat\admin\material\model;


class WechatAttachmentVideo extends \Yunshop\Wechat\common\model\WechatAttachment
{
// 获取本地和微信视频
    public static function getAttachmentVideos($page)
    {
        return static::uniacid()
            ->where('type','=',self::ATTACHMENT_TYPE_VIDEO)
            ->orderBy('id','desc')
            ->paginate(static::PAGE_SIZE,['*'],'page',$page);
    }

    // 只获取微信视频
    public static function getAttachmentWechatVideos($page)
    {
        return static::uniacid()
            ->where('type','=',self::ATTACHMENT_TYPE_VIDEO)
            ->where('model','=',self::ATTACHMENT_MODEL_WECHAT)
            ->orderBy('id','desc')
            ->paginate(static::PAGE_SIZE,['*'],'page',$page);
    }

    // 只获取服务器视频
    public static function getAttachmentLocalVideos($page)
    {
        return static::uniacid()
            ->where('type','=',self::ATTACHMENT_TYPE_VIDEO)
            ->where('model','=',self::ATTACHMENT_MODEL_LOCAL)
            ->orderBy('id','desc')
            ->paginate(static::PAGE_SIZE,['*'],'page',$page);
    }
}