<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/3/6 下午5:40
 * Email: livsyitian@163.com
 */

namespace Yunshop\Sign\Backend\Modules\Sign\Controllers;


use app\common\components\BaseController;
use app\common\exceptions\AppException;
use app\common\helpers\PaginationHelper;
use Yunshop\Sign\Backend\Models\Sign;
use Yunshop\Sign\Backend\Models\SignLog;

class SignLogController extends BaseController
{
    public function index()
    {
        $member_id = $this->getPostMemberId();

        $page_list = SignLog::records()->ofUid($member_id)->orderBy('created_at','desc')->paginate();
        $page = PaginationHelper::show($page_list->total(), $page_list->currentPage(), $page_list->perPage());

        return view('Yunshop\Sign::Backend.Sign.detail', [
            'member_sign'   => $this->getMemberSign(),
            'page_list'     => $page_list,
            'page'          => $page,
        ])->render();
    }


    private function getMemberSign()
    {
        $member_id = $this->getPostMemberId();

        $_model = Sign::ofUid($member_id)->withMember()->first();

        if (!$_model) {
            throw new AppException('信息错误，请重试！');
        }
        return $_model;
    }


    private function getPostMemberId()
    {
        $member_id = \YunShop::request()->member_id;

        if (!$member_id) {
            throw new AppException('信息错误，请重试！');
        }
        return $member_id;
    }

}
