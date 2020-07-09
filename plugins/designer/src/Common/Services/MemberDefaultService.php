<?php
/**
 * Created by PhpStorm.
 * User: win 10
 * Date: 2019/7/19
 * Time: 11:16
 */

namespace Yunshop\Designer\Common\Services;

use app\common\components\BaseController;
use app\common\services\member\MemberCenterService;

class MemberDefaultService
{
    public function index()
    {
        $arr = (new MemberCenterService())->getMemberData(0);
        return json_encode($this->defaultPageInfo($arr));
    }

    private function defaultPageInfo($arr)
    {
        $memberCenter = [
            [
                'id'     => 'ME0000000000',
                'temp'   => 'membercenter',
                'params' => [
                    'memberportrait'    => '1',
                    'membernamecolor'   => '#333',
                    'memberbg'          => '1',
                    'memberbgcolor'     => '#FFFFFF',
                    'bgimg'             => '',
                    'memberID'          => '1',
                    'memberlevel'       => '1',
                    'memberlevelstyle'  => '1',
                    'memberleveltype'   => '1',
                    'memberlevelcolor'  => '#E6C776',
                    'memberintegral'    => true,
                    'memberwhitelove'   => true,
                    'memberredlove'     => true,
                    'membercredit'      => true,
                    'memberpoint'       => true,
                    'memberincome'      => true,
                    'judgelove'         => $this->judgeLove(),
                    'judgeintegral'     => $this->judgeIntegral(),
                    'judgecommission'   => $this->judgeCommission(),
                    'judgeteamdividend' => $this->judgeTeamDividend(),
                    'memberordername'   => '我的订单',
                    'memberorderbg'     => '1',
                    'memberordercolor'  => '#fff',
                    'memberorderimg'    => '',
                ],
                'data'   => [
                    [
                        'id'     => 'ME000000000001',
                        'imgurl' => $this->getUrl('myOrder_a.png'),
                        'text'   => '待付款',
                        'color'  => '#333',
                    ],
                    [
                        'id'     => 'ME000000000002',
                        'imgurl' => $this->getUrl('myOrder_b.png'),
                        'text'   => '待发货',
                        'color'  => '#333',
                    ],
                    [
                        'id'     => 'ME000000000003',
                        'imgurl' => $this->getUrl('myOrder_c.png'),
                        'text'   => '待收货',
                        'color'  => '#333',
                    ],
                    [
                        'id'     => 'ME000000000004',
                        'imgurl' => $this->getUrl('myOrder_d.png'),
                        'text'   => '售后列表',
                        'color'  => '#333',
                    ],
                ]
            ],
        ];

        $memberCenter = $this->validatePluginOrder($memberCenter, 'lease-toy', '租赁订单', 'ME0000000007', 'memberleaseorder', 'MEL');
        $memberCenter = $this->validatePluginOrder($memberCenter, 'fight-groups', '拼团订单', 'ME0000000008', 'membergrouporder', 'MEG');
        $memberCenter = $this->validatePluginOrder($memberCenter, 'net-car', '网约车订单', 'ME0000000005', 'membercarorder', 'MEC');
        $memberCenter = $this->validatePluginOrder($memberCenter, 'hotel', '酒店订单', 'ME0000000006', 'memberhotelorder', 'MEH');

        $parts = [[
            'id'     => 'ME0000000001',
            'temp'   => 'membertool',
            'params' => [
                'tooltitle'      => '实用工具',
                'toolstyle'      => '1',
                'tooltitlecolor' => '#333',
                'toolbg'         => '1',
                'toolbgcolor'    => '#FFFFFF',
                'bgimg'          => '',
            ],
            'data'   => [
                'part' => array_merge($this->getToolDefault(), $this->getToolData($arr, 'tool', 'T', 111111114)),
                'more' => []
            ]
        ],
            [
                'id'     => 'ME0000000002',
                'temp'   => 'membermerchant',
                'params' => [
                    'merchanttitle'      => '商家管理',
                    'merchantstyle'      => '1',
                    'merchanttitlecolor' => '#333',
                    'merchantbg'         => '1',
                    'merchantbgcolor'    => '#FFFFFF',
                    'bgimg'              => '',
                ],
                'data'   => ['part' => $this->getToolData($arr, 'merchant', 'M', 111111110), 'more' => []]
            ],
            [
                'id'     => 'ME0000000003',
                'temp'   => 'membermarket',
                'params' => [
                    'markettitle'      => '营销互动',
                    'marketstyle'      => '1',
                    'markettitlecolor' => '#333',
                    'marketbg'         => '1',
                    'marketbgcolor'    => '#FFFFFF',
                    'bgimg'            => '',
                ],
                'data'   => [
                    'part' => array_merge($this->getMarketDefault(), $this->getToolData($arr, 'market', 'K', 111111114)),
                    'more' => []
                ]
            ],
            [
                'id'     => 'ME0000000004',
                'temp'   => 'memberasset',
                'params' => [
                    'assettitle'      => '资产权益',
                    'assetstyle'      => '1',
                    'assettitlecolor' => '#333',
                    'assetbg'         => '1',
                    'assetbgcolor'    => '#FFFFFF',
                    'bgimg'           => '',
                ],
                'data'   => ['part' => $this->getToolData($arr, 'asset_equity', 'A', 111111110), 'more' => []]
            ],
        ];
        $memberCenter = array_merge($memberCenter, $parts);
        return $memberCenter;
    }

