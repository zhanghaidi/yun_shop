<?php

namespace Yunshop\ActivityQrcode\admin;

use app\common\components\BaseController;
use app\common\facades\Setting;
use app\common\helpers\Url;
use app\common\helpers\PaginationHelper;
use Yunshop\ActivityQrcode\models\Activity;
use Yunshop\ActivityQrcode\models\Qrcode;
use Yunshop\ActivityQrcode\services\ActivityQrcodeService;

class QrcodeController extends BaseController
{

    protected $activityId;

    public function __construct()
    {
        $this->activityId =  \YunShop::request()->id;
        $activityModel = Activity::getActivity($this->activityId);
        if(!$activityModel){

            return $this->message('无此活码记录或已被删除','','error');
        }

    }

    //首页
    public function index()
    {

        $records = Qrcode::records();
        $search = \YunShop::request()->search;
        if ($search) {

            $records = $records->search($search);

        }

        $recordList = $records->where('code_id', $this->activityId)->orderBy('sort', 'asc')->paginate();

        $pager = PaginationHelper::show($recordList->total(), $recordList->currentPage(), $recordList->perPage());


        return view('Yunshop\ActivityQrcode::admin.qrcodeList',
            [
                'activityId' => $this->activityId,
                'pageList'    => $recordList,
                'page'          => $pager,
                'search'        => $search
            ]
        )->render();

    }

    //新增
    public function add()
    {
        $qrList = Qrcode::where('code_id', $this->activityId)->get()->toArray();
        $sortArr = array_column($qrList, 'sort');

        $qrcodeModel = new Qrcode();
        $requestQrcode = \YunShop::request()->info;
        if ($requestQrcode) {
            $requestQrcode['end_time'] = strtotime($requestQrcode['end_time']);

            if(in_array($requestQrcode['sort'], $sortArr)){
                return $this->message('排序已存在','','error');
            }

            $qrcodeModel->fill($requestQrcode);
            $qrcodeModel->uniacid = \YunShop::app()->uniacid;
            $qrcodeModel->code_id = $this->activityId;
            //解析二维码内容
            $qrcodeModel->qr_path = ActivityQrcodeService::parseQrCode($qrcodeModel->qr_img);
            $qrcodeModel->qr_code = ActivityQrcodeService::getQrCode($qrcodeModel->qr_path);
            $validator = $qrcodeModel->validator();
            if($validator->fails()){
                $this->error($validator->messages());
            }else{
                if($qrcodeModel->save()){
                    return $this->message('添加成功', Url::absoluteWeb('plugin.activity-qrcode.admin.qrcode.index', array('id' => $this->activityId)));
                }else{
                    return $this->message('添加失败','','error');
                }
            }
        }
        return view('Yunshop\ActivityQrcode::admin.qrcodeInfo',
            [
                'info' => $qrcodeModel,
            ]
        )->render();

    }


    //编辑
    public function edit()
    {

        $qrcodeId = \YunShop::request()->qrcode_id;
        $qrList = Qrcode::where('code_id', $this->activityId)->where('id', '<>', $qrcodeId)->get()->toArray();
        $sortArr = array_column($qrList, 'sort');

        $qrcodeModel = Qrcode::getInfo($qrcodeId);
        if(!$qrcodeModel){
            return $this->message('二维码不存在或已被删除','','error');
        }
        $requestQrcode = \YunShop::request()->info;
        if ($requestQrcode) {
            $requestQrcode['end_time'] = strtotime($requestQrcode['end_time']);

            if(in_array($requestQrcode['sort'], $sortArr)){
                return $this->message('排序已存在','','error');
            }

            $qrcodeModel->fill($requestQrcode);
            //解析二维码内容
            $qrcodeModel->qr_path = ActivityQrcodeService::parseQrCode($qrcodeModel->qr_img);
            $qrcodeModel->qr_code = ActivityQrcodeService::getQrCode($qrcodeModel->qr_path);
            $validator = $qrcodeModel->validator();
            if ($validator->fails()) {
                $this->error($validator->messages());
            } else {
                if ($qrcodeModel->save()) {
                    return $this->message('修改成功', Url::absoluteWeb('plugin.activity-qrcode.admin.qrcode.index', array('id' => $this->activityId)));
                } else {
                    return $this->message('修改失败','','error');
                }
            }
        }
        return view('Yunshop\ActivityQrcode::admin.qrcodeInfo',
            [
                'info' => $qrcodeModel,
            ]
        )->render();

    }


    //删除活码
    public function deleted()
    {
        $qrcodeId = \YunShop::request()->qrcode_id;

        if (!Qrcode::getInfo($qrcodeId)) {
            return $this->message('没有此二维码或已被删除','','error');
        }

        if(Qrcode::deletedQrcode($qrcodeId)){
            return $this->message('删除成功', Url::absoluteWeb('plugin.activity-qrcode.admin.qrcode.index',  array('id' => $this->activityId)));
        }

    }


}