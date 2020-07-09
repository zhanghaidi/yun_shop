<?php
namespace Yunshop\Commission\services;

use app\common\models\Goods;
use Yunshop\Commission\models\AgentLevel;

/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/3/15
 * Time: 下午3:05
 */
class AgentLevelService
{

    /**
     * @param string $upgradeData
     * @return array
     */
    public static function setUpgradedata($upgradeData = '')
    {
        $upgrade = [];
        if ($upgradeData) {
            $upgrade = unserialize($upgradeData);
        }

        $upgrade_config = static::upgradeConfig();
        $data = [];
        foreach ($upgrade_config as $config) {
            $data['type'][$config['key']] = isset($upgrade[$config['key']]) ? 1 : 0;
            $data['value'][$config['key']] = isset($upgrade[$config['key']]) ? $upgrade[$config['key']] : '';
            if (!empty($upgrade) && $config['key'] == 'goods') {
                $data['goods'] = Goods::getGoodsById($upgrade[$config['key']]);
            }elseif (!empty($upgrade) && $config['key'] == 'many_good') {
                $data['many_good'] = Goods::getGoodsByIds($upgrade[$config['key']]);
            }
        }
        $data['buy_and_sum'] = $upgrade['buy_and_sum'];
        return $data;
    }

    /**
     * @param $upgradeType
     * @param $upgradeValue
     * @return mixed
     */
    public static function addUpgrades($upgradeType, $upgradeValue)
    {

        $upgrades = [];
        if ($upgradeType) {
            foreach ($upgradeType as $key => $type) {
                if ($upgradeValue[$key]) {
                    $upgrades[$key] = $upgradeValue[$key];
                }
                if ($key == 'buy_and_sum' && intval($upgradeValue['buy']) && intval($upgradeValue['sum'])) {
                    $upgrades[$key]['buy'] = intval($upgradeValue['buy']);
                    $upgrades[$key]['sum'] = intval($upgradeValue['sum']);
                }
            }
        }

        return serialize($upgrades);
    }

    /**
     * @param $data
     * @return mixed
     */
    public static function setUpgrades($data)
    {
        $upgrade_config = static::upgradeConfig();
        foreach ($data as &$level) {
            $upgrades = [];
            if ($level->upgraded) {
                $upgrade = unserialize($level->upgraded);
                foreach ($upgrade as $type => $value) {
                    foreach ($upgrade_config as $config) {
                        if ($type == $config['key']) {
                            $upgrades[$type]['type'] = $config['text'];
                            $upgrades[$type]['value'] = $value;
                            $upgrades[$type]['unit'] = $config['unit'];
                            $level->goods = Goods::getGoodsById($value);
                        }
                    }
                    if ($type == 'buy_and_sum') {
                        $upgrades['buy_and_sum']['type'] = '一级客户消费满'.$upgrade['buy_and_sum']['buy'].'元人数达到'.$upgrade['buy_and_sum']['sum'].'个';
                    }
                }
                $level->upgrades = $upgrades;
            }
        }
        unset($level);
        return $data;
    }

    /**
     * @return array
     */
    public static function upgradeConfig()
    {
        return [
            '0' => [
                'key' => 'order_money',
                'text' => '分销订单金额满',
                'unit' => '元'
            ],
            '1' => [
                'key' => 'order_count',
                'text' => '分销订单数量满',
                'unit' => '个'
            ],
            '2' => [
                'key' => 'first_order_money',
                'text' => '一级分销订单金额满',
                'unit' => '元'
            ],
            '3' => [
                'key' => 'first_order_count',
                'text' => '一级分销订单数量满',
                'unit' => '个'
            ],
            '4' => [
                'key' => 'self_buy_money',
                'text' => '自购订单金额满',
                'unit' => '元'
            ],
            '5' => [
                'key' => 'self_buy_count',
                'text' => '自购单数量满',
                'unit' => '个'
            ],
            '6' => [
                'key' => 'lower_count',
                'text' => '粉丝人数满',
                'unit' => '人'
            ],
            '7' => [
                'key' => 'first_lower_count',
                'text' => '一级粉丝人数满',
                'unit' => '人'
            ],
            '8' => [
                'key' => 'lower_agent_count',
                'text' => '粉丝分销商人数满',
                'unit' => '人'
            ],
            '9' => [
                'key' => 'first_lower_agent_count',
                'text' => '一级粉丝分销商人数满',
                'unit' => '人'
            ],
            '10' => [
                'key' => 'settle_money',
                'text' => '结算佣金总额满',
                'unit' => '元'
            ],
            '11' => [
                'key' => 'goods',
                'text' => '购买指定商品',
                'unit' => ''
            ],
            '12' => [
                'key' => 'many_good',
                'text' => '购买指定商品之一',
                'unit' => ''
            ],
            '13' => [
                'key' => 'self_order_after',
                'text' => '自购订单付款后or完成后',
                'unit' => ''
            ],
        ];

    }
    
    public function getAgentLevels()
    {
        $agentLevel = AgentLevel::getLevels()->get();

        return $agentLevel;
    }
}