<?php

namespace Yunshop\MinappContent\admin;

use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use Yunshop\MinappContent\services\MinappContentService;
use Illuminate\Support\Facades\DB;

class QuickCommentController extends BaseController
{
    private $pageSize = 20;

    /**
     * 快捷评语列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|mixed|\think\response\View
     */
    public function index()
    {
        $input = \YunShop::request();
        $uniacid = \YunShop::app()->uniacid;
        $where[] = ['uniacid', '=', $uniacid];
        if (isset($input->search)) {
            $search = $input->search;
            if (intval($search['status']) > 0) {
                $where[] = ['status', '=', intval($search['status'])];
            }
            if (trim($search['content']) !== '') {
                $where[] = ['content', 'like', '%' . trim($search['content']) . '%'];
            }
        }

        $comments = DB::table('diagnostic_service_quick_comment')
            ->where($where)
            ->orderBy('create_time', 'desc')
            ->paginate($this->pageSize);

        $pager = PaginationHelper::show($comments->total(), $comments->currentPage(), $comments->perPage());

        return view('Yunshop\MinappContent::admin.quick_comment.quick_comment_list', [
            'pluginName' => MinappContentService::get('name'),
            'comments' => $comments,
            'pager' => $pager,
            'request' => $input,
        ]);
    }

    /**
     * 添加|编辑快捷评语
     */
    public function edit()
    {
        $uniacid = \YunShop::app()->uniacid;
        $id = intval(request()->input('id'));
        $info = [];
        if ($id > 0) {
            $info = DB::table('diagnostic_service_quick_comment')->where(['id' => $id])->first();
            if (empty($info)) {
                return $this->message('快评不存在或已被删除', '', 'danger');
            }
        }
        if (request()->isMethod('post')) {
            $param = request()->all();
            $content = trim($param['content']);
            if (empty($content)) {
                return $this->message('内容不能为空', '', 'danger');
            }
            $type = intval($param['type']);
            if (!$type) {
                return $this->message('内容不能为空', '', 'danger');
            }
            $data = array(
                'uniacid' => $uniacid,
                'content' => $content,
                'status' => intval($param['status']),
                'type' => $type,   //快捷语类型
                'create_time' => TIMESTAMP
            );
            if ($id > 0) {
                $res = DB::table('diagnostic_service_quick_comment')->where('id', $id)->update($data);;
            } else {
                $res = DB::table('diagnostic_service_quick_comment')->insert($data);
            }
            if ($res) {
                return $this->message('成功', Url::absoluteWeb('plugin.minapp-content.admin.quick-comment.index'));
            } else {
                return $this->message('失败', '', 'danger');
            }
        }

        return view('Yunshop\MinappContent::admin.quick_comment.edit', [
            'pluginName' => MinappContentService::get('name'),
            'info' => $info,
        ]);
    }

    /**
     * 删除快捷评语
     * @return mixed
     */
    public function delete()
    {
        $id = intval(request()->input('id'));
        if ($id <= 0) {
            return $this->message('ID参数错误', '', 'danger');
        }

        DB::table('diagnostic_service_quick_comment')->where([
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
                $res = pdo_update('diagnostic_service_quick_comment',array('status' => 0), array('id' => $id));
                if($res){
                    $data = array(
                        'errno' => 0,
                        'msg' => '关闭成功',
                        'data' => ''
                    );
                    exit(json_encode($data));
                }else{
                    $data = array(
                        'errno' => 1,
                        'msg' => '关闭失败',
                        'data' => ''
                    );
                    exit(json_encode($data));
                }
            }else{
                $res = pdo_update('diagnostic_service_quick_comment',array('status' => 1), array('id' => $id));
                if($res){
                    $data = array(
                        'errno' => 0,
                        'msg' => '开启成功',
                        'data' => ''
                    );
                    exit(json_encode($data));
                }else{
                    $data = array(
                        'errno' => 1,
                        'msg' => '开启失败',
                        'data' => ''
                    );
                    exit(json_encode($data));
                }
            }
        }
    }
}
