<?php

namespace Yunshop\MaterialCenter\admin;

use Yunshop\MaterialCenter\models\GoodsMaterial;
use app\common\components\BaseController;
use app\common\helpers\Url;
use app\common\helpers\PaginationHelper;

class MaterialController extends BaseController
{
    public function index()
    {
        $search = \YunShop::request()->search;
        // dd($search);
        $list = GoodsMaterial::search($search)->with('goods')->orderBy('id', 'desc')->paginate();

        $pager = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());

        return view('Yunshop\MaterialCenter::material.index', [
            'list' => $list,
            'search' => $search,
            'pager' => $pager,
        ])->render();
    }

    public function getSearchGoods()
    {
        $keyword = \YunShop::request()->keyword;
        $goods = \app\common\models\Goods::getGoodsByName($keyword);
        if (!$goods->isEmpty()) {
            $goods = set_medias($goods->toArray(), array('thumb', 'share_icon'));
        }
        return view('Yunshop\MaterialCenter::material.query.query', [
            'goods' => $goods
        ])->render();

    }

    public function add()
    {
        if (request()->data) {
            
            if ($this->validatorInfo() == 1) {
                return $this->message('添加成功', Url::absoluteWeb('plugin.material-center.admin.material.index'));
            }
            return $this->message('添加失败, '.$this->validatorInfo(), Url::absoluteWeb('plugin.material-center.admin.material.index'));
        }
        return view('Yunshop\MaterialCenter::material.detail',[
            'var' => \YunShop::app()->get()
        ])->render();
    }

    public function edit()
    {
        $id = request()->id;

        $data = GoodsMaterial::find($id);

        $data['images'] = unserialize($data['images']);

        // $data['thumb'] = Goods::where('id', intval($id))->select('thumb')->first()->thumb;

        if (request()->data) {
            // dd(request()->data);

           if ($this->validatorInfo() == 1) {
                // dd('ok');
                return $this->message('修改成功', Url::absoluteWeb('plugin.material-center.admin.material.index'));
            }
            // dd($this->validatorInfo());
            return $this->message('修改失败,'.$this->validatorInfo(), Url::absoluteWeb('plugin.material-center.admin.material.index'));
        }

        return view('Yunshop\MaterialCenter::material.detail',[
            'data'=> $data,
            'var' => \YunShop::app()->get()
        ])->render();
    }

    public function delete()
    {
        $id = request()->id;

        $data = GoodsMaterial::find($id);

        if (!$id || !$data) {
            return $this->message('请传入正确参数', Url::absoluteWeb('plugin.material-center.admin.material.index'));
        }

        $res = $data->delete($id);
        
        if (!$res) {
            return $this->message('删除失败', Url::absoluteWeb('plugin.material-center.admin.material.index'));
        }
        
        return $this->message('删除成功', Url::absoluteWeb('plugin.material-center.admin.material.index'));

    }

    public function changeStatus()
    {
        $id = request()->id;

        $data = GoodsMaterial::find($id);

        if (!$id || !$data) {
            return $this->message('请传入正确参数', Url::absoluteWeb('plugin.material-center.admin.material.index'));
        }
        
        $status = $data->is_show == 1 ? 0 : 1;

        $res = GoodsMaterial::where('id', $id)->update(['is_show'=>$status]);
        
        if ($res == 1) {
            return $this->message('设置成功', Url::absoluteWeb('plugin.material-center.admin.material.index'));

        }
        return $this->message('设置失败', Url::absoluteWeb('plugin.material-center.admin.material.index'));

    }

    private function validatorInfo()
    {
        $info = request()->data;

        $goods = new GoodsMaterial;

        $validator = $goods->validator($info);
        // dd($validator);

        if ($validator->fails()) {
            // dd('error;', $validator->messages()->first());
           return $validator->messages()->first();
        }

        if (count(request()->data['images']) > 9) {
            return '图片大于9张';
        }
        
        $info['images'] = serialize(request()->data['images']);

        // $info['content'] = trim(request()->data['content']);

        $info['uniacid'] = \YunShop::app()->uniacid;
        // dd($info);
        if (isset(request()->id) && request()->id > 0) {

            $goods->where('id', intval(request()->id))->update($info);

        } else {
            $goods->fill($info);
            $goods->save();
        }

        return 1;
    }
}