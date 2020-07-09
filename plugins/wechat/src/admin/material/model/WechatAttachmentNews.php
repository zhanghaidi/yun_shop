<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/6/6
 * Time: 下午9:09
 */

namespace Yunshop\Wechat\admin\material\model;

use Yunshop\Wechat\common\model\WechatNews;
use Illuminate\Support\Facades\DB;

class WechatAttachmentNews extends \Yunshop\Wechat\common\model\WechatAttachment
{
    public function hasManyWechatNews()
    {
        return $this->hasMany(WechatNews::class,'attach_id','id');
    }

    // 获取本地和微信图文
    public static function getAttachmentNews($page)
    {
        return static::uniacid()
            ->with('hasManyWechatNews')
            ->where('type','=',self::ATTACHMENT_TYPE_NEWS)
            ->orderBy('id','desc')
            ->paginate(static::PAGE_SIZE,['*'],'page',$page);
    }

    // 只获取微信图文
    public static function getAttachmentWechatNews($page,$search = '')
    {
        $result = static::uniacid()
            ->where('type','=',self::ATTACHMENT_TYPE_NEWS)
            ->where('model','=',self::ATTACHMENT_MODEL_WECHAT);
        if ($search){
            $result->whereHas('hasManyWechatNews',function ($q) use ($search){
                $q->where('digest','like','%'.$search.'%')
                    ->orWhere('author','like','%'.$search.'%')
                    ->orWhere('title','like','%'.$search.'%');
            });
        }
        return $result->orderBy('id','desc')
            ->with('hasManyWechatNews')
            ->paginate(static::PAGE_SIZE,['*'],'page',$page);
//        return static::uniacid()
//            ->where('type','=',self::ATTACHMENT_TYPE_NEWS)
//            ->where('model','=',self::ATTACHMENT_MODEL_WECHAT)
//            ->orderBy('id','desc')
//            ->with('hasManyWechatNews')
//            ->paginate(static::PAGE_SIZE,['*'],'page',$page);
    }

    // 只获取服务器图文
    public static function getAttachmentLocalNews($page,$search = '')
    {
        $result = static::uniacid()
            ->where('type','=',self::ATTACHMENT_TYPE_NEWS)
            ->where('model','=',self::ATTACHMENT_MODEL_LOCAL);
        if ($search){
            $result->whereHas('hasManyWechatNews',function ($q) use ($search){
                $q->where('digest','like','%'.$search.'%')
                    ->orWhere('author','like','%'.$search.'%')
                    ->orWhere('title','like','%'.$search.'%');
            });
        }
        return $result->orderBy('id','desc')
            ->with('hasManyWechatNews')
            ->paginate(static::PAGE_SIZE,['*'],'page',$page);
//        return static::uniacid()
//            ->where('type','=',self::ATTACHMENT_TYPE_NEWS)
//            ->where('model','=',self::ATTACHMENT_MODEL_LOCAL)
//            ->orderBy('id','desc')
//            ->with('hasManyWechatNews')
//            ->paginate(static::PAGE_SIZE,['*'],'page',$page);
    }

    // 通过id获取图文信息
    public static function getNewsById($id)
    {
        $news = static::uniacid()->with('hasManyWechatNews')->find($id);
        if (empty($news)) {
            return ['status' => 0, 'message' => '图文不存在或已删除!', 'data' => []];
        } else {
            return ['status' => 1, 'message' => 'ok', 'data' => $news];
        }
    }
}