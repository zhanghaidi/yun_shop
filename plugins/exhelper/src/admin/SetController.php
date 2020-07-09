<?php

/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/6/14
 * Time: 上午11:43
 */

namespace Yunshop\Exhelper\admin;

use app\common\components\BaseController;
use app\common\helpers\Url;
use Yunshop\Exhelper\common\models\ExhelperSys;

class SetController extends BaseController
{
    public function index()
    {
        $data = \YunShop::request()->data;
        $result = $this->query($data);
        if ($result === 1) {
            return $this->message('保存成功', Url::absoluteWeb('plugin.exhelper.admin.set.index'));
        }

        return view('Yunshop\Exhelper::admin.set', [
            'set'   => $result
        ]);
    }

    private function query($data)
    {
        $result = ExhelperSys::getOnlyOne()->first();
        if ($data) {
            $data['uniacid'] = \YunShop::app()->uniacid;
            if ($result) {
                $result->fill($data);
                if ($result->save()) {
                    return 1;
                }
            } else {
                $result = new ExhelperSys();
                $result->fill($data);
                if ($result->save()) {
                    return 1;
                }
            }
        }
        return $result;
    }
}