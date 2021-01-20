<?php

namespace Yunshop\GoodsPackage\admin;

use app\common\components\BaseController;
use Yunshop\GoodsPackage\common\models\Goods;
use Yunshop\GoodsPackage\admin\models\GoodsPackage;
use app\common\helpers\Url;

class PackageController extends BaseController
{

    /**
     * 获取搜索商品
     * @return html
     */
    public function getSearchGoods(){
        $keyword = \YunShop::request()->keyword;
        $goods = Goods::getGoodsByName($keyword);
        if (!$goods->isEmpty()) {
            $goods = set_medias($goods->toArray(), array('thumb', 'share_icon'));
        }
        return view('Yunshop\GoodsPackage::admin.detail.goods_query', [
            'goods' => $goods
        ])->render();
    }

    public function getSearchPackage(){
        $keyword = \YunShop::request()->keyword;
        $packages = GoodsPackage::getGoodsPackagesByName($keyword);
        return view('Yunshop\GoodsPackage::admin.detail.package_query', [
            'packages' => $packages
        ])->render();
    }

    //用于"适用范围"添加商品或者分类
    public function addParam(){
        $type = \YunShop::request()->type;
        switch($type){
            case 'goods':
                return view('Yunshop\GoodsPackage::admin.detail.goods')->render();
                break;
            case 'package':
                return view('Yunshop\GoodsPackage::admin.detail.package')->render();
                break;
            case 'carousel':
                return view('Yunshop\GoodsPackage::admin.detail.carousel')->render();
                break;
        }
    }

    public function index(){
        $params = \YunShop::request()->search;
        $result = GoodsPackage::search($params);
        return view('Yunshop\GoodsPackage::admin.index',['packages'=>$result['packages'],'pager' =>$result['pager'],'search'=>$params])->render();
    }

    public function create(){
        //獲取頁面提交的參數
        $package = (Array)\YunShop::request()->package;
        if (!empty($package)) {
            //將參數提交model处理
            $goodsPackageModel = new GoodsPackage();
            $result = $goodsPackageModel->createGoodsPackage($package);
            if ($result['status']) {
                return $this->message($result['message'], Url::absoluteWeb('plugin.goods-package.admin.package.index'));
            } else {
                $this->error($result['message']);
            }
        }
        return view('Yunshop\GoodsPackage::admin.edit',['package' => $package])->render();
    }

    public function edit(){
        $id = \YunShop::request()->id;
        if (empty($id)) {
            return $this->message('参数错误!',Url::absoluteWeb('plugin.goods-package.admin.package.index'),'danger');
        }
        $package = \YunShop::request()->package;
        if (!empty($package)) { // 是修改后的提交
            if ($id == $package['id']) {
                $result = GoodsPackage::saveGoodsPackage($package);
                if ($result['status']) {
                    return $this->message($result['message'],Url::absoluteWeb('plugin.goods-package.admin.package.index'));
                } else {
                    $this->error($result['message']);
                }
            } else {
                $this->error('无效请求!');
            }
        }
        $result = GoodsPackage::editGoodsPackage($id);
        if (!$result['status']) {
            $this->error($result['message']);
        }
        
        return view('Yunshop\GoodsPackage::admin.edit',['package'=>$result['data']])->render();
    }

    public function delete(){
        $id = \YunShop::request()->id;
        if($id) {
            $result = GoodsPackage::deleteGoodsPackage($id);
            if ($result['status']) {
                return $this->message($result['message'],Url::absoluteWeb('plugin.goods-package.admin.package.index'));
            } else {
                return $this->message($result['message'],Url::absoluteWeb('plugin.goods-package.admin.package.index'),'danger');
            }
        } else {
            return $this->message('参数错误!',Url::absoluteWeb('plugin.goods-package.admin.package.index'),'danger');
        }
    }
}