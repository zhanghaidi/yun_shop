<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/9
 * Time: 14:44
 */

namespace Yunshop\Supplier\supplier\controllers;


use app\common\components\BaseController;
use Yunshop\Supplier\supplier\models\Adv;
use app\common\helpers\Url;

class SupplierAdvsController extends BaseController
{
    public function index()
    {
        $supplier_uid = \YunShop::app()->uid;
        $adv = Adv::whereHas('hasOneSupplier', function ($query) use ($supplier_uid) {
            $query->where('uid', $supplier_uid);
        })->first();

        if (request()->isMethod('post')) {

            $adv =  $adv ? $adv : (new Adv());

            $data['advs'] = request()->adv;
            $data['uniacid'] = \YunShop::app()->uniacid;
            $data['supplier_uid'] = \YunShop::app()->uid; //供应商辅助id

            $adv->fill($data);
            $bool = $adv->save();

            if (!$bool) {
                $this->error('广告位保存失败');
            }

            return $this->message('广告位保存成功', Url::absoluteWeb('plugin.supplier.supplier.controllers.supplier-advs.index'));
        }

        return view('Yunshop\Supplier::supplier.adv.advertisement', [
            'adv' => $adv,
        ]);
    }
}