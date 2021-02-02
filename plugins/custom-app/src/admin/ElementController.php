<?php

namespace Yunshop\CustomApp\admin;

use app\common\components\BaseController;
use app\common\helpers\Url;
use Yunshop\CustomApp\models\CustomAppElementModel;
use Yunshop\CustomApp\models\CustomAppElementSortModel;
use Yunshop\CustomApp\services\CustomAppService;

class ElementController extends BaseController
{
    public function edit()
    {
        $data = \YunShop::request()->data;
        if ($data) {
            $data = array_filter($data);
            if (!isset($data['sort_id']) || $data['sort_id'] <= 0) {
                return $this->message('程序错误，请联系开发人员', '', 'danger');
            }

            $sortRs = CustomAppElementSortModel::select('id', 'type')
                ->where('id', $data['sort_id'])->first();
            if (!isset($sortRs->id)) {
                return $this->message('元素数据错误，请联系开发', '', 'danger');
            }

            if (in_array($sortRs->type, [1, 2, 5])) {
                if (!isset($data['content']) || empty($data['content'])) {
                    return $this->message('请填写内容', '', 'danger');
                }
            } elseif (in_array($sortRs->type, [3, 4])) {
                if (!isset($data['content']) || !is_array($data['content'])) {
                    return $this->message('请填写内容', '', 'danger');
                }
                $data['content'] = array_filter($data['content']);
                if (!isset($data['content'][0])) {
                    return $this->message('请填写内容!', '', 'danger');
                }
            } else {
                $data['content'] = '';
            }

            $elementRs = CustomAppElementModel::where([
                'sort_id' => $data['sort_id'],
                'uniacid' => \YunShop::app()->uniacid,
            ])->first();
            if (!isset($elementRs->id)) {
                $elementRs = new CustomAppElementModel;
                $elementRs->uniacid = \YunShop::app()->uniacid;
                $elementRs->sort_id = $data['sort_id'];
            }
            if (in_array($sortRs->type, [3, 4])) {
                $elementRs->content = json_encode(array_values($data['content']));
            } else {
                $elementRs->content = $data['content'];
            }
            $elementRs->save();

            return $this->message('保存成功', Url::absoluteWeb('plugin.custom-app.admin.element-sort.index'));
        }

        $id = (int) \YunShop::request()->id;
        if ($id <= 0) {
            return $this->message('页面元素选择错误，请联系开发', '', 'danger');
        }

        $sortRs = CustomAppElementSortModel::select('id', 'type')->where('id', $id)->first();
        if (!isset($sortRs->id)) {
            return $this->message('页面元素数据错误，请联系开发', '', 'danger');
        }

        $elementRs = CustomAppElementModel::where([
            'sort_id' => $id,
            'uniacid' => \YunShop::app()->uniacid,
        ])->first();
        if (isset($elementRs->id) && in_array($sortRs->type, [3, 4])) {
            $elementRs->content = json_decode($elementRs->content, true);
        }

        return view('Yunshop\CustomApp::admin.element.edit', [
            'pluginName' => CustomAppService::get('name'),
            'data' => $elementRs,
            'sort' => $sortRs,
        ]);
    }
}
