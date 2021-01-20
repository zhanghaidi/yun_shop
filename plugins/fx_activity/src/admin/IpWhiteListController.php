<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/4/27
 * Time: 16:44
 */

namespace Yunshop\FxActivity\admin;

use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use Yunshop\FxActivity\models\IpWhiteList;

class IpWhiteListController extends BaseController
{
    public function index() {
        $pageSize = 10;

        $ip_address = request()->ip_address;
        if ($ip_address) {
            //检测IP
            if (filter_var($ip_address, FILTER_VALIDATE_IP)) {
                $whitelist = new IpWhiteList();
                $whitelist->uniacid = \YunShop::app()->uniacid;
                $whitelist->ip_address = $ip_address;
                $whitelist->save();
                return $this->message('保存成功', Url::absoluteWeb('plugin.fx_activity.admin.IpWhiteList.index'));
            } else {
                return $this->message('请输入正确的IP地址', Url::absoluteWeb('plugin.fx_activity.admin.IpWhiteList.index'));
            }
        }

        $list = IpWhiteList::uniacid()->orderBy('id', 'desc')->orderBy('id', 'desc')->paginate($pageSize)->toArray();
        $pager = PaginationHelper::show($list['total'], $list['current_page'], $list['per_page']);

        return view('Yunshop\FxActivity::admin.whitelist',[
            'list' => $list,
            'pager' => $pager
        ])->render();
    }
}