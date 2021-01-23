<?php

namespace Yunshop\MinappContent\admin;

use app\common\components\BaseController;
use Yunshop\MinappContent\models\AcupointMerModel;
use Yunshop\MinappContent\models\AcupointModel;
use Yunshop\MinappContent\models\ArticleCategoryModel;
use Yunshop\MinappContent\models\ArticleModel;
use Yunshop\MinappContent\models\LabelModel;
use Yunshop\MinappContent\models\MeridianModel;
use Yunshop\MinappContent\models\QuestionBankModel;
use Yunshop\MinappContent\models\SomatoQuestionModel;
use Yunshop\MinappContent\models\SomatoTypeModel;
use Yunshop\MinappContent\services\MinappContentService;

class InitializationController extends BaseController
{
    public $sourceAppid = 45;

    public function index()
    {
        $oldMeridian = MeridianModel::where('uniacid', $this->sourceAppid)->count();
        $newMeridian = MeridianModel::where('uniacid', \YunShop::app()->uniacid)->count();

        $oldAcupoint = AcupointModel::where('uniacid', $this->sourceAppid)->count();
        $newAcupoint = AcupointModel::where('uniacid', \YunShop::app()->uniacid)->count();

        $oldArticleCategory = ArticleCategoryModel::where('uniacid', $this->sourceAppid)->count();
        $newArticleCategory = ArticleCategoryModel::where('uniacid', \YunShop::app()->uniacid)->count();

        $oldArticle = ArticleModel::where('uniacid', $this->sourceAppid)->count();
        $newArticle = ArticleModel::where('uniacid', \YunShop::app()->uniacid)->count();

        $oldLabel = LabelModel::where('uniacid', $this->sourceAppid)->count();
        $newLabel = LabelModel::where('uniacid', \YunShop::app()->uniacid)->count();

        $oldSomato = SomatoTypeModel::where('uniacid', $this->sourceAppid)->count();
        $newSomato = SomatoTypeModel::where('uniacid', \YunShop::app()->uniacid)->count();

        $oldQuestion = QuestionBankModel::where('uniacid', $this->sourceAppid)->count();
        $newQuestion = QuestionBankModel::where('uniacid', \YunShop::app()->uniacid)->count();

        return view('Yunshop\MinappContent::admin.init.index', [
            'pluginName' => MinappContentService::get('name'),
            'meridian' => [
                'old' => $oldMeridian,
                'new' => $newMeridian,
            ],
            'acupoint' => [
                'old' => $oldAcupoint,
                'new' => $newAcupoint,
            ],
            'article_category' => [
                'old' => $oldArticleCategory,
                'new' => $newArticleCategory,
            ],
            'article' => [
                'old' => $oldArticle,
                'new' => $newArticle,
            ],
            'label' => [
                'old' => $oldLabel,
                'new' => $newLabel,
            ],
            'somato' => [
                'old' => $oldSomato,
                'new' => $newSomato,
            ],
            'question' => [
                'old' => $oldQuestion,
                'new' => $newQuestion,
            ],
        ]);
    }

