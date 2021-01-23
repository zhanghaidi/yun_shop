<?php

namespace Yunshop\MinappContent\admin;

use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use Yunshop\MinappContent\models\ComplainModel;
use Yunshop\MinappContent\models\ComplainTypeModel;
use Yunshop\MinappContent\models\FeedbackModel;
use Yunshop\MinappContent\services\MinappContentService;

class FeedbackController extends BaseController
{
    private $pageSize = 30;

    public function index()
    {
        $list = FeedbackModel::selectRaw('*,count(1) as counts')
            ->where('uniacid', \YunShop::app()->uniacid)
            ->groupBy('user_id')
            ->orderBy('add_time', 'asc')
            ->paginate($this->pageSize)->toArray();
        foreach ($list['data'] as &$v) {
            $v['images'] = json_decode($v['images'], true);
        }
        unset($v);

        $pager = PaginationHelper::show($list['total'], $list['current_page'], $this->pageSize);

        return view('Yunshop\MinappContent::admin.feedback.list', [
            'pluginName' => MinappContentService::get('name'),
            'data' => $list['data'],
            'pager' => $pager,
        ]);
    }

    public function msg()
    {
        $id = (int) \YunShop::request()->id;
        if ($id <= 0) {
            return $this->message('ID参数错误', '', 'danger');
        }

        $list = FeedbackModel::where('uniacid', \YunShop::app()->uniacid)
            ->where('user_id', $id)
            ->orderBy('add_time', 'asc')
            ->get()->toArray();
        foreach ($list as &$v) {
            $v['images'] = json_decode($v['images'], true);
        }
        unset($v);

        return view('Yunshop\MinappContent::admin.feedback.msg', [
            'pluginName' => MinappContentService::get('name'),
            'data' => $list,
        ]);
    }

    public function delete()
    {
        $id = (int) \YunShop::request()->id;
        if ($id <= 0) {
            return $this->message('ID参数错误', '', 'danger');
        }

        FeedbackModel::where([
            'id' => $id,
            'uniacid' => \YunShop::app()->uniacid,
        ])->delete();

        return $this->message('删除成功');
    }

    //用户投诉类型
    public function complainType()
    {
        $recordList = ComplainTypeModel::uniacid()->orderBy('list_order','desc')->paginate();
        $pager = PaginationHelper::show($recordList->total(), $recordList->currentPage(), $recordList->perPage());

        return view('Yunshop\MinappContent::admin.feedback.complain-type',
            [
                'pageList'    => $recordList,
                'page'          => $pager,

            ])->render();

    }

    //添加投诉类型
    public function complainTypeAdd()
    {
        $complainTypeModel = new ComplainTypeModel();
        $requestComplainType = \YunShop::request()->info;
        if ($requestComplainType) {

            $complainTypeModel->fill($requestComplainType);
            $complainTypeModel->uniacid = \YunShop::app()->uniacid;

            $validator = $complainTypeModel->validator();
            if($validator->fails()){
                $this->error($validator->messages());
            }else{
                if($complainTypeModel->save()){
                    return $this->message('添加成功', Url::absoluteWeb('plugin.minapp-content.admin.feedback.complain-type'));
                }else{
                    return $this->message('添加失败','','error');
                }
            }
        }
        return view('Yunshop\MinappContent::admin.feedback.complain-type-info',
            [
                'info' => $complainTypeModel,
            ]
        )->render();

    }

    //编辑投诉类型

    public function complainTypeEdit()
    {
        $id = (int) \YunShop::request()->id;
        $complainTypeModel = ComplainTypeModel::uniacid()->where('id',$id)->first();
        $requestComplainType = \YunShop::request()->info;
        if ($requestComplainType) {

            $complainTypeModel->fill($requestComplainType);
            //$complainTypeModel->uniacid = \YunShop::app()->uniacid;

            $validator = $complainTypeModel->validator();
            if($validator->fails()){
                $this->error($validator->messages());
            }else{
                if($complainTypeModel->save()){
                    return $this->message('修改成功', Url::absoluteWeb('plugin.minapp-content.admin.feedback.complain-type'));
                }else{
                    return $this->message('修改失败','','error');
                }
            }
        }
        return view('Yunshop\MinappContent::admin.feedback.complain-type-info',
            [
                'info' => $complainTypeModel,
            ]
        )->render();

    }

    //删除投诉类型
    public function complainTypeDelete()
    {
        $id = (int) \YunShop::request()->id;

        if(!$id){
            return $this->message('id不能为空','','error');

        }
        $res = ComplainTypeModel::where('id', $id)->forceDelete();
        if(!$res){
            return $this->message('删除失败','','error');
        }

        return $this->message('删除成功', Url::absoluteWeb('plugin.minapp-content.admin.feedback.complain-type'));

    }

    //用户投诉
    public function complain()
    {
        $recordList = ComplainModel::uniacid()->orderBy('id','desc')
            ->with([
                'type',
                'user' => function($user){
                    return $user->select('ajy_uid','nickname','avatarurl');
                }
            ])->paginate();
        $pager = PaginationHelper::show($recordList->total(), $recordList->currentPage(), $recordList->perPage());

        return view('Yunshop\MinappContent::admin.feedback.complain',
            [
               'pageList' => $recordList,
               'page' => $pager
            ])->render();

    }

    //用户投诉删除
    public function complainDelete()
    {
        $id = (int) \YunShop::request()->id;

        if(!$id){
            return $this->message('id不能为空','','error');

        }
        $res = ComplainModel::where('id', $id)->forceDelete();
        if(!$res){
            return $this->message('删除失败','','error');
        }

        return $this->message('删除成功', Url::absoluteWeb('plugin.minapp-content.admin.feedback.complain'));

    }
}
