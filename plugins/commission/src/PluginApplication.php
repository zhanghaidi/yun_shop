<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/12/24
 * Time: 3:38 PM
 */

namespace Yunshop\Commission;

use Config;
use Yunshop\Commission\Listener\MemberDelListener;
use Yunshop\Commission\Listener\MemberRelationListener;
use Yunshop\Commission\Listener\OrderCanceledListener;
use Yunshop\Commission\Listener\OrderCreatedListener;
use Yunshop\Commission\Listener\OrderPaidListener;
use Yunshop\Commission\Listener\OrderReceiveListener;
use Yunshop\Commission\Listener\RegisterByAgentListener;
use Yunshop\Commission\Listener\WithdrawApplyListener;
use Yunshop\Commission\Listener\WithdrawAuditListener;
use Yunshop\Commission\Listener\WithdrawPayedListener;
use Yunshop\Commission\models\AgentLevel;
use Yunshop\Commission\models\Agents;

class PluginApplication extends \app\common\services\PluginApplication
{
    public function getIncomePageItems()
    {
        return ['commission' => ['class' => 'Yunshop\Commission\Frontend\Services\IncomePageService']];
    }

    public function getIncomeItems()
    {
        return ['commission' => [
            'title' => trans('Yunshop\Commission::index.commission'),
            'type' => 'commission',
            'class' => 'Yunshop\Commission\models\CommissionOrder',
            'name' => 'updatedWithdraw',
            'value' => 'withdraw',
        ]];
    }

    public function getTemplateItems()
    {
        return [
            'commission_become_agent' => [
                'title' => trans('Yunshop\Commission::index.title') . "(成为分销商通知)",
                'subtitle' => '成为分销商通知',
                'value' => 'commission_become_agent',
                'param' => [
                    '昵称', '时间'
                ]
            ], 'commission_created_order' => [
                'title' => trans('Yunshop\Commission::index.title') . "(下级下单通知)",
                'subtitle' => '下级下单通知',
                'value' => 'commission_created_order',
                'param' => [
                    '昵称', '下级昵称', '订单编号', '订单金额', '商品详情', '佣金金额', '层级', '时间'
                ]
            ], 'commission_receive_order' => [
                'title' => trans('Yunshop\Commission::index.title') . "(下级确认收货通知)",
                'subtitle' => '下级确认收货通知',
                'value' => 'commission_receive_order',
                'param' => [
                    '昵称', '下级昵称', '订单编号', '订单金额', '商品详情', '佣金金额', '层级', '时间'
                ]
            ], 'commission_upgrade' => [
                'title' => trans('Yunshop\Commission::index.title') . "(分销商等级升级通知)",
                'subtitle' => '分销商等级升级通知',
                'value' => 'commission_upgrade',
                'param' => [
                    '昵称', '旧等级', '旧一级分销比例', '旧二级分销比例', '新等级', '新一级分销比例', '新二级分销比例', '时间'
                ]
            ], 'commission_statement' => [
                'title' => trans('Yunshop\Commission::index.title') . "(分销佣金结算通知)",
                'subtitle' => '分销佣金结算通知',
                'value' => 'commission_statement',
                'param' => [
                    '昵称', '结算时间', '佣金金额'
                ]
            ]
        ];
    }

    public function getWidgetItems()
    {
        return ['goods.tab_commission' => [
            'title' => trans('Yunshop\Commission::index.title'),
            'class' => 'Yunshop\Commission\widgets\CommissionWidget'
        ], 'withdraw.tab_income' => [
            'title' => trans('Yunshop\Commission::index.tab_income'),
            'class' => 'Yunshop\Commission\widgets\CommissionWithdrawWidget',
            'name' => 'updatedWithdraw'
        ]];
    }

    protected function setConfig()
    {
        \app\common\modules\shop\ShopConfig::current()->set('manual_arr.agent_and_agent_level', [
            'uidColumn' => 'member_id',
            'levelNameColumn' => 'name',
            'relationsColumn' => 'agent_level_id',
            'enableLevel' => true,
            'type_name' => '分销商',
            'role_type' => 'agent_and_agent_level'
        ]);

        \app\common\modules\shop\ShopConfig::current()->set('manual_arr_cfg.agent_and_agent_level', [
            'roleTableClass' => Agents::class,
            'levelTableClass' => AgentLevel::class,
        ]);

        \app\common\modules\shop\ShopConfig::current()->set('observer.goods.commission', [
            'class' => 'Yunshop\Commission\models\Commission',
            'function_validator' => 'relationValidator',
            'function_save' => 'relationSave'
        ]);


        \app\common\modules\shop\ShopConfig::current()->set('plugin.commission', [
            'title' => trans('Yunshop\Commission::index.title'),
            'ico' => 'icon-fenxiao01',
            'type' => 'commission',
            'class' => 'Yunshop\Commission\models\CommissionOrder',
            'order_class' => 'app\common\models\finance\IncomeOrder',
            'agent_class' => 'Yunshop\Commission\models\Agents',
            'agent_name' => 'getAgentByMemberId',
        ]);

        \app\common\modules\shop\ShopConfig::current()->set('notice.commission', [
//        'commission_order_title', // 下级下单通知
//        'commission_order_finish_title', // 下级确认收货通知
//        'commission_upgrade_title', // 分销商等级升级通知
//        'commission_manage_title', // 分销管理奖获得通知
        ]);
        \app\common\modules\shop\ShopConfig::current()->set('coupon.commission', [
            'name' => '按分销商等级发送',
            'list' => [
                'class' => '\Yunshop\Commission\services\AgentLevelService',
                'function' => 'getAgentLevels'
            ],
            'member' => [
                'class' => '\Yunshop\Commission\services\AgentService',
                'function' => 'getMemberIdByLevelId'
            ]
        ]);
    }

