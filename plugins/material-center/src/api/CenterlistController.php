<?php

namespace Yunshop\MaterialCenter\api;

use app\common\components\ApiController;
use Yunshop\MaterialCenter\models\GoodsMaterial;
use Yunshop\Commission\models\Agents;

class CenterlistController extends ApiController
{
	public function index()
	{
		$list = GoodsMaterial::with('goods')
			->where('uniacid', \YunShop::app()->uniacid)
			->where('is_show', 1)
			->orderBy('id', 'desc')
			->paginate();

		if (!$list) {
			return $this->errorJson('暂无数据');
		}

		$list = $list->toArray();

		$logo =  yz_tomedia(\Setting::get('shop.shop.logo'));

		$name =  \Setting::get('shop.shop.name');

		foreach ($list['data'] as $k => $v) {

			$list['data'][$k]['images'] = unserialize($v['images']);
			$list['data'][$k]['logo'] = $logo;
			$list['data'][$k]['name'] = $name;

			foreach ($list['data'][$k]['images'] as $key => $value) {
				
				$list['data'][$k]['images'][$key] = yz_tomedia($value);
			}
			
			$list['data'][$k]['goods']['thumb'] = yz_tomedia($v['goods']['thumb']);
		}

		$set = \Setting::get('plugins.material-center');
        $set['icon'] = yz_tomedia($set['icon']);
		// dd($list['data']);

		return $this->successJson('成功', [
            'list' => $list,
            'set'  => $set,
        ]);
	}
	
	//下载, 分享, 收藏
	public function updateNum()
	{	
		$id = intval(request()->id);

		$params = trim(request()->params);
		
		$allparam = ['download', 'share', 'collect'];

		if (!in_array($params, $allparam)) {
			return $this->errorJson('操作码有误');
		}

		$data = GoodsMaterial::find($id);
		
		if (!$id || !$data) {
			return $this->errorJson('暂无数据');
		}

		GoodsMaterial::where('id', $id)->update([ $params => $data[$params] + 1 ]);
		
		return $this->successJson('成功');
	}

	// public function addMaterial()
	// {
	// 	//推客插件安装并开启
	// 	if (!app('plugins')->isEnabled('commission')) {
	// 		return $this->errorJson('分销插件未开启');
	// 	}

	// 	$uid = \YunShop::app()->getMemberId();
		
	// 	$agent = Agents::getAgentByMemberId($uid);
		
	// 	if (!$agent) {
	// 		return $this->errorJson('您的身份不是推广员, 请先成为推广员');
	// 	}

	// 	GoodsMaterial::insert([
	// 		'images' => serialize(trim(request()->images)),
	// 		'goods_id' => intval(trim(request()->goods_id)),
	// 		'uniacid' => \YunShop::app()->uniacid
	// 	]);
	// }
}
