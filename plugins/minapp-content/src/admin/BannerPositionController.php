<?php

namespace Yunshop\MinappContent\admin;

use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use Yunshop\MinappContent\services\MinappContentService;
use Illuminate\Support\Facades\DB;

class BannerPositionController extends BaseController
{
    private $pageSize = 20;

    /**
     * 轮播图位置列表
     */
    public function index()
    {
        $input = \YunShop::request();
        $uniacid = \YunShop::app()->uniacid;

        $bannerPosition = DB::table('diagnostic_service_banner_position')
            ->where('uniacid',$uniacid)
            ->paginate($this->pageSize);

        $pager = PaginationHelper::show($bannerPosition->total(), $bannerPosition->currentPage(), $bannerPosition->perPage());

        return view('Yunshop\MinappContent::admin.banner_position.banner_position_list', [
            'pluginName' => MinappContentService::get('name'),
            'type' => 'banner_position',
            'bannerPosition' => $bannerPosition,
            'pager' => $pager,
            'request' => $input,
        ]);
    }

    /**
     * 添加|轮播图
     */
    public function add()
    {
        return view('Yunshop\MinappContent::admin.banner_position.add', [
            'pluginName' => MinappContentService::get('name'),
            'type' => 'banner_position',
        ]);
    }

    /**
     * 编辑轮播图位置
     */
    public function edit()
    {

    }

    /**
     * 删除轮播图位置
     * @return mixed
     */
    public function delete()
    {

    }

    /**
     * 显示隐藏 位置
     */
    public function display()
    {

    }
}
