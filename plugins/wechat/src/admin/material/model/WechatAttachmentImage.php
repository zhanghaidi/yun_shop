<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/6/6
 * Time: 下午9:09
 */

namespace Yunshop\Wechat\admin\material\model;


class WechatAttachmentImage extends \Yunshop\Wechat\common\model\WechatAttachment
{
    /*
    protected $appends = ['attachment_url'];
    public function getAttachmentUrlAttribute()
    {
        return yzWebFullUrl('plugin.wechat.admin.material.controller.material.getWechatImageResource',['attachment'=>$this->attachment]);
    }
    public function getAttachmentAttribute()
    {
        $url = $this->attachment;
        return yzWebFullUrl('plugin.wechat.admin.material.controller.material.getWechatImageResource',['attachment'=>$url]);
    }
    */
// 获取本地和微信图片
    public static function getAttachmentImages($page)
    {
        return static::uniacid()
            ->where('type','=',self::ATTACHMENT_TYPE_IMAGE)
            ->orderBy('id','desc')
            ->paginate(static::PAGE_SIZE,['*'],'page',$page);
    }

    // 只获取微信图片
    public static function getAttachmentWechatImages($page)
    {
        return static::uniacid()
            ->where('type','=',self::ATTACHMENT_TYPE_IMAGE)
            ->where('model','=',self::ATTACHMENT_MODEL_WECHAT)
            ->orderBy('id','desc')
            ->paginate(static::PAGE_SIZE,['*'],'page',$page);
    }

    // 只获取服务器图片
    public static function getAttachmentLocalImages()
    {
        return static::uniacid()
            ->where('type','=',self::ATTACHMENT_TYPE_IMAGE)
            ->where('model','=',self::ATTACHMENT_MODEL_LOCAL)
            ->orderBy('id','desc')
            ->get();
    }

    // 根据media_id获取微信图片
    public static function getAttachmentWechatImageByMediaId($media_id)
    {
        return static::uniacid()
            ->where('type','=',self::ATTACHMENT_TYPE_IMAGE)
            ->where('model','=',self::ATTACHMENT_MODEL_WECHAT)
            ->where('media_id','=',$media_id)
            ->first();
    }

    // 获取服务器缩略图
    public static function getAttachmentThumb()
    {
        return static::uniacid()
            ->where('type','=',self::ATTACHMENT_TYPE_THUMB)
            ->where('model','=',self::ATTACHMENT_MODEL_WECHAT);
    }

}