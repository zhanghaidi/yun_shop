<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2019/1/23
 * Time: 13:49
 */

namespace Yunshop\Designer\admin;

use app\common\components\BaseController;
use app\common\facades\Setting;
use Yunshop\Wechat\common\helper\Helper;


class FirstScreenController extends BaseController
{

    public function index()
    {
        if (config('app.framework') == 'platform') {
            $uploadurl = '/admin/system/upload/upload?upload_type=image';
        } else {
            $uploadurl = './index.php?c=site&a=entry&m=yun_shop&do=shop&route=upload.upload.upload&upload_type=image';
        }
        if(request()->form_data){
            \Setting::set('designer.first-screen',request()->form_data);
        }

        $advertisement_data = \Setting::get('designer.first-screen');
        return view('Yunshop\Designer::admin.first-screen',[
            'uploadurl'             => $uploadurl,
            'advertisement_data'   => json_encode($advertisement_data)
        ]);
    }

    /*
     * 全屏广告
     */
    public function fullScreen()
    {
        if (config('app.framework') == 'platform') {
            $uploadurl = '/admin/system/upload/upload?upload_type=image';
        } else {
            $uploadurl = './index.php?c=site&a=entry&m=yun_shop&do=shop&route=upload.upload.upload&upload_type=image';
        }
        if(request()->form_data){
//            \Setting::set('designer.first-screen',request()->form_data);
            \Setting::set('designer.full-screen',request()->form_data);
        }

        $advertisement_data = \Setting::get('designer.full-screen');//\Setting::get('designer.first-screen');
        return view('Yunshop\Designer::admin.first-screen',[
            'uploadurl'             => $uploadurl,
            'advertisement_data'   => json_encode($advertisement_data)
        ]);
    }

    /*
     * 弹窗广告
     */
    public function bulletFrameAdvertising()
    {
        if (config('app.framework') == 'platform') {
            $uploadurl = '/admin/system/upload/upload?upload_type=image';
        } else {
            $uploadurl = './index.php?c=site&a=entry&m=yun_shop&do=shop&route=upload.upload.upload&upload_type=image';
        }
        if(request()->form_data){
//            \Setting::set('designer.first-screen',request()->form_data);
            \Setting::set('designer.bullet-frame-advertising',request()->form_data);
        }

        $advertisement_data = \Setting::get('designer.bullet-frame-advertising');//\Setting::get('designer.first-screen');
        return view('Yunshop\Designer::admin.first-screen',[
            'uploadurl'             => $uploadurl,
            'advertisement_data'   => json_encode($advertisement_data)
        ]);
    }


}