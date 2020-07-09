<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/8/21
 * Time: 10:28
 */

namespace Yunshop\JdSupply\frontend\order;


use app\common\exceptions\AppException;
use Yunshop\JdSupply\services\sdk\JdClient;
use Yunshop\JdSupply\services\sdk\JdRequest;
use Yunshop\JdSupply\frontend\order\PreOrder;

class JdOrderDispatch
{
    /**
     * @var PreOrder
     */
    private $order;
    /**
     * @var float
     */
    private $freight;

    /**
     * OrderDispatch constructor.
     * @param PreOrder $preOrder
     */
    public function __construct($preOrder)
    {
        $this->order = $preOrder;
    }

    /**
     * 订单运费
     * @return float|int
     */
    public function getFreight()
    {

        if (!isset($this->freight)) {

            //$freight = $this->aaa();

            $freight = $this->ccc();

            //这里要取第三方返回的运费
            return $this->freight = $freight;
        }
        return $this->freight;
    }

    //动态获取第三方运费
    public function aaa()
    {
        $set = \Setting::get('plugin.jd_supply');

        $request = new JdRequest($set['app_secret'], $set['app_key']);

        $request->batchAddParam($this->order->jd_request['param']);

        $response = JdClient::post('/platform/order/JdFreight', $request);

        $jd_response = json_decode($response, true);


        if (!isset($jd_response['code']) || $jd_response['code'] != 1) {
            throw new AppException($jd_response['msg']);
        }

        return $jd_response['data'];
    }

    //写死的运费规则，不可取
    public function ccc()
    {
        //这里是取订单所以商品优惠后的价格
        $order_price =  $this->order->getPriceBefore('orderDispatchPrice');


        //订单最终实际支付金额＜49元，收取基础运费8元，不收续重运费；
        //49元≤订单最终实际支付金额＜99元，收取基础运费6元，不收续重运费；
        //订单最终实际支付金额≥99元，免基础运费，不收续重运费。
        if ($order_price < 49) {
            return 8;
        } elseif ($order_price < 99) {
            return 6;
        }  elseif ($order_price >= 99) {
            return 0;
        }
        return 0;
    }

}