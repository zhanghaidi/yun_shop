<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/6/6
 * Time: 下午9:09
 */

namespace Yunshop\Wechat\admin\reply\model;

use Yunshop\Wechat\common\model\WechatAttachment;
use Illuminate\Support\Facades\DB;

class Rule extends \Yunshop\Wechat\common\model\Rule
{
    public static function changeRuleStatus($id,$status)
    {
        if ($status == 0 || $status == 1) {
            $rule = Rule::uniacid()->where('module',Rule::WECHAT_MODULE)->find($id);
            if ($rule) {
                $rule->status = $status;
                DB::beginTransaction();
                if ($rule->save()) {
                    // 修改关键字的状态
                    $keywords = RuleKeyword::getRuleKeywordsByRid($rule->id);
                    foreach ($keywords as $keyword) {
                        $keyword->status = $rule->status;
                        if (!$keyword->save()) {
                            DB::rollBack();
                            return ['status' => 0, 'message' => '关键字'.$keyword->id.'状态修改失败', 'data' => []];
                        }
                    }
                    DB::commit();
                    return ['status' => 1, 'message' => '修改成功', 'data' => []];
                } else {
                    return ['status' => 0, 'message' => '规则状态修改失败', 'data' => []];
                }
            } else {
                return ['status' => 0, 'message' => '规则不存在或属于其他插件，id:'.$id, 'data' => []];
            }
        } else {
            return ['status' => 0, 'message' => '参数错误', 'data' => []];
        }
    }

    // 通过类型获取关键字
    public static function getRuleByType($type,$search_type,$search,$page)
    {
        $type = ($type == null) ? '' : $type;
        // 找出所有该类型的规则及其关键字
        $rules = static::uniacid();
        if (!empty($type)) {
            $rules->where('containtype','like','%'.$type.'%');
        }
        if ($search_type == 1) {
            $rules = $rules->where('name','like',$search.'%')
                ->with('hasManyKeywords');
        } else if ($search_type == 2) {
            $rules = $rules->with(['hasManyKeywords'=>function ($query) use ($search) {
                    return $query->where('content','like',$search.'%');
                }]);
        } else {
            $rules = $rules->with('hasManyKeywords');
        }
        $rules = $rules->orderBy('id','desc')->paginate(static::PAGE_SIZE,['*'],'page',$page);
        // 由于按关键字查询，hasManyKeywords为空则说明这条规则不符合查询条件，需要过滤关键字为空的规则
        $rulesItems = $rules->filter(
            function (Rule $rule){
                if (empty($rule->hasManyKeywords->toArray())) {
                    return false;
                }
                return true;
            }
        );
        $rules = $rules->toArray();
        $rulesItems = array_values($rulesItems->toArray());
        $rules['data'] = $rulesItems;
        // 对每一个规则的回复内容的数量进行统计
        foreach ($rules['data'] as &$rule) {
            $replySum = static::getRuleRepliesSum($rule['id'],$rule['containtype']);
            $rule['replySum'] = $replySum;
        }
        return ['status' => 1, 'message' => '成功', 'data' => $rules];
    }

    // 查找一个规则有几条回复
    public static function getRuleRepliesSum($id,$containtype)
    {
        $replyType = explode(',',$containtype);
        $sum = 0;
        foreach ($replyType as $type) {
            switch ($type) {
                case Rule::REPLY_TYPE_NEWS :
                    $sum += NewsReply::where('rid','=',$id)->count();
                    break;
                case Rule::REPLY_TYPE_BASIC :
                    $sum += BasicReply::where('rid','=',$id)->count();
                    break;
                case Rule::REPLY_TYPE_IMAGE :
                    $sum += ImageReply::where('rid','=',$id)->count();
                    break;
                case Rule::REPLY_TYPE_MUSIC :
                    $sum += MusicReply::where('rid','=',$id)->count();
                    break;
                case Rule::REPLY_TYPE_USERAPI :
                    $sum += UserapiReply::where('rid','=',$id)->count();
                    break;
                case Rule::REPLY_TYPE_VIDEO :
                    $sum += VideoReply::where('rid','=',$id)->count();
                    break;
                case Rule::REPLY_TYPE_VOICE :
                    $sum += VoiceReply::where('rid','=',$id)->count();
                    break;
                default:
                    break;
            }
        }
        return $sum;
    }
    



