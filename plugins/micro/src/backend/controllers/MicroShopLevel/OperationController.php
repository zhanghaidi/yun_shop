<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/9
 * Time: 下午4:52
 */

namespace Yunshop\Micro\backend\controllers\MicroShopLevel;


use app\common\components\BaseController;
use app\common\helpers\Url;
use Yunshop\Micro\common\models\MicroShopLevel;
use Yunshop\Micro\common\services\MicroShopLevel\LevelService;

class OperationController extends BaseController
{

    private $list_url = 'plugin.micro.backend.controllers.MicroShopLevel.list';

    /**
     * @name 添加微店等级
     * @author 杨洋
     * @param \Request $request
     * @return mixed|string
     */
    public function add(\Request $request)
    {
        $level_model = new MicroShopLevel();
        if (isset($request->level)) {
            $data = $request->level;
            $data['uniacid'] = \YunShop::app()->uniacid;
            if($data['level_weight']==0){
               return $this->message('等级权重不可为0 ');
            }
            $level_model->fill($data);
            $validator = $level_model->validator();
            if ($validator->fails()) {
                $this->error($validator->messages());
            } else {
                $level_model->save();
                return $this->message('添加等级成功', Url::absoluteWeb($this->list_url));
            }
        }

        return $this->view_render([]);
    }

    /**
     * @name 修改微店等级
     * @author 杨洋
     * @param \Request $request
     * @return mixed|string
     */
    public function edit(\Request $request)
    {
        $level_model = MicroShopLevel::getLevelById($request->id);

        if (isset($request->level)) {
            $data=$request->level;
            if($data['level_weight']==0){
                   return $this->message('等级权重不可为0');
            }
            $level_model->fill($request->level);
            $validator = $level_model->validator($level_model->getAttributes());
            if ($validator->fails()) {
                $this->error($validator->messages());
            } else {
                //$result = MicroShopLevel::updateLevelById($request->id, $request->level);
                if ($level_model->save()) {
                    return $this->message('更新等级成功', Url::absoluteWeb($this->list_url));
                } else {
                    return $this->message('更新等级失败', Url::absoluteWeb($this->list_url), 'error');
                }
            }
        }

        return $this->view_render($level_model);
    }

    /**
     * @name 删除微店等级
     * @author 杨洋
     * @param \Request $request
     * @return mixed
     */
    public function delete(\Request $request)
    {
        // todo 验证微店等级下是否存在微店
        if (LevelService::thisLevelExistsMiceoShop($request->id)) {
            return $this->message('该等级下存在微店，不允许删除！', Url::absoluteWeb($this->list_url), 'error');
        }

        $result = MicroShopLevel::destroy($request->id);
        if ($result) {
            return $this->message('删除成功', Url::absoluteWeb($this->list_url));
        } else {
            return $this->message('删除失败', Url::absoluteWeb($this->list_url), 'error');
        }
    }

    /**
     * @name 通用返回模板
     * @author 杨洋
     * @param $level
     * @return string
     */
    public function view_render($level)
    {
        return view('Yunshop\Micro::backend.MicroShopLevel.info', [
            'level'          => $level
        ])->render();
    }
}