<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/6/19
 * Time: 下午6:03
 */

namespace Yunshop\Exhelper\admin;


use app\common\components\BaseController;
use app\common\helpers\Url;
use app\common\models\Address;
use PhpXmlRpc\Request;
use Yunshop\Exhelper\common\models\ExhelperOrder;
use Yunshop\Exhelper\common\models\ExhelperSys;
use Yunshop\Exhelper\common\models\ExhelperPanel;
use Yunshop\Exhelper\common\models\Express;
use Yunshop\Exhelper\common\models\Order;
use Yunshop\Exhelper\common\models\PrintStatus;
use Yunshop\Exhelper\common\models\SendUser;
use Yunshop\Exhelper\common\models\Short;
use Ixudra\Curl\Facades\Curl;

class PrintOnceController extends BaseController
{
    public function search()
    {
        $print_set = ExhelperSys::getOnlyOne()->first();
        $search = \YunShop::request()->search;

        $list = [];
        if ($search) {
//            $orders = Order::orders($search)->isPlugin()->wherePluginId(0)->get();
            $orders = Order::orders($search)->whereIn('plugin_id',[0,54])->get();
            if (!$orders->isEmpty()) {
                foreach ($orders->toArray() as $order) {
                    if ($order['address'] && is_array($order['address'])) {
                        $addresskey = $order['address']['realname'] . $order['address']['mobile'] . $order['address']['address'];
                        if (!isset($list[$addresskey])) {
                            $list[$addresskey] = array('realname' => $order['address']['realname'], 'orderids' => array());
                        }
                        $list[$addresskey]['orderids'][] = $order['id'];
                    }
                }
            }
            return view('Yunshop\Exhelper::admin.print.print_tpl', [
                'print_set' => $print_set,
                'list'      => $list
            ]);
        }
        return view('Yunshop\Exhelper::admin.doprint', [
            'print_set' => $print_set,
            'list'      => $list
        ]);
    }

    public function detail()
    {
        $order_ids = \YunShop::request()->orderids;
        if (!$order_ids) {
            return $this->message('无任何订单，无法查看', Url::absoluteWeb('plugin.exhelper.admin.print-once.search'), 'error');
        }
        $order_ids_arr = explode(',', $order_ids);
        if (!$order_ids_arr) {
            return $this->message('无任何订单，无法查看', Url::absoluteWeb('plugin.exhelper.admin.print-once.search'), 'error');
        }
        $list = Order::orders([])->whereInIds($order_ids_arr)->with('hasOnePrint')->get();
        $address = $list[0]->address;
        $province = Address::select()->whereId($address->province_id)->value('areaname');
        $city = Address::select()->whereId($address->city_id)->value('areaname');
        $district = Address::select()->whereId($address->district_id)->value('areaname');
        $sendinfo = [];
        foreach ($list as $item) {
            foreach ($item->hasManyOrderGoods as $k => $g) {
                if (isset($sendinfo[$g->goods_id])) {
                    $sendinfo[$g->goods_id]['num'] += $g->total;
                } else {
                    $sendinfo[$g->goods_id] = array('title' => empty($g->goods->hasOneShort) ? $g->title : $g->goods->hasOneShort->short_title, 'num' => $g->total, 'optiontitle' => !empty($g->goods_option_title) ? '(' . $g->goods_option_title . ')' : '');
                }
            }
        }

        $sendinfos = array();
        foreach ($sendinfo as $gid => $info) {
            $info['gid'] = $gid;
            $sendinfos[] = $info;
            $address['sendinfo'] .= $info['title'] . $info['optiontitle'] . ' x ' . $info['num'] . '; ';
        }

        return view('Yunshop\Exhelper::admin.print.print_tpl_detail', [
            'list'      => $list,
            'address'   => $address,
            'sendinfo'  => $sendinfo,
            'province'  => $province,
            'city'      => $city,
            'district'  => $district,
            'panel'     => 0
        ]);
    }

