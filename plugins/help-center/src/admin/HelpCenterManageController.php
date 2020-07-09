<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/29 0029
 * Time: 下午 2:20
 */

namespace Yunshop\HelpCenter\admin;

use app\common\components\BaseController;
use Yunshop\HelpCenter\models\HelpCenterAddModel;
use app\common\helpers\Url;
use app\common\helpers\PaginationHelper;

class HelpCenterManageController extends BaseController
{
    private $pageSize = 10;

    public function index()
    {
        $list = HelpCenterAddModel::getList()->orderBy('sort')->orderBy('id', 'desc')->paginate($this->pageSize)->toArray();

        $pager = PaginationHelper::show($list['total'], $list['current_page'], $this->pageSize);

        $addurl = Url::absoluteWeb('plugin.help-center.admin.help-center-add.index');

         return view('Yunshop\HelpCenter::admin.manage', [
             'data' => $list['data'],
             'pager' => $pager,
             'addurl' => $addurl,
         ]);
    }


}