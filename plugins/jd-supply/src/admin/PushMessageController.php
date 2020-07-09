<?php


namespace Yunshop\JdSupply\admin;


use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use Yunshop\JdSupply\models\JdPushMessage;

class PushMessageController extends BaseController
{
    public function index()
    {
        $pageSize = 20;
        $messageModel = JdPushMessage::uniacid();

        $search = \YunShop::request()->search;
        if ($search) {
            $messageModel = $messageModel->search($search);
        }
        $list = $messageModel->orderBy('created_at','desc')->paginate($pageSize)->toArray();
        $data = $this->category();
        $pager = PaginationHelper::show($list['total'], $list['current_page'], $list['per_page']);
        return view('Yunshop\JdSupply::admin.push-message',[
            'list'=>$list['data'],
            'page'=>$pager,
            'search'=>$search,
            'data'=>$data
        ]);

    }

    protected function category()
    {
        return [
            'msg'=>[
                'goods.price.alter'     =>  '商品价格变更',
                'goods.alter'           =>  '商品修改',
                'goods.on.sale'         =>  '商品上架',
                'goods.undercarriage'   =>  '商品下架',
                'goods.storage.delete'  =>  '商品删除',
                'order.delivery'        =>  '订单发货',
                'order.cancel'          =>  '订单取消',
                'goods.control'         =>  '风控策略',
                ],
            'type'=>[
                'goods.price.alter'     =>  1,
                'goods.alter'           =>  1,
                'goods.on.sale'         =>  1,
                'goods.undercarriage'   =>  1,
                'goods.storage.delete'  =>  1,
                'order.delivery'        =>  2,
                'order.cancel'          =>  2,
                'goods.control'         =>  1,
            ]
        ];
    }
}