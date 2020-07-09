<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/6/6
 * Time: 下午9:09
 */

namespace Yunshop\Wechat\common\model;

use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class WechatAttachment extends BaseModel
{
    //public $table = 'wechat_attachment';
    //public $timestamps = false;

    public $table = 'yz_wechat_attachment';
    protected $guarded = [''];
    use SoftDeletes;

    protected $hidden = [
        'uid',
        'width',
        'uniacid',
        'height',
        'module_upload_dir',
        'group_id'
    ];

    // 素材类型
    const ATTACHMENT_TYPE_IMAGE = 'image';//图片（image）: 2M，支持bmp/png/jpeg/jpg/gif格式
    const ATTACHMENT_TYPE_VOICE = 'voice';//语音（voice）：2M，播放长度不超过60s，mp3/wma/wav/amr格式
    const ATTACHMENT_TYPE_VIDEO = 'video';//视频（video）：10MB，支持MP4格式
    const ATTACHMENT_TYPE_THUMB = 'thumb';//缩略图（thumb）：64KB，支持JPG格式
    const ATTACHMENT_TYPE_NEWS = 'news';//缩略图（thumb）：64KB，支持JPG格式

    // 存储模式，是存服务器本地还是微信端
    const ATTACHMENT_MODEL_LOCAL = 'local';
    const ATTACHMENT_MODEL_WECHAT = 'perm';

    // 每页记录数
    const PAGE_SIZE = 20;

    /**
     * 字段规则
     *
     * @return array
     */
    public function rules()
    {
        return [
            'uniacid' => 'required|numeric',
            'acid' => 'required|numeric',
            'uid' => 'required|numeric',
            'width' => 'numeric',
            'height' => 'numeric',
            'type' => 'required',
            'model' => 'required',
            'createtime' => 'numeric',
        ];
    }

    /**
     * 定义字段名
     *
     * @return array
     */
    public function atributeNames()
    {
        return [
            'acid' => '公众号id',
            'uniacid' => '公众号id',
            'uid' => '用户id',
            'type' => '素材类型',
            'model' => '存储方式',
            'createtime' => '创建时间',
        ];
    }
    //修改tag 字段
    public function getTagAttribute($value)
    {
        if($value){
            return unserialize($value);
        } else {
            return [];
        }

    }
    // 通过id获取模型对象
    public static function getWechatAttachmentById($id)
    {
        return static::uniacid()->find($id);
    }

    // 通过id获取素材以及素材下的图文
    public static function getWechatAttachmentAndNewsById($id)
    {
        return static::uniacid()->with('hasManyNews')->find($id);
    }

    public function hasManyNews()
    {
        return $this->hasMany(WechatNews::class,'attach_id','id')->orderBy('displayorder','asc');
    }

    // 根据media_id获取微信图片
    public static function getWechatAttachmentByMediaId($media_id)
    {
        return static::uniacid()
            ->where('model','=',self::ATTACHMENT_MODEL_WECHAT)
            ->where('media_id','=',$media_id)
            ->first();
    }

    // 根据media_id获取微信图片
    public static function getWechatAttachmentAndNewsByMediaId($media_id)
    {
        return static::uniacid()
            ->where('model','=',self::ATTACHMENT_MODEL_WECHAT)
            ->where('media_id','=',$media_id)
            ->with('hasManyNews')
            ->first();
    }

    // 保存微信图片，视频，语音，图文
    // $media是微信端传入的，$type是媒体类型，如图片，视频等
    public static function saveWechatAttachment($media,$mediaType,$coreAttach,$resource = [],$model = self::ATTACHMENT_MODEL_WECHAT)
    {
        // 获取当前用户
        $user = \Auth::guard('admin')->user();
        if (empty($coreAttach)) {
            $filename = '';
            $attachment = '';
        } else {
            $filename = $coreAttach->filename;
            $attachment = $coreAttach->attachment;
        }
        // 同步视频时不会存储本地
        if ($mediaType == static::ATTACHMENT_TYPE_VIDEO && $coreAttach == null && !empty($resource)) {
            $filename = $resource['title'];
            $attachment = $resource['description'];
        }

        $attachmentWechat = new self;
        $attachmentWechat->media_id = $media['media_id'];
        $attachmentWechat->filename = !empty($media['name']) ? $media['name'] : $filename;
        $attachmentWechat->createtime = !empty($media['update_time']) ? $media['update_time'] : time();
        $attachmentWechat->attachment = !empty($media['url']) ? $media['url'] : $attachment;
        $attachmentWechat->tag = !empty($resource) ? serialize($resource) : '';
        $attachmentWechat->uniacid = \YunShop::app()->uniacid;
        $attachmentWechat->acid = \YunShop::app()->uniacid;
        $attachmentWechat->uid = $user->uid;
        $attachmentWechat->width = 0;
        $attachmentWechat->height = 0;
        $attachmentWechat->type = $mediaType;
        $attachmentWechat->model = $model;
        $attachmentWechat->module_upload_dir = '';//模块目录，新框架后不使用
        $attachmentWechat->group_id = 0;//组id，暂时没用
        // 验证和保存
        $validate = $attachmentWechat->validator();
        if ($validate->fails()) {
            return ['status' => 0, 'message' => $validate->messages(), 'data' => []];
        }
        if ($attachmentWechat->save()) {
            return ['status' => 1, 'message' => '保存成功', 'data' => $attachmentWechat];
        } else {
            return ['status' => 0, 'message' => $mediaType.':'.$media['media_id'].'保存失败!', 'data' => []];
        }
    }

    // 根据media_id获取微信图片
    public static function getWechatAttachmentBy()
    {
        return static::uniacid()
            ->where('model','=',self::ATTACHMENT_MODEL_WECHAT)
            ->where('type',self::ATTACHMENT_TYPE_NEWS)
            ->select('id')
            ->get();
    }


}