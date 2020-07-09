<?php
/**
 * Created by PhpStorm.
 * User: weifeng
 * Date: 2020-02-29
 * Time: 10:32
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

namespace Yunshop\Appletslive\frontend\controllers;


use app\common\components\ApiController;
use app\common\exceptions\AppException;

class BaseController extends \app\common\components\BaseController
{
    public function __construct()
    {
        parent::__construct();

        $this->validatePlugin();
    }

    private function validatePlugin()
    {
        $is_open = \Setting::get('plugin.appletslive.is_open');
        if($is_open != 1)
        {
            throw new AppException('小程序直播功能已关闭!');
        }
    }
}