    public function shortTitle()
    {
        $goods_id = intval(\YunShop::request()->goodid);
        $short_title = trim(\YunShop::request()->shorttitle) ? trim(\YunShop::request()->shorttitle) : '';
        if ($goods_id) {
            $short_model = Short::getShortByGoodsId($goods_id)->first();
            if ($short_model) {
                $short_model->short_title = $short_title;
                $short_model->save();
                return json_encode(['result' => 'success', 'resp' => '修改成功']);
            } else {
                $short_model = new Short();
                $short_model->goods_id = $goods_id;
                $short_model->short_title = $short_title;
                $short_model->save();
                return json_encode(['result' => 'success', 'resp' => '新增成功']);
            }
        } else {
            return json_encode(['result' => 'error', 'resp' => '参数错误']);
        }
    }

    public function saveAddress()
    {
        $order_sns = \YunShop::request()->ordersns;
        $address = array(
            'realname' => \YunShop::request()->realname,
            'mobile' => \YunShop::request()->mobile,
            'address' => \YunShop::request()->address
        );
        if (empty($order_sns)) {
            die(json_encode(array('result' => 'error', 'resp' => '订单数据为空')));
        }
        foreach ($order_sns as $order_sn) {
            $order_model = ExhelperOrder::getOrderSend($order_sn)->first();
            if ($order_model) {
                $order_model->fill($address);
                $order_model->save();
            } else {
                $order_model = new ExhelperOrder();
                $address['order_sn'] = $order_sn;
                $order_model->fill($address);
                $order_model->save();
            }
        }

        die;
    }

    public function getPrintTemp()
    {
        $type = intval(\YunShop::request()->type);

        if (empty($type)) {
            die(json_encode(array('result' => 'error', 'resp' => '加载模版错误! 请刷新重试。')));
        }
        $expSendTemp = SendUser::getDefault()->first();
        \Log::info('expSendTemp', $expSendTemp);
        if (!$expSendTemp) {
            die(json_encode(array('result' => 'error', 'resp' => '请先设置默认发货人信息！')));
        }
        $expSendTemp = $expSendTemp->toArray();

        $expTemp = Express::getDefault($type)->first();
        \Log::info('expTemp', $expTemp);

        if (!$expTemp) {
            die(json_encode(array('result' => 'error', 'resp' => '请先设置默认打印模版！')));
        }

        $shop_set = \Setting::get('shop.shop');

        $expDatas = $expTemp['datas'];

        $expTemp['shopname'] = $shop_set['name'];
        $repItems = array('sendername', 'sendertel', 'senderaddress', 'sendersign', 'sendertime', 'sendercode', 'sender_city');

        $repDatas = array($expSendTemp['sender_name'], $expSendTemp['sender_tel'], $expSendTemp['sender_address'], $expSendTemp['sender_sign'], date('Y-m-d H:i'), $expSendTemp['sender_code'], $expSendTemp['sender_city']);

        if (!is_array($expDatas)) {
            die(json_encode(array('result' => 'error', 'resp' => '请先设置默认打印模版！')));
        }
        foreach ($expDatas as $index => $data) {
            $expDatas[$index]['items'] = str_replace($repItems, $repDatas, $data['items']);
        }
        die(json_encode(array('result' => 'success', 'respDatas' => $expDatas, 'respUser' => $expSendTemp, 'respTemp' => $expTemp)));
    }

    //获取电子面单模板
    public function getPanelTemp()
    {
        $defaultTemp = ExhelperPanel::getDefault()->first();

        if (empty($defaultTemp)) {
            die(json_encode(array('result' => 'error', 'resp' => '请设置默认模板')));
        }
        $expSendTemp = SendUser::getDefault()->first();
        if (!$expSendTemp) {
            //如果无对应信息
            die(json_encode(array('result' => 'error', 'resp' => '请先设置默认发货人信息！')));
        }
        
        die(json_encode(array('result' => 'success', 'respDatas' => $defaultTemp)));
    }


    public function getOrderState()
    {
        $order_sns = \YunShop::request()->ordersns;
        $arr = array();
        foreach ($order_sns as $order_sn) {
            $order_info = Order::orders([])->byOrderSn($order_sn)->first();
            $arr[] = array('ordersn' => $order_sn, 'status' => $order_info->status, 'expresssn' => $order_info->express->express_sn, 'expresscom' => $order_info->express->express_company);
        }
        $printTemp = Express::getDefault(1)->first();
        die(json_encode(array('printTemp' => $printTemp, 'datas' => $arr)));
    }

