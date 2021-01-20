<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2019/6/4
 * Time: 15:00
 */

namespace Yunshop\MaterialCenter\admin;

use app\common\components\BaseController;
use app\common\helpers\Url;

class SetController extends BaseController
{
    public function index()
    {
        $share = \Setting::get('plugins.material-center');

        if (\YunShop::request()->share) {

            if (\Setting::set('plugins.material-center', \YunShop::request()->share)) {
                return $this->message('分享设置成功', Url::absoluteWeb('plugin.material-center.admin.set.index'));

            } else {
                return $this->error('分享设置失败');
            }
        }
        return view('Yunshop\MaterialCenter::set.index', [
            'set' => $share
        ])->render();
    }
}