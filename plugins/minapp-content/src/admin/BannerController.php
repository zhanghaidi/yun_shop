<?php

namespace Yunshop\MinappContent\admin;

use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use Illuminate\Support\Facades\DB;
use Yunshop\MinappContent\services\MinappContentService;

class BannerController extends BaseController
{
    private $pageSize = 20;

    /**
     * 轮播图列表
     */
    public function index()
    {
        $uniacid = \YunShop::app()->uniacid;

        $where[] = ['diagnostic_service_banner.uniacid', '=', $uniacid];
        $banner = DB::table('diagnostic_service_banner')
            ->leftjoin('diagnostic_service_banner_position', 'diagnostic_service_banner.position_id', '=', 'diagnostic_service_banner_position.id')
            ->select('diagnostic_service_banner_position.name', 'diagnostic_service_banner.*')
            ->where($where)
            ->orderBy('diagnostic_service_banner.position_id', 'desc')
            ->orderBy('diagnostic_service_banner.list_order', 'desc')
            ->paginate($this->pageSize);

        $pager = PaginationHelper::show($banner->total(), $banner->currentPage(), $banner->perPage());

        return view('Yunshop\MinappContent::admin.banner.banner_list', [
            'pluginName' => MinappContentService::get('name'),
            'banner' => $banner,
            'pager' => $pager,
        ]);
    }

    /**
     * 编辑轮播图
     */
    public function edit()
    {
        $id = intval(request()->input('id', 0));
        $uniacid = \YunShop::app()->uniacid;
        $info = [];
        if ($id > 0) {
            $info = DB::table('diagnostic_service_banner')->where(['id' => $id])->first();
            if (empty($info)) {
                return $this->message('轮播图位置不存在或已被删除', '', 'danger');
            }
        }
        if (request()->isMethod('post')) {
            $param = request()->all();
            $data = array(
                'uniacid' => $uniacid,
                'title' => $param['title'],
                'list_order' => $param['list_order'],
                'position_id' => $param['position_id'],
                'image' => $param['image'],
                'is_href' => $param['is_href'],
                'jumpurl' => $param['jumpurl'],
                'appid' => trim($param['appid']),
                'jumptype' => $param['jumptype'],
                'type' => $param['type'],
                'status' => $param['status'],
                'add_time' => time()
            );
            if (empty($data['position_id'])) {
                return $this->message('请选择轮播图位置', '', 'danger');
            }
            if ($id > 0) {
                $res = DB::table('diagnostic_service_banner')->where('id', $id)->update($data);
            } else {
                $res = DB::table('diagnostic_service_banner')->insert($data);
            }
            if ($res) {
                return $this->message('成功', Url::absoluteWeb('plugin.minapp-content.admin.banner.index'));
            } else {
                return $this->message('失败，请重新操作', '', 'danger');
            }
        }

        $minapp_list = DB::table('account_wxapp')->select('uniacid', 'key', 'name')->orderBy('uniacid','DESC')->get();
        $bannerPosition = DB::table('diagnostic_service_banner_position')->where('uniacid',$uniacid)->get();

        return view('Yunshop\MinappContent::admin.banner.edit', [
            'pluginName' => MinappContentService::get('name'),
            'type' => 'banner',
            'info' => $info,
            'bannerPosition' => $bannerPosition,
            'minappList'=>$minapp_list
        ]);
    }

    /**
     * 删除轮播图
     * @return mixed
     */
    public function delete()
    {
        $id = intval(request()->input('id'));
        if ($id <= 0) {
            return $this->message('ID参数错误', '', 'danger');
        }

        DB::table('diagnostic_service_banner')->where([
            'id' => $id,
            'uniacid' => \YunShop::app()->uniacid,
        ])->delete();

        return $this->message('删除成功');
    }

    /**
     * 显示隐藏
     */
    public function display()
    {
        $param = request()->all();
        $id = intval($param['id']);
        $status = intval($param['status']);
        if ($id > 0) {
            if ($status == 1) {
                $res = pdo_update('diagnostic_service_banner', array('status' => 0), array('id' => $id));
                if ($res) {
                    $data = array(
                        'errno' => 0,
                        'msg' => '关闭成功',
                        'data' => '',
                    );
                    exit(json_encode($data));
                } else {
                    $data = array(
                        'errno' => 1,
                        'msg' => '关闭失败',
                        'data' => '',
                    );
                    exit(json_encode($data));
                }
            } else {
                $res = pdo_update('diagnostic_service_banner', array('status' => 1), array('id' => $id));
                if ($res) {
                    $data = array(
                        'errno' => 0,
                        'msg' => '开启成功',
                        'data' => '',
                    );
                    exit(json_encode($data));
                } else {
                    $data = array(
                        'errno' => 1,
                        'msg' => '开启失败',
                        'data' => '',
                    );
                    exit(json_encode($data));
                }
            }
        }
    }
}
