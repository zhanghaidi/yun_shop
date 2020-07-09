<?php


namespace Yunshop\JdSupply\admin;


use app\common\components\BaseController;
use app\common\exceptions\ShopException;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use app\common\models\Goods;
use Yunshop\JdSupply\models\JdGoodsControl;
use Yunshop\JdSupply\models\JdPushMessage;
use Yunshop\Room\models\Room;

class GoodsControlController extends BaseController
{
    public function index()
    {
        $goods_ids = JdGoodsControl::pluck('goods_id')->toArray();
        $list = \Yunshop\JdSupply\models\Goods::whereIn('id',$goods_ids)->paginate(20);
        $pager = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());
        return view('Yunshop\JdSupply::admin.goods-control',[
            'list'=>$list,
            'page'=>$pager,
        ]);

    }

    public function add()
    {
        $ids = request()->goods;
        foreach ($ids as $id) {
            $goods_control = JdGoodsControl::where('goods_id', $id)->first();
            if (empty($id)) {
                continue;
            }
            if (empty($goods_control)) {
                $goods_control = new JdGoodsControl();
                $goods_control->goods_id = $id;
                $goods_control->uniacid = \YunShop::app()->uniacid;
                $goods_control->save();
            }
        }

        return $this->message('添加成功',Url::absoluteWeb('plugin.jd-supply.admin.goods-control.index'));
    }

    public function delete()
    {
        $id = intval(request()->id);
        $goods_control = JdGoodsControl::where('goods_id',$id)->first();
        if ($goods_control) {
            $result = $goods_control->delete();
        }
        if ($result) {
            return $this->message('删除成功',Url::absoluteWeb('plugin.jd-supply.admin.goods-control.index'));
        }
        return $this->message('删除失败',Url::absoluteWeb('plugin.jd-supply.admin.goods-control.index'),'error');
    }

    public function goodsSearch()
    {
        $keyword = request()->keyword;
        $list = \Yunshop\JdSupply\models\Goods::Search(['keyword'=>$keyword])
            ->pluginId()
            ->orderBy('display_order', 'desc')
            ->orderBy('yz_goods.id', 'desc')
            ->get();
        foreach ($list as $key => $item){
            $list[$key]['thumb']  = yz_tomedia($item->thumb);

            $list[$key]['link'] = yzAppFullUrl('goods/'.$item['id']);
        }
        return view('Yunshop\JdSupply::admin.goods-query', [
            'goods' => $list
        ])->render();
    }
}