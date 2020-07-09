<?php

namespace Yunshop\Poster\observers;

use app\common\observers\BaseObserver;
use app\common\models\frame\Rule;
use app\common\models\frame\RuleKeyword;
use app\common\traits\MessageTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PosterObserver extends BaseObserver
{
    use MessageTrait;

    public function created(Model $model)
    {
        $moduleName = \Config::get('app.module_name');

        if ($model->keyword) {
            $ruleModel = new Rule(['containtype'=>'basic', 'reply_type'=>'2']);//兼容微擎新版新增的 2 个字段

            $ruleData = array(
                'uniacid'   => \YunShop::app()->uniacid,
                'name'      => $moduleName . ':poster:' . $model->id,
                'status' => $model->status, //Rule 表的"启用/禁用"状态和海报的"启用/禁用"状态是一致的
            );
            $ruleModel->fill(array_merge($ruleData,$ruleModel->attributes));
            $validator = $ruleModel->validator($ruleModel->getAttributes());
            if ($validator->fails()) {
                $this->error($validator->messages());
                return false;
            }
            if (!$ruleModel->save()) {
                $this->error('rule写入出错，请重试！');
                return false;
            }

            $keywordModel = new RuleKeyword();

            $keywordDate = array(
                'uniacid'   => \YunShop::app()->uniacid,
                'rid'       => $ruleModel->id,
                'content'   => trim($model->keyword),
            );
            $keywordModel->fill(array_merge($keywordDate,$keywordModel->attributes));
            $validator = $keywordModel->validator($keywordModel->getAttributes());
            if ($validator->fails()) {
                $this->error($validator->messages());
                return false;
            }
            if (!$keywordModel->save()) {
                $this->error('keyword写入出错，请重试！');
                return false;
            }

            return true;

        } else {
            $this->error('缺少关键字, rule写入出错，请重试！');
            return false;
        }
    }

    public function updated(Model $model)
    {
        $moduleName = \Config::get('app.module_name');
        $ruleModel = Rule::getRuleByName($moduleName . ':poster:' . $model->id);
        $uniacid = \YunShop::app()->uniacid;

        if($ruleModel && $model->widgets['status'] != $model->status){
            $ruleModel->update(['status' => $model->status]); //更新"禁用/启用"状态
        }

        if ($ruleModel && $model->keyword && $model->widgets['keyword'] != $model->keyword) { //rule存在,且新旧关键字不相同,则修改rule_key_word
            if (config('APP_Framework') == 'platform') {
                DB::table('yz_qrcode')->where('scene_str', 'like', $moduleName.'_'.$uniacid.'_'.$model->id.'_%')
                    ->update(['keyword'=>$model->keyword]); //更新ims_yz_qrcode的keyword
            } else {
                DB::table('qrcode')->where('scene_str', 'like', $moduleName.'_'.$uniacid.'_'.$model->id.'_%')
                    ->update(['keyword'=>$model->keyword]); //更新ims_qrcode的keyword
            }
            if (!RuleKeyword::updateKeywordByRoleId($ruleModel->id, $model->keyword)) {
                $this->error('关键字修改失败，请重试！');
                return false;
            }

        } elseif($ruleModel && !$model->keyword) { //rule存在，提交表单不包含关键字，则删除rule_key_word
            if (!RuleKeyword::destroyKeywordByRuleId($ruleModel->id)) {
                $this->error('keyword修改失败，请重试！');
                return false;
            }
            if ($ruleModel->delete()) {
                $this->error('rule修改失败，请重试！');
                return false;
            }

        } elseif(!$ruleModel && $model->keyword) { //rule不存在，提交表单包含关键字，则添加
            $this->created($model);
        }
    }

    public function deleting(Model $model)
    {
        $moduleName = \Config::get('app.module_name');
        $ruleModel = Rule::getRuleByName($moduleName . ':poster:' . $model->id);

        if (!is_null($ruleModel)) {
            $result01 = RuleKeyword::destroyKeywordByRuleId($ruleModel->id); //删除海报时, 删除关联的rule_keyword
            $result02 = $ruleModel->delete(); //删除海波时, 删除关联的rule
            if(!$result01 || !$result02) {
                $this->error('无法删除海报关联的 Rule, 删除失败.');
                return false;
            }
        }
    }
}