    // 对页面提交的规则数据进行处理，得到数据库需要的数据
    public static function transformData($form)
    {
        $ruleType = [];
        // 编辑的数据id，用来判断哪些是删除的，哪些是新增的。如果是编辑关键字，则将数据库查出的id对比页面传入的id，两数组对比，少了则删除
        $editId = [];
        // has_many_keywords 关键字表
        $data['keywords'] = [];
        $editId['keywords'] = [];
        foreach ($form['has_many_keywords'] as $keyword) {
            $tempKeyword = [];
            if (!empty($keyword['id']) && intval($keyword['id'])) {
                $editId['keywords'][] = $keyword['id'];
                $tempKeyword['id'] = $keyword['id'];
            }
            //rid保存规则时才生成，保存关键字时再存入rid
            $tempKeyword['uniacid'] = \YunShop::app()->uniacid;
            $tempKeyword['module'] = Rule::WECHAT_MODULE;
            $tempKeyword['content'] = $keyword['content'];
            $tempKeyword['type'] = $keyword['type'];
            $tempKeyword['displayorder'] = empty($form['displayorder']) ? 0 : $form['displayorder'];
            $tempKeyword['status'] = empty($form['status']) ? 0 : 1;
            $data['keywords'][] = $tempKeyword;
        }

        // has_many_basic_reply 文字回复
        $data['basic_reply'] = [];
        $editId['basic_reply'] = [];
        foreach ($form['has_many_basic_reply'] as $basic_reply) {
            $ruleType[] = Rule::REPLY_TYPE_BASIC;
            $temp_basic_reply = [];
            if (!empty($basic_reply['id']) && intval($basic_reply['id'])) {
                $editId['basic_reply'][] = $basic_reply['id'];
                $temp_basic_reply['id'] = $basic_reply['id'];
            }
            //rid保存规则时才生成，保存关键字时再存入rid
            $temp_basic_reply['content'] = $basic_reply['content'];
            $data['basic_reply'][] = $temp_basic_reply;
        }

        // has_many_news_reply  图文回复
        $data['news_reply'] = [];
        $editId['news_reply'] = [];
        foreach ($form['has_many_news_reply'] as $news_reply) {
            $ruleType[] = Rule::REPLY_TYPE_NEWS;
            $temp_news_reply = [];
            if (!empty($news_reply['id']) && intval($news_reply['id'])) { //编辑
                $editId['news_reply'][] = $news_reply['id'];
                $temp_news_reply['id'] = $news_reply['id'];
            }
            //rid保存规则时才生成，保存关键字时再存入rid
            // 这个字段parent_id没用
            $temp_news_reply['parent_id'] = 0;
            $temp_news_reply['media_id'] = $news_reply['media_id'];
            // 通过media_id查询出图文信息
            $attachment = WechatAttachment::getWechatAttachmentAndNewsById($news_reply['media_id']);
            if ($attachment) {
                $attachment = $attachment->toArray();
                // 取第一条进行存储，存储只是为了页面显示，真正在微信发送的是整个图文素材
                $news = $attachment['has_many_news'][0];
                $temp_news_reply['title'] = $news['title'];
                $temp_news_reply['author'] = $news['author'];
                $temp_news_reply['description'] = empty($news['description']) ? '' : $news['description'];
                $temp_news_reply['thumb'] = $news['thumb_url'];
                $temp_news_reply['content'] = $news['content'];
                $temp_news_reply['url'] = !empty($news['content_source_url']) ? $news['content_source_url'] : $news['url'];
                $temp_news_reply['displayorder'] = $news['displayorder'];
                $temp_news_reply['incontent'] = 0;
                $temp_news_reply['createtime'] = time();
            } else {
                return ['status' => 0, 'message' => '图文media_id'.':'.$news_reply['media_id'].'不存在!', 'data' => []];
            }
            $data['news_reply'][] = $temp_news_reply;
        }

        // has_many_image_reply 图片回复
        $data['image_reply'] = [];
        $editId['image_reply'] = [];
        foreach ($form['has_many_image_reply'] as $image_reply) {
            $ruleType[] = Rule::REPLY_TYPE_IMAGE;
            $temp_image_reply = [];
            if (!empty($image_reply['id']) && intval($image_reply['id'])) {
                $editId['image_reply'][] = $image_reply['id'];
                $temp_image_reply['id'] = $image_reply['id'];
            }

            // 根据media_id查询是否存在，确保数据正确
            if (WechatAttachment::getWechatAttachmentByMediaId($image_reply['mediaid'])) {
                $temp_image_reply['mediaid'] = $image_reply['mediaid'];
                //rid保存规则时才生成，保存关键字时再存入rid
                $temp_image_reply['title'] = '';
                $temp_image_reply['description'] = '';
                $temp_image_reply['createtime'] = time();
            } else {
                return ['status' => 0, 'message' => '图片media_id'.':'.$image_reply['mediaid'].'不存在!', 'data' => []];
            }
            $data['image_reply'][] = $temp_image_reply;
        }

        // has_many_voice_reply 语音回复
        $data['voice_reply'] = [];
        $editId['voice_reply'] = [];
        foreach ($form['has_many_voice_reply'] as $voice_reply) {
            $ruleType[] = Rule::REPLY_TYPE_VOICE;
            $temp_voice_reply = [];
            if (!empty($voice_reply['id']) && intval($voice_reply['id'])) {
                $editId['voice_reply'][] = $voice_reply['id'];
                $temp_voice_reply['id'] = $voice_reply['id'];
            }
            //rid保存规则时才生成，保存关键字时再存入rid
            if (WechatAttachment::getWechatAttachmentByMediaId($voice_reply['mediaid'])) {
                $temp_voice_reply['mediaid'] = $voice_reply['mediaid'];
                $temp_voice_reply['createtime'] = time();
                $temp_voice_reply['title'] = '';
            } else {
                return ['status' => 0, 'message' => '语音media_id'.':'.$voice_reply['mediaid'].'不存在!', 'data' => []];
            }

            $data['voice_reply'][] = $temp_voice_reply;
        }

        // has_many_video_reply 视频回复
        $data['video_reply'] = [];
        $editId['video_reply'] = [];
        foreach ($form['has_many_video_reply'] as $video_reply) {
            $ruleType[] = Rule::REPLY_TYPE_VIDEO;
            $temp_video_reply = [];
            if (!empty($video_reply['id']) && intval($video_reply['id'])) {
                $editId['voice_reply'][] = $video_reply['id'];
                $temp_video_reply['id'] = $video_reply['id'];
            }
            // 判断是否存在mediaid
            if (WechatAttachment::getWechatAttachmentByMediaId($video_reply['mediaid'])) {
                $temp_video_reply['mediaid'] = $video_reply['mediaid'];
                //rid保存规则时才生成，保存关键字时再存入rid
                $temp_video_reply['title'] = '';
                $temp_video_reply['description'] = '';
                $temp_video_reply['createtime'] = time();
            } else {
                return ['status' => 0, 'message' => '视频media_id'.':'.$video_reply['mediaid'].'不存在!', 'data' => []];
            }
            $data['video_reply'][] = $temp_video_reply;
        }

        // has_many_music_reply 音乐回复
        $data['music_reply'] = [];
        $editId['music_reply'] = [];
        foreach ($form['has_many_music_reply'] as $music_reply) {
            $ruleType[] = Rule::REPLY_TYPE_MUSIC;
            $temp_music_reply = [];
            if (!empty($music_reply['id']) && intval($music_reply['id'])) {
                $editId['music_reply'][] = $music_reply['id'];
                $temp_music_reply['id'] = $music_reply['id'];
            }
            //rid保存规则时才生成，保存关键字时再存入rid
            $temp_music_reply['title'] = $music_reply['title'];
            $temp_music_reply['description'] = $music_reply['description'];
            $temp_music_reply['url'] = $music_reply['url'];
            $temp_music_reply['hqurl'] = $music_reply['hqurl'];
            $data['music_reply'][] = $temp_music_reply;
        }

        // 组装规则表
        if (!empty($form['id']) && intval($form['id'])) {
            $data['rule']['id'] = $form['id'];
        }
        $data['rule']['uniacid'] = \YunShop::app()->uniacid;
        $data['rule']['name'] = $form['name'];
        // 规则名称中包含模块名称，拆分后获取
        $data['rule']['module'] = Rule::WECHAT_MODULE;
        $data['rule']['displayorder'] = empty($form['displayorder']) ? 0 : $form['displayorder'];
        $data['rule']['status'] = empty($form['status']) ? 0 : 1;
        //这个字段自己进行拼接
        $data['rule']['containtype'] = implode(',',array_unique($ruleType));
        // 该字段在新框架下不需要
        $data['rule']['reply_type'] = 0;

        return ['status' => 1, 'message' => '转换成功', 'data' => ['data'=>$data,'editId'=>$editId]];
    }

