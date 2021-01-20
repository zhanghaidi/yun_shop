<?php

/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/9/4
 * Time: 下午4:49
 */

namespace Yunshop\Mryt\store\common\controller;

use app\common\components\BaseController;
use app\common\services\Session;
use Yunshop\Mryt\admin\model\MemberShopInfo;
use Yunshop\Mryt\models\MemberChildrenModel;
use Yunshop\Mryt\models\MrytMemberModel;
use Yunshop\Mryt\store\models\Store;

class CommonController extends BaseController
{
    public $child_ids;

    public function __construct()
    {
        parent::__construct();
        $this->preAction();
    }

    public function preAction()
    {
        parent::preAction();

        session_start();
        $store = MrytMemberModel::verify(\YunShop::app()->uid);
        if ($store) {
            Session::set('mryt_store', $store->toArray(), '86400');
            $ids = MemberShopInfo::uniacid()
                ->where('parent_id', $store->uid)
                ->whereHas('hasOneStore',function ($query) {
                    $query->where('is_black', 0);
                })
                ->get(['member_id'])->toArray();
            $this->child_ids = array_column($ids, 'member_id');
        } else {
            exit($this->message('您不是合伙人！', '', 'error'));
        }
    }
}