    private function getToolData($arr, $type, $mark, $id)
    {
        $newArr = [];
        if ($arr[$type]) {
            foreach ($arr[$type] as $v) {
                $v['image'] = yz_tomedia(static_url('yunshop/designer/images/' . $v['image']));
                $new = array_merge($v, ['is_open' => true, 'id' => $mark . $id]);
                $newArr[] = $new;
                $id += 1;
            }
        }
        return $newArr;
    }

    private function validatePluginOrder($arr, $plugin, $name, $id, $temp, $mark)
    {
        if (app('plugins')->isEnabled($plugin)) {
            $orderArr = [
                [
                    'id'     => $id,
                    'temp'   => $temp,
                    'params' => [
                        'memberordername'  => $name,
                        'memberorderbg'    => '1',
                        'memberordercolor' => '#fff',
                        'memberorderimg'   => '',
                    ],
                    'data'   => $this->getOrderData($mark, $plugin)
                ]
            ];
            $arr = array_merge($arr, $orderArr);
            return $arr;
        }
        return $arr;
    }

    private function getOrderData($mark, $plugin)
    {
        if ($plugin == 'net-car') {
            $arr = [
                [
                    'id'     => $mark . '00000000001',
                    'imgurl' => $this->getUrl('order_card_a.png'),
                    'text'   => '待审核',
                    'color'  => '#333',
                ],
                [
                    'id'     => $mark . '00000000002',
                    'imgurl' => $this->getUrl('order_card_b.png'),
                    'text'   => '待贷款',
                    'color'  => '#333',
                ],
                [
                    'id'     => $mark . '00000000003',
                    'imgurl' => $this->getUrl('order_card_c.png'),
                    'text'   => '待提车',
                    'color'  => '#333',
                ],
                [
                    'id'     => $mark . '00000000004',
                    'imgurl' => $this->getUrl('order_card_d.png'),
                    'text'   => '售后列表',
                    'color'  => '#333',
                ],
            ];
        } elseif ($plugin == 'hotel') {
            $arr = [
                [
                    'id'     => $mark . '00000000001',
                    'imgurl' => $this->getUrl('hotel_a.png'),
                    'text'   => '待付款',
                    'color'  => '#333',
                ],
                [
                    'id'     => $mark . '00000000002',
                    'imgurl' => $this->getUrl('hotel_b.png'),
                    'text'   => '待确认',
                    'color'  => '#333',
                ],
                [
                    'id'     => $mark . '00000000003',
                    'imgurl' => $this->getUrl('hotel_c.png'),
                    'text'   => '待入住',
                    'color'  => '#333',
                ],
                [
                    'id'     => $mark . '00000000004',
                    'imgurl' => $this->getUrl('hotel_d.png'),
                    'text'   => '待退房',
                    'color'  => '#333',
                ],
            ];
        } elseif ($plugin == 'lease-toy') {
            $arr = [
                [
                    'id'     => $mark . '00000000001',
                    'imgurl' => $this->getUrl('myOrder_a.png'),
                    'text'   => '待付款',
                    'color'  => '#333',
                ],
                [
                    'id'     => $mark . '00000000002',
                    'imgurl' => $this->getUrl('myOrder_b.png'),
                    'text'   => '待发货',
                    'color'  => '#333',
                ],
                [
                    'id'     => $mark . '00000000003',
                    'imgurl' => $this->getUrl('myOrder_c.png'),
                    'text'   => '待收货',
                    'color'  => '#333',
                ],
                [
                    'id'     => $mark . '00000000004',
                    'imgurl' => $this->getUrl('myOrder_d.png'),
                    'text'   => '待归还',
                    'color'  => '#333',
                ],
            ];
        } else {
            $arr = [
                [
                    'id'     => $mark . '00000000001',
                    'imgurl' => $this->getUrl('myOrder_a.png'),
                    'text'   => '待付款',
                    'color'  => '#333',
                ],
                [
                    'id'     => $mark . '00000000002',
                    'imgurl' => $this->getUrl('myOrder_b.png'),
                    'text'   => '待发货',
                    'color'  => '#333',
                ],
                [
                    'id'     => $mark . '00000000003',
                    'imgurl' => $this->getUrl('myOrder_c.png'),
                    'text'   => '待收货',
                    'color'  => '#333',
                ],
                [
                    'id'     => $mark . '00000000004',
                    'imgurl' => $this->getUrl('myOrder_d.png'),
                    'text'   => '售后列表',
                    'color'  => '#333',
                ],
            ];
        }

        return $arr;

    }

