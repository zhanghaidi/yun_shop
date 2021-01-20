<?php

namespace Yunshop\LeaseToy\admin;

use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use app\common\models\MemberLevel;
use app\common\facades\Setting;
use Yunshop\LeaseToy\models\LevelRightsModel;


/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/1
 * Time: 15:42
 */
class LevelRightsController extends BaseController
{
    
    public function index()
    {

        $list = $this->getLevel();


        // $free = \YunShop::request()->free;
        // if ($free) {
        //     foreach($free as $id => $data){
        //         $error = $this->conserveFree($id, $data);

        //         if ($error) {
        //             $this->error($error);
        //         }
        //     }
        //     if (!$error) {
        //         return $this->message('保存成功', Url::absoluteWeb('plugin.lease-toy.admin.level-rights.index'));
        //     }
        // }
        return view('Yunshop\LeaseToy::admin.level-rights-list', [
            'list' => $list,
        ])->render();   
    }

    /**
     * 获取会员等级信息
     * @return [type] [description]
     */
    public function getLevel()
    {
        $levelSet = Setting::get('member');

        if ($levelSet['level_type'] != 2) {
            return [];
        }
        $levelModel = MemberLevel::uniacid()->records()->orderBy('level')->get();

        return $this->getMap($levelModel);
    }

    private function getMap($levelModel)
    {
        $levelModel->map(function($model){
            $bool =  LevelRightsModel::getRights($model->id);

            if ($bool) {
                $model->rent_free = $bool->rent_free;
                $model->deposit_free = $bool->deposit_free;
            } else {
                $model->rent_free = 0;
                $model->deposit_free = 0;
                LevelRightsController::setDefault($model->id);
            }

        });

        return $levelModel;
    }

    /**
     * 设置免押金、免租金权益
     */
    public function setFree()
    {
        $free = \YunShop::request()->free;

        foreach($free as $id => $data){
            $error = $this->conserveFree($id, $data);

            if (!$error['status']) {
                return $this->message('ID为('.$error['msg'].')的参数有误', '', 'error');
            }
        }

        return $this->message('保存成功', Url::absoluteWeb('plugin.lease-toy.admin.level-rights.index'));


    }

    /**
     * 保存设置
     * @return [type] [description]
     */
    private function conserveFree($id, $data)
    {
        if (!$id) {
            return $this->message('无此等级或以删除', '', 'error');
        }
        $model = LevelRightsModel::getModel($id);

        $model->setRawAttributes($data);
        //其他字段赋值
        $model->uniacid = \YunShop::app()->uniacid;
        $model->level_id = $id;
        //字段检测
        $validator = $model->validator($model->getAttributes());
        if ($validator->fails()) {
            //检测失败

            return [
                'status' => false,
                'msg' => $id,
            ];
        }
        return [
            'status' => $model->save(),
        ];

    }

    /**
     * 设置默认
     * @param [type] $levelId [description]
     */
    private static function setDefault($levelId)
    {
        $data = [
            'level_id' => $levelId,
            'uniacid' => \Yunshop::app()->uniacid,
            'rent_free' => 0,
            'deposit_free' => 0,
        ];

        $model = LevelRightsModel::getModel($levelId);
        $model->setRawAttributes($data);
        $model->save();
    }
}