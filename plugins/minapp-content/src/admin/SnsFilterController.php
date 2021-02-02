<?php

namespace Yunshop\MinappContent\admin;

use app\common\components\BaseController;
use app\common\helpers\Url;
use Yunshop\MinappContent\models\SnsFilterModel;
use Yunshop\MinappContent\services\MinappContentService;

class SnsFilterController extends BaseController
{
    public function post()
    {
        $data = \YunShop::request()->data;
        if ($data) {
            $data = array_filter($data);
            if (!isset($data['id']) || $data['id'] <= 0) {
                return $this->message('参数ID错误', '', 'danger');
            }
            if (!isset($data['content'])) {
                return $this->message('内容不能为空', '', 'danger');
            }
            $data['content'] = trim($data['content']);
            if (!isset($data['content'][0])) {
                return $this->message('内容不能为空', '', 'danger');
            }
            SnsFilterModel::where('id', $data['id'])->update([
                'content' => $data['content'],
            ]);

            return $this->message('修改成功', Url::absoluteWeb('plugin.minapp-content.admin.sns-filter.post'));
        }

        $list = SnsFilterModel::orderBy('list_order', 'desc')->get()->toArray();

        return view('Yunshop\MinappContent::admin.sns_filter.post', [
            'pluginName' => MinappContentService::get('name'),
            'data' => $list,
        ]);
    }

    public function category()
    {
        $data = \YunShop::request()->data;
        if ($data) {
            $data = array_filter($data);
            if (!isset($data['title'])) {
                return $this->message('类目名称不能为空', '', 'danger');
            }
            $data['title'] = trim($data['title']);
            if (!isset($data['title'][0])) {
                return $this->message('类目名称不能为空', '', 'danger');
            }
            $filter = new SnsFilterModel;
            $filter->title = $data['title'];
            $filter->list_order = (isset($data['list_order']) && $data['list_order'] > 0) ? $data['list_order'] : 0;
            $filter->content = '';
            $filter->save();
            if (!isset($filter->id) || $filter->id <= 0) {
                return $this->message('添加失败', '', 'danger');
            }

            return $this->message('添加成功', Url::absoluteWeb('plugin.minapp-content.admin.sns-filter.post'));
        }

        return view('Yunshop\MinappContent::admin.sns_filter.category', [
            'pluginName' => MinappContentService::get('name'),
        ]);
    }
}
