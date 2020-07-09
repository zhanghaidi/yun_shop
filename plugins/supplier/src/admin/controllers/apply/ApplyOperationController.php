<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/21
 * Time: 下午4:56
 */

namespace Yunshop\Supplier\admin\controllers\apply;


use app\common\components\BaseController;
use app\common\helpers\Url;
use Yunshop\Supplier\admin\models\Supplier;
use Yunshop\Supplier\common\events\SupplierDisposeApplyEvent;
use Yunshop\Supplier\common\models\SupplierObserverMethods;
use Yunshop\Supplier\common\models\WeiQingUsers;
use Yunshop\Supplier\common\services\apply\SupplierApplyService;

class ApplyOperationController extends BaseController
{
    private $type;
    private $apply_id;
    private $result;

    private function pass($uid)
    {
        $this->result['apply_model']->status = $this->type;
        $this->result['apply_model']->uid = $uid;
        $this->result['apply_model']->save();
    }

    private function shopEsignEvent($data)
    {
        event(new \app\common\events\plugin\SupplierEvent($data));
    }


    private function reject()
    {
        $this->result['apply_model']->delete();
    }

    //通过或驳回
    private function passOrOverRule()
    {
        $result = SupplierApplyService::verifyApply(Supplier::getSupplierById($this->apply_id, 0));
        if ($result['status'] == 0) {
            return $this->message($result['msg'], Url::absoluteWeb('plugin.supplier.admin.controllers.apply.supplier-apply'), 'error');
        }
        $this->result = $result;
        $data =[
            'member_id' => $this->result['apply_model']->member_id,
            'username' =>$this->result['apply_model']->username,
            'created_at' => time(),
        ];

        event(new SupplierDisposeApplyEvent($result['apply_model'], $this->type));

        if ($this->type == 1) {

            $result = WeiQingUsers::select()
                ->byUserName($this->result['apply_model']->username)
                ->first();
            if ($result) {
                return $this->message('此用户为系统存在用户,请联系管理员', Url::absoluteWeb('plugin.supplier.admin.controllers.apply.supplier-apply.index'), 'error');
            }

            $uid = Supplier::addWeiqingTables($this->result['apply_model']->username, $this->result['apply_model']->password);
            if (is_array($uid) && $uid['errno'] == -1) {
                return $this->message($uid['message'], Url::absoluteWeb('plugin.supplier.admin.controllers.apply.supplier-apply.index'), 'error');
            } else {
                $this->pass($uid);
                $this->shopEsignEvent($data);
            }
        } else {
            $this->reject();
        }
        SupplierObserverMethods::applyNotice($result['apply_model']->member_id, $this->type);
        return $this->message($this->type == 1?'审核成功':'驳回成功', Url::absoluteWeb('plugin.supplier.admin.controllers.apply.supplier-apply'));
    }

    public function applyOperation()
    {
        if (intval(request()->apply_id) == 0) {
            return $this->message('参数错误', Url::absoluteWeb('plugin.supplier.admin.controllers.apply.supplier-apply'), 'error');
        }
        $this->apply_id = intval(request()->apply_id);
        $this->type = intval(request()->type);
        return $this->passOrOverRule();
    }
}