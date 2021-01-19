<?php

namespace Yunshop\MinappContent\admin;

use app\common\components\BaseController;
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
        var_dump($meridianRelationRs);exit;

        return $this->successJson('经络信息迁移出错了');

    }
}