    protected function setMenuConfig()
    {
        $is_expand = \Setting::get('plugin.commission_expand')['is_expand'];
        \app\backend\modules\menu\Menu::current()->setPluginMenu('commission', [
            'name' => '推客',
            'type' => 'dividend',
            'url' => 'plugin.commission.admin.agent',
            'url_params' => '',
            'permit' => 1,
            'menu' => 1,
            'top_show' => 0,
            'left_first_show' => 0,
            'left_second_show' => 1,
            'icon' => 'fa-share-alt',
            'list_icon' => 'commission',
            'parents' => [],
            'child' => [
                'commission_set' => [
                    'name' => '分销设置',
                    'permit' => 1,
                    'menu' => 1,
                    'icon' => 'fa-gear',
                    'url' => 'plugin.commission.admin.set.index',
                    'url_params' => '',
                    'parents' => ['commission'],
                    'child' => [
                        'commission_set_index' => [
                            'name' => '分销设置',
                            'permit' => 1,
                            'menu' => 0,
                            'icon' => 'fa-gear',
                            'url' => 'plugin.commission.admin.set.index',
                            'url_params' => '',
                            'parents' => ['commission', 'commission_set'],
                        ],
                        'commission_notice_index' => [
                            'name' => '消息设置',
                            'permit' => 1,
                            'menu' => 0,
                            'icon' => 'fa-gear',
                            'url' => 'plugin.commission.admin.set.notice',
                            'url_params' => '',
                            'parents' => ['commission', 'commission_set'],
                        ],
                        'commission_expand_index' => [
                            'name' => '定制设置',
                            'permit' => 1,
                            'menu' => 0,
                            'icon' => 'fa-gear',
                            'url' => 'plugin.commission.admin.set.expand',
                            'url_params' => '',
                            'parents' => ['commission', 'commission_set'],
                        ],
                        'commission_set_identical' => [
                            'name' => '数据同步',
                            'permit' => 1,
                            'menu' => 0,
                            'icon' => 'fa-gear',
                            'url' => 'plugin.commission.admin.data-identical.index',
                            'url_params' => '',
                            'parents' => ['commission', 'commission_set'],
                        ],
                    ]
                ],
                'commission_level' => [
                    'name' => '分销商等级',
                    'permit' => 1,
                    'menu' => 1,
                    'icon' => 'fa-sliders',
                    'url' => 'plugin.commission.admin.level.index',
                    'url_params' => '',
                    'parents' => ['commission'],
                    'child' => [
                        'level_add' => [
                            'name' => '添加等级',
                            'permit' => 1,
                            'menu' => 0,
                            'icon' => '',
                            'url' => 'plugin.commission.admin.level.add',
                            'url_params' => '',
                            'parents' => ['commission', 'commission_level'],
                            'child' => []
                        ],
                        //todo 完善修改删除路由权限控制
                        'level_edit' => [
                            'name' => '编辑等级',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.commission.admin.level.edit',
                            'parents' => ['commission', 'commission_level']
                        ],
                        'level_deleted' => [
                            'name' => '删除等级',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.commission.admin.level.deleted',
                            'parents' => ['commission', 'commission_level']
                        ],
                    ]
                ],
                'commission_agent' => [
                    'name' => '分销商管理',
                    'permit' => 1,
                    'menu' => 1,
                    'icon' => 'fa-share-alt-square',
                    'url' => 'plugin.commission.admin.agent.index',
                    'url_params' => '',
                    'parents' => ['commission'],
                    'child' => [
                        //todo 完善路由权限控制
                        'agent_index' => ['name' => '分销商列表', 'permit' => 1, 'menu' => 0, 'url' => 'plugin.commission.admin.agent.index', 'parents' => ['commission', 'commission_agent']],
                        'agent_export' => ['name' => '分销商导出', 'permit' => 1, 'menu' => 0, 'url' => 'plugin.commission.admin.agent.export', 'parents' => ['commission', 'commission_agent']],
                        'agent_detail' => ['name' => '分销商详细信息', 'permit' => 1, 'menu' => 0, 'url' => 'plugin.commission.admin.agent.detail', 'parents' => ['commission', 'commission_agent']],
                        'agent_lower' => ['name' => '分销商下线', 'permit' => 1, 'menu' => 0, 'url' => 'plugin.commission.admin.agent.lower', 'parents' => ['commission', 'commission_agent']],
                        'agent_black' => ['name' => '加入黑名单', 'permit' => 1, 'menu' => 0, 'url' => 'plugin.commission.admin.agent.black', 'parents' => ['commission', 'commission_agent']],
                        'agent_deleted' => ['name' => '删除分销商', 'permit' => 1, 'menu' => 0, 'url' => 'plugin.commission.admin.agent.deleted', 'parents' => ['commission', 'commission_agent']],
                    ]
                ],
                'commission_order' => [
                    'name' => '分销订单管理',
                    'permit' => 1,
                    'menu' => 1,
                    'icon' => 'fa-files-o',
                    'url' => 'plugin.commission.admin.commission-order.index',
                    'url_params' => '',
                    'parents' => ['commission'],
                    'child' => [
                        //todo 完善路由权限控制
                        'order.index' => ['name' => '分销订单列表', 'permit' => 1, 'menu' => 0, 'url' => 'plugin.commission.admin.commission-order.index', 'parents' => ['commission', 'commission_order']],
                        'order.export' => ['name' => '分销订单导出', 'permit' => 1, 'menu' => 0, 'url' => 'plugin.commission.admin.commission-order.export', 'parents' => ['commission', 'commission_order']],
                        'order.edit' => ['name' => '编辑佣金', 'permit' => 1, 'menu' => 0, 'url' => 'plugin.commission.admin.commission-order.edit', 'parents' => ['commission', 'commission_order']],
                        'order.details' => ['name' => '佣金详情', 'permit' => 1, 'menu' => 0, 'url' => 'plugin.commission.admin.commission-order.details', 'parents' => ['commission', 'commission_order']]
                    ]
                ],
                'commission_expand' => [
                    'name' => '分销定制设置',
                    'permit' => 1,
                    'menu' => $is_expand,
                    'icon' => 'fa-files-o',
                    'url' => 'plugin.commission.admin.expand-set.index',
                    'url_params' => '',
                    'parents' => ['commission'],
                    'child' => [
                        'order.index' => ['name' => '分销定制设置', 'permit' => 1, 'menu' => 0, 'url' => 'plugin.commission.admin.expand-set.index', 'parents' => ['commission', 'commission_expand']],
                    ]
                ],
                /*'commission_operation' => [
                    'name' => '明细',
                    'permit' => 1,
                    'menu' => 1,
                    'icon' => 'fa-files-o',
                    'url' => 'plugin.commission.admin.operation.index',
                    'url_params' => '',
                    'parents' => ['commission'],
                    'child' => [

                    ]
                ],*/
//            'commission_manage'     => [
//                'name'              => '分销管理奖',
//                'permit'            => 1,
//                'menu'              => 1,
//                'icon'              => 'fa-trophy',
//                'url'               => 'plugin.commission.admin.commission-manage.index',
//                'url_params'        => '',
//                'parents'           => ['commission'],
//                'child'             => [
//    //todo 完善路由权限控制
//                    'order.index' => ['name' => '管理奖列表', 'permit' => 1, 'menu' => 0, 'parents' => ['commission', 'commission_manage']],
//                ]
//            ],


            ]

        ]);
    }

