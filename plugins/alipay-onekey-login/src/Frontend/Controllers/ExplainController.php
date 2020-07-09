<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/6/30 上午10:30
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace Yunshop\Love\Frontend\Controllers;


use app\common\components\ApiController;
use Yunshop\Love\Common\Models\Member;
use Yunshop\Love\Common\Services\SetService;

class ExplainController extends ApiController
{
    /**
     * 爱心值说明接口
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        if ($this->getMemberModel()) {
            return $this->successJson('ok',$this->getExplain());
        }
        return $this->errorJson('未获取到会员信息，请重试');
    }

    /**
     * 获取说明标题、内容
     * @return array
     */
    private function getExplain()
    {
        return [
            'title'     => $this->getExplainTitle(),
            'content'   => $this->getExplainContent()
        ];
    }

    /**
     * 获取说明标题
     * @return string
     */
    private function getExplainTitle()
    {
        return SetService::getExplainTitle();
    }

    /**
     * 获取说明内容
     * @return string
     */
    private function getExplainContent()
    {
        return SetService::getExplainContent();
    }

    /**
     * 获取会员主信息
     * @return mixed
     */
    private function getMemberModel()
    {
        return Member::ofUid($this->getMemberId())->first();
    }

    /**
     * 获取登陆会员ID值
     * @return int
     */
    private function getMemberId()
    {
        return \YunShop::app()->getMemberId();
    }

}
