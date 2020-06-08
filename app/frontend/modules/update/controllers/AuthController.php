<?php
/**
 * Created by PhpStorm.
 * User: 马赛克
 * Date: 2020/4/28
 * Time: 下午4:33
 */

namespace app\frontend\modules\update\controllers;


use app\common\components\BaseController;
use app\frontend\modules\update\models\authModel;

class AuthController extends BaseController
{
    public function index()
    {
        $code = request()->input('code');

        if (authModel::create(['code' => $code])) {
            return $this->successJson('ok');
        }

        return $this->errorJson('fail');
    }
}