    public function boot()
    {

        $events = app('events');
        \Event::listen('cron.collectJobs', function () {
            \Cron::add('Commission', '*/10 * * * *', function () {
                (new \Yunshop\Commission\services\TimedTaskService)->handle();
                return;
            });

        });
        /**
         * 创建订单
         * OrderCreatedListener
         */
        $events->subscribe(OrderCreatedListener::class);

        /**
         * 支付完成
         * OrderPaidListener
         */
        $events->subscribe(OrderPaidListener::class);

        /**
         * 订单收货
         * OrderReceiveListener
         *
         */
        $events->subscribe(OrderReceiveListener::class);

        /**
         * 订单取消
         * OrderCanceledListener
         *
         */
        $events->subscribe(OrderCanceledListener::class);

        /**
         * 注册会员成功
         */
        $events->subscribe(RegisterByAgentListener::class);

        /**
         * 会员获得推广资格
         * MemberRelationEvent
         */
        //不跟推广资格同时进行
        $events->subscribe(MemberRelationListener::class);

        /**
         * 删除会员 - 删除分销商
         * MemberDelEvent
         */
        $events->subscribe(MemberDelListener::class);

        /**
         * 收入提现申请 监听者
         */
        $events->subscribe(WithdrawApplyListener::class);

        /**
         * 收入提现成审核监听者
         */
        $events->subscribe(WithdrawAuditListener::class);
        /**
         *
         * 收入提现成功监听者
         */
        $events->subscribe(WithdrawPayedListener::class);

    }

}