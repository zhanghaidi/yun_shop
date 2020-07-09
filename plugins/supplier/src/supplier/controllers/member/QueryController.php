<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/30
 * Time: 下午2:03
 */

namespace Yunshop\Supplier\supplier\controllers\member;


use app\backend\modules\member\models\Member;
use app\common\components\BaseController;


class QueryController extends BaseController
{
    public function index()
    {
        $kwd = \YunShop::request()->keyword;
        if ($kwd) {
            $members = Member::getMemberByName($kwd);
            if ($members) {
                $members = $members->toArray();
            } else {
                $members = [];
            }
            return view('Yunshop\Supplier::supplier.member.query', [
                'members' => $members
            ])->render();
        }
    }
}