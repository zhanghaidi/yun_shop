<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com yangyu
 * Date: 2019/1/9
 * Time: 14:54
 */
namespace Yunshop\Tbk\admin;

use app\common\components\BaseController;

use Illuminate\Support\Facades\Storage;
use Yunshop\Tbk\common\jobs\OrderSynJob;
use Yunshop\Tbk\common\models\TbkOrder;
use Yunshop\Tbk\common\models\TbkPid;
use Yunshop\Tbk\common\services\OrderService;

class OrderController extends BaseController
{
    public function __construct()
    {

    }

    public function testOrder() {

        $tbkOrders = TbkOrder::select()->where('order_sn', '263245837733044711')->get();
        //dd($tbkOrders);
        $osj = new OrderSynJob($tbkOrders);
        $osj->handle();
        //$osj->pay('684');
    }

    public function index()
    {

    }

    public function testimportPid()
    {
        $file = 'app/pid1.txt';

        //dd(storage_path(Storage::url($file)));
        $contents = file_get_contents('/Users/yangyu/www/yunzhong/addons/yun_shop/storage/app/pid1.txt');
        $contents = mb_convert_encoding($contents, 'UTF-8', 'GBK');
        $rows = explode("\n", $contents);

        //print_r($rows);

        foreach($rows as $row) {
            $pids = explode(",", $row);
            //dd($pids[0]);
            TbkPid::insert([
                "uniacid" => \YunShop::app()->uniacid,
                "name" => $pids[0],
                "full_pid" => $pids[2],
                "pid" => $pids[1],
            ]);
        }
        //$Order = new OrderService();
        //dd(Storage::url($file));
        //$Order->importOrder(Storage::url($file));
        //dd($res);
    }

    public function testimportOrder()
    {
        $file = 'app/order/6cc8efa61570e6d9eef8dc79a52be0f3.';
        $Order = new OrderService();
        //dd(Storage::url($file));
        $Order->importOrder(Storage::url($file));
        //dd($res);
    }

    public function import()
    {
        $file = request()->file('file');
        if ($file) {
            $filename = "app/".$file->store('order');

            $Order = new OrderService();
            $Order->importOrder(Storage::url($filename));
            $this->message('导入淘宝客订单成功');
        }

        return view('Yunshop\Tbk::admin.orderUpload', [
        ])->render();
    }
}