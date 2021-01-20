<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/9
 * Time: 下午4:47
 */

namespace Yunshop\Micro\backend\controllers\MicroShopLevel;

use app\common\components\BaseController;
use Yunshop\Micro\common\models\MicroShopLevel;
use app\common\helpers\PaginationHelper;

class ListController extends BaseController
{
    private $operation_url = [
        'add_level_url'     => 'plugin.micro.backend.controllers.MicroShopLevel.operation.add',
        'edit_level_url'    => 'plugin.micro.backend.controllers.MicroShopLevel.operation.edit',
        'delete_level_url'  => 'plugin.micro.backend.controllers.MicroShopLevel.operation.delete'
    ];

    public function index()
    {
        $level_list = MicroShopLevel::getLevelList()->paginate(10);
        $pager = PaginationHelper::show($level_list->total(), $level_list->currentPage(), $level_list->perPage());

        return view('Yunshop\Micro::backend.MicroShopLevel.list', [
            'list'          => $level_list,
            'pager'         => $pager,
            'operation_url' => $this->operation_url
        ])->render();
    }
}