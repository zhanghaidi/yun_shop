<?php
/**
 * WeChat - Applet by BaoJia Li
 *
 * @author      BaoJia Li
 * @User        king/QQ:995265288
 * @Tool        PhpStorm
 * @Date        2019/12/18  9:25 AM
 * @link        https://gitee.com/li-bao-jia
 */

namespace Yunshop\MinApp\Backend\Modules\Manual\Controllers;


use app\common\components\BaseController;
use Ixudra\Curl\Facades\Curl;

class OutputController extends BaseController
{
    /**
     * @var string
     */
    protected $url = 'http://134.175.117.38/api/devtools/login/output';


    public function index()
    {
        $result = Curl::to($this->url)->withData($this->params())->post();

        $result && $result = json_decode($result, true);

        if (isset($result['status']) && $result['status'] == 'SUCCESS') {
            return $this->successJson('SUCCESS', ['status' => 'SUCCESS']);
        }
        if (isset($result['status']) && $result['status'] == 'WAIT') {
            return $this->successJson('WAIT', ['status' => 'WAIT']);
        }
        return $this->errorJson(isset($result['message']) ? $result['message'] : 'FAIL');
    }

    private function params()
    {
        return ['identifier' => request()->identifier];
    }

}
