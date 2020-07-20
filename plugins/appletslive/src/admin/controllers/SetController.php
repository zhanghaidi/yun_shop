<?php
/**
 * Created by PhpStorm.
 * User: weifeng
 * Date: 2019-12-18
 * Time: 16:00
 *
 *    .--,       .--,
 *   ( (  \.---./  ) )
 *    '.__/o   o\__.'
 *       {=  ^  =}
 *        >  -  <
 *       /       \
 *      //       \\
 *     //|   .   |\\
 *     "'\       /'"_.-~^`'-.
 *        \  _  /--'         `
 *      ___)( )(___
 *     (((__) (__)))     梦之所想,心之所向.
 */

namespace Yunshop\Appletslive\admin\controllers;

use app\common\components\BaseController;
use app\common\facades\Setting;

class SetController extends BaseController
{
    public function index()
    {
        $form_data = request()->form_data;
        if ($form_data) {
            foreach ($form_data as $key => $value) {
                Setting::set('plugin.appletslive'. '.' . $key, $value);
            }

            return $this->successJson('保存成功');
        }

        $set = Setting::get('plugin.appletslive');
        $copy_url = '/packageD/livePlayer/livePlayer';

        return view('Yunshop\Appletslive::admin.set', [
            'link' => json_encode($copy_url),
            'set' => json_encode($set),
        ])->render();
    }
}