    public function acupoint()
    {
        if (\YunShop::app()->uniacid == $this->sourceAppid) {
            return $this->errorJson('养居益自身项目数据，无需同步');
        }
        $update = (int) \YunShop::request()->update;
        if ($update === 1) {
            $update = true;
        } else {
            $update = false;
        }

        // 经络信息迁移
        $sourceRs = MeridianModel::where('uniacid', $this->sourceAppid)->get()->toArray();

        $nowRs = MeridianModel::select('id', 'name', 'discription')
            ->where('uniacid', \YunShop::app()->uniacid)->get()->toArray();

        $insertData = [];
        $nowTime = time();
        foreach ($sourceRs as $v) {
            $tempId = 0;
            foreach ($nowRs as $v1) {
                if ($v['name'] != $v1['name']) {
                    continue;
                }
                if ($v['discription'] != $v1['discription']) {
                    continue;
                }
                $tempId = $v1['id'];
                break;
            }

            if ($tempId > 0) {
                if ($update == true) {
                    MeridianModel::where([
                        'id' => $tempId,
                        'uniacid' => \YunShop::app()->uniacid,
                    ])->limit(1)->update([
                        'name' => $v['name'],
                        'discription' => $v['discription'],
                        'image' => $v['image'],
                        'type_id' => $v['type_id'],
                        'list_order' => $v['list_order'],
                        'start_time' => $v['start_time'],
                        'end_time' => $v['end_time'],
                        'status' => $v['status'],
                        'content' => $v['content'],
                        'video' => $v['video'],
                        'audio' => $v['audio'],
                        'is_hot' => $v['is_hot'],
                        'video_image_f' => $v['video_image_f'],
                        'video_image_s' => $v['video_image_s'],
                        'notice' => $v['notice'],
                        'audio_play_time' => $v['audio_play_time'],
                    ]);
                }

                continue;
            }
            $insertData[] = [
                'uniacid' => \YunShop::app()->uniacid,
                'name' => $v['name'],
                'discription' => $v['discription'],
                'image' => $v['image'],
                'type_id' => $v['type_id'],
                'add_time' => $nowTime,
                'list_order' => $v['list_order'],
                'start_time' => $v['start_time'],
                'end_time' => $v['end_time'],
                'status' => $v['status'],
                'content' => $v['content'],
                'video' => $v['video'],
                'audio' => $v['audio'],
                'is_hot' => $v['is_hot'],
                'video_image_f' => $v['video_image_f'],
                'video_image_s' => $v['video_image_s'],
                'notice' => $v['notice'],
                'audio_play_time' => $v['audio_play_time'],
            ];
        }
        if (isset($insertData[0])) {
            MeridianModel::insert($insertData);
        }

        // 经络ID对照关系
        $nowRs = MeridianModel::select('id', 'name', 'discription')
            ->where('uniacid', \YunShop::app()->uniacid)->get()->toArray();
        $meridianRelationRs = [];
        foreach ($sourceRs as $v1) {
            foreach ($nowRs as $v2) {
                if ($v1['name'] != $v2['name']) {
                    continue;
                }
                if ($v1['discription'] != $v2['discription']) {
                    continue;
                }
                $meridianRelationRs[$v1['id']] = $v2['id'];
                break;
            }
        }
        if (count($sourceRs) != count($meridianRelationRs)) {
            return $this->errorJson('经络信息迁移出错了');
        }

        // 穴位信息迁移
        $sourceRs = AcupointModel::where('uniacid', $this->sourceAppid)->get()->toArray();

        $nowRs = AcupointModel::select('id', 'name')
            ->where('uniacid', \YunShop::app()->uniacid)->get()->toArray();

        $insertData = [];
        $nowTime = time();
        foreach ($sourceRs as $v) {
            $tempId = 0;
            foreach ($nowRs as $v1) {
                if ($v['name'] != $v1['name']) {
                    continue;
                }
                $tempId = $v1['id'];
                break;
            }

            $v['meridian_id'] = explode('、', $v['meridian_id']);
            $newMeridian = [];
            foreach ($v['meridian_id'] as $v2) {
                if (!isset($meridianRelationRs[$v2])) {
                    continue 2;
                }

                $newMeridian[] = $meridianRelationRs[$v2];
            }

            if ($tempId > 0) {
                if ($update == true) {
                    AcupointModel::where([
                        'id' => $tempId,
                        'uniacid' => \YunShop::app()->uniacid,
                    ])->limit(1)->update([
                        'name' => $v['name'],
                        'meridian_id' => implode('、', $newMeridian),
                        'type' => $v['type'],
                        'get_position' => $v['get_position'],
                        'effect' => $v['effect'],
                        'image' => $v['image'],
                        'video' => $v['video'],
                        'audio' => $v['audio'],
                        'zh' => $v['zh'],
                        'jingluo' => $v['jingluo'],
                        'is_hot' => $v['is_hot'],
                        'chart' => $v['chart'],
                        'video_image_f' => $v['video_image_f'],
                        'video_image_s' => $v['video_image_s'],
                        'to_type_id' => $v['to_type_id'],
                        'status' => $v['status'],
                    ]);
                }
                continue;
            }

            $insertData[] = [
                'uniacid' => \YunShop::app()->uniacid,
                'name' => $v['name'],
                'meridian_id' => implode('、', $newMeridian),
                'type' => $v['type'],
                'get_position' => $v['get_position'],
                'effect' => $v['effect'],
                'image' => $v['image'],
                'add_time' => $nowTime,
                'video' => $v['video'],
                'audio' => $v['audio'],
                'zh' => $v['zh'],
                'jingluo' => $v['jingluo'],
                'is_hot' => $v['is_hot'],
                'chart' => $v['chart'],
                'video_image_f' => $v['video_image_f'],
                'video_image_s' => $v['video_image_s'],
                'to_type_id' => $v['to_type_id'],
                'status' => $v['status'],
            ];
        }
        if (isset($insertData[0])) {
            AcupointModel::insert($insertData);
        }

        // 穴位ID对照关系
        $nowRs = AcupointModel::select('id', 'name')
            ->where('uniacid', \YunShop::app()->uniacid)->get()->toArray();
        $acupointRelationRs = [];
        foreach ($sourceRs as $v1) {
            foreach ($nowRs as $v2) {
                if ($v1['name'] != $v2['name']) {
                    continue;
                }
                $acupointRelationRs[$v1['id']] = $v2['id'];
                break;
            }
        }
        if (count($sourceRs) != count($acupointRelationRs)) {
            return $this->errorJson('穴位信息迁移出错了');
        }

        $sourceRs = AcupointMerModel::where('uniacid', $this->sourceAppid)->get()->toArray();

        if ($update == true) {
            AcupointMerModel::where('uniacid', \YunShop::app()->uniacid)->delete();
        }

        $nowRs = AcupointMerModel::select('id', 'meridian_id', 'acupoint_id', 'acupoint_name')
            ->where('uniacid', \YunShop::app()->uniacid)->get()->toArray();

        $insertData = [];
        $nowTime = time();
        foreach ($sourceRs as $v) {
            if (!isset($meridianRelationRs[$v['meridian_id']])) {
                continue;
            }
            if (!isset($acupointRelationRs[$v['acupoint_id']])) {
                continue;
            }

            $tempId = 0;
            foreach ($nowRs as $v1) {
                if ($v['acupoint_name'] != $v1['acupoint_name']) {
                    continue;
                }
                if ($meridianRelationRs[$v['meridian_id']] != $v1['meridian_id']) {
                    continue;
                }
                if ($acupointRelationRs[$v['acupoint_id']] != $v1['acupoint_id']) {
                    continue;
                }

                $tempId = $v1['id'];
                break;
            }
            if ($tempId > 0) {
                continue;
            }

            $insertData[] = [
                'uniacid' => \YunShop::app()->uniacid,
                'meridian_id' => $meridianRelationRs[$v['meridian_id']],
                'acupoint_id' => $acupointRelationRs[$v['acupoint_id']],
                'add_time' => $nowTime,
                'sort' => $v['sort'],
                'acupoint_name' => $v['acupoint_name'],
            ];
        }
        if (isset($insertData[0])) {
            AcupointMerModel::insert($insertData);
        }

        return $this->successJson('经络、穴位信息迁移完成了');
    }

