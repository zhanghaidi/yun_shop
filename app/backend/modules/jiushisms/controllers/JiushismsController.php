<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/10/23 下午2:26
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\backend\modules\jiushisms\controllers;


use app\common\components\BaseController;

class JiushismsController extends BaseController
{

    public function sendsms()
    {
        $post = request()->input();
        if ($post) {

        }

        return view('jiushisms.sendsms')->render();
    }


}
