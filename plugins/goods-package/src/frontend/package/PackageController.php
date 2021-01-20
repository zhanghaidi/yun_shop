<?php

/**
 * Author: 芸众商城 www.yunzshop.com
 */

namespace Yunshop\GoodsPackage\frontend\package;

use app\common\components\ApiController;
use app\common\exceptions\AppException;
use Yunshop\GoodsPackage\frontend\package\models\GoodsPackage;

class PackageController extends ApiController
{
    public function index(){
        $package_id = intval(request()->package_id);
        if (!$package_id) {
            throw new AppException('参数错误');
        }
        $result = GoodsPackage::search($package_id);
        if ($result['status']) {
            return $this->successJson($result['message'], $result['data']);
        }
        return $this->errorJson($result['message']);
    }
}