<?php

/**
 * Created by PhpStorm.
 * User: zlt
 * Date: 2020/10/19
 * Time: 17:45
 */

namespace app\backend\modules\live\controllers;

use app\common\components\BaseController;
use app\common\helpers\Url;
use app\common\helpers\PaginationHelper;
use app\common\models\live\ImCallbackLog;


class IMLogController extends BaseController
{
    public function index()
    {

        $requestSearch = \YunShop::request()->search;
        if ($requestSearch) {
            $requestSearch = array_filter($requestSearch, function ($item) {
                return $item !== '';
            });

        }

        $list = ImCallbackLog::Search($requestSearch)->orderBy('id', 'decs')->paginate(20);

        $pager = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());

        return view('live.im-log', [
            'list' => $list,
            'pager' => $pager,
            'search' => $requestSearch,
        ])->render();

    }

    public function del()
    {
        $start = \YunShop::request()->start;
        $end = \YunShop::request()->end;
        if (empty($start) || empty($end)) {
            return json_encode(['result' => 0, 'msg'=>'时间不能为空']);
        }

        $del = ImCallbackLog::del($start, $end)->delete();

        if ($del) {
            return json_encode(['result' => 1, 'msg'=>'删除成功']);
        }
        return json_encode(['result' => 0, 'msg'=>'删除失败']);

    }

    //删除一条发言日志
    public function deleted()
    {
        $id = \YunShop::request()->id;
        if(empty($id)){
            return $this->message('Id不能为空', '', 'error');
        }
        $res = ImCallbackLog::destroy($id);

        if(!$res){
            return $this->message('删除失败', '', 'error');
        }

        return $this->message('删除成功', Url::absoluteWeb('live.IM-log.index'));
    }



}