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
            ->where('uniacid', $uniacid)
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
     * 编辑轮播图位置
     */
    public function edit()
    {
        $id = intval(request()->input('id', 0));
        $info = [];
        if ($id > 0) {
            $info = DB::table('diagnostic_service_banner_position')->where(['id' => $id])->first();
            if (empty($info)) {
                return $this->message('轮播图位置不存在或已被删除', '', 'danger');
            }
        }
        if (request()->isMethod('post')) {
            $name = trim(request()->input('name'));
            $uniacid = \YunShop::app()->uniacid;
            $data = [
                'uniacid' => $uniacid,
                'name' => $name,
                'add_time' => time()
            ];
            if (empty($data['name'])) {
                return $this->message('位置名不能为空', '', 'danger');
            }
            if ($id > 0) {
                $res = DB::table('diagnostic_service_banner_position')->where('id', $id)->update($data);
            } else {
                $res = DB::table('diagnostic_service_banner_position')->insert($data);
            }
            if ($res) {
                return $this->message('成功', Url::absoluteWeb('plugin.minapp-content.admin.banner-position.index'));
            } else {
                return $this->message('失败，请重新操作', '', 'danger');
            }
        }
        return view('Yunshop\MinappContent::admin.banner_position.edit', [
            'pluginName' => MinappContentService::get('name'),
            'type' => 'banner_position',
            'info' => $info,
        ]);
    }

    /**
     * 删除轮播图位置
     * @return mixed
     */
    public function delete()
    {
        $id = intval(request()->input('id'));
        $uniacid = \YunShop::app()->uniacid;
        if ($id <= 0) {
            return $this->message('ID参数错误', '', 'danger');
        }
        $banners = DB::table('diagnostic_service_banner')->where(['position_id' => $id,'uniacid' => $uniacid])->first();
        if($banners){
            $this->message('此轮播位下面有轮播图，无法删除', '', 'danger');
        }
        DB::table('diagnostic_service_banner_position')->where([
            'id' => $id,
            'uniacid' => $uniacid,
        ])->delete();

        return $this->message('删除成功');
    }
}