    public function article()
    {
        if (\YunShop::app()->uniacid == $this->sourceAppid) {
            return $this->errorJson('养居益自身项目数据，无需同步');
        }
        $update = (int) \YunShop::request()->update;
        if ($update === 1) {
            $update = true;
        } else {
            $update = false;
        }

        // 文章分类信息迁移
        $sourceRs = ArticleCategoryModel::where('uniacid', $this->sourceAppid)->get()->toArray();

        $nowRs = ArticleCategoryModel::select('id', 'name')
            ->where('uniacid', \YunShop::app()->uniacid)->get()->toArray();

        $insertData = [];
        $nowTime = time();
        foreach ($sourceRs as $v) {
            $tempId = 0;
            foreach ($nowRs as $v1) {
                if ($v['name'] != $v1['name']) {
                    continue;
                }
                $tempId = $v1['id'];
                break;
            }

            if ($tempId > 0) {
                if ($update == true) {
                    ArticleCategoryModel::where([
                        'id' => $tempId,
                        'uniacid' => \YunShop::app()->uniacid,
                    ])->limit(1)->update([
                        'name' => $v['name'],
                        'image' => $v['image'],
                        'jumpurl' => $v['jumpurl'],
                        'status' => $v['status'],
                        'list_order' => $v['list_order'],
                        'is_href' => $v['is_href'],
                        'type' => $v['type'],
                    ]);
                }

                continue;
            }
            $insertData[] = [
                'uniacid' => \YunShop::app()->uniacid,
                'name' => $v['name'],
                'image' => $v['image'],
                'jumpurl' => $v['jumpurl'],
                'status' => $v['status'],
                'list_order' => $v['list_order'],
                'is_href' => $v['is_href'],
                'create_time' => $nowTime,
                'type' => $v['type'],
            ];
        }
        if (isset($insertData[0])) {
            ArticleCategoryModel::insert($insertData);
        }

        // 文章分类ID对照关系
        $nowRs = ArticleCategoryModel::select('id', 'name')
            ->where('uniacid', \YunShop::app()->uniacid)->get()->toArray();
        $categoryRelationRs = [];
        foreach ($sourceRs as $v1) {
            foreach ($nowRs as $v2) {
                if ($v1['name'] != $v2['name']) {
                    continue;
                }
                $categoryRelationRs[$v1['id']] = $v2['id'];
                break;
            }
        }
        if (count($sourceRs) != count($categoryRelationRs)) {
            return $this->errorJson('文章分类信息迁移出错了');
        }

        // 穴位ID对照关系
        $sourceRs = AcupointModel::where('uniacid', $this->sourceAppid)->get()->toArray();

        $nowRs = AcupointModel::select('id', 'name')
            ->where('uniacid', \YunShop::app()->uniacid)->get()->toArray();
        $acupointRelationRs = [];
        foreach ($sourceRs as $v1) {
            foreach ($nowRs as $v2) {
                if ($v1['name'] != $v2['name']) {
                    continue;
                }
                $acupointRelationRs[$v1['id']] = $v2['id'];
                break;
            }
        }
        if (count($sourceRs) != count($acupointRelationRs)) {
            return $this->errorJson('穴位信息迁移出错了');
        }

        // 文章信息迁移
        $sourceRs = ArticleModel::where('uniacid', $this->sourceAppid)->get()->toArray();

        $nowRs = ArticleModel::select('id', 'title', 'description')
            ->where('uniacid', \YunShop::app()->uniacid)->get()->toArray();

        $insertData = [];
        $nowTime = time();
        foreach ($sourceRs as $v) {
            $tempId = 0;
            foreach ($nowRs as $v1) {
                if ($v['name'] != $v1['name']) {
                    continue;
                }
                if ($v['description'] != $v1['description']) {
                    continue;
                }
                $tempId = $v1['id'];
                break;
            }

            if (!isset($categoryRelationRs[$v['cateid']])) {
                continue;
            }

            $v['recommend_acupotion'] = explode(',', $v['recommend_acupotion']);
            $v['recommend_acupotion'] = array_values(array_unique(array_filter($v['recommend_acupotion'])));
            $newAcupoint = [];
            foreach ($v['recommend_acupotion'] as $v2) {
                if (!isset($acupointRelationRs[$v2])) {
                    continue 2;
                }

                $newAcupoint[] = $acupointRelationRs[$v2];
            }

            if ($tempId > 0) {
                if ($update == true) {
                    ArticleModel::where([
                        'id' => $tempId,
                        'uniacid' => \YunShop::app()->uniacid,
                    ])->limit(1)->update([
                        'cateid' => $categoryRelationRs[$v['cateid']],
                        'title' => $v['title'],
                        'description' => $v['description'],
                        'share_img' => $v['share_img'],
                        'content' => $v['content'],
                        'thumb' => $v['thumb'],
                        'images' => $v['images'],
                        'author' => $v['author'],
                        'list_order' => $v['list_order'],
                        'status' => $v['status'],
                        'video' => $v['video'],
                        'is_discuss' => $v['is_discuss'],
                        'ture_option' => $v['ture_option'],
                        'discuss_title' => $v['discuss_title'],
                        'discuss_answer_description' => $v['discuss_answer_description'],
                        'discuss_start' => $v['discuss_start'],
                        'end_time' => $v['end_time'],
                        'to_type_id' => $v['to_type_id'],
                        'recommend_acupotion' => implode(',', $newAcupoint),
                        'is_hot' => $v['is_hot'],
                    ]);
                }

                continue;
            }
            $insertData[] = [
                'cateid' => $categoryRelationRs[$v['cateid']],
                'uniacid' => \YunShop::app()->uniacid,
                'title' => $v['title'],
                'description' => $v['description'],
                'share_img' => $v['share_img'],
                'content' => $v['content'],
                'thumb' => $v['thumb'],
                'images' => $v['images'],
                'uid' => $v['uid'],
                'author' => $v['author'],
                'list_order' => $v['list_order'],
                'status' => $v['status'],
                'create_time' => $nowTime,
                'video' => $v['video'],
                'is_discuss' => $v['is_discuss'],
                'ture_option' => $v['ture_option'],
                'discuss_title' => $v['discuss_title'],
                'discuss_answer_description' => $v['discuss_answer_description'],
                'discuss_start' => $v['discuss_start'],
                'end_time' => $v['end_time'],
                'to_type_id' => $v['to_type_id'],
                'recommend_acupotion' => implode(',', $newAcupoint),
                'is_hot' => $v['is_hot'],
            ];
        }
        if (isset($insertData[0])) {
            ArticleModel::insert($insertData);
        }

        return $this->successJson('文章、分类信息迁移完成了');
    }

