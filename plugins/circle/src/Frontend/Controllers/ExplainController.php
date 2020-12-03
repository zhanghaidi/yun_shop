<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/3/8 上午11:36
 * Email: livsyitian@163.com
 */

namespace Yunshop\Sign\Frontend\Controllers;


use app\common\components\ApiController;
use Yunshop\Sign\Common\Services\SetService;

class ExplainController extends ApiController
{
    public function index()
    {
        $content = SetService::getSignSet('explain_content');


        return $this->successJson('ok', ['explain_content' => html_entity_decode($content)]);
    }

}