    // 通过id删除规则，删除规则的同时，关键字也要删除,以及各个回复表中该规则的回复也删除
    public static function deleteRuleById($id)
    {
        $rule = static::getRuleById($id);
        if ($rule) {
            DB::beginTransaction();
            // 删除关键字及回复
            RuleKeyword::destroy(array_column(RuleKeyword::getRuleKeywordIdsByRid($rule->id)->toArray(),'id'));
            BasicReply::destroy(array_column(BasicReply::getBasicReplyIdsByRid($rule->id)->toArray(),'id'));
            NewsReply::destroy(array_column(NewsReply::getNewsReplyIdsByRid($rule->id)->toArray(),'id'));
            ImageReply::destroy(array_column(ImageReply::getImageReplyIdsByRid($rule->id)->toArray(),'id'));
            VoiceReply::destroy(array_column(VoiceReply::getVoiceReplyIdsByRid($rule->id)->toArray(),'id'));
            VideoReply::destroy(array_column(VideoReply::getVideoReplyIdsByRid($rule->id)->toArray(),'id'));
            MusicReply::destroy(array_column(MusicReply::getMusicReplyIdsByRid($rule->id)->toArray(),'id'));
            // 删除规则
            if (!$rule->delete()) {
                DB::rollBack();
                return ['status' => 0, 'message' => '规则:' . $rule->id . '删除失败!', 'data' => []];
            }
            DB::commit();
            return ['status'=>1,'message'=>'规则删除成功!','data'=>[]];
        } else {
            return ['status' => 0, 'message' => '删除失败,规则:' . $id . '不存在或属于其他插件!', 'data' => []];
        }
    }

