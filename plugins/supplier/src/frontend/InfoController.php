<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/20
 * Time: 9:49
 */

namespace Yunshop\Supplier\frontend;


use app\common\components\ApiController;
use app\common\components\BaseController;
use Setting;
use Yunshop\Supplier\common\models\Supplier;

class InfoController extends ApiController
{
    // plugin.supplier.frontend.info.getFillSet
    public function getFillSet()
    {
        $set = Setting::get('plugin.supplier');

        if (!$set['info']) {
            $set['info'] = [
                'company_bank' => 1,
                'bank_username' => 1,
                'bank_of_accounts' => 1,
                'opening_branch' => 1,
                'company_ali' => 1,
                'company_ali_username' => 1,
                'ali' => 1,
                'ali_username' => 1,
                'wechat' => 1
            ];
        }

        return $this->successJson('成功', [
            'fill_set' => $set['info']
        ]);
    }

    public function index()
    {
        $member_id = \YunShop::app()->getMemberId();
        $supplier_model = Supplier::getSupplierByMemberId($member_id);

        if (empty($member_id)) {
            return $this->errorJson('会员不存在！');
        }
        if (empty($supplier_model)) {
            return $this->errorJson('没有权限,跳转供应商申请!', ['url'=> yzAppFullUrl('member/supplier')]);
        }
        if ($supplier_model->member_id != $member_id) {
            $memberSupplier = Supplier::uniacid()->where('member_id',$member_id)->first();
            if (!empty($memberSupplier)) {
                return $this->errorJson('没有权限,跳转会员中心!', ['url'=> yzAppFullUrl('member')]);
            } else {
                return $this->errorJson('没有权限,跳转供应商申请!', ['url'=> yzAppFullUrl('member/supplier')]);
            }
        }

        if (strexists($supplier_model->logo, 'image/')) {
            $supplier_model->logo = replace_yunshop(yz_tomedia($supplier_model->logo,'image'));
        } else {
            $supplier_model->logo = replace_yunshop(yz_tomedia($supplier_model->logo));
        }

        return $this->successJson('ok', $supplier_model);
    }

    public function edit()
    {
        $success_code = 1;
        $supplier_data = \YunShop::request()->get();
        $member_id = \YunShop::app()->getMemberId();
        $supplier_model = Supplier::getSupplierByMemberId($member_id);

        $supplier_model->uniacid = \YunShop::app()->uniacid;
        $supplier_model->member_id = $member_id;
        $supplier_model->username = $supplier_model['username'];
        $supplier_model->password = $supplier_model['password'];
        $supplier_model->realname = $supplier_data['realname'];
        $supplier_model->mobile = $supplier_data['mobile'];
        $supplier_model->status = $supplier_model['status'];
        $supplier_model->store_name = $supplier_data['store_name'];
        $supplier_model->apply_time = time();
        $supplier_model->salt = $supplier_model['salt'];
        $supplier_model->product = $supplier_model['product'];
        $supplier_model->remark = $supplier_model['remark'];
        $supplier_model->uid = $supplier_model['uid'];

        if (strexists($supplier_data['logo'], request()->getSchemeAndHttpHost()) && !strexists($supplier_data['logo'], '/addons/')) {
            $urls = parse_url($supplier_data['logo']);
            if (strexists($supplier_data['logo'],'image/')) {
                $supplier_data['logo'] = substr($urls['path'], strpos($urls['path'], 'image'));
            } else {
                $supplier_data['logo'] = substr($urls['path'], strpos($urls['path'], 'images'));
            }
        }
        $supplier_model->logo = $supplier_data['logo'];

        $supplier_model->company_bank = $supplier_data['company_bank'];
        $supplier_model->company_ali = $supplier_data['company_ali'];
        $supplier_model->ali = $supplier_data['ali'];
        $supplier_model->wechat = $supplier_data['wechat'];
        $supplier_model->diyform_data_id = $supplier_model['diyform_data_id'];
        $supplier_model->bank_username = $supplier_data['bank_username'];
        $supplier_model->bank_of_accounts = $supplier_data['bank_of_accounts'];
        $supplier_model->opening_branch = $supplier_data['opening_branch'];
        $supplier_model->company_ali_username = $supplier_data['company_ali_username'];
        $supplier_model->ali_username = $supplier_data['ali_username'];

        if ($supplier_model->save()) {
            return $this->successJson('修改成功！', $success_code);
        }
    }

    //上传图片
    public function upload()
    {
        $file = request()->file('file');
        if (!$file) {
            return $this->errorJson('请传入正确参数.');
        }
        if ($file->isValid()) {
            // 获取文件相关信息
            $originalName = $file->getClientOriginalName(); // 文件原名
            $realPath = $file->getRealPath();   //临时文件的绝对路径
            $ext = $file->getClientOriginalExtension();
            $newOriginalName = md5($originalName . str_random(6)) . '.' . $ext;

            \Storage::disk('image')->put($newOriginalName, file_get_contents($realPath));

            return $this->successJson('上传成功', [
                'img'    => \Storage::disk('image')->url($newOriginalName),
            ]);
        }

    }
}

