<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/6/15
 * Time: 下午4:25
 */

namespace Yunshop\Exhelper\admin;


use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use Yunshop\Exhelper\common\models\SendUser;

class SendUserController extends BaseController
{
    public function index()
    {
        $list = SendUser::getList()->paginate(20);
        $pager = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());

        return view('Yunshop\Exhelper::admin.send_user_list', [
            'list'  => $list,
            'pager' => $pager
        ]);
    }

    private function updateDefault()
    {
        $default = SendUser::getDefault()->first();
        if ($default && $default->id != \YunShop::request()->id) {
            $default->isdefault = SendUser::NO_DEFAULT;
            $default->save();
        }
    }

    public function add()
    {
        $user_data = \YunShop::request()->user;
        if ($user_data) {
            $user_data['uniacid'] = \YunShop::app()->uniacid;
            $model = new SendUser();
            $model->fill($user_data);
            $validator = $model->validator();
            if ($validator->fails()) {
                $this->error($validator->messages());
            } else {
                if ($model->isdefault == SendUser::IS_DEFAULT) {
                    $this->updateDefault();
                }
                $model->save();
                return $this->message('添加成功', Url::absoluteWeb('plugin.exhelper.admin.send-user.index'));
            }
        }
        return view('Yunshop\Exhelper::admin.send_user_detail', [

        ]);
    }

    public function edit()
    {
        $id = \YunShop::request()->id;
        if (!$id) {
            return $this->message('参数错误', Url::absoluteWeb('plugin.exhelper.admin.send-user.index'), 'error');
        }
        $model = SendUser::find($id);
        if (!$model) {
            return $this->message('未找到数据', Url::absoluteWeb('plugin.exhelper.admin.send-user.index'), 'error');
        }
        $user_data = \YunShop::request()->user;
        if ($user_data) {
            $model->fill($user_data);
            $validator = $model->validator();
            if ($validator->fails()) {
                $this->error($validator->messages());
            } else {
                if ($model->isdefault == SendUser::IS_DEFAULT) {
                    $this->updateDefault();
                }
                $model->save();
                return $this->message('添加成功', Url::absoluteWeb('plugin.exhelper.admin.send-user.index'));
            }
        }

        return view('Yunshop\Exhelper::admin.send_user_detail', [
            'item' => $model
        ]);
    }

    public function delete()
    {
        $id = \YunShop::request()->id;
        if (!$id) {
            return $this->message('参数错误', Url::absoluteWeb('plugin.exhelper.admin.send-user.index'), 'error');
        }
        $model = SendUser::find($id);
        if (!$model) {
            return $this->message('未找到数据', Url::absoluteWeb('plugin.exhelper.admin.send-user.index'), 'error');
        }
        $model->delete();
        return $this->message('删除成功', Url::absoluteWeb('plugin.exhelper.admin.send-user.index'));
    }

    public function isDefault()
    {
        $id = \YunShop::request()->id;
        if (!$id) {
            return $this->message('参数错误', Url::absoluteWeb('plugin.exhelper.admin.send-user.index'), 'error');
        }
        $model = SendUser::find($id);
        if (!$model) {
            return $this->message('未找到数据', Url::absoluteWeb('plugin.exhelper.admin.send-user.index'), 'error');
        }
        if ($model->isdefault == SendUser::IS_DEFAULT) {
            return $this->message('已经设置为默认', Url::absoluteWeb('plugin.exhelper.admin.send-user.index'), 'error');
        }
        $model->isdefault = SendUser::IS_DEFAULT;
        if ($model->isdefault == SendUser::IS_DEFAULT) {
            $this->updateDefault();
        }
        $model->save();
        return $this->message('设置默认成功', Url::absoluteWeb('plugin.exhelper.admin.send-user.index'));
    }
}