    private function getToolDefault()
    {
        return [
            [
                'title'   => '收藏',
                'name'    => 'm-collection',
                'url'     => 'collection',
                'class'   => 'icon-shoucang',
                'image'   => yz_tomedia(static_url('yunshop/designer/images/tool_a(6).png')),
                'is_open' => true,
                'id'      => 111111110
            ],
            [
                'title'   => '足迹',
                'name'    => 'm-footprint',
                'url'     => 'footprint',
                'class'   => 'icon-zuji',
                'image'   => yz_tomedia(static_url('yunshop/designer/images/tool_a(8).png')),
                'is_open' => true,
                'id'      => 111111111
            ],
            [
                'title'   => '地址管理',
                'name'    => 'm-address',
                'url'     => 'address',
                'class'   => 'icon-dizhi',
                'image'   => yz_tomedia(static_url('yunshop/designer/images/tool_a(1).png')),
                'is_open' => true,
                'id'      => 111111112
            ],
            [
                'title'   => '设置',
                'name'    => 'm-info',
                'url'     => 'info',
                'class'   => 'icon-shezhi',
                'image'   => yz_tomedia(static_url('yunshop/designer/images/tool_a(5).png')),
                'is_open' => true,
                'id'      => 111111113
            ],
        ];
    }

    private function getmarketDefault()
    {
        return [
            [
                'title'   => '二维码',
                'name'    => 'm-erweima',
                'class'   => 'icon-erweima',
                'image'   => yz_tomedia(static_url('yunshop/designer/images/tool_a(2).png')),
                'is_open' => true,
                'id'      => 111111110
            ],
            [
                'title'   => '评论',
                'name'    => 'm-pinglun',
                'url'     => 'myEvaluation',
                'class'   => 'icon-pinglun',
                'image'   => yz_tomedia(static_url('yunshop/designer/images/tool_a(4).png')),
                'is_open' => true,
                'id'      => 111111111
            ],
            [
                'title'   => '客户',
                'name'    => 'm-guanxi',
                'url'     => 'myRelationship',
                'class'   => 'icon-guanxi',
                'image'   => yz_tomedia(static_url('yunshop/designer/images/tool_a(3).png')),
                'is_open' => true,
                'id'      => 111111112
            ],
            [
                'title'   => '优惠券',
                'name'    => 'm-coupon',
                'url'     => 'coupon',
                'class'   => 'icon-youhuiquan1',
                'image'   => yz_tomedia(static_url('yunshop/designer/images/tool_a(7).png')),
                'is_open' => true,
                'id'      => 111111113
            ],
        ];
    }

    private function judgeIntegral()
    {
        return !!app('plugins')->isEnabled('integral');
    }

    private function judgeLove()
    {
        return !!app('plugins')->isEnabled('love');
    }

    private function judgeCommission()
    {
        return !!app('plugins')->isEnabled('commission');
    }

    private function judgeTeamDividend()
    {
        return !!app('plugins')->isEnabled('team-dividend');
    }

    private function getUrl($img)
    {
        if (config('app.framework') == 'platform') {
            return request()->getSchemeAndHttpHost() . plugin_assets('designer', 'assets/imgsrc/member/' . $img);
        } else {
            return yz_tomedia(plugin_assets('designer', 'assets/imgsrc/member/' . $img));
        }
    }
}
