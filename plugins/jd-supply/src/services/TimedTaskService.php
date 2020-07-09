<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/7/24
 * Time: 14:03
 */

namespace Yunshop\JdSupply\services;

use app\common\facades\Setting;
use app\common\models\UniAccount;
use Yunshop\JdSupply\models\JdSupplyError;
use Yunshop\JdSupply\services\sdk\JdClient;
use Yunshop\JdSupply\services\sdk\JdRequest;

class TimedTaskService
{

    public $set;

    public function handle()
    {
        $uniAccount = UniAccount::getEnable();
        foreach ($uniAccount as $u) {
            \YunShop::app()->uniacid = $u->uniacid;
            Setting::$uniqueAccountId = $u->uniacid;
            $this->pushNotice();
        }
    }

    public function pushNotice()
    {
        if (config('app.framework') == 'platform') {
            $path = $_SERVER['DOCUMENT_ROOT'] . '/plugins/jd-supply/logs/' . \YunShop::app()->uniacid;
        } else {
            $path = $_SERVER['DOCUMENT_ROOT'] . '/addons/yun_shop/plugins/jd-supply/logs/' . \YunShop::app()->uniacid;
        }
        if (!is_dir($path)) {
            return;
        }
        $time = date('YmdHi');
        $files = scandir($path);
        $list = [];
        foreach ($files as $filename) {
            if ($filename == '.' || $filename == '..') {
                continue;
            }
            if ($time > intval($filename)) {
                $data = file_get_contents($path.'/'.$filename);
                \Log::debug('新聚合供应链推送',$data);
                $data = explode('--',$data);
                foreach ($data as $k=>$v) {
                    $message = json_decode($v,true);
                    switch ($message['type']) {
                        case 'goods.price.alter':
                        case 'goods.alter':
                        case 'goods.on.sale':
                        case 'goods.undercarriage':
                        case 'goods.storage.delete':
                            $list = array_merge($list,array_values($message['data']['goodsIds']));
                            break;
                        case 'order.cancel':
                            JdOrderService::cancel($data['data']);
                            break;
                        case 'order.delivery':
                            JdOrderService::send($data['data']);
                            break;
                        default:
                    }
                }
                unlink($path.'/'.$filename);
            }
        }
        if (empty($list)) {
            return;
        }
        $list = array_unique($list);
        $result = array_chunk($list,20);
        foreach ($result as $jd_goods_ids) {
            JdGoodsService::batchUpdate($jd_goods_ids);
        }

    }

}