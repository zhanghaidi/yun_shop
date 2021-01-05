<?php

namespace Yunshop\MinappContent\api;

use app\common\components\ApiController;
use Yunshop\MinappContent\models\CustomShareModel;

class CustomShareController extends ApiController
{
    protected $publicAction = ['index'];
    protected $ignoreAction = ['index'];

    public function index()
    {
        $key = \YunShop::request()->key;
        $key = trim($key);

        if (!isset($key[0])) {
            return $this->errorJson('参数错误');
        }

        $infoRs = CustomShareModel::select('id', 'title', 'image')->where([
            'key' => $key,
            'uniacid' => \YunShop::app()->uniacid,
            'status' => 1,
        ])->first();
        if (!isset($infoRs->id)) {
            return $this->errorJson('数据不存在');
        }
        return $this->successJson('成功', [
            'id' => $infoRs->id,
            'title' => $infoRs->title,
            'image' => $infoRs->image,
        ]);
    }
}
