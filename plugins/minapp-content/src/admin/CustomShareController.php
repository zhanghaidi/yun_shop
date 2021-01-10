<?php

namespace Yunshop\MinappContent\admin;

use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use Yunshop\MinappContent\services\MinappContentService;
use Illuminate\Support\Facades\DB;

class CustomShareController extends BaseController
{
    private $pageSize = 20;

    /**
     * 自定义列表
     */
    public function index()
    {
        $input = \YunShop::request();
        $uniacid = \YunShop::app()->uniacid;
        $where[] = ['uniacid', '=', $uniacid];
        if (isset($input->search)) {
            $search = $input->search;
            if (trim($search['name']) !== '') {
                $where[] = ['name', 'like', '%' . trim($search['name']) . '%'];
            }
        }

        $shares = DB::table('diagnostic_service_custom_share')
            ->where($where)
            ->orderBy('id', 'desc')
            ->paginate($this->pageSize);

        $pager = PaginationHelper::show($shares->total(), $shares->currentPage(), $shares->perPage());

        return view('Yunshop\MinappContent::admin.custom_share.custom_share_list', [
            'pluginName' => MinappContentService::get('name'),
            'shares' => $shares,
            'pager' => $pager,
            'request' => $input,
        ]);
    }

    /**
     * 添加|编辑自定义
     */
    public function edit()
    {
        $uniacid = \YunShop::app()->uniacid;
        $id = intval(request()->input('id', 0));
        $info = [];
        if ($id > 0) {
            $info = DB::table('diagnostic_service_custom_share')->where(['id' => $id])->first();
            if (empty($info)) {
                return $this->message('分享页面不存在或已被删除', '', 'danger');
            }
        }
        if (request()->isMethod('post')) {
            $param = request()->all();
            $key = trim($param['key']);
            if (empty($key)) {
                return $this->message('页面标识不能为空', '', 'danger');
            }
            $title = trim($param['title']);
            if (empty($title)) {
                return $this->message('分享标题不能为空', '', 'danger');
            }
            $image = trim($param['image']);
            if (empty($image)) {
                return $this->message('分享图片不能为空', '', 'danger');
            }
            $repeat_info = DB::table('diagnostic_service_custom_share')->where(['uniacid' => $uniacid, 'key' => $key])->first();

            if (isset($repeat_info['id'])) {
                if ($id > 0 && $repeat_info['id'] == $id) {
                } else {
                    return $this->message('页面标识重复，请检查', '', 'danger');
                }
            }

            $data = [
                'uniacid' => $uniacid,
                'name' => isset($param['name']) ? trim($param['name']) : '',
                'key' => $key,
                'title' => $title,
                'image' => tomedia($image),
                'status' => isset($param['status']) ? intval($param['status']) : 1,
            ];
            if ($id > 0) {
                $res = DB::table('diagnostic_service_custom_share')->where('id', $id)->update($data);;
            } else {
                $res = DB::table('diagnostic_service_custom_share')->insert($data);
            }
            if ($res) {
                return $this->message('成功', Url::absoluteWeb('plugin.minapp-content.admin.custom-share.index'));
            } else {
                return $this->message('失败', '', 'danger');
            }
        }

        return view('Yunshop\MinappContent::admin.custom_share.edit', [
            'pluginName' => MinappContentService::get('name'),
            'info' => $info,
        ]);
    }
}
