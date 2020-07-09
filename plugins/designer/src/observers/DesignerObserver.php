<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/3/26
 * Time: 上午11:09
 */

namespace Yunshop\Designer\observers;


use app\common\models\frame\Rule;
use app\common\models\frame\RuleKeyword;
use app\common\observers\BaseObserver;
use app\common\traits\MessageTrait;
use Illuminate\Database\Eloquent\Model;
use PhpParser\Node\Expr\Eval_;
use Yunshop\Designer\models\Designer;

class DesignerObserver extends BaseObserver
{
    use MessageTrait;

    public function created(Model $model)
    {
        if ($model->keyword) {

            $ruleModel = new Rule();

            $ruleData = array(
                'uniacid'   => \YunShop::app()->uniacid,
                'name'      => 'yun_shop:designer:' . $model->id
            );
            $ruleModel->fill($ruleData + $ruleModel->attributes);
            $validator = $ruleModel->validator($ruleModel->getAttributes());
            if ($validator->fails()) {
                $this->error($validator->messages());
                return false;
            }
            if (!$ruleModel->save()) {
                $this->error('rule写入错误，请重试！');
                return false;
            }


            $keywordModel = new RuleKeyword();

            $keywordDate = array(
                'uniacid'   => \YunShop::app()->uniacid,
                'rid'       => $ruleModel->id,
                'content'   => trim($model->keyword),
            );
            $keywordModel->fill($keywordDate + $keywordModel->attributes);
            $validator = $keywordModel->validator($keywordModel->getAttributes());
            if ($validator->fails()) {
                $this->error($validator->messages());
                return false;
            }
            if (!$keywordModel->save()) {
                $this->error('keyword写入出错，请重试！');
                return false;
            }
        }

    }

    public function updated(Model $model)
    {
        $ruleModel = Rule::getRuleByName('yun_shop:designer:' . $model->id);
        //数据库和提交表单同时存在并且不相同则修改关键字
        if ($ruleModel && $model->keyword && $model->widgets != $model->keyword) {
            if (!RuleKeyword::updateKeywordByRoleId($ruleModel->id, $model->keyword)) {
                $this->error('关键字修改失败，请重试！');
                return false;
            }
        //数据库存在，提交表单不存在，则删除关键字
        } elseif($ruleModel && !$model->keyword) {
            if (!RuleKeyword::destroyKeywordByRuleId($ruleModel->id)) {
                $this->error('keyword修改失败，请重试！');
                return false;
            }
            if ($ruleModel->delete()) {
                $this->error('rule修改失败，请重试！');
                return false;
            }
        //数据库不存在，提交表单存在，则添加关键字
        } elseif(!$ruleModel && $model->keyword) {
            $this->created($model);
        }
    }

    public function saved(Model $model)
    {
        if ($model->page_type != 0 && $model->page_type != 10) {
            $this->updateHomePage($model);
        }
    }


    private function updateHomePage(Model $model)
    {
        $page_type = explode(',', $model->page_type);

        //dd($page_type);

        foreach ($page_type as $key => $value) {

            $designerModel = Designer::uniacid()->whereRaw('FIND_IN_SET(?,page_type)', [$value])->where('id', '<>', $model->id)->first();

            if ($designerModel) {
                $new_page_type = $this->getNewPageType($designerModel->page_type, $value);

                Designer::whereId($designerModel->id)->update(['page_type' => $new_page_type]);
            }
            unset($designerModel);
        }
    }


    private function getNewPageType($pageType, $type)
    {
        //如果是原生小程序直接返回原生小程序其他页面
        if ($pageType == 9) {
            return 10;
        }
        $pageType = explode(',', $pageType);

        $pageType = array_diff($pageType, [$type]);

        return empty($pageType) ? 0 : implode(',', $pageType);
    }



}
