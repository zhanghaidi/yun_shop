<?php
namespace Yunshop\Supplier\supplier\controllers;
use app\backend\modules\menu\Menu;
use app\common\components\BaseController;
use Carbon\Carbon;
use app\backend\modules\charts\models\Supplier;
use Yunshop\Supplier\supplier\models\SupplierOrderJoinOrder;
use app\common\services\Session;

class SupplierIndexController extends BaseController
{
    public $orderModel;
    //供应商独立后台首页
    static public function index()
    {
        return view('Yunshop\Supplier::supplier.index',['data' => json_encode(self::_allSupplierData())]);
    }

    private function allSupplierData()
    {
        /*if (!\Cache::has('supplierIndex' . \YunShop::app()->uniacid.Session::get('supplier')['id'])) {
            \Cache::put('supplierIndex' .\YunShop::app()->uniacid. Session::get('supplier')['id'], self::_allSupplierData(), 0.5);
        }
        return \Cache::get('supplierIndex' .\YunShop::app()->uniacid. Session::get('supplier')['id']);*/
    }

    private function _allSupplierData()
    {
        $supplier = Supplier::getSupplierById(\YunShop::isRole()['id']);
        $supplier_id['supplier_id'] = $supplier->id;
        if(!$supplier_id){
            throw new \Exception('您不是供应商!');
        }
        //常用功能
        $menu = Menu::current()->getItems()['supplier_supplier_menu']['child'];
        $data['menu']['supplier.supplier.info'] = $menu['supplier.supplier.info'];
        $data['menu']['supplier.goods.add'] = $menu['supplier.supplier.goods']['child']['supplier.goods.add'];
        $data['menu']['supplier.supplier.goods'] = $menu['supplier.supplier.goods'];
        $data['menu']['supplier.supplier.order'] = $menu['supplier.supplier.order'];
        $data['menu']['supplier.supplier.waitSend'] = $menu['supplier.supplier.waitSend'];
        $data['menu']['supplier.supplier.withdraw'] = $menu['supplier.supplier.withdraw'];
        $data['menu']['supplier.supplier.batchsend'] = $menu['supplier.supplier.batchsend'];

        foreach ($data['menu'] as $k=>&$v){
          $v['url'] = yzWebFullUrl($v['url']);
          $v['icon'] =  $v['icon'] ?: 'fa-circle-o';
        }
        //交易总金额
        $cancelled = self::getOrder($supplier_id)->status(-1)->sum('price');
        $watiPay = self::getOrder($supplier_id)->status(0)->sum('price');
        $all = self::getOrder($supplier_id)->sum('price');
        $data['all_money']  =  $all - $cancelled - $watiPay;

        //待支付订单数
        $data['waitPayOrder'] =  self::getOrder($supplier_id)->status(0)->count();

        //代发货订单
        $data['waitSendOrder'] =  self::getOrder($supplier_id)->status(1)->count();

        //今日交易额and今日订单
        $today['ambiguous']['field'] = 'order';
        $today['time_range']['field'] = 'create_time';
        $today['time_range']['start'] = Carbon::now()->startOfDay()->format('Y-m-d H:i:s');
        $today['time_range']['end'] = Carbon::now()->endOfDay()->format('Y-m-d H:i:s');
        $today['supplier_id'] = $supplier_id;
        $data['today_order'] = self::getOrder($today)->count() - self::getOrder($today)->status(-1)->count() - self::getOrder($today)->status(0)->count() ;
        $data['today_money'] = self::getOrder($today)->sum('price')- self::getOrder($today)->status(-1)->sum('price') - self::getOrder($today)->status(0)->sum('price');


        //本周订单
        $week['ambiguous']['field'] = 'order';
        $times = self::timeRangeItems();
        for ($i = 0;$i<7;$i++){
            $week['time_range']['start'] = $times[$i]. '00:00';
            $week['time_range']['end'] = $times[$i].' 23:59';
            $week['supplier_id'] = $supplier_id;
            $week['time_range']['field'] = 'create_time';
            $data['week'][$i]['week_order'] = self::getOrder($week)->count();
            $week['time_range']['field'] = 'send_time';
            $data['week'][$i]['send_order'] = self::getOrder($week)->count();
            $week['time_range']['field'] = 'finish_time';
            $data['week'][$i]['completed_order'] = self::getOrder($week)->count();
            $data['week'][$i]['data'] = $times[$i];
        }
        return $data;
    }
    private  function getOrder($search)
    {
        return SupplierOrderJoinOrder::getSupplierOrderList($search);
    }
    /**
     * 获取一星期的时间
     * @return array
     */
    private function timeRangeItems()
    {
        $result = [];
        for ($i = 6; $i > -1; $i--) {
            Carbon::now()->subDay($i)->format('Y-m-d ');
            $result[] = Carbon::now()->subDay($i)->format('Y-m-d ');
        }
        return $result;
    }


}
