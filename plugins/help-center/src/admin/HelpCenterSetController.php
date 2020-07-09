<?php
namespace Yunshop\HelpCenter\admin;
use app\common\components\BaseController;
use Yunshop\HelpCenter\services\HelpCenterService;
use app\common\facades\Setting;
use Yunshop\HelpCenter\models\HelpCenterSetModel;
use app\common\helpers\Url;
use Illuminate\Http\Request;
use Yunshop\HelpCenter\services\SetService;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/29 0029
 * Time: 下午 1:58
 */
class HelpCenterSetController extends BaseController
{
//    public function index1()
//    {
//
//        $helpCenterService = new HelpCenterService();
//
//        $set = Setting::get('plugin.help_center');
//
//        $helpCenterModel = new HelpCenterSetModel();
//
//        $requestData = \Yunshop::request()->setdata;
//
//        $url = Url::absoluteWeb('plugin.help-center.admin.help-center-set.index');
//
//        if ($requestData) {
//            //将数据赋值到model
//            $helpCenterModel->fill($requestData);
//            //字段检测
//            $validator = $helpCenterModel->validator();
//            if ($validator->fails()) {
//                //验证失败
//                $this->error($validator->messages());
//            } else {
//                //保存数据
//                if ($helpCenterModel->save()) {
//                    //显示信息并跳转
//                    return $this->message('保存成功',$url);
//                } else {
//                    $this->error('保存失败');
//                }
//            }
//        }
//
//        $pluginName = $helpCenterService->get('plugin_name');
//
//        return view('Yunshop\HelpCenter::admin.set', [
//            'pluginName' => $pluginName,
//            'set' => $set,
//        ])->render();
//    }

    public function index()
    {
        $helpCenterService = new HelpCenterService();
        $set = Setting::get('help-center.status');

        $pluginName = $helpCenterService->get('plugin_name');

        $data_title = Setting::get('help-center.title');
        $data_icon = Setting::get('help-center.icon');
        $data_description = Setting::get('help-center.description');

        $share_data['title'] = $data_title;
        $share_data['icon'] = yz_tomedia($data_icon);
        $share_data['description'] = $data_description;

        $requestData = \YunShop::request()->setdata;

        if ($requestData) {
            $result = SetService::storeSet($requestData);
            if ($result === true) {
                return $this->message('保存设置成功', Url::absoluteWeb('plugin.help-center.admin.help-center-set.index'));
            }
            $this->error($result);
        }
        return view('Yunshop\HelpCenter::admin.set', [
            'set' => $set,
            'pluginName' => $pluginName,
            'share_data' => $share_data,
        ]);
    }

}