    public function dosend()
    {

    }
    //修改订单打印    
    public function editOrderPrintStatus()
    {
        $column = request()->column;
        $ids = request()->arr;
        \Log::info('ids', $ids);

        if (count($ids) > 1) {
            foreach ($ids as $id) {
                $more = 1;

                if (!$this->doEditOrderPrintStatus(trim($id['orderid']), $column, $more)) {

                    \Log::info('执行订单打印状态修改异常',['order_id'=>trim($id['orderid']), 'column'=>$column]);
                }
            }

        } else {
            $more = 0;
            if(!$this->doEditOrderPrintStatus($ids[0]['orderid'], $column, $more)) {

                \Log::info('执行订单打印状态修改异常',[$ids[0]['orderid'], 'column'=>$column]);
            }
        }
        return $this->successJson('操作成功');
    }

    //执行多次修改
    public function doEditOrderPrintStatus($ids, $column, $more)
    {
        //查找订单信息
        if (!Order::find($ids)) {
           return false;
        }

        $printStatus = new PrintStatus();
        //获取订单状态信息
        $status = $printStatus::where('order_id', $ids)->first();

        if (empty($status)) {
            //无该订单数据时添加
            $printStatus->fill([
                'order_id' => $ids,
                "$column" => $status["$column"] + 1
            ]);
            
            if (!$printStatus->save()) {
                return false;
            }
            return true;
        }

        //此前有打印该订单时修改打印次数
        $res = PrintStatus::where('order_id', $ids)->update(["$column" => $status->$column + 1]);

        if ($res < 1) {
            return false;
        }     

        return true;
    }


    //获取订单打印状态信息
    public function getOrderPrintStatus()
    {
        if (strpos(request()->ordersn, ',')) {
            $ordersn =  explode(',', trim(request()->ordersn));
        } else {
            $ordersn = trim(request()->ordersn);
        }

        if (count($ordersn) > 1) {

            //查找快递助手订单信息
            foreach ($ordersn as $v) {

                // $exhelper_order_id = ExhelperOrder::whereIn('order_sn', trim($v))->select('id')->get()->toArray();
                $exhelper_order_id = Order::where('order_sn', $v)->select('id')->first();

                //获取订单状态信息
                $data = PrintStatus::where('order_id', $v)->first();
                if ($data) {
                    //执行查询操作,返回该订单的打印状态信息
                    // $data[] = $status[$column];
                    \Log::info('check: status_value', $data);
                } else {
                    \Log::info('check-_error');
                    return $this->errorJson('查询失败');
                }
            }
        } else {
            $id = ExhelperOrder::where('order_sn', $ordersn)->select('id')->first()->id;
            $data = PrintStatus::find($id);
        }
        return $this->successJson('查询成功',['orderprintstate' => $data = $data ?  : 0]);
    }

    public function zipCodeQuery()
    {
        $url = 'http://www.ems.com.cn/ems/tool/rpq/provinces';
        'http://www.ems.com.cn/ems/tool/rpq/cities?country=CN&province=11';
        $provinces = json_decode(Curl::to($url)->get(), true);
        return view('Yunshop\Exhelper::admin.print.zip_code_query', [
            'provinces' => $provinces['model']['selectedProv'],
            'list'      =>''
        ])->render();
    }

    public function city()
    {
        $provinces_id = \YunShop::request()->provinces_id;
        $url = 'http://www.ems.com.cn/ems/tool/rpq/cities?country=CN&province='.$provinces_id;

        $city = json_decode(Curl::to($url)->get(), true);
        return $this->successJson('查询成功',$city['model']['selectedCity']);

    }

    public function queryPostCode()
    {
        $provinces_id = \YunShop::request()->provinces_id;
        $city_id = \YunShop::request()->city_id;
        $url = 'http://www.ems.com.cn/ems/tool/rpq/queryPostCode?city='.$city_id.'&province='.$provinces_id;

        $code = json_decode(Curl::to($url)->get(), true);

        return $this->successJson('查询成功',$code['model']['postCode']);
    }
}