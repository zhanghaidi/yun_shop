<?php
/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/3/24
 * Time: 下午8:14
 */

namespace Yunshop\Commission\admin;


use app\common\components\BaseController;
use EasyWeChat\Support\Log;
use Yunshop\Commission\models\Agents;
use Yunshop\Commission\models\CommissionOrder;
use Yunshop\Commission\models\Income;
use Yunshop\Commission\services\UpgradeService;

class TimedTaskController extends BaseController
{
    /**
     * 佣金结算处理
     */
    public function handle()
    {
//        (new \Yunshop\Commission\services\TimedTaskService)->handle();
        
        $config = \app\backend\modules\income\Income::current()->getItem('commission');
        $requestOrder = CommissionOrder::getStatement()->toArray();
        if ($requestOrder) {
            $times = time();

            $request = static::updatedStatement($times);
            foreach ($requestOrder as $item) {
                //更新累计佣金
                $requestAgent = static::updateCommission($item);
                //插入收入
                $requestAgent = static::addIncome($item, $times, $config);
            }


        }
    }

    /**
     * @param $times
     * @return mixed
     */
    public function updatedStatement($times)
    {
        $request = CommissionOrder::updatedStatement($times);
        if ($request) {
            \Log::info($times . ":结算" . $request . "条佣金订单.");
        }
        return $request;
    }

    /**
     * @param $item
     * @return mixed
     */
    public function updateCommission($item)
    {
        $requestAgent = Agents::updateCommission($item['commission'], $item['member_id'], 'plus');
        
        if ($requestAgent) {
            \Log::info("ID:" . $item['member_id'] . "获得" . $item['commission'] . "累计佣金.");
            UpgradeService::commission($item['member_id']);
        }
        return $requestAgent;
    }

    /**
     * @param $item
     * @param $times
     * @param $config
     * @return bool
     */
    public function addIncome($item, $times, $config)
    {

        $data = [
            'commission' => [
                'title' => '分销',
                'data' => [
                    '0' => [
                        'title' => '佣金',
                        'value' => $item['commission'] . "元",
                    ],
                    '1' => [
                        'title' => '分销层级',
                        'value' => $item['hierarchy'] . "级",
                    ],
                    '2' => [
                        'title' => '佣金比例',
                        'value' => $item['commission_rate'] . "%",
                    ],
                    '3' => [
                        'title' => '结算天数',
                        'value' => $item['settle_days'] . "天",
                    ],
                    '4' => [
                        'title' => '佣金方式',
                        'value' => $item['formula'],
                    ],
                    '5' => [
                        'title' => '分佣时间',
                        'value' => date("Y-m-d H:i:s", $item['recrive_at']),
                    ],
                    '6' => [
                        'title' => '结算时间',
                        'value' => date("Y-m-d H:i:s", $times)
                    ],
                ]

            ],
            'order' => [
                'title' => '订单',
                'data' => [
                    '0' => [
                        'title' => '订单号',
                        'value' => $item['order']['order_sn'],
                    ],
                    '1' => [
                        'title' => '状态',
                        'value' => $item['order']['status_name'],
                    ],
                ]
            ]
        ];
        $data['goods']['title'] = '商品';
        foreach ($item['order_goods'] as $key => $order_good) {

            $data['goods']['data'][$key][] = [
                'title' => '名称',
                'value' => $order_good['title'],
            ];
            $data['goods']['data'][$key][] = [
                'title' => '金额',
                'value' => $order_good['goods_price'] . "元",
            ];
        }

        //收入明细数据
        $incomeDetail = json_encode($data);

        //收入数据
        $incomeData = [
            'uniacid' => \YunShop::app()->uniacid,
            'member_id' => $item['member_id'],
            'incometable_type' => $config['class'],
            'incometable_id' => $item['id'],
            'type_name' => $config['type_name'],
            'amount' => $item['commission'],
            'status' => '0',
            'detail' => $incomeDetail,
            'create_month' => date("Y-m"),
        ];
        //插入收入
        $incomeModel = new Income();
        $incomeModel->setRawAttributes($incomeData);
        $requestIncome = $incomeModel->save();
        if ($requestIncome) {
            \Log::info($times . ":收入统计插入数据!");
        }
        return $requestIncome;
    }
}