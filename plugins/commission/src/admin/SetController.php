<?php


namespace Yunshop\Commission\admin;

use app\common\facades\Setting;
use app\common\models\notice\MessageTemp;
use app\common\components\BaseController;
use app\common\helpers\Url;

class SetController extends BaseController
{
    /**
     * @return mixed
     */
    public function index()
    {
        $set = Setting::get('plugin.commission');
        $requestModel = \YunShop::request()->setdata;

        if ($requestModel) {
            if (Setting::set('plugin.commission', $requestModel)) {
                return $this->message('设置成功', Url::absoluteWeb('plugin.commission.admin.set'));
            } else {
                $this->error('设置失败');
            }
        }

        return view('Yunshop\Commission::admin.set', [
            'set' => $set
        ])->render();
    }

    public function notice()
    {
        $data = [
            'template_id' => '',
            'become_agent_title' => '成为分销商通知',
            'commission_become' => '尊敬的[昵称]，您于[时间]成为了我们的分销商！',
            'commission_order_title' => '下级下单通知',
            'commission_order' => '尊敬的[昵称]，您的客户[下级昵称]于[时间]下单成功，订单金额·[订单金额]，满足结算条件之后，您将会获得相应的推广奖励。',
            'commission_order_finish_title' => '下级确认收货通知',
            'commission_order_finish' => '尊敬的[昵称]，您的客户[下级昵称]于[时间]确认收货，订单金额为[订单金额]，满足结算条件之后，您将会获得相应的推广奖励。',
            'commission_upgrade_title' => '分销商等级升级通知',
            'commission_upgrade' => '尊敬的[昵称]，您的分销商等级已经由[旧等级]升级为[新等级]，新等级分销佣金为一级[新一级分销比例]、二级[新二级分销比例]、三级[新三级分销比例]',
        ];
        $set = Setting::get('plugin.commission_notice',$data);
        $request = \YunShop::request()->yz_notice;

        if ($request) {
            if (Setting::set('plugin.commission_notice', $request)) {
                return $this->message('设置成功', Url::absoluteWeb('plugin.commission.admin.set.notice'));
            } else {
                $this->error('设置失败');
            }
        }
        $temp_list = MessageTemp::getList();
        return view('Yunshop\Commission::admin.notice', [
            'set' => $set,
            'temp_list' => $temp_list,
        ])->render();
    }

    public function manage()
    {
        $commissionSet = Setting::get('plugin.commission');
        $set = Setting::get('plugin.commission_manage');
        $request = \YunShop::request()->manage;

        if ($request) {
            if (Setting::set('plugin.commission_manage', $request)) {
                return $this->message('设置成功', Url::absoluteWeb('plugin.commission.admin.set.manage'));
            } else {
                $this->error('设置失败');
            }
        }

        return view('Yunshop\Commission::admin.manage', [
            'set' => $set,
            'commissionSet' => $commissionSet
        ])->render();
    }

    /**
     * 定制版设置
     * @return mixed|string
     * @throws \Throwable
     */
    public function expand()
    {
        $commissionSet = Setting::get('plugin.commission');
        $set = Setting::get('plugin.commission_expand');
        $request = \YunShop::request()->setdata;

        if ($request) {
            if (Setting::set('plugin.commission_expand', $request)) {
                return $this->message('设置成功', Url::absoluteWeb('plugin.commission.admin.set.expand'));
            } else {
                $this->error('设置失败');
            }
        }

        return view('Yunshop\Commission::admin.expand', [
            'set' => $set,
            'commissionSet' => $commissionSet
        ])->render();
    }
}
