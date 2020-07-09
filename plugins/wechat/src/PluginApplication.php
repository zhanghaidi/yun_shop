<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/12/24
 * Time: 3:38 PM
 */

namespace Yunshop\Wechat;


class PluginApplication extends \app\common\services\PluginApplication
{
    protected function setConfig()
    {
    }
    protected function setMenuConfig()
    {
        \app\backend\modules\menu\Menu::current()->setPluginMenu('wechat', [
            'name'             => '公众号',
            'type'             => 'tool',
            'url'              => 'plugin.wechat.admin.setting.setting',
            'url_params'       => '',
            'permit'           => 1,
            'menu'             => 1,
            'top_show'         => 0,
            'left_first_show'  => 1,
            'left_second_show' => 1,
            'icon'             => 'fa-hourglass-1',
            'list_icon'        => 'wechat',
            'parents'          => [],
            'child'            => [
                'wechatFans'      => [
                    'name'       => '粉丝管理',
                    'url'        => 'plugin.wechat.admin.fans.controller.fans.index',
                    'url_params' => '',
                    'permit'     => 1,
                    'menu'       => 1,
                    'icon'       => '',
                    'sort'       => 1,
                    'item'       => 'wechatFans',
                    'parents'    => ['wechat'],
                    'child'      => [
                        'wechatFansStaff'     => [
                            'name'       => '发送消息',
                            'url'        => 'plugin.wechat.admin.staff.controller.staff.index',
                            'url_params' => '',
                            'permit'     => 1,
                            'menu'       => 0,
                            'icon'       => '',
                            'sort'       => 1   ,
                            'item'       => 'wechatFansStaff',
                            'parents'    => ['wechat', 'wechatFans'],
                        ],
                        'wechatFansStaffSendMessage'     => [
                            'name'       => '发送',
                            'url'        => 'plugin.wechat.admin.staff.controller.staff.sendMessage',
                            'url_params' => '',
                            'permit'     => 1,
                            'menu'       => 0,
                            'icon'       => '',
                            'sort'       => 1,
                            'item'       => 'wechatFansStaffSendMessage',
                            'parents'    => ['wechat', 'wechatFans'],
                        ],
                        'wechatFansSyncSetting'     => [
                            'name'       => '同步设置',
                            'url'        => 'plugin.wechat.admin.fans.controller.fans.syncSetting',
                            'url_params' => '',
                            'permit'     => 1,
                            'menu'       => 0,
                            'icon'       => '',
                            'sort'       => 1,
                            'item'       => 'wechatFansSyncSetting',
                            'parents'    => ['wechat', 'wechatFans']
                        ],
                        'wechatFansAutoSyncSetting' => [
                            'name'       => '自动同步设置',
                            'url'        => 'plugin.wechat.admin.fans.controller.fans.fansSyncSetting',
                            'url_params' => '',
                            'permit'     => 1,
                            'menu'       => 0,
                            'icon'       => '',
                            'sort'       => 1,
                            'item'       => 'wechatFansAutoSyncSetting',
                            'parents'    => ['wechat', 'wechatFans']
                        ],
                        'wechatFansBatchSetGroup'   => [
                            'name'       => '添加标签',
                            'url'        => 'plugin.wechat.admin.fans.controller.fans.batchSetFansGroups',
                            'url_params' => '',
                            'permit'     => 1,
                            'menu'       => 0,
                            'icon'       => '',
                            'sort'       => 1,
                            'item'       => 'wechatFansBatchSetGroup',
                            'parents'    => ['wechat', 'wechatFans']
                        ],
                        'wechatFansSyncChoose'      => [
                            'name'       => '同步选中信息',
                            'url'        => 'plugin.wechat.admin.fans.controller.fans.syncBatch',
                            'url_params' => '',
                            'permit'     => 1,
                            'menu'       => 0,
                            'icon'       => '',
                            'sort'       => 1,
                            'item'       => 'wechatFansSyncChoose',
                            'parents'    => ['wechat', 'wechatFans']
                        ],
                        'wechatFansSyncAll'         => [
                            'name'       => '同步全部信息',
                            'url'        => 'plugin.wechat.admin.fans.controller.fans.syncAll',
                            'url_params' => '',
                            'permit'     => 1,
                            'menu'       => 0,
                            'icon'       => '',
                            'sort'       => 1,
                            'item'       => 'wechatFansSyncAll',
                            'parents'    => ['wechat', 'wechatFans']
                        ],
                        'wechatFansAddGroup'        => [
                            'name'       => '添加分组',
                            'url'        => 'plugin.wechat.admin.fans.controller.fans.addGroup',
                            'url_params' => '',
                            'permit'     => 1,
                            'menu'       => 0,
                            'icon'       => '',
                            'sort'       => 1,
                            'item'       => 'wechatFansAddGroup',
                            'parents'    => ['wechat', 'wechatFans']
                        ],

                        //白名单
                        'wechatFansList'            => [
                            'name'       => '粉丝数据',
                            'url'        => 'plugin.wechat.admin.fans.controller.fans.getFansList',
                            'url_params' => '',
                            'permit'     => 0,
                            'menu'       => 0,
                            'icon'       => '',
                            'sort'       => 1,
                            'item'       => 'wechatFansList',
                            'parents'    => ['wechat', 'wechatFans']
                        ],
                    ]
                ],
                'wechatMaterial'  => [
                    'name'       => '素材管理',
                    'url'        => 'plugin.wechat.admin.material.controller.material.index',
                    'url_params' => '',
                    'permit'     => 1,
                    'menu'       => 1,
                    'icon'       => '',
                    'sort'       => 1,
                    'item'       => 'wechatMaterial',
                    'parents'    => ['wechat'],
                    'child'      => [
                        'wechatMaterialSync' => [
                            'name'       => '同步素材',
                            'url'        => 'plugin.wechat.admin.material.controller.sync-wechat.index',
                            'url_params' => '',
                            'permit'     => 1,
                            'menu'       => 0,
                            'icon'       => '',
                            'sort'       => 1,
                            'item'       => 'wechatMaterialSync',
                            'parents'    => ['wechat', 'wechatMaterial']
                        ],
                        'wechatMaterialNews' => [
                            'name'       => '图文素材',
                            'url'        => 'plugin.wechat.admin.material.controller.news',
                            'url_params' => '',
                            'permit'     => 1,
                            'menu'       => 0,
                            'icon'       => '',
                            'sort'       => 1,
                            'item'       => 'wechatMaterialNews',
                            'parents'    => ['wechat', 'wechatMaterial'],
                            'child'      => [
                                'wechatMaterialNewsIndex'  => [
                                    'name'       => '浏览图文',
                                    'url'        => 'plugin.wechat.admin.material.controller.news.index',
                                    'url_params' => '',
                                    'permit'     => 1,
                                    'menu'       => 0,
                                    'icon'       => '',
                                    'sort'       => 1,
                                    'item'       => 'wechatMaterialNewsIndex',
                                    'parents'    => ['wechat', 'wechatMaterial', 'wechatMaterialNews']
                                ],
                                'wechatMaterialNewsEdit'   => [
                                    'name'       => '编辑图文',
                                    'url'        => 'plugin.wechat.admin.material.controller.news.edit',
                                    'url_params' => '',
                                    'permit'     => 1,
                                    'menu'       => 0,
                                    'icon'       => '',
                                    'sort'       => 1,
                                    'item'       => 'wechatMaterialNewsEdit',
                                    'parents'    => ['wechat', 'wechatMaterial', 'wechatMaterialNews']
                                ],
                                'wechatMaterialNewsSave'   => [
                                    'name'       => '保存图文',
                                    'url'        => 'plugin.wechat.admin.material.controller.news.edit',
                                    'url_params' => '',
                                    'permit'     => 1,
                                    'menu'       => 0,
                                    'icon'       => '',
                                    'sort'       => 1,
                                    'item'       => 'wechatMaterialNewsSave',
                                    'parents'    => ['wechat', 'wechatMaterial', 'wechatMaterialNews']
                                ],
                                'wechatMaterialNewsDelete' => [
                                    'name'       => '删除图文',
                                    'url'        => 'plugin.wechat.admin.material.controller.news.delete',
                                    'url_params' => '',
                                    'permit'     => 1,
                                    'menu'       => 0,
                                    'icon'       => '',
                                    'sort'       => 1,
                                    'item'       => 'wechatMaterialNewsDelete',
                                    'parents'    => ['wechat', 'wechatMaterial', 'wechatMaterialNews']
                                ],
                                'wechatMaterialUploadImage'   => [
                                    'name'       => '上传图片',
                                    'url'        => 'plugin.wechat.admin.material.controller.news.upload-image',
                                    'url_params' => '',
                                    'permit'     => 1,
                                    'menu'       => 0,
                                    'icon'       => '',
                                    'sort'       => 1,
                                    'item'       => 'wechatMaterialUploadImage',
                                    'parents'    => ['wechat', 'wechatMaterial', 'wechatMaterialNews']
                                ],

                                'wechatNewsSave'   => [
                                    'name'       => '保存图文',
                                    'url'        => 'plugin.wechat.admin.material.controller.news.save',
                                    'url_params' => '',
                                    'permit'     => 1,
                                    'menu'       => 0,
                                    'icon'       => '',
                                    'sort'       => 1,
                                    'item'       => 'wechatNewsSave',
                                    'parents'    => ['wechat', 'wechatMaterial', 'wechatMaterialNews']
                                ],
                                'localToWechatImg'  => [
                                    'name'       => '选择图片',
                                    'url'        => 'plugin.wechat.admin.material.controller.image.local-to-wechat',
                                    'url_params' => '',
                                    'permit'     => 1,
                                    'menu'       => 0,
                                    'icon'       => '',
                                    'sort'       => 1,
                                    'item'       => 'localToWechatImg',
                                    'parents'    => ['wechat', 'wechatMaterial', 'wechatMaterialNews']
                                ],
                            ]
                        ],

                        'wechatMaterialImage'          => [
                            'name'       => '图片素材',
                            'url'        => 'plugin.wechat.admin.material.controller.image.index',
                            'url_params' => '',
                            'permit'     => 1,
                            'menu'       => 0,
                            'icon'       => '',
                            'sort'       => 1,
                            'item'       => 'wechatMaterialImage',
                            'parents'    => ['wechat', 'wechatMaterial'],
                            'child'      => [
                                'wechatMaterialImageIndex'  => [
                                    'name'       => '浏览图片',
                                    'url'        => 'plugin.wechat.admin.material.controller.image.index',
                                    'url_params' => '',
                                    'permit'     => 1,
                                    'menu'       => 0,
                                    'icon'       => '',
                                    'sort'       => 1,
                                    'item'       => 'wechatMaterialImageIndex',
                                    'parents'    => ['wechat', 'wechatMaterial', 'wechatMaterialImage']
                                ],
                                'wechatMaterialImageUpload' => [
                                    'name'       => '上传图片',
                                    'url'        => 'plugin.wechat.admin.material.controller.image.upload',
                                    'url_params' => '',
                                    'permit'     => 0,
                                    'menu'       => 0,
                                    'icon'       => '',
                                    'sort'       => 1,
                                    'item'       => 'wechatMaterialImageUpload',
                                    'parents'    => ['wechat', 'wechatMaterial', 'wechatMaterialImage']
                                ],
                                'wechatMaterialImageDelete' => [
                                    'name'       => '删除图片',
                                    'url'        => 'plugin.wechat.admin.material.controller.image.delete',
                                    'url_params' => '',
                                    'permit'     => 1,
                                    'menu'       => 0,
                                    'icon'       => '',
                                    'sort'       => 1,
                                    'item'       => 'wechatMaterialImageDelete',
                                    'parents'    => ['wechat', 'wechatMaterial', 'wechatMaterialImage']
                                ]
                            ]
                        ],
                        'wechatMaterialVoice'          => [
                            'name'       => '语音素材',
                            'url'        => 'plugin.wechat.admin.material.controller.voice',
                            'url_params' => '',
                            'permit'     => 1,
                            'menu'       => 0,
                            'icon'       => '',
                            'sort'       => 1,
                            'item'       => 'wechatMaterialVoice',
                            'parents'    => ['wechat', 'wechatMaterial'],
                            'child'      => [
                                'wechatMaterialVoiceIndex'  => [
                                    'name'       => '浏览音频',
                                    'url'        => 'plugin.wechat.admin.material.controller.voice.index',
                                    'url_params' => '',
                                    'permit'     => 1,
                                    'menu'       => 0,
                                    'icon'       => '',
                                    'sort'       => 1,
                                    'item'       => 'wechatMaterialVoiceIndex',
                                    'parents'    => ['wechat', 'wechatMaterial', 'wechatMaterialVoice']
                                ],
                                'wechatMaterialVoiceUpload' => [
                                    'name'       => '上传音频',
                                    'url'        => 'plugin.wechat.admin.material.controller.voice.upload',
                                    'url_params' => '',
                                    'permit'     => 0,
                                    'menu'       => 0,
                                    'icon'       => '',
                                    'sort'       => 1,
                                    'item'       => 'wechatMaterialVoiceUpload',
                                    'parents'    => ['wechat', 'wechatMaterial', 'wechatMaterialVoice']
                                ],
                                'wechatMaterialVoiceDelete' => [
                                    'name'       => '删除音频',
                                    'url'        => 'plugin.wechat.admin.material.controller.voice.delete',
                                    'url_params' => '',
                                    'permit'     => 1,
                                    'menu'       => 0,
                                    'icon'       => '',
                                    'sort'       => 1,
                                    'item'       => 'wechatMaterialVoiceDelete',
                                    'parents'    => ['wechat', 'wechatMaterial', 'wechatMaterialVoice']
                                ]
                            ]
                        ],
                        'wechatMaterialVideo'          => [
                            'name'       => '视频素材',
                            'url'        => 'plugin.wechat.admin.material.controller.video',
                            'url_params' => '',
                            'permit'     => 1,
                            'menu'       => 0,
                            'icon'       => '',
                            'sort'       => 1,
                            'item'       => 'wechatMaterialVideo',
                            'parents'    => ['wechat', 'wechatMaterial'],
                            'child'      => [
                                'wechatMaterialVideoIndex'  => [
                                    'name'       => '浏览视频',
                                    'url'        => 'plugin.wechat.admin.material.controller.video.index',
                                    'url_params' => '',
                                    'permit'     => 1,
                                    'menu'       => 0,
                                    'icon'       => '',
                                    'sort'       => 1,
                                    'item'       => 'wechatMaterialVideoIndex',
                                    'parents'    => ['wechat', 'wechatMaterial', 'wechatMaterialVideo']
                                ],
                                'wechatMaterialVideoUpload' => [
                                    'name'       => '上传视频',
                                    'url'        => 'plugin.wechat.admin.material.controller.video.upload',
                                    'url_params' => '',
                                    'permit'     => 0,
                                    'menu'       => 0,
                                    'icon'       => '',
                                    'sort'       => 1,
                                    'item'       => 'wechatMaterialVideoUpload',
                                    'parents'    => ['wechat', 'wechatMaterial', 'wechatMaterialVideo']
                                ],
                                'wechatMaterialVideoDelete' => [
                                    'name'       => '删除视频',
                                    'url'        => 'plugin.wechat.admin.material.controller.video.delete',
                                    'url_params' => '',
                                    'permit'     => 1,
                                    'menu'       => 0,
                                    'icon'       => '',
                                    'sort'       => 1,
                                    'item'       => 'wechatMaterialVideoDelete',
                                    'parents'    => ['wechat', 'wechatMaterial', 'wechatMaterialVideo']
                                ]
                            ]
                        ],
                        //白名单
                        'wechatMaterialGetWeChatImage' => [
                            'name'       => '获取微信图片',
                            'url'        => 'plugin.wechat.admin.material.controller.image.get-wechat-image',
                            'url_params' => '',
                            'permit'     => 0,
                            'menu'       => 0,
                            'icon'       => '',
                            'sort'       => 1,
                            'item'       => 'wechatMaterialGetWeChatImage',
                            'parents'    => ['wechat', 'wechatMaterial']
                        ],
                        'wechatMaterialGetLocalImage'  => [
                            'name'       => '获取本地图片',
                            'url'        => 'plugin.wechat.admin.material.controller.image.get-local-image',
                            'url_params' => '',
                            'permit'     => 0,
                            'menu'       => 0,
                            'icon'       => '',
                            'sort'       => 1,
                            'item'       => 'wechatMaterialGetLocalImage',
                            'parents'    => ['wechat', 'wechatMaterial']
                        ],
                        'wechatMaterialImageFetch'     => [
                            'name'       => '获取网络图片',
                            'url'        => 'plugin.wechat.admin.material.controller.image.fetch',
                            'url_params' => '',
                            'permit'     => 0,
                            'menu'       => 0,
                            'icon'       => '',
                            'sort'       => 1,
                            'item'       => 'wechatMaterialGetLocalImage',
                            'parents'    => ['wechat', 'wechatMaterial']
                        ],
                        'wechatMaterialImageUpload'    => [
                            'name'       => '图片上传',
                            'url'        => 'plugin.wechat.admin.material.controller.image.upload',
                            'url_params' => '',
                            'permit'     => 0,
                            'menu'       => 0,
                            'icon'       => '',
                            'sort'       => 1,
                            'item'       => 'wechatMaterialGetLocalImage',
                            'parents'    => ['wechat', 'wechatMaterial']
                        ],
                    ]
                ],
                'wechatAutoReply' => [
                    'name'       => '自动回复',
                    'url'        => 'plugin.wechat.admin.reply.controller.keywords-auto-reply',
                    'url_params' => '',
                    'permit'     => 1,
                    'menu'       => 1,
                    'icon'       => '',
                    'sort'       => 1,
                    'item'       => 'wechatAutoReply',
                    'parents'    => ['wechat'],
                    'child'      => [
                        'wechatAutoReplyKeyword'    => [
                            'name'       => '关键字回复',
                            'url'        => 'plugin.wechat.admin.reply.controller.keywords-auto-reply',
                            'url_params' => '',
                            'permit'     => 1,
                            'menu'       => 0,
                            'icon'       => '',
                            'sort'       => 1,
                            'item'       => 'wechatAutoReplyKeyword',
                            'parents'    => ['wechat', 'wechatAutoReply'],
                            'child'      => [
                                'wechatAutoReplyKeywordIndex'  => [
                                    'name'       => '浏览关键字',
                                    'url'        => 'plugin.wechat.admin.reply.controller.keywords-auto-reply.index',
                                    'url_params' => '',
                                    'permit'     => 1,
                                    'menu'       => 0,
                                    'icon'       => '',
                                    'sort'       => 1,
                                    'item'       => 'wechatAutoReplyKeywordIndex',
                                    'parents'    => ['wechat', 'wechatAutoReply', 'wechatAutoReplyKeyword']
                                ],
                                'wechatAutoReplyKeywordQuick'  => [
                                    'name'       => '快捷开启关闭',
                                    'url'        => 'plugin.wechat.admin.reply.controller.keywords-auto-reply.status',
                                    'url_params' => '',
                                    'permit'     => 1,
                                    'menu'       => 0,
                                    'icon'       => '',
                                    'sort'       => 1,
                                    'item'       => 'wechatAutoReplyKeywordQuick',
                                    'parents'    => ['wechat', 'wechatAutoReply', 'wechatAutoReplyKeyword']
                                ],
                                'wechatAutoReplyKeywordEdit'   => [
                                    'name'       => '编辑关键字',
                                    'url'        => 'plugin.wechat.admin.reply.controller.keywords-auto-reply.edit',
                                    'url_params' => '',
                                    'permit'     => 1,
                                    'menu'       => 0,
                                    'icon'       => '',
                                    'sort'       => 1,
                                    'item'       => 'wechatAutoReplyKeywordEdit',
                                    'parents'    => ['wechat', 'wechatAutoReply', 'wechatAutoReplyKeyword']
                                ],
                                'wechatAutoReplyKeywordSave'   => [
                                    'name'       => '保存关键字',
                                    'url'        => 'plugin.wechat.admin.reply.controller.keywords-auto-reply.save',
                                    'url_params' => '',
                                    'permit'     => 1,
                                    'menu'       => 0,
                                    'icon'       => '',
                                    'sort'       => 1,
                                    'item'       => 'wechatAutoReplyKeywordSave',
                                    'parents'    => ['wechat', 'wechatAutoReply', 'wechatAutoReplyKeyword']
                                ],
                                'wechatAutoReplyKeywordDelete' => [
                                    'name'       => '删除关键字',
                                    'url'        => 'plugin.wechat.admin.reply.controller.keywords-auto-reply.delete',
                                    'url_params' => '',
                                    'permit'     => 1,
                                    'menu'       => 0,
                                    'icon'       => '',
                                    'sort'       => 1,
                                    'item'       => 'wechatAutoReplyKeywordDelete',
                                    'parents'    => ['wechat', 'wechatAutoReply', 'wechatAutoReplyKeyword']
                                ],
                            ]
                        ],
                        'wechatAutoReplyWelcome'    => [
                            'name'       => '首次访问回复',
                            'url'        => 'plugin.wechat.admin.reply.controller.welcome-auto-reply',
                            'url_params' => '',
                            'permit'     => 1,
                            'menu'       => 0,
                            'icon'       => '',
                            'sort'       => 1,
                            'item'       => 'wechatAutoReplyWelcome',
                            'parents'    => ['wechat', 'wechatAutoReply'],
                            'child'      => [
                                'wechatAutoReplyWelcomeIndex'   => [
                                    'name'       => '浏览设置',
                                    'url'        => 'plugin.wechat.admin.reply.controller.welcome-auto-reply.index',
                                    'url_params' => '',
                                    'permit'     => 1,
                                    'menu'       => 0,
                                    'icon'       => '',
                                    'sort'       => 1,
                                    'item'       => 'wechatAutoReplyWelcomeIndex',
                                    'parents'    => ['wechat', 'wechatAutoReply', 'wechatAutoReplyWelcome']
                                ],
                                'wechatAutoReplyWelcomeKeyword' => [
                                    'name'       => '触发关键字',
                                    'url'        => 'plugin.wechat.admin.reply.controller.keywords-auto-reply.get-keywords',
                                    'url_params' => '',
                                    'permit'     => 0,
                                    'menu'       => 0,
                                    'icon'       => '',
                                    'sort'       => 1,
                                    'item'       => 'wechatAutoReplyWelcomeKeyword',
                                    'parents'    => ['wechat', 'wechatAutoReply', 'wechatAutoReplyWelcome']
                                ],
                                'wechatAutoReplyWelcomeSave'    => [
                                    'name'       => '保存设置',
                                    'url'        => 'plugin.wechat.admin.reply.controller.welcome-auto-reply.add',
                                    'url_params' => '',
                                    'permit'     => 1,
                                    'menu'       => 0,
                                    'icon'       => '',
                                    'sort'       => 1,
                                    'item'       => 'wechatAutoReplyWelcomeSave',
                                    'parents'    => ['wechat', 'wechatAutoReply', 'wechatAutoReplyWelcome']
                                ],
                            ]
                        ],
                        'wechatAutoReplyDefault'    => [
                            'name'       => '首次访问回复',
                            'url'        => 'plugin.wechat.admin.reply.controller.default-reply',
                            'url_params' => '',
                            'permit'     => 1,
                            'menu'       => 1,
                            'icon'       => '',
                            'sort'       => 1,
                            'item'       => 'wechatAutoReplyDefault',
                            'parents'    => ['wechat', 'wechatAutoReply'],
                            'child'      => [
                                'wechatAutoReplyDefaultIndex' => [
                                    'name'       => '浏览设置',
                                    'url'        => 'plugin.wechat.admin.reply.controller.default-reply.index',
                                    'url_params' => '',
                                    'permit'     => 1,
                                    'menu'       => 0,
                                    'icon'       => '',
                                    'sort'       => 1,
                                    'item'       => 'wechatAutoReplyDefaultIndex',
                                    'parents'    => ['wechat', 'wechatAutoReply', 'wechatAutoReplyDefault']
                                ],
                                'wechatAutoReplyDefaultSave'  => [
                                    'name'       => '保存设置',
                                    'url'        => 'plugin.wechat.admin.reply.controller.default-reply.add',
                                    'url_params' => '',
                                    'permit'     => 1,
                                    'menu'       => 0,
                                    'icon'       => '',
                                    'sort'       => 1,
                                    'item'       => 'wechatAutoReplyDefaultSave',
                                    'parents'    => ['wechat', 'wechatAutoReply', 'wechatAutoReplyDefault']
                                ],
                            ]
                        ],
                        //白名单
                        'wechatAutoReplySearch'     => [
                            'name'       => '搜索数据',
                            'url'        => 'plugin.wechat.admin.reply.controller.keywords-auto-reply.search',
                            'url_params' => '',
                            'permit'     => 0,
                            'menu'       => 0,
                            'icon'       => '',
                            'sort'       => 1,
                            'item'       => 'wechatAutoReplySearch',
                            'parents'    => ['wechat', 'wechatAutoReply']
                        ],
                        'wechatAutoReplyLocalNews'  => [
                            'name'       => '本地图文',
                            'url'        => 'plugin.wechat.admin.material.controller.news.get-local-news',
                            'url_params' => '',
                            'permit'     => 0,
                            'menu'       => 0,
                            'icon'       => '',
                            'sort'       => 1,
                            'item'       => 'wechatAutoReplyLocalNews',
                            'parents'    => ['wechat', 'wechatAutoReply']
                        ],
                        'wechatAutoReplyCloudNews'  => [
                            'name'       => '微信图文',
                            'url'        => 'plugin.wechat.admin.material.controller.news.get-wechat-news',
                            'url_params' => '',
                            'permit'     => 0,
                            'menu'       => 0,
                            'icon'       => '',
                            'sort'       => 1,
                            'item'       => 'wechatAutoReplySearch',
                            'parents'    => ['wechat', 'wechatAutoReply']
                        ],
                        'wechatAutoReplyLocalVoice' => [
                            'name'       => '本地音频',
                            'url'        => 'plugin.wechat.admin.material.controller.voice.get-local-voice',
                            'url_params' => '',
                            'permit'     => 0,
                            'menu'       => 0,
                            'icon'       => '',
                            'sort'       => 1,
                            'item'       => 'wechatAutoReplyLocalVoice',
                            'parents'    => ['wechat', 'wechatAutoReply']
                        ],
                        'wechatAutoReplyCloudVoice' => [
                            'name'       => '微信音频',
                            'url'        => 'plugin.wechat.admin.material.controller.voice.get-wechat-voice',
                            'url_params' => '',
                            'permit'     => 0,
                            'menu'       => 0,
                            'icon'       => '',
                            'sort'       => 1,
                            'item'       => 'wechatAutoReplyCloudVoice',
                            'parents'    => ['wechat', 'wechatAutoReply']
                        ],
                        'wechatAutoReplyLocalVideo' => [
                            'name'       => '本地视频',
                            'url'        => 'plugin.wechat.admin.material.controller.video.get-local-video',
                            'url_params' => '',
                            'permit'     => 0,
                            'menu'       => 0,
                            'icon'       => '',
                            'sort'       => 1,
                            'item'       => 'wechatAutoReplyLocalVideo',
                            'parents'    => ['wechat', 'wechatAutoReply']
                        ],
                        'wechatAutoReplyCloudVideo' => [
                            'name'       => '微信视频',
                            'url'        => 'plugin.wechat.admin.material.controller.video.get-wechat-video',
                            'url_params' => '',
                            'permit'     => 0,
                            'menu'       => 0,
                            'icon'       => '',
                            'sort'       => 1,
                            'item'       => 'wechatAutoReplyCloudVideo',
                            'parents'    => ['wechat', 'wechatAutoReply']
                        ],
                    ]
                ],

                'wechatMenu' => [
                    'name'       => '自定义菜单',
                    'url'        => 'plugin.wechat.admin.menu.controller.default-menu',
                    'url_params' => '',
                    'permit'     => 1,
                    'menu'       => 1,
                    'icon'       => '',
                    'sort'       => 1,
                    'item'       => 'wechatMenu',
                    'parents'    => ['wechat'],
                    'child'      => [
                        'wechatMenuPush'        => [
                            'name'       => '菜单发布',
                            'url'        => 'plugin.wechat.admin.menu.controller.default-menu.push-menu',
                            'url_params' => '',
                            'permit'     => 1,
                            'menu'       => 0,
                            'icon'       => '',
                            'sort'       => 1,
                            'item'       => 'wechatMenuPush',
                            'parents'    => ['wechat', 'wechatMenu']
                        ],
                        'wechatMenuEnable'      => [
                            'name'       => '快捷生效',
                            'url'        => 'plugin.wechat.admin.menu.controller.default-menu.enable-menu',
                            'url_params' => '',
                            'permit'     => 1,
                            'menu'       => 0,
                            'icon'       => '',
                            'sort'       => 1,
                            'item'       => 'wechatMenuEnable',
                            'parents'    => ['wechat', 'wechatMenu']
                        ],
                        'wechatMenuEdit'        => [
                            'name'       => '添加编辑',
                            'url'        => 'plugin.wechat.admin.menu.controller.default-menu.index',
                            'url_params' => '',
                            'permit'     => 1,
                            'menu'       => 0,
                            'icon'       => '',
                            'sort'       => 1,
                            'item'       => 'wechatMenuEdit',
                            'parents'    => ['wechat', 'wechatMenu']
                        ],
                        'wechatMenuSave'        => [
                            'name'       => '保存菜单',
                            'url'        => 'plugin.wechat.admin.menu.controller.default-menu.save-menu',
                            'url_params' => '',
                            'permit'     => 1,
                            'menu'       => 0,
                            'icon'       => '',
                            'sort'       => 1,
                            'item'       => 'wechatMenuSave',
                            'parents'    => ['wechat', 'wechatMenu']
                        ],
                        'wechatMenuDelete'      => [
                            'name'       => '删除菜单',
                            'url'        => 'plugin.wechat.admin.menu.controller.default-menu.del-menu',
                            'url_params' => '',
                            'permit'     => 1,
                            'menu'       => 0,
                            'icon'       => '',
                            'sort'       => 1,
                            'item'       => 'wechatMenuDelete',
                            'parents'    => ['wechat', 'wechatMenu']
                        ],
                        'wechatMenuHistory'     => [
                            'name'       => '历史菜单',
                            'url'        => 'plugin.wechat.admin.menu.controller.default-menu.display-menu',
                            'url_params' => '',
                            'permit'     => 1,
                            'menu'       => 0,
                            'icon'       => '',
                            'sort'       => 1,
                            'item'       => 'wechatMenuHistory',
                            'parents'    => ['wechat', 'wechatMenu']
                        ],
                        'wechatMenuConditional' => [
                            'name'       => '个性化菜单',
                            'url'        => 'plugin.wechat.admin.menu.controller.conditional-menu.conditional-menu',
                            'url_params' => '',
                            'permit'     => 1,
                            'menu'       => 0,
                            'icon'       => '',
                            'sort'       => 1,
                            'item'       => 'wechatMenuConditional',
                            'parents'    => ['wechat', 'wechatMenu'],
                            'child'      => [
                                'wechatMenuConditionalSee'    => [
                                    'name'       => '浏览菜单',
                                    'url'        => 'plugin.wechat.admin.menu.controller.conditional-menu.conditional-menu',
                                    'url_params' => '',
                                    'permit'     => 1,
                                    'menu'       => 0,
                                    'icon'       => '',
                                    'sort'       => 1,
                                    'item'       => 'wechatMenuConditionalSee',
                                    'parents'    => ['wechat', 'wechatMenu', 'wechatMenuConditional']
                                ],
                                'wechatMenuConditionalEnable' => [
                                    'name'       => '快捷生效',
                                    'url'        => 'plugin.wechat.admin.menu.controller.conditional-menu.enable-menu',
                                    'url_params' => '',
                                    'permit'     => 1,
                                    'menu'       => 0,
                                    'icon'       => '',
                                    'sort'       => 1,
                                    'item'       => 'wechatMenuConditionalEnable',
                                    'parents'    => ['wechat', 'wechatMenu', 'wechatMenuConditional']
                                ],
                                'wechatMenuConditionalAdd'    => [
                                    'name'       => '查看编辑',
                                    'url'        => 'plugin.wechat.admin.menu.controller.conditional-menu.index',
                                    'url_params' => '',
                                    'permit'     => 1,
                                    'menu'       => 0,
                                    'icon'       => '',
                                    'sort'       => 1,
                                    'item'       => 'wechatMenuConditionalAdd',
                                    'parents'    => ['wechat', 'wechatMenu', 'wechatMenuConditional']
                                ],
                                'wechatMenuConditionalCopy'   => [
                                    'name'       => '复制菜单',
                                    'url'        => 'plugin.wechat.admin.menu.controller.conditional-menu.copy-menu',
                                    'url_params' => '',
                                    'permit'     => 1,
                                    'menu'       => 0,
                                    'icon'       => '',
                                    'sort'       => 1,
                                    'item'       => 'wechatMenuConditionalCopy',
                                    'parents'    => ['wechat', 'wechatMenu', 'wechatMenuConditional']
                                ],
                                'wechatMenuConditionalDelete' => [
                                    'name'       => '删除菜单',
                                    'url'        => 'plugin.wechat.admin.menu.controller.conditional-menu.del-menu',
                                    'url_params' => '',
                                    'permit'     => 1,
                                    'menu'       => 0,
                                    'icon'       => '',
                                    'sort'       => 1,
                                    'item'       => 'wechatMenuConditionalDelete',
                                    'parents'    => ['wechat', 'wechatMenu', 'wechatMenuConditional']
                                ],
                            ]
                        ],
                    ]
                ],


                'wechatUploadJs' => [
                    'name'       => '上传JS文件',
                    'url'        => 'plugin.wechat.admin.upload.uploadjs.index',
                    'url_params' => '',
                    'permit'     => 1,
                    'menu'       => 1,
                    'icon'       => '',
                    'sort'       => 1,
                    'item'       => 'wechatUploadJs',
                    'parents'    => ['wechat']
                ],
                'wechatSetting'  => [
                    'name'       => '公众号设置',
                    'url'        => 'plugin.wechat.admin.setting.setting.index',
                    'url_params' => '',
                    'permit'     => 1,
                    'menu'       => 1,
                    'icon'       => '',
                    'sort'       => 1,
                    'item'       => 'wechatSetting',
                    'parents'    => ['wechat']
                ],
            ]
        ]);
    }

    public function boot()
    {
        $events = app('events');
        $events->subscribe(\Yunshop\Wechat\Listener\WechatMessageListener::class);


        /**
         * 添加定时任务，每天半夜3点自动更新粉丝和会员信息
         */
        \Event::listen('cron.collectJobs', function () {
            \Cron::add('wechat_fans_sync', '0 3 * * *', function () {//每天半夜三点
                (new \Yunshop\Wechat\service\FansService())->handle();
                return;
            });
        });

    }

}