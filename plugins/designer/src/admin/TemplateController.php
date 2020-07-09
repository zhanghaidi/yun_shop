<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2019/1/23
 * Time: 13:49
 */

namespace Yunshop\Designer\admin;

use app\common\components\BaseController;
use Illuminate\Support\Facades\DB;
use Yunshop\Designer\models\ViewSet;

class TemplateController extends BaseController
{

    public function index()
    {
        $data = ViewSet::uniacid()->get()->toArray();

        $result = [];
        $result['extension'] = [];
        $result['member'] = [];
        $result['category'] = [];
        $result['goods'] = [];
        foreach ($data as $value) {
            if ($value['type'] == 'member') {
                $result['member']['type'] = $value['type'];
                $result['member']['names'] = $value['names'];
            }
            if ($value['type'] == 'extension') {
                $result['extension']['type'] = $value['type'];
                $result['extension']['names'] = $value['names'];
            }
            if ($value['type'] == 'category') {
                $result['category']['type'] = $value['type'];
                $result['category']['names'] = $value['names'];
            }
            if ($value['type'] == 'goods') {
                $result['goods']['type'] = $value['type'];
                $result['goods']['names'] = $value['names'];
            }
        }

        return view('Yunshop\Designer::admin.template', ['data' => $result]);
    }

    public function setTmp()
    {
        $viewset = new ViewSet();
        $type = $_POST['type'];

        switch ($type){
            case '会员中心': $viewset->type = 'member'; break;
            case '推广中心': $viewset->type = 'extension'; break;
            case '分类中心': $viewset->type = 'category'; break;
            case '商品模版': $viewset->type = 'goods'; break;
        }

        $viewset->uniacid = \Yunshop::app()->uniacid;
        $viewset->names = $_POST['names'];
        $viewset->path = $_SERVER['HTTP_REFERER'] . $_POST['path'];

        $data = [
            'type' => $viewset->type,
            'uniacid' => \Yunshop::app()->uniacid,
            'names' => $_POST['names'],
            'path' => $_POST['path'],
        ];
        
        $validator = $viewset->validator();

    	if ($validator->fails()) {
    		return $this->errorJson($validator->messages()->first());
    	} else {
    		$one = $viewset->where('type', $data['type'])->first();
    		if (!$one) {
    			//不存在该分类的模板则添加
	        	if (!$viewset->save()) {
	        		return $this->errorJson('添加错误');
	        	}

    			return $this->successJson('操作成功');
    		}

			//存在同种类型的时候判断是否有其他相等名称的模板
			$two = $viewset->where(['type' => $data['type'], 'names' => $data['names']])->first();

    		if (!$two) {
    			//有类型但名称不一样的模板添加,删除其他同类同名的模板
        		if (!$viewset->uniacid()->where('type', $data['type'])->delete() ) {
	        		return $this->errorJson('删除错误');
        		}

        		if (!$viewset->save()) {

	        		return $this->errorJson('添加错误');
	        	}

    			return $this->successJson('操作成功');
    		} 
			//同类同名的模板存在
			return $this->successJson('操作成功');
    	}

    }

}