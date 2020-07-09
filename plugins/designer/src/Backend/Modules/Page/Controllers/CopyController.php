<?php
/****************************************************************
 * Author:  king -- LiBaoJia
 * Date:    2020/6/3 9:43 AM
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * IDE:     PhpStorm
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/


namespace Yunshop\Designer\Backend\Modules\Page\Controllers;


use app\common\components\BaseController;
use app\common\helpers\Url;
use Yunshop\Designer\models\Designer;

class CopyController extends BaseController
{
    public function index()
    {
        if ($finder = $this->designerModel()) {

            $replica = $finder->replicate(['page_type']);
            if (in_array($finder->page_type, [9, 10])) {
                $replica->page_type = 10;
            }
            if ($replica->save()) {
                return $this->message('复制成功', Url::absoluteWeb('plugin.designer.Backend.Modules.Page.Controllers.records'));
            }
        }
        return $this->message('复制失败', '', 'error');
    }

    private function designerModel()
    {
        return Designer::find($this->postId());
    }

    private function postId()
    {
        return request()->id;
    }
}
