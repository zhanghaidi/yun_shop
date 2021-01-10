<?php

namespace Yunshop\MinappContent\admin;

use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use Yunshop\MinappContent\services\MinappContentService;
use Illuminate\Support\Facades\DB;

class BannerController extends BaseController
{
    private $pageSize = 20;

    /**
     * 轮播图列表
     */
    public function index()
    {
        $input = \YunShop::request();
        $uniacid = \YunShop::app()->uniacid;

        $where[] = ['diagnostic_service_banner.uniacid', '=', $uniacid];
        $banner = DB::table('diagnostic_service_banner')
            ->leftjoin('diagnostic_service_banner_position', 'diagnostic_service_banner.position_id', '=', 'diagnostic_service_banner_position.id')
            ->where($where)
            ->orderBy('diagnostic_service_banner.position_id', 'desc')
            ->orderBy('diagnostic_service_banner.list_order', 'desc')
            ->paginate($this->pageSize);

        $pager = PaginationHelper::show($banner->total(), $banner->currentPage(), $banner->perPage());

        return view('Yunshop\MinappContent::admin.banner.banner_list', [
            'pluginName' => MinappContentService::get('name'),
            'type' => 'banner',
            'exchanges' => $banner,
            'pager' => $pager,
            'request' => $input,
        ]);
    }
    /**
     * 添加|轮播图
     */
    public function add()
    {

    }
    /**
     * 编辑轮播图
     */
    public function edit()
    {

    }

    /**
     * 删除轮播图
     * @return mixed
     */
    public function delete()
    {

    }

    /**
     * 显示隐藏
     */
    public function display()
    {

    }
}
