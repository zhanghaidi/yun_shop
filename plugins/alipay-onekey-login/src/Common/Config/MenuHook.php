<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/6/26 上午11:18
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace Yunshop\Love\Common\Config;


use Yunshop\Love\Common\Services\CommonService;

class MenuHook
{
    public static function menu()
    {
        $love = CommonService::getLoveName();
        return [
            'name'             => $love,
            'type'             => 'marketing',
            'url'              => 'plugin.love.Backend.Modules.Member.Controllers.member-love',
            'url_params'       => '',
            'permit'           => 1,
            'menu'             => 1,
            'top_show'         => 0,
            'left_first_show'  => 0,
            'left_second_show' => 1,
            'icon'             => 'fa-heart-o',
            'list_icon'        => 'love',
            'parents'          => [],
            'child'            => [
                'love_member'     => [
                    'name'       => $love . '管理',
                    'permit'     => 1,
                    'menu'       => 1,
                    'icon'       => 'fa-heart',
                    'url'        => 'plugin.love.Backend.Modules.Member.Controllers.member-love.index',
                    'url_params' => '',
                    'parents'    => ['love'],
                    'child'      => [

                        'love_recharge' => [
                            'name'       => '会员充值',
                            'permit'     => 1,
                            'menu'       => 1,
                            'icon'       => '',
                            'item'       => 'love_member_recharge',
                            'url'        => 'plugin.love.Backend.Modules.Love.Controllers.recharge.index',
                            'url_params' => '',
                            'parents'    => ['love', 'love_member'],
                            'child'      => []
                        ],

                        'love_member_timing_recharge' => [
                            'name'       => '定时充值',
                            'permit'     => 1,
                            'menu'       => 0,
                            'icon'       => '',
                            'item'       => 'love_member_timing_recharge',
                            'url'        => 'plugin.love.Backend.Modules.Love.Controllers.timing-recharge.index',
                            'url_params' => '',
                            'parents'    => ['love', 'love_member'],
                        ],
                    ]
                ],
                'love_timing_log' => [
                    'name'       => '定期充明细',
                    'permit'     => 1,
                    'menu'       => 1,
                    'icon'       => 'fa-gratipay',
                    'url'        => 'plugin.love.Backend.Modules.Love.Controllers.timing-log.index',
                    'url_params' => '',
                    'parents'    => ['love'],
                    'child'      => [
                        'love_timing_log_index'  => [
                            'name'       => '浏览记录',
                            'permit'     => 1,
                            'menu'       => 0,
                            'icon'       => 'fa-eye',
                            'item'       => 'love_timing_log_index',
                            'url'        => 'plugin.love.Backend.Modules.Love.Controllers.timing-log.index',
                            'url_params' => '',
                            'parents'    => ['love', 'love_timing_log'],
                        ],
                        'love_timing_log_detail' => [
                            'name'       => '详情',
                            'permit'     => 1,
                            'menu'       => 0,
                            'icon'       => '',
                            'item'       => 'love_timing_log_detail',
                            'url'        => 'plugin.love.Backend.Modules.Love.Controllers.timing-log.detail',
                            'url_params' => '',
                            'parents'    => ['love', 'love_timing_log'],
                        ],
                    ]
                ],
                'love_record'     => [
                    'name'       => $love . '明细',
                    'permit'     => 1,
                    'menu'       => 1,
                    'icon'       => 'fa-gratipay',
                    'url'        => 'plugin.love.Backend.Modules.Love.Controllers.change-records.index',
                    'url_params' => '',
                    'parents'    => ['love'],
                    'child'      => [
                        'love_record_see'    => [
                            'name'       => '浏览记录',
                            'permit'     => 1,
                            'menu'       => 0,
                            'icon'       => 'fa-eye',
                            'url'        => 'plugin.love.Backend.Modules.Love.Controllers.change-records.index',
                            'url_params' => '',
                            'parents'    => ['love', 'love_record'],
                            'child'      => []
                        ],
                        'love_record_export' => [
                            'name'       => '导出 Excel',
                            'permit'     => 1,
                            'menu'       => 0,
                            'icon'       => '',
                            'url'        => 'plugin.love.Backend.Modules.Love.Controllers.change-records.export',
                            'url_params' => '',
                            'parents'    => ['love', 'love_record'],
                            'child'      => []
                        ],
                    ]
                ],
                'love_trading'    => [
                    'name'       => $love . '交易',
                    'permit'     => 1,
                    'menu'       => 1,
                    'icon'       => 'fa-file-text',
                    'url'        => 'plugin.love.Backend.Modules.Love.Controllers.trading-love.index',
                    'url_params' => '',
                    'parents'    => ['love'],
                    'child'      => [
                        'love_trading_see' => [
                            'name'       => '浏览记录',
                            'permit'     => 1,
                            'menu'       => 0,
                            'icon'       => 'fa-eye',
                            'url'        => 'plugin.love.Backend.Modules.Love.Controllers.trading-love.index',
                            'url_params' => '',
                            'parents'    => ['love', 'love_trading'],
                        ],
                    ]
                ],

                'love_recharge_record' => [
                    'name'       => '充值记录',
                    'permit'     => 1,
                    'menu'       => 1,
                    'icon'       => 'fa-file-text',
                    'item'       => 'love_recharge_record',
                    'url'        => 'plugin.love.Backend.Modules.Love.Controllers.recharge-records.index',
                    'url_params' => '',
                    'parents'    => ['love'],
                    'child'      => [
                        'love_recharge_record_see' => [
                            'name'       => '浏览记录',
                            'permit'     => 1,
                            'menu'       => 0,
                            'icon'       => 'fa-eye',
                            'item'       => 'love_recharge_record_see',
                            'url'        => 'plugin.love.Backend.Modules.Love.Controllers.trading-love.index',
                            'url_params' => '',
                            'parents'    => ['love', 'love_recharge_record'],
                        ],
                    ]
                ],

                'activation_record' => [
                    'name'       => '激活记录',
                    'permit'     => 1,
                    'menu'       => 1,
                    'icon'       => 'fa-file-text',
                    'url'        => 'plugin.love.Backend.Modules.Love.Controllers.activation-records.index',
                    'url_params' => '',
                    'parents'    => ['love'],
                    'child'      => [
                        'activation_record_see' => [
                            'name'       => '浏览记录',
                            'permit'     => 1,
                            'menu'       => 0,
                            'icon'       => 'fa-eye',
                            'url'        => 'plugin.love.Backend.Modules.Love.Controllers.activation-records.index',
                            'url_params' => '',
                            'parents'    => ['love', 'activation_record'],
                        ],
                        'activation_record_detail' => [
                            'name'       => '查看详情',
                            'permit'     => 1,
                            'menu'       => 0,
                            'icon'       => '',
                            'url'        => 'plugin.love.Backend.Modules.Love.Controllers.activation-record-detail.index',
                            'url_params' => '',
                            'parents'    => ['love', 'activation_record'],
                        ],
                        'activationRecordManualActivation' => [
                            'name'       => '手动激活',
                            'permit'     => 1,
                            'menu'       => 0,
                            'icon'       => '',
                            'url'        => 'plugin.love.Backend.Controllers.activation-set.activation',
                            'url_params' => '',
                            'parents'    => ['love', 'activation_record'],
                        ],
                    ]
                ],
                'love_return'       => [
                    'name'       => '返现记录',
                    'permit'     => 1,
                    'menu'       => 1,
                    'icon'       => 'fa-file-text',
                    'url'        => 'plugin.love.Backend.Modules.Love.Controllers.love-return-log.index',
                    'url_params' => '',
                    'parents'    => ['love'],
                    'child'      => [
                        'love_return_see' => [
                            'name'       => '浏览记录',
                            'permit'     => 1,
                            'menu'       => 0,
                            'icon'       => 'fa-eye',
                            'url'        => 'plugin.love.Backend.Modules.Love.Controllers.love-return-log.index',
                            'url_params' => '',
                            'parents'    => ['love', 'love_return'],
                            'child'      => []
                        ],
                    ]
                ],

                'love_dividend_log' => [
                    'name'       => '分红统计记录',
                    'permit'     => 1,
                    'menu'       => 1,
                    'icon'       => 'fa-file-text',
                    'url'        => 'plugin.love.Backend.Modules.Love.Controllers.dividend-log.index',
                    'url_params' => '',
                    'parents'    => ['love'],
                    'child'      => [
                        'love_dividend_log.list'   => [
                            'name'       => '浏览记录',
                            'permit'     => 1,
                            'menu'       => 0,
                            'icon'       => 'fa-eye',
                            'item'       => 'love_dividend_log.list',
                            'url'        => 'plugin.love.Backend.Modules.Love.Controllers.dividend-log.index',
                            'url_params' => '',
                            'parents'    => ['love', 'love_dividend_log'],
                        ],
                        'love_dividend_log.export' => [
                            'name'       => '导出excel',
                            'permit'     => 1,
                            'menu'       => 0,
                            'icon'       => '',
                            'item'       => 'love_dividend_log.export',
                            'url'        => 'plugin.love.Backend.Modules.Love.Controllers.dividend-log.export',
                            'url_params' => '',
                            'parents'    => ['love', 'love_dividend_log'],
                        ],
                    ]
                ],

                'love_set' => [
                    'name'       => '基础设置',
                    'permit'     => 1,
                    'menu'       => 1,
                    'icon'       => 'fa-gears',
                    'url'        => 'plugin.love.Backend.Controllers.base-set.see',
                    'url_params' => '',
                    'parents'    => ['love'],
                    'child'      => [
                        'love_set_see'        => [
                            'name'       => '查看设置',
                            'permit'     => 1,
                            'menu'       => 0,
                            'icon'       => 'fa-eye',
                            'url'        => 'plugin.love.Backend.Controllers.base-set.see',
                            'url_params' => '',
                            'parents'    => ['love', 'love_set'],
                        ],
                        'love_set_store'      => [
                            'name'       => '保存设置',
                            'permit'     => 1,
                            'menu'       => 0,
                            'icon'       => 'fa-floppy-o',
                            'url'        => 'plugin.love.Backend.Controllers.base-set.store',
                            'url_params' => '',
                            'parents'    => ['love', 'love_set'],
                        ],
                        'loveRechargeSet'     => [
                            'name'       => '充值设置',
                            'url'        => 'plugin.love.Backend.Controllers.recharge-set.see',
                            'url_params' => '',
                            'permit'     => 1,
                            'menu'       => 0,
                            'icon'       => '',
                            'sort'       => 1,
                            'item'       => 'loveRechargeSet',
                            'parents'    => ['love', 'love_set'],
                            'child'      => [
                                'loveRechargeSetSee'   => [
                                    'name'       => '查看设置',
                                    'url'        => 'plugin.love.Backend.Controllers.recharge-set.see',
                                    'url_params' => '',
                                    'permit'     => 1,
                                    'menu'       => 0,
                                    'icon'       => '',
                                    'sort'       => 1,
                                    'item'       => 'loveRechargeSetSee',
                                    'parents'    => ['love', 'love_set', 'loveRechargeSet'],
                                ],
                                'loveRechargeSetStore' => [
                                    'name'       => '保存设置',
                                    'url'        => 'plugin.love.Backend.Controllers.recharge-set.store',
                                    'url_params' => '',
                                    'permit'     => 1,
                                    'menu'       => 0,
                                    'icon'       => '',
                                    'sort'       => 1,
                                    'item'       => 'loveRechargeSetStore',
                                    'parents'    => ['love', 'love_set', 'loveRechargeSet'],
                                ]
                            ]
                        ],
                        'love_award_set'      => [
                            'name'       => '奖励设置',
                            'permit'     => 1,
                            'menu'       => 0,
                            'icon'       => 'fa-bookmark',
                            'url'        => 'plugin.love.Backend.Controllers.award-set.see',
                            'url_params' => '',
                            'parents'    => ['love', 'love_set'],
                            'child'      => [
                                'award_set_see'   => [
                                    'name'       => '查看设置',
                                    'permit'     => 1,
                                    'menu'       => 0,
                                    'icon'       => 'fa-eye',
                                    'url'        => 'plugin.love.Backend.Controllers.award-set.see',
                                    'url_params' => '',
                                    'parents'    => ['love', 'love_set', 'love_award_set'],
                                ],
                                'award_set_store' => [
                                    'name'       => '保存设置',
                                    'permit'     => 1,
                                    'menu'       => 0,
                                    'icon'       => 'fa-floppy-o',
                                    'url'        => 'plugin.love.Backend.Controllers.award-set.store',
                                    'url_params' => '',
                                    'parents'    => ['love', 'love_set', 'love_award_set'],
                                ],
                            ],
                        ],
                        'love_activation_set' => [
                            'name'       => '激活设置',
                            'permit'     => 1,
                            'menu'       => 0,
                            'icon'       => 'fa-bolt',
                            'url'        => 'plugin.love.Backend.Controllers.activation-set.see',
                            'url_params' => '',
                            'parents'    => ['love', 'love_set'],
                            'child'      => [
                                'love_activation_set_see'   => [
                                    'name'       => '查看设置',
                                    'permit'     => 1,
                                    'menu'       => 0,
                                    'icon'       => 'fa-eye',
                                    'url'        => 'plugin.love.Backend.Controllers.activation-set.see',
                                    'url_params' => '',
                                    'parents'    => ['love', 'love_set', 'love_activation_set'],
                                ],
                                'love_activation_set_store' => [
                                    'name'       => '保存设置',
                                    'permit'     => 1,
                                    'menu'       => 0,
                                    'icon'       => 'fa-floppy-o',
                                    'url'        => 'plugin.love.Backend.Controllers.activation-set.store',
                                    'url_params' => '',
                                    'parents'    => ['love', 'love_set', 'love_activation_set'],
                                ],
                            ],
                        ],
                        'love_notice_set'     => [
                            'name'       => '消息设置',
                            'permit'     => 1,
                            'menu'       => 0,
                            'icon'       => 'fa-bullhorn',
                            'url'        => 'plugin.love.Backend.Controllers.notice-set.see',
                            'url_params' => '',
                            'parents'    => ['love', 'love_set'],
                            'child'      => [
                                'activation_set_see'   => [
                                    'name'       => '查看设置',
                                    'permit'     => 1,
                                    'menu'       => 0,
                                    'icon'       => 'fa-eye',
                                    'url'        => 'plugin.love.Backend.Controllers.notice-set.see',
                                    'url_params' => '',
                                    'parents'    => ['love', 'love_set', 'notice_set'],
                                ],
                                'activation_set_store' => [
                                    'name'       => '保存设置',
                                    'permit'     => 1,
                                    'menu'       => 0,
                                    'icon'       => 'fa-floppy-o',
                                    'url'        => 'plugin.love.Backend.Controllers.notice-set.store',
                                    'url_params' => '',
                                    'parents'    => ['love', 'love_set', 'notice_set'],
                                ],
                            ],
                        ],

                        'love_trading_set' => [
                            'name'       => '交易设置',
                            'permit'     => 1,
                            'menu'       => 0,
                            'icon'       => 'fa-bullhorn',
                            'url'        => 'plugin.love.Backend.Controllers.trading-set.see',
                            'url_params' => '',
                            'parents'    => ['love', 'love_set'],
                            'child'      => [
                                'activation_set_see'   => [
                                    'name'       => '查看设置',
                                    'permit'     => 1,
                                    'menu'       => 0,
                                    'icon'       => 'fa-eye',
                                    'url'        => 'plugin.love.Backend.Controllers.notice-set.see',
                                    'url_params' => '',
                                    'parents'    => ['love', 'love_set', 'love_trading_set'],
                                ],
                                'activation_set_store' => [
                                    'name'       => '保存设置',
                                    'permit'     => 1,
                                    'menu'       => 0,
                                    'icon'       => 'fa-floppy-o',
                                    'url'        => 'plugin.love.Backend.Controllers.notice-set.store',
                                    'url_params' => '',
                                    'parents'    => ['love', 'love_set', 'love_trading_set'],
                                ],
                            ],
                        ],

                        'love_return_set' => [
                            'name'       => '返现设置',
                            'permit'     => 1,
                            'menu'       => 0,
                            'icon'       => 'fa-bullhorn',
                            'url'        => 'plugin.love.Backend.Controllers.notice-set.index',
                            'url_params' => '',
                            'parents'    => ['love', 'love_set'],
                            'child'      => [
                                'activation_set_see' => [
                                    'name'       => '查看编辑',
                                    'permit'     => 1,
                                    'menu'       => 0,
                                    'icon'       => 'fa-eye',
                                    'url'        => 'plugin.love.Backend.Controllers.return-set.index',
                                    'url_params' => '',
                                    'parents'    => ['love', 'love_set', 'love_return_set'],
                                ],
                            ],
                        ],

                        'love_withdraw_set' => [
                            'name'       => '提现设置',
                            'permit'     => 1,
                            'menu'       => 0,
                            'icon'       => 'fa-bookmark',
                            'url'        => 'plugin.love.Backend.Controllers.withdraw-set.see',
                            'url_params' => '',
                            'parents'    => ['love', 'love_set'],
                            'child'      => [
                                'award_set_see'   => [
                                    'name'       => '查看设置',
                                    'permit'     => 1,
                                    'menu'       => 0,
                                    'icon'       => 'fa-eye',
                                    'url'        => 'plugin.love.Backend.Controllers.withdraw-set.see',
                                    'url_params' => '',
                                    'parents'    => ['love', 'love_set', 'love_withdraw_set'],
                                ],
                                'award_set_store' => [
                                    'name'       => '保存设置',
                                    'permit'     => 1,
                                    'menu'       => 0,
                                    'icon'       => 'fa-floppy-o',
                                    'url'        => 'plugin.love.Backend.Controllers.withdraw-set.store',
                                    'url_params' => '',
                                    'parents'    => ['love', 'love_set', 'love_withdraw_set'],
                                ],
                            ],
                        ],

                        'love_dividend_set' => [
                            'name'       => '分红设置',
                            'permit'     => 1,
                            'menu'       => 0,
                            'icon'       => 'fa-bookmark',
                            'url'        => 'plugin.love.Backend.Controllers.dividend-set.index',
                            'url_params' => '',
                            'parents'    => ['love', 'love_set'],
                            'child'      => [
                                'dividend_set_index' => [
                                    'name'       => '查看设置',
                                    'permit'     => 1,
                                    'menu'       => 0,
                                    'icon'       => 'fa-eye',
                                    'url'        => 'plugin.love.Backend.Controllers.dividend-set.index',
                                    'url_params' => '',
                                    'parents'    => ['love', 'love_set', 'love_dividend_set'],
                                ],
                            ],
                        ],

                    ]
                ],


                //一级菜单结束
            ]
            //主菜单结束
        ];
    }

}