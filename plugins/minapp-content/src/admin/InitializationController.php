<?php

namespace Yunshop\MinappContent\admin;

use app\common\components\BaseController;
use Yunshop\MinappContent\models\AcupointMerModel;
use Yunshop\MinappContent\models\AcupointModel;
use Yunshop\MinappContent\models\MeridianModel;
use Yunshop\MinappContent\models\QuestionBankModel;
use Yunshop\MinappContent\services\MinappContentService;

class InitializationController extends BaseController
{
    public $sourceAppid = 45;

    public function index()
    {
        $oldAcupoint = AcupointModel::where('uniacid', $this->sourceAppid)->count();
        $newAcupoint = AcupointModel::where('uniacid', \YunShop::app()->uniacid)->count();

        $oldQuestion = QuestionBankModel::where('uniacid', $this->sourceAppid)->count();
        $newQuestion = QuestionBankModel::where('uniacid', \YunShop::app()->uniacid)->count();

        return view('Yunshop\MinappContent::admin.init.index', [
            'pluginName' => MinappContentService::get('name'),
            'acupoint' => [
                'old' => $oldAcupoint,
                'new' => $newAcupoint,
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
            if ($tempId > 0) {
                continue;
            }

            $v['meridian_id'] = explode('、', $v['meridian_id']);
            $newMeridian = [];
            foreach ($v['meridian_id'] as $v2) {
                if (!isset($meridianRelationRs[$v2])) {
                    continue 2;
                }

                $newMeridian[] = $meridianRelationRs[$v2];
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

        $sourceRs = AcupointMerModel::where('uniacid', $this->sourceAppid)->get()->toArray();

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
}
