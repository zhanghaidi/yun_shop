<?php

namespace Yunshop\ActivityQrcode\admin;

use app\common\components\BaseController;
use app\common\facades\Setting;
use app\common\helpers\Url;
use app\common\helpers\PaginationHelper;
use Yunshop\ActivityQrcode\models\Activity;
use Yunshop\ActivityQrcode\services\ActivityQrcodeService;

class ActivityController extends BaseController
{

    //首页
    public function index()
    {
        $records = Activity::records();
        $search = \YunShop::request()->search;
        if ($search) {

            $records = $records->search($search);

        }

        $recordList = $records->orderBy('id', 'desc')->paginate();

        $pager = PaginationHelper::show($recordList->total(), $recordList->currentPage(), $recordList->perPage());


        return view('Yunshop\ActivityQrcode::admin.list',
            [
                'pageList'    => $recordList,
                'page'          => $pager,
                'search'        => $search
            ]
        )->render();

    }

    //新增
    public function add()
    {
        $activityModel = new Activity();
        $requestActivity = \YunShop::request()->info;
        $activitySetting = Setting::get('plugin.activity-qrcode');

        if ($requestActivity) {
            $activityModel->fill($requestActivity);
            $activityModel->uniacid = \YunShop::app()->uniacid;

            $validator = $activityModel->validator();
            if($validator->fails()){
                $this->error($validator->messages());
            }else{
                if($activityModel->save()){
                    $url = $activitySetting['host'].$activitySetting['domain'].$activityModel->id;
                    $activityModel->qrcode = ActivityQrcodeService::getQrCode($url);
                    $activityModel->link_path = $url;
                    $activityModel->save();
                    return $this->message('添加成功', Url::absoluteWeb('plugin.activity-qrcode.admin.activity.qrcode.index', array('id' => $activityModel->id)));
                }else{
                    $this->message('活码创建失败','','error');
                }
            }
        }
        return view('Yunshop\ActivityQrcode::admin.info',
            [
                'info' => $activityModel,
            ]
        )->render();

    }


    //编辑
    public function edit()
    {
        $activityId =  \YunShop::request()->id;
        $activitySetting = Setting::get('plugin.activity-qrcode');
        $activityModel = Activity::getActivity($activityId);
        if(!$activityModel){
            return $this->message('无此记录或已被删除','','error');
        }

        $requestActivity = \YunShop::request()->info;
        if ($requestActivity) {
            $activityModel->fill($requestActivity);
            $url = $activitySetting['host'].$activitySetting['domain'].$activityId;

            $activityModel->qrcode = ActivityQrcodeService::getQrCode($url);
            $activityModel->link_path = $url;

            $validator = $activityModel->validator();
            if ($validator->fails()) {
                $this->error($validator->messages());
            } else {
                if ($activityModel->save()) {
                    return $this->message('修改成功', Url::absoluteWeb('plugin.activity-qrcode.admin.activity.qrcode.index', array('id' => $activityModel->id)));
                } else {
                    $this->message('活码修改失败','','error');
                }
            }
        }
        return view('Yunshop\ActivityQrcode::admin.info',
            [
                'info' => $activityModel,
            ]
        )->render();

    }


    //删除活码
    public function deleted()
    {
        $id = \YunShop::request()->id;

        if (!Activity::getActivity($id)) {
            return $this->message('没有此活码或已被删除','','error');
        }

        if(Activity::deletedActivity($id)){
            return $this->message('删除成功', Url::absoluteWeb('plugin.activity-qrcode.admin.activity.index'));
        }

    }


}