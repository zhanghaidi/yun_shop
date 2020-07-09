<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/6/16
 * Time: 下午5:04
 */

namespace Yunshop\Exhelper\admin;


use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use Yunshop\Exhelper\common\models\ExhelperSys;
use Yunshop\Exhelper\common\models\Express;

class ExpressController extends BaseController
{
    public function index()
    {
        $list = Express::getList(Express::EXPRESS_TYPE)->orderBy('id', 'desc')->paginate(20);
        $pager = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());

        return view('Yunshop\Exhelper::admin.express_list', [
            'list'  => $list,
            'pager' => $pager
        ]);
    }

    public function add()
    {
        $print_set = ExhelperSys::getOnlyOne()->first();
        $express_model = new Express();
        if (\Request::ajax()) {
            $express_data = [];
            $express_data['express'] = \YunShop::request()->express;
            $datas = json_decode(htmlspecialchars_decode(\YunShop::request()->datas), true);

            if ($express_data or $datas) {
                $express_data['uniacid'] = \YunShop::app()->uniacid;
                $express_data['width'] = \YunShop::request()->width;
                $express_data['type'] = 1;
                $express_data['height'] = \YunShop::request()->height;
                $express_data['datas'] = $datas;
                $express_data['expresscom'] = \YunShop::request()->expresscom;
                $express_data['expressname'] = \YunShop::request()->expressname;
                $express_data['express'] = \YunShop::request()->express;
                $express_data['bg'] = \YunShop::request()->background;
                $express_data['isdefault'] = \YunShop::request()->isdefault;

                if ($express_data['isdefault'] == Express::DEFAULT_SUCCESS) {
                    $this->updateDefault();
                }

                $express_model->fill($express_data);
                if ($express_model->save()) {
                    echo 'success';
                    return;
                    //return $this->message('添加快递单成功', Url::absoluteWeb(Express::EXPRESS_INDEX_URL));
                } else {
                    //return $this->message('添加快递单失败', Url::absoluteWeb(Express::EXPRESS_INDEX_URL), 'error');
                }
            }
        }

        return view('Yunshop\Exhelper::admin.express_detail', [
            'print_set' => $print_set
        ]);
    }

    public function edit()
    {
        $print_set = ExhelperSys::getOnlyOne()->first();
        $result = $this->verify(\YunShop::request()->express_id);
        //$datas = html_entity_decode(\YunShop::request()->datas);
        //echo $datas;exit;


        if ($result['status'] == -1) {
            return $this->message($result['msg'], Url::absoluteWeb($result['url']), 'error');
        } else if (\Request::ajax()) {
            //echo '<pre>';print_r(\YunShop::request()->datas);exit();
            //echo '<pre>';print_r(json_decode(htmlspecialchars_decode(\YunShop::request()->datas), true));exit();
            $express_data = [];
            $express_data['express'] = \YunShop::request()->express;
            //dd(\YunShop::request());exit;
            $datas = json_decode(htmlspecialchars_decode(\YunShop::request()->datas), true);
            if ($express_data or $datas) {
                $express_data['width'] = \YunShop::request()->width;
                $express_data['height'] = \YunShop::request()->height;
                $express_data['datas'] = $datas;
                $express_data['expresscom'] = \YunShop::request()->expresscom;
                $express_data['expressname'] = \YunShop::request()->expressname;
                $express_data['express'] = \YunShop::request()->express;
                $express_data['bg'] = \YunShop::request()->background;
                $express_data['isdefault'] = \YunShop::request()->isdefault;

                if ($express_data['isdefault'] == Express::DEFAULT_SUCCESS) {
                    $this->updateDefault();
                }

                $result['model']->fill($express_data);
                $result['model']->save();
                echo 'success';
                return;
            }
        }
        //dd($result['model']);exit;

        return view('Yunshop\Exhelper::admin.express_detail', [
            'print_set' => $print_set,
            'item'  => $result['model']
        ]);
    }

    private function updateDefault()
    {
        $express_default = Express::getDefault(Express::EXPRESS_TYPE)->first();
        if ($express_default && $express_default->id != \YunShop::request()->express_id) {
            $express_default->isdefault = Express::DEFAULT_ERROR;
            $express_default->save();
        }
    }

    public function delete()
    {
        $result = $this->verify(\YunShop::request()->express_id);
        if ($result['status'] == -1) {
            return $this->message($result['msg'], Url::absoluteWeb($result['url']), 'error');
        } else {
            $result['model']->delete();
            return $this->message('删除快递单成功', Url::absoluteWeb(Express::EXPRESS_INDEX_URL));
        }
    }

    public function isDefault()
    {
        $result = $this->verify(\YunShop::request()->express_id);
        if ($result['status'] == -1) {
            return $this->message($result['msg'], Url::absoluteWeb($result['url']), 'error');
        } else {
            if ($result['model']->isdefault == Express::DEFAULT_SUCCESS) {
                return $this->message('已经为默认', Url::absoluteWeb(Express::EXPRESS_INDEX_URL), 'error');
            }
            $result['model']->isdefault = Express::DEFAULT_SUCCESS;
            if ($result['model']->isdefault == Express::DEFAULT_SUCCESS) {
                $this->updateDefault();
            }
            $result['model']->save();
            return $this->message('修改默认快递单成功', Url::absoluteWeb(Express::EXPRESS_INDEX_URL));
        }
    }

    public function verify($express_id)
    {
        if (!$express_id) {
            return [
                'status' => -1,
                'msg'    => '参数错误',
                'url'    => Express::EXPRESS_INDEX_URL
            ];
        }
        $express_model = Express::find($express_id);
        //$express_model->datas = html_entity_decode($express_model->datas);
        if (!$express_model) {
            return [
                'status' => -1,
                'msg'    => '未找到快递单',
                'url'    => Express::EXPRESS_INDEX_URL
            ];
        }
        return [
            'status'    => 1,
            'model'     => $express_model
        ];
    }
}