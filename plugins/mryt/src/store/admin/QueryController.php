<?php

/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/8/1
 * Time: 下午6:28
 */

namespace Yunshop\Mryt\store\admin;

use app\common\components\BaseController;
use app\backend\modules\member\models\Member;

class QueryController extends BaseController
{
    public function index()
    {
        $kwd = \YunShop::request()->keyword;
        if ($kwd) {
            $members = Member::getMemberByName($kwd)->toArray();
            return view('Yunshop\StoreCashier::admin.member.query', [
                'members' => $members
            ])->render();
        }
    }
}