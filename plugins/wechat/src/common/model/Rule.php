<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/6/6
 * Time: 下午9:09
 */

namespace Yunshop\Wechat\common\model;

use Illuminate\Support\Facades\DB;

class Rule extends \app\common\modules\wechat\models\Rule
{
    // 每页记录数
    const PAGE_SIZE = 20;
    // 通过id获取规则及关键字
    public static function getRuleAndKeywordsByRuleId($id)
    {
        return static::uniacid()->where('module',Rule::WECHAT_MODULE)->with('hasManyKeywords')->find($id);
    }
    // 通过id获取模型对象
    public static function getRuleById($id)
    {
        return static::uniacid()->where('module',Rule::WECHAT_MODULE)->find($id);
    }
    // 通过id获取规则，规则下的关键字，规则下的回复
    public static function getRuleAndKeywordsAndRepliesByRuleId($id)
    {
        if (empty($id)) {
            return [];
        }
        $rule = static::uniacid()->find($id);
        if (empty($rule)) {
            return [];
        }
        $replyType = explode(',',$rule['containtype']);
        $rule = $rule->uniacid()->where('id',$id);
        //dd($rule);
        foreach ($replyType as $type) {
            switch ($type) {
                case Rule::REPLY_TYPE_NEWS :
                    $rule->with('hasManyNewsReply');
                    break;
                case Rule::REPLY_TYPE_BASIC :
                    $rule->with('hasManyBasicReply');
                    break;
                case Rule::REPLY_TYPE_IMAGE :
                    $rule->with('hasManyImageReply');
                    break;
                case Rule::REPLY_TYPE_MUSIC :
                    $rule->with('hasManyMusicReply');
                    break;
                case Rule::REPLY_TYPE_USERAPI :
                    $rule->with('hasManyUserapiReply');
                    break;
                case Rule::REPLY_TYPE_VIDEO :
                    $rule->with('hasManyVideoReply');
                    break;
                case Rule::REPLY_TYPE_VOICE :
                    $rule->with('hasManyVoiceReply');
                    break;
                default:
                    break;
            }
        }
        $rule = $rule->with('hasManyKeywords')->first();
        return $rule->toArray();
    }

    public function hasManyNewsReply()
    {
        return $this->hasMany(NewsReply::class,'rid','id');
    }
    public function hasManyBasicReply()
    {
        return $this->hasMany(BasicReply::class,'rid','id');
    }
    public function hasManyImageReply()
    {
        return $this->hasMany(ImageReply::class,'rid','id')->with('hasOneAttachment');
    }
    public function hasManyMusicReply()
    {
        return $this->hasMany(MusicReply::class,'rid','id');
    }
    public function hasManyUserapiReply()
    {
        return $this->hasMany(UserapiReply::class,'rid','id');
    }
    public function hasManyVideoReply()
    {
        return $this->hasMany(VideoReply::class,'rid','id');
    }
    public function hasManyVoiceReply()
    {
        return $this->hasMany(VoiceReply::class,'rid','id');
    }

}