    public function question()
    {
        if (\YunShop::app()->uniacid == $this->sourceAppid) {
            return $this->errorJson('养居益自身项目数据，无需同步');
        }
        $update = (int) \YunShop::request()->update;
        if ($update === 1) {
            $update = true;
        } else {
            $update = false;
        }

        // 症状标签信息迁移
        $sourceRs = LabelModel::where('uniacid', $this->sourceAppid)->get()->toArray();

        $nowRs = LabelModel::select('id', 'name', 'type')
            ->where('uniacid', \YunShop::app()->uniacid)->get()->toArray();

        $insertData = [];
        $nowTime = time();
        foreach ($sourceRs as $v) {
            $tempId = 0;
            foreach ($nowRs as $v1) {
                if ($v['type'] != $v1['type']) {
                    continue;
                }
                if ($v['name'] != $v1['name']) {
                    continue;
                }
                $tempId = $v1['id'];
                break;
            }

            if ($tempId > 0) {
                if ($update == true) {
                    LabelModel::where([
                        'id' => $tempId,
                        'uniacid' => \YunShop::app()->uniacid,
                    ])->limit(1)->update([
                        'name' => $v['name'],
                        'status' => $v['status'],
                        'list_order' => $v['list_order'],
                        'type' => $v['type'],
                    ]);
                }

                continue;
            }
            $insertData[] = [
                'uniacid' => \YunShop::app()->uniacid,
                'name' => $v['name'],
                'create_time' => $nowTime,
                'status' => $v['status'],
                'list_order' => $v['list_order'],
                'type' => $v['type'],
            ];
        }
        if (isset($insertData[0])) {
            LabelModel::insert($insertData);
        }

        // 症状标签ID对照关系
        $nowRs = LabelModel::select('id', 'name', 'type')
            ->where('uniacid', \YunShop::app()->uniacid)->get()->toArray();
        $labelRelationRs = [];
        foreach ($sourceRs as $v1) {
            foreach ($nowRs as $v2) {
                if ($v1['type'] != $v2['type']) {
                    continue;
                }
                if ($v1['name'] != $v2['name']) {
                    continue;
                }
                $labelRelationRs[$v1['id']] = $v2['id'];
                break;
            }
        }
        if (count($sourceRs) != count($labelRelationRs)) {
            return $this->errorJson('症状标签信息迁移出错了');
        }

        // 穴位ID对照关系
        $sourceRs = AcupointModel::where('uniacid', $this->sourceAppid)->get()->toArray();

        $nowRs = AcupointModel::select('id', 'name')
            ->where('uniacid', \YunShop::app()->uniacid)->get()->toArray();
        $acupointRelationRs = [];
        foreach ($sourceRs as $v1) {
            foreach ($nowRs as $v2) {
                if ($v1['name'] != $v2['name']) {
                    continue;
                }
                $acupointRelationRs[$v1['id']] = $v2['id'];
                break;
            }
        }
        if (count($sourceRs) != count($acupointRelationRs)) {
            return $this->errorJson('穴位信息迁移出错了');
        }

        // 文章ID对照关系
        $sourceRs = ArticleModel::where('uniacid', $this->sourceAppid)->get()->toArray();

        $nowRs = ArticleModel::select('id', 'title', 'description')
            ->where('uniacid', \YunShop::app()->uniacid)->get()->toArray();
        $articleRelationRs = [];
        foreach ($sourceRs as $v1) {
            foreach ($nowRs as $v2) {
                if ($v1['name'] != $v2['name']) {
                    continue;
                }
                if ($v1['description'] != $v2['description']) {
                    continue;
                }
                $articleRelationRs[$v1['id']] = $v2['id'];
                break;
            }
        }
        if (count($sourceRs) / 2 > count($articleRelationRs)) {
            return $this->errorJson('文章信息迁移，可能出错了；此处判断迁移后文章，不能少于迁移前文章的一半');
        }

        // 体质信息迁移
        $sourceRs = SomatoTypeModel::where('uniacid', $this->sourceAppid)->get()->toArray();

        $nowRs = SomatoTypeModel::select('id', 'name')
            ->where('uniacid', \YunShop::app()->uniacid)->get()->toArray();

        $insertData = [];
        $nowTime = time();
        foreach ($sourceRs as $v) {
            $tempId = 0;
            foreach ($nowRs as $v1) {
                if ($v['name'] != $v1['name']) {
                    continue;
                }
                $tempId = $v1['id'];
                break;
            }

            $v['symptom'] = explode(',', $v['symptom']);
            $v['symptom'] = array_values(array_unique(array_filter($v['symptom'])));
            $newLabel = [];
            foreach ($v['symptom'] as $v2) {
                if (!isset($labelRelationRs[$v2])) {
                    continue 2;
                }

                $newLabel[] = $labelRelationRs[$v2];
            }

            $v['recommend_article'] = explode(',', $v['recommend_article']);
            $v['recommend_article'] = array_values(array_unique(array_filter($v['recommend_article'])));
            $newArticle = [];
            foreach ($v['recommend_article'] as $v3) {
                // TODO 文章不存在，跳过不处理
                if (!isset($articleRelationRs[$v3])) {
                    continue;
                }

                $newArticle[] = $articleRelationRs[$v3];
            }

            $v['recommend_acupotion'] = explode(',', $v['recommend_acupotion']);
            $v['recommend_acupotion'] = array_values(array_unique(array_filter($v['recommend_acupotion'])));
            $newAcupoint = [];
            foreach ($v['recommend_acupotion'] as $v4) {
                if (!isset($acupointRelationRs[$v4])) {
                    continue 2;
                }

                $newAcupoint[] = $acupointRelationRs[$v4];
            }

            if ($tempId > 0) {
                if ($update == true) {
                    SomatoTypeModel::where([
                        'id' => $tempId,
                        'uniacid' => \YunShop::app()->uniacid,
                    ])->limit(1)->update([
                        'name' => $v['name'],
                        'nums' => $v['nums'],
                        'pass_score' => $v['pass_score'],
                        'description' => $v['description'],
                        'symptom' => implode(',', $newLabel),
                        'disease' => $v['disease'],
                        'content' => $v['content'],
                        'recommend_article' => implode(',', $newArticle),
                        'recommend_acupotion' => implode(',', $newAcupoint),
                        'title' => $v['title'],
                    ]);
                }

                continue;
            }
            $insertData[] = [
                'uniacid' => \YunShop::app()->uniacid,
                'name' => $v['name'],
                'nums' => $v['nums'],
                'pass_score' => $v['pass_score'],
                'description' => $v['description'],
                'symptom' => $v['symptom'],
                'disease' => $v['disease'],
                'content' => $v['content'],
                'recommend_article' => implode(',', $newArticle),
                'recommend_acupotion' => implode(',', $newAcupoint),
                'create_time' => $nowTime,
                'title' => $v['title'],
            ];
        }
        if (isset($insertData[0])) {
            SomatoTypeModel::insert($insertData);
        }

        // 体质ID对照关系
        $nowRs = SomatoTypeModel::select('id', 'name')
            ->where('uniacid', \YunShop::app()->uniacid)->get()->toArray();
        $typeRelationRs = [];
        foreach ($sourceRs as $v1) {
            foreach ($nowRs as $v2) {
                if ($v1['name'] != $v2['name']) {
                    continue;
                }
                $typeRelationRs[$v1['id']] = $v2['id'];
                break;
            }
        }
        if (count($sourceRs) != count($typeRelationRs)) {
            return $this->errorJson('体质信息迁移出错了');
        }

        // 测评题库信息迁移
        $sourceRs = QuestionBankModel::where('uniacid', $this->sourceAppid)->get()->toArray();

        $nowRs = QuestionBankModel::select('id', 'title')
            ->where('uniacid', \YunShop::app()->uniacid)->get()->toArray();

        $insertData = [];
        $nowTime = time();
        foreach ($sourceRs as $v) {
            $tempId = 0;
            foreach ($nowRs as $v1) {
                if ($v['title'] != $v1['title']) {
                    continue;
                }
                $tempId = $v1['id'];
                break;
            }

            $v['somato_type_id'] = explode(',', $v['somato_type_id']);
            $v['somato_type_id'] = array_values(array_unique(array_filter($v['somato_type_id'])));
            $newType = [];
            foreach ($v['somato_type_id'] as $v2) {
                if (!isset($typeRelationRs[$v2])) {
                    continue 2;
                }

                $newType[] = $typeRelationRs[$v2];
            }

            if ($tempId > 0) {
                if ($update == true) {
                    QuestionBankModel::where([
                        'id' => $tempId,
                        'uniacid' => \YunShop::app()->uniacid,
                    ])->limit(1)->update([
                        'list_order' => $v['list_order'],
                        'title' => $v['title'],
                        'option1_score' => $v['option1_score'],
                        'option2_score' => $v['option2_score'],
                        'option3_score' => $v['option3_score'],
                        'option4_score' => $v['option4_score'],
                        'option5_score' => $v['option5_score'],
                        'options' => $v['options'],
                        'somato_type_id' => implode(',', $newType),
                        'status' => $v['status'],
                        'gender' => $v['gender'],
                        'somato_type_name' => $v['somato_type_name'],
                    ]);
                }

                continue;
            }
            $insertData[] = [
                'uniacid' => \YunShop::app()->uniacid,
                'list_order' => $v['list_order'],
                'title' => $v['title'],
                'option1_score' => $v['option1_score'],
                'option2_score' => $v['option2_score'],
                'option3_score' => $v['option3_score'],
                'option4_score' => $v['option4_score'],
                'option5_score' => $v['option5_score'],
                'options' => $v['options'],
                'somato_type_id' => implode(',', $newType),
                'status' => $v['status'],
                'create_time' => $nowTime,
                'gender' => $v['gender'],
                'somato_type_name' => $v['somato_type_name'],
            ];
        }
        if (isset($insertData[0])) {
            QuestionBankModel::insert($insertData);
        }

        // 题库ID对照关系
        $nowRs = QuestionBankModel::select('id', 'title')
            ->where('uniacid', \YunShop::app()->uniacid)->get()->toArray();

        $questionRelationRs = [];
        foreach ($sourceRs as $v1) {
            foreach ($nowRs as $v2) {
                if ($v1['title'] != $v2['title']) {
                    continue;
                }
                $questionRelationRs[$v1['id']] = $v2['id'];
                break;
            }
        }
        if (count($sourceRs) != count($questionRelationRs)) {
            return $this->errorJson('测评题库信息迁移出错了');
        }

        $sourceRs = SomatoQuestionModel::where('uniacid', $this->sourceAppid)->get()->toArray();

        if ($update == true) {
            SomatoQuestionModel::where('uniacid', \YunShop::app()->uniacid)->delete();
        }

        $nowRs = SomatoQuestionModel::select('id', 'somato_type_id', 'question_id')
            ->where('uniacid', \YunShop::app()->uniacid)->get()->toArray();

        $insertData = [];
        $nowTime = time();
        foreach ($sourceRs as $v) {
            if (!isset($typeRelationRs[$v['somato_type_id']])) {
                continue;
            }
            if (!isset($questionRelationRs[$v['question_id']])) {
                continue;
            }

            $tempId = 0;
            foreach ($nowRs as $v1) {
                if ($typeRelationRs[$v['somato_type_id']] != $v1['somato_type_id']) {
                    continue;
                }
                if ($questionRelationRs[$v['question_id']] != $v1['question_id']) {
                    continue;
                }

                $tempId = $v1['id'];
                break;
            }
            if ($tempId > 0) {
                continue;
            }

            $insertData[] = [
                'uniacid' => \YunShop::app()->uniacid,
                'somato_type_id' => $typeRelationRs[$v['somato_type_id']],
                'question_id' => $questionRelationRs[$v['question_id']],
                'add_time' => $nowTime,
                'score_sort' => $v['score_sort'],
            ];
        }
        if (isset($insertData[0])) {
            SomatoQuestionModel::insert($insertData);
        }

        return $this->successJson('症状标签、体质、测评题库信息迁移完成了');
    }
}