    // 保存和修改
    // 保存，分两种情况:1.创建规则，则创建关键字 2.修改规则，则修改，创建，删除关键字
    public static function saveRule($form)
    {
        if (empty($form['id'])) {
            $rule = new self();
        } else {
            $rule = static::getRuleById($form['id']);
            if (empty($rule)) {
                return ['status' => 0, 'message' => '规则不存在或属于其他插件，请检查!ID:'.$form['id'], 'data' => []];
            }
        }
        // 数据转换，将前端数据转为数据库需要的
        $result = static::transformData($form);
        if ($result['status']) {
            $data = $result['data']['data'];
            $editId = $result['data']['editId'];
        } else {
            return $result;
        }
        // 填充
        $rule->fill($data['rule']);
        // 验证数据
        $validate = $rule->validator();
        if ($validate->fails()) {
            return ['status' => 0, 'message' => $validate->messages(), 'data' => []];
        }
        DB::beginTransaction();
        if ($rule->save()) {
            // 当页面是编辑的时候，用户会删除一些，然后新增一些，修改一些
            // 删除，先获取数据库原有的数据的id，和页面传入的id对比，原有但是页面没传入，则删除
            RuleKeyword::destroy(array_diff(array_column(RuleKeyword::getRuleKeywordIdsByRid($rule->id)->toArray(),'id'),$editId['keywords']));
            BasicReply::destroy(array_diff(array_column(BasicReply::getBasicReplyIdsByRid($rule->id)->toArray(),'id'),$editId['basic_reply']));
            NewsReply::destroy(array_diff(array_column(NewsReply::getNewsReplyIdsByRid($rule->id)->toArray(),'id'),$editId['news_reply']));
            ImageReply::destroy(array_diff(array_column(ImageReply::getImageReplyIdsByRid($rule->id)->toArray(),'id'),$editId['image_reply']));
            VoiceReply::destroy(array_diff(array_column(VoiceReply::getVoiceReplyIdsByRid($rule->id)->toArray(),'id'),$editId['voice_reply']));
            VideoReply::destroy(array_diff(array_column(VideoReply::getVideoReplyIdsByRid($rule->id)->toArray(),'id'),$editId['video_reply']));
            MusicReply::destroy(array_diff(array_column(MusicReply::getMusicReplyIdsByRid($rule->id)->toArray(),'id'),$editId['music_reply']));
            // 修改和新增
            // 保存关键字
            foreach ($data['keywords'] as $keyword) {
                $keyword['rid'] = $rule->id;
                $result = RuleKeyword::saveRuleKeyword($keyword);
                if ($result['status']==0) {
                    DB::rollBack();
                    return $result;
                }
            }
            // 保存文字回复
            foreach ($data['basic_reply'] as $basic_reply) {
                $basic_reply['rid'] = $rule->id;
                $result = BasicReply::saveBasicReply($basic_reply);
                if ($result['status']==0) {
                    DB::rollBack();
                    return $result;
                }
            }

            // 保存图文回复
            foreach ($data['news_reply'] as $news_reply) {
                $news_reply['rid'] = $rule->id;
                $result = NewsReply::saveNewsReply($news_reply);
                if ($result['status']==0) {
                    DB::rollBack();
                    return $result;
                }
            }

            // 保存图片回复
            foreach ($data['image_reply'] as $image_reply) {
                $image_reply['rid'] = $rule->id;
                $result = ImageReply::saveImageReply($image_reply);
                if ($result['status']==0) {
                    DB::rollBack();
                    return $result;
                }
            }

            // 保存语音回复
            foreach ($data['voice_reply'] as $voice_reply) {
                $voice_reply['rid'] = $rule->id;
                $result = VoiceReply::saveVoiceReply($voice_reply);
                if ($result['status']==0) {
                    DB::rollBack();
                    return $result;
                }
            }

            // 保存视频回复
            foreach ($data['video_reply'] as $video_reply) {
                $video_reply['rid'] = $rule->id;
                $result = VideoReply::saveVideoReply($video_reply);
                if ($result['status']==0) {
                    DB::rollBack();
                    return $result;
                }
            }

            // 保存音乐回复
            foreach ($data['music_reply'] as $music_reply) {
                $music_reply['rid'] = $rule->id;
                $result = MusicReply::saveMusicReply($music_reply);
                if ($result['status']==0) {
                    DB::rollBack();
                    return $result;
                }
            }
        } else {
            return ['status' => 0, 'message' => '规则'.$rule->id.'保存失败!', 'data' => []];
        }
        DB::commit();
        return ['status' => 1, 'message' => '规则保存成功', 'data' => []];
    }






}
