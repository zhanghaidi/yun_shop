<?php

namespace Yunshop\Wechat\admin\reply\controller;

use app\common\components\BaseController;
use app\common\facades\Setting;
use app\common\helpers\Url;
use Yunshop\Wechat\common\model\RuleKeyword;
/**
* 默认回复，设置到setting里
*/
class DefaultReplyController extends BaseController
{
    public function index()
    {
        $keywordsId = Setting::get('wechat.reply.default_keywords_id');
        $data = RuleKeyword::getKeywordsInfo($keywordsId);
        if (empty($data)) {
            $data['is_set'] = 0;//给前端增加标志位，是否已设置默认
        } else {
            $data = $data->toArray();
            $data['is_set'] = 1;//给前端增加标志位，是否已设置默认
        }
        return view('Yunshop\Wechat::admin.reply.default', [
            'data' => json_encode($data)
        ]);
    }
    public function add()
    {
        $keywords_id = request('keywords_id');
        Setting::set('wechat.reply.default_keywords_id', $keywords_id);
        $id = Setting::get('wechat.reply.default_keywords_id');
        if (!empty($id)) {
            return $this->successJson('保存成功');
        } else {
            return $this->errorJson('保存失败');
        }
    }
    public function delete()
    {
        Setting::set('wechat.reply.default_keywords_id', 0);
        $id = Setting::get('wechat.reply.default_keywords_id');
        if (empty($id)) {
            return $this->successJson('删除成功');
        } else {
            return $this->errorJson('删除失败');
        }
    }
}