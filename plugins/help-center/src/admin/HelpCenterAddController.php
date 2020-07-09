<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/30 0030
 * Time: 上午 11:14
 */

namespace Yunshop\HelpCenter\admin;

use app\common\components\BaseController;
use app\common\helpers\Url;
use Yunshop\HelpCenter\models\HelpCenterAddModel;

class HelpCenterAddController extends BaseController
{
    public function index()
    {
        $helpcenteraddModel = new HelpCenterAddModel();

        $requestdata = \Yunshop::request()->adddata;

        $url = Url::absoluteWeb('plugin.help-center.admin.help-center-manage.index');

        if ($requestdata) {
            //添加数据到model

            $requestdata['uniacid'] = \YunShop::app()->uniacid;

//            $requestdata['content'] = html_entity_decode($requestdata['content'];
//            dd($requestdata);
            $helpcenteraddModel->fill($requestdata);
            //字段验证
            $validator = $helpcenteraddModel->validator();
            if ($validator->fails()) {
                //验证失败
                $this->error($validator->messages());
            } else {
                //保存数据
                if ($helpcenteraddModel->save()) {
                    return $this->message('保存成功', $url);
                } else {
                    $this->error('保存失败');
                }
            }
        }

        $backurl = Url::absoluteWeb('plugin.help-center.admin.help-center-manage.index');

        return view('Yunshop\HelpCenter::admin.add-help', [
            'backurl' => $backurl,
        ]);
    }


    public function edit()
    {
        $backurl = Url::absoluteWeb('plugin.help-center.admin.help-center-manage.index');

        $manageModel = HelpCenterAddModel::find(\YunShop::request()->id);

        if (!$manageModel) {
            return $this->message('此条记录已被删除', '', 'error');
        }

        $requestdata = \YunShop::request()->adddata;

        if ($requestdata) {
            $manageModel->title = $requestdata['title'];
            $manageModel->sort = $requestdata['sort'];
            $manageModel->content = $requestdata['content'];
            if ($manageModel->save()) {
                return $this->message('更改成功',$backurl);
            } else {
                $this->error('更改失败');
            }
        }

        return view('Yunshop\HelpCenter::admin.add-help', [
            'backurl' => $backurl,
            'data' => $manageModel,
        ]);
    }

    public function del()
    {
        $backurl = Url::absoluteWeb('plugin.help-center.admin.help-center-manage.index');
       $managemodel = HelpCenterAddModel::find(\YunShop::request()->id);
       if (!$managemodel) {
           return $this->message('此条数据以被删除', '', 'error');
       }

       if ($managemodel->delete()) {
           return $this->message('删除成功',$backurl);
       } else {
           return $this->message('删除失败','');
       }
    }
}