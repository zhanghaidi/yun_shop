<?php

namespace Yunshop\MinappContent;

use Yunshop\MinappContent\services\MinappContentService;

class PluginApplication extends \app\common\services\PluginApplication
{
    protected function setConfig()
    {
    }

    protected function setMenuConfig()
    {
        \app\backend\modules\menu\Menu::current()->setPluginMenu(MinappContentService::get(), [
            'name' => MinappContentService::get('name'),
            'type' => 'tool',
            'url' => 'plugin.minapp-content.admin.initialization.index', // url 可以填写http 也可以直接写路由
            'url_params' => '', //如果是url填写的是路由则启用参数否则不启用
            'permit' => 1, //如果不设置则不会做权限检测
            'menu' => 1, //如果不设置则不显示菜单，子菜单也将不显示
            'top_show' => 0,
            'left_first_show' => 0,
            'left_second_show' => 1,
            'icon' => '', //菜单图标
            'list_icon' => 'declaration',
            'parents' => [],
            'child' => [
                'initialization_manage'=>[
                    'name' => '内容数据初始化',
                    'permit' => 1,
                    'menu' => 1,
                    'icon' => '',
                    'url' => 'plugin.minapp-content.admin.initialization.index',
                    'url_params' => '',
                    'parents' => ['minapp_content'],
                    'child' => [
                        'custom_share_edit' => [
                            'name' => '编辑|添加自定义分享',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.initialization.edit',
                            'parents' => ['minapp_content', 'initialization_manage'],
                        ],
                    ],
                ],
                'acupoint_manage' => [
                    'name' => '穴位图',
                    'permit' => 1,
                    'menu' => 1,
                    'icon' => '',
                    'url' => 'plugin.minapp-content.admin.acupoint.index',
                    'url_params' => '',
                    'parents' => ['minapp_content'],
                    'child' => [
                        'acupoint_edit' => [
                            'name' => '编辑穴位',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.acupoint.edit',
                            'parents' => ['minapp_content', 'acupoint_manage'],
                        ],
                        'acupoint_del' => [
                            'name' => '删除穴位',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.acupoint.delete',
                            'parents' => ['minapp_content', 'acupoint_manage'],
                        ],
                        'meridian_manage' => [
                            'name' => '经络管理',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.meridian.index',
                            'parents' => ['minapp_content', 'acupoint_manage'],
                        ],
                        'meridian_edit' => [
                            'name' => '编辑经络',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.meridian.edit',
                            'parents' => ['minapp_content', 'acupoint_manage'],
                        ],
                        'meridian_del' => [
                            'name' => '删除经络',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.meridian.delete',
                            'parents' => ['minapp_content', 'acupoint_manage'],
                        ],
                        'course_hour' => [
                            'name' => '经络关联课时列表',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.meridian.course-hour',
                            'parents' => ['minapp_content', 'acupoint_manage'],
                        ],
                        'meridian_acupoints' => [
                            'name' => '经络所属穴位',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.meridian.acupoints',
                            'parents' => ['minapp_content', 'acupoint_manage'],
                        ],
                        'meridian_acupoints' => [
                            'name' => '经络所属穴位',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.meridian.acupoints',
                            'parents' => ['minapp_content', 'acupoint_manage'],
                        ],
                        'acupoint_reply_manage' => [
                            'name' => '穴位笔记管理',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.acupoint-replys.index',
                            'parents' => ['minapp_content', 'acupoint_manage'],
                        ],
                        'acupoint_reply_post' => [
                            'name' => '穴位笔记评论',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.acupoint-replys.post',
                            'parents' => ['minapp_content', 'acupoint_manage'],
                        ],
                        'acupoint_reply_status' => [
                            'name' => '穴位笔记状态管理',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.acupoint-replys.status',
                            'parents' => ['minapp_content', 'acupoint_manage'],
                        ],
                        'acupoint_reply_delete' => [
                            'name' => '删除穴位笔记',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.acupoint-replys.delete',
                            'parents' => ['minapp_content', 'acupoint_manage'],
                        ],
                    ],
                ],
                'feedback_manage' => [
                    'name' => '用户反馈与投诉',
                    'permit' => 1,
                    'menu' => 1,
                    'icon' => '',
                    'url' => 'plugin.minapp-content.admin.feedback.index',
                    'url_params' => '',
                    'parents' => ['minapp_content'],
                    'child' => [
                        'feedback_detail' => [
                            'name' => '反馈详情列表',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.feedback.msg',
                            'parents' => ['minapp_content', 'feedback_manage'],
                        ],
                        'feedback_delete' => [
                            'name' => '删除反馈详情',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.feedback.delete',
                            'parents' => ['minapp_content', 'feedback_manage'],
                        ],
                        'complain_type' => [
                            'name' => '投诉类型',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.feedback.complain-type',
                            'parents' => ['minapp_content', 'feedback_manage'],
                            'child' => [
                                'complain_type_add' => [
                                    'name' => '增加投诉类型',
                                    'permit' => 1,
                                    'menu' => 0,
                                    'url' => 'plugin.minapp-content.admin.feedback.complain-type-add',
                                    'parents' => ['minapp_content', 'feedback_manage','complain_type'],
                                ],
                                'complain_type_edit' => [
                                    'name' => '编辑投诉类型',
                                    'permit' => 1,
                                    'menu' => 0,
                                    'url' => 'plugin.minapp-content.admin.feedback.complain-type-edit',
                                    'parents' => ['minapp_content', 'feedback_manage','complain_type'],
                                ],
                                'complain_type_delete' => [
                                    'name' => '删除投诉类型',
                                    'permit' => 1,
                                    'menu' => 0,
                                    'url' => 'plugin.minapp-content.admin.feedback.complain-type-delete',
                                    'parents' => ['minapp_content', 'feedback_manage','complain_type'],
                                ],

                            ]
                        ],
                        'complain' => [
                            'name' => '用户投诉列表',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.feedback.complain',
                            'parents' => ['minapp_content', 'feedback_manage'],
                        ],
                        'complain_delete' => [
                            'name' => '删除用户投诉',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.feedback.complain-delete',
                            'parents' => ['minapp_content', 'feedback_manage'],
                        ],


                    ],
                ],
                'article_manage' => [
                    'name' => '文章管理',
                    'permit' => 1,
                    'menu' => 1,
                    'icon' => '',
                    'url' => 'plugin.minapp-content.admin.article.index',
                    'url_params' => '',
                    'parents' => ['minapp_content'],
                    'child' => [
                        'article_edit' => [
                            'name' => '编辑文章',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.article.edit',
                            'parents' => ['minapp_content', 'article_manage'],
                        ],
                        'article_del' => [
                            'name' => '删除文章',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.article.delete',
                            'parents' => ['minapp_content', 'article_manage'],
                        ],
                        'article_status' => [
                            'name' => '文章状态管理',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.article.status',
                            'parents' => ['minapp_content', 'article_manage'],
                        ],
                        'article_reply_manage' => [
                            'name' => '文章评论管理',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.article-replys.index',
                            'parents' => ['minapp_content', 'article_manage'],
                        ],
                        'article_reply_post' => [
                            'name' => '文章评论回复',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.article-replys.post',
                            'parents' => ['minapp_content', 'article_manage'],
                        ],
                        'article_reply_status' => [
                            'name' => '文章评论状态管理',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.article-replys.status',
                            'parents' => ['minapp_content', 'article_manage'],
                        ],
                        'article_reply_del' => [
                            'name' => '删除文章评论',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.article-replys.delete',
                            'parents' => ['minapp_content', 'article_manage'],
                        ],
                        'article_category_manage' => [
                            'name' => '文章分类管理',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.article-category.index',
                            'parents' => ['minapp_content', 'article_manage'],
                        ],
                        'article_category_edit' => [
                            'name' => '编辑文章分类',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.article-category.edit',
                            'parents' => ['minapp_content', 'article_manage'],
                        ],
                        'article_category_del' => [
                            'name' => '删除文章分类',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.article-category.delete',
                            'parents' => ['minapp_content', 'article_manage'],
                        ],
                    ],
                ],
                'sns_manage' => [
                    'name' => '健康社区',
                    'permit' => 1,
                    'menu' => 1,
                    'icon' => '',
                    'url' => 'plugin.minapp-content.admin.post.index',
                    'url_params' => '',
                    'parents' => ['minapp_content'],
                    'child' => [
                        'post_edit' => [
                            'name' => '社区帖子编辑',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.post.edit',
                            'parents' => ['minapp_content', 'sns_manage'],
                        ],
                        'post_del' => [
                            'name' => '删除社区帖子',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.post.delete',
                            'parents' => ['minapp_content', 'sns_manage'],
                        ],
                        'post_status' => [
                            'name' => '社区帖子状态管理',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.post.status',
                            'parents' => ['minapp_content', 'sns_manage'],
                        ],
                        'sns_board_manage' => [
                            'name' => '社区版块管理',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.sns-board.index',
                            'parents' => ['minapp_content', 'sns_manage'],
                        ],
                        'sns_board_edit' => [
                            'name' => '社区版块编辑',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.sns-board.edit',
                            'parents' => ['minapp_content', 'sns_manage'],
                        ],
                        'sns_board_del' => [
                            'name' => '删除社区版块',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.sns-board.delete',
                            'parents' => ['minapp_content', 'sns_manage'],
                        ],
                        'sns_board_status' => [
                            'name' => '社区版块状态管理',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.sns-board.status',
                            'parents' => ['minapp_content', 'sns_manage'],
                        ],
                        'sns_reply_manage' => [
                            'name' => '社区帖子评论管理',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.sns-replys.index',
                            'parents' => ['minapp_content', 'sns_manage'],
                        ],
                        'sns_reply_post' => [
                            'name' => '社区帖子评论回复管理',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.sns-replys.post',
                            'parents' => ['minapp_content', 'sns_manage'],
                        ],
                        'sns_reply_status' => [
                            'name' => '社区帖子评论回复状态管理',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.sns-replys.status',
                            'parents' => ['minapp_content', 'sns_manage'],
                        ],
                        'sns_reply_del' => [
                            'name' => '删除社区帖子评论',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.sns-replys.delete',
                            'parents' => ['minapp_content', 'sns_manage'],
                        ],
                        'sns_filter_post' => [
                            'name' => '敏感词库管理',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.sns-filter.post',
                            'parents' => ['minapp_content', 'sns_manage'],
                        ],
                        'sns_filter_category' => [
                            'name' => '添加敏感词库类目',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.sns-filter.category',
                            'parents' => ['minapp_content', 'sns_manage'],
                        ],
                        'sns_upload_filter_manage' => [
                            'name' => '敏感图用户管理',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.sns-upload-filter.index',
                            'parents' => ['minapp_content', 'sns_manage'],
                        ],
                        'sns_upload_filter_del' => [
                            'name' => '删除敏感图用户',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.sns-upload-filter.delete',
                            'parents' => ['minapp_content', 'sns_manage'],
                        ],
                        'cos_images_manage' => [
                            'name' => '敏感图片管理',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.cos-images.index',
                            'parents' => ['minapp_content', 'sns_manage'],
                        ],
                        'cos_images_del' => [
                            'name' => '删除敏感图片',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.cos-images.delete',
                            'parents' => ['minapp_content', 'sns_manage'],
                        ],
                        'cos_video_manage' => [
                            'name' => '敏感视频管理',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.cos-video.index',
                            'parents' => ['minapp_content', 'sns_manage'],
                        ],
                        'cos_video_del' => [
                            'name' => '删除敏感视频',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.cos-video.delete',
                            'parents' => ['minapp_content', 'sns_manage'],
                        ],
                    ],
                ],
                'search_manage' => [
                    'name' => '搜索关键词',
                    'permit' => 1,
                    'menu' => 1,
                    'icon' => '',
                    'url' => 'plugin.minapp-content.admin.search.index',
                    'url_params' => '',
                    'parents' => ['minapp_content'],
                    'child' => [
                        'search_lists' => [
                            'name' => '搜索关键词查看',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.search.lists',
                            'parents' => ['minapp_content', 'search_manage'],
                        ],
                    ],
                ],
                'somato_manage' => [
                    'name' => '体质管理',
                    'permit' => 1,
                    'menu' => 1,
                    'icon' => '',
                    'url' => 'plugin.minapp-content.admin.somato-type.index',
                    'url_params' => '',
                    'parents' => ['minapp_content'],
                    'child' => [
                        'somato_type_edit' => [
                            'name' => '体质类型编辑',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.somato-type.edit',
                            'parents' => ['minapp_content', 'somato_manage'],
                        ],
                        'somato_type_del' => [
                            'name' => '删除体质类型',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.somato-type.delete',
                            'parents' => ['minapp_content', 'somato_manage'],
                        ],
                        'label_manage' => [
                            'name' => '症状标签管理',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.label.index',
                            'parents' => ['minapp_content', 'somato_manage'],
                        ],
                        'label_edit' => [
                            'name' => '症状标签编辑',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.label.edit',
                            'parents' => ['minapp_content', 'somato_manage'],
                        ],
                        'label_del' => [
                            'name' => '删除症状标签',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.label.delete',
                            'parents' => ['minapp_content', 'somato_manage'],
                        ],
                        'question_manage' => [
                            'name' => '测评题库管理',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.question.index',
                            'parents' => ['minapp_content', 'somato_manage'],
                        ],
                        'question_edit' => [
                            'name' => '测评题库编辑',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.question.edit',
                            'parents' => ['minapp_content', 'somato_manage'],
                        ],
                        'question_del' => [
                            'name' => '删除测评题库',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.question.delete',
                            'parents' => ['minapp_content', 'somato_manage'],
                        ],
                        'answer_manage' => [
                            'name' => '用户测评管理',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.answer.index',
                            'parents' => ['minapp_content', 'somato_manage'],
                        ],
                        'answer_detail' => [
                            'name' => '用户测评查看',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.answer.detail',
                            'parents' => ['minapp_content', 'somato_manage'],
                        ],
                        'answer_del' => [
                            'name' => '删除用户测评',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.answer.delete',
                            'parents' => ['minapp_content', 'somato_manage'],
                        ],
                    ],
                ],
                'banner_manage' => [
                    'name' => '轮播图管理',
                    'permit' => 1,
                    'menu' => 1,
                    'icon' => '',
                    'url' => 'plugin.minapp-content.admin.banner.index',
                    'url_params' => '',
                    'parents' => ['minapp_content'],
                    'child' => [
                        'banner_edit' => [
                            'name' => '轮播图编辑',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.banner.edit',
                            'parents' => ['minapp_content', 'banner_manage'],
                        ],
                        'banner_del' => [
                            'name' => '删除轮播图',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.banner.delete',
                            'parents' => ['minapp_content', 'banner_manage'],
                        ],
                        'banner_display' => [
                            'name' => '轮播图状态管理',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.banner.display',
                            'parents' => ['minapp_content', 'banner_manage'],
                        ],
                        'banner_list' => [
                            'name' => '轮播图列表',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.banner.index',
                            'parents' => ['minapp_content', 'banner_manage'],
                        ],
                        'banner_position' => [
                            'name' => '轮播位列表',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.banner-position.index',
                            'parents' => ['minapp_content', 'banner_manage'],
                        ],
                        'banner_position_edit' => [
                            'name' => '轮播位添加|编辑',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.banner-position.edit',
                            'parents' => ['minapp_content', 'banner_manage'],
                        ],
                        'banner_position_delete' => [
                            'name' => '轮播位删除',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.banner-position.delete',
                            'parents' => ['minapp_content', 'banner_manage'],
                        ],
                        'system_category_manage' => [
                            'name' => '首页功能区分类管理',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.system-category.index',
                            'parents' => ['minapp_content', 'banner_manage'],
                        ],
                        'system_category_edit' => [
                            'name' => '首页功能区分类编辑',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.system-category.edit',
                            'parents' => ['minapp_content', 'banner_manage'],
                        ],
                        'system_category_status' => [
                            'name' => '首页功能区分类状态管理',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.system-category.status',
                            'parents' => ['minapp_content', 'banner_manage'],
                        ],
                        'system_category_del' => [
                            'name' => '删除首页功能区分类',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.system-category.delete',
                            'parents' => ['minapp_content', 'banner_manage'],
                        ],
                        'system_image_manage' => [
                            'name' => '系统图片管理',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.system-image.index',
                            'parents' => ['minapp_content', 'banner_manage'],
                        ],
                        'system_image_edit' => [
                            'name' => '系统图片编辑',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.system-image.edit',
                            'parents' => ['minapp_content', 'banner_manage'],
                        ],
                        'system_image_status' => [
                            'name' => '系统图片状态管理',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.system-image.status',
                            'parents' => ['minapp_content', 'banner_manage'],
                        ],
                        'system_image_del' => [
                            'name' => '删除系统图片',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.system-image.delete',
                            'parents' => ['minapp_content', 'banner_manage'],
                        ],
                        'system_notice_manage' => [
                            'name' => '系统通知管理',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.system-notice.index',
                            'parents' => ['minapp_content', 'banner_manage'],
                        ],
                        'system_notice_edit' => [
                            'name' => '系统通知编辑',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.system-notice.edit',
                            'parents' => ['minapp_content', 'banner_manage'],
                        ],
                        'system_notice_status' => [
                            'name' => '系统通知状态管理',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.system-notice.status',
                            'parents' => ['minapp_content', 'banner_manage'],
                        ],
                        'system_notice_del' => [
                            'name' => '删除系统通知',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.system-notice.delete',
                            'parents' => ['minapp_content', 'banner_manage'],
                        ],

                        'hot_spot_manage' => [
                            'name' => '首页热区图片管理',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.hot-spot.index',
                            'parents' => ['minapp_content', 'banner_manage'],
                        ],
                        'hot_spot_edit' => [
                            'name' => '编辑热区图片',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.hot-spot.edit',
                            'parents' => ['minapp_content', 'banner_manage'],
                        ],
                        'hot_spot_status' => [
                            'name' => '热区显示状态管理',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.hot-spot.status',
                            'parents' => ['minapp_content', 'banner_manage'],
                        ],
                        'hot_spot_del' => [
                            'name' => '删除热区',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.hot-spot.delete',
                            'parents' => ['minapp_content', 'banner_manage'],
                        ],
                    ],
                ],
                'sport_clock' => [
                    'name' => '运动打卡管理',
                    'permit' => 1,
                    'menu' => 1,
                    'icon' => '',
                    'url' => 'plugin.minapp-content.admin.sport-clock.step',
                    'url_params' => '',
                    'parents' => ['minapp_content'],
                    'child' => [
                        'step_exchange_list' => [
                            'name' => '兑换步数列表',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.sport-clock.step-exchange-list',
                            'parents' => ['minapp_content', 'sport_clock'],
                        ],

                    ],
                ],
                'quick_comment_list' => [
                    'name' => '快捷评语管理',
                    'permit' => 1,
                    'menu' => 1,
                    'icon' => '',
                    'url' => 'plugin.minapp-content.admin.quick-comment.index',
                    'url_params' => '',
                    'parents' => ['minapp_content'],
                    'child' => [
                        'quick_comment_edit' => [
                            'name' => '编辑|添加快捷评语',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.quick-comment.edit',
                            'parents' => ['minapp_content', 'quick_comment_list'],
                        ],
                        'quick_comment_delete' => [
                            'name' => '删除快捷评语',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.quick-comment.delete',
                            'parents' => ['minapp_content', 'quick_comment_list'],
                        ],
                        'quick_comment_display' => [
                            'name' => '快捷评语显示|隐藏',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.quick-comment.display',
                            'parents' => ['minapp_content', 'quick_comment_list'],
                        ],
                    ],
                ],
                'custom_share_list' => [
                    'name' => '自定义分享管理',
                    'permit' => 1,
                    'menu' => 1,
                    'icon' => '',
                    'url' => 'plugin.minapp-content.admin.custom-share.index',
                    'url_params' => '',
                    'parents' => ['minapp_content'],
                    'child' => [
                        'custom_share_edit' => [
                            'name' => '编辑|添加自定义分享',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.custom-share.edit',
                            'parents' => ['minapp_content', 'custom_share_list'],
                        ],
                    ],
                ],
            ],
        ]);
    }
}
