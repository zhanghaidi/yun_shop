<?php

namespace Yunshop\Examination;

use Yunshop\Examination\services\ExaminationService;

class PluginApplication extends \app\common\services\PluginApplication
{
    protected function setConfig()
    {
    }

    protected function setMenuConfig()
    {
        \app\backend\modules\menu\Menu::current()->setPluginMenu(ExaminationService::get(), [
            'name' => ExaminationService::get('name'),
            'type' => 'marketing',
            'url' => 'plugin.examination.admin.question.index', // url 可以填写http 也可以直接写路由
            'url_params' => '', //如果是url填写的是路由则启用参数否则不启用
            'permit' => 1, //如果不设置则不会做权限检测
            'menu' => 1, //如果不设置则不显示菜单，子菜单也将不显示
            'top_show' => 0,
            'left_first_show' => 0,
            'left_second_show' => 1,
            'icon' => '', //菜单图标
            'list_icon' => 'fdd_contract',
            'parents' => [],
            'child' => [
                'question_manage' => [
                    'name' => '题库管理',
                    'permit' => 1,
                    'menu' => 1,
                    'icon' => '',
                    'url' => 'plugin.examination.admin.question.index',
                    'url_params' => '',
                    'parents' => ['examination'],
                    'child' => [
                        'question_add' => [
                            'name' => '添加题库',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.examination.admin.question.add',
                            'parents' => ['examination', 'question_manage'],
                        ],
                        'question_edit' => [
                            'name' => '编辑题库',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.examination.admin.question.edit',
                            'parents' => ['examination', 'question_manage'],
                        ],
                        'question_del' => [
                            'name' => '删除题库',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.examination.admin.question.del',
                            'parents' => ['examination', 'question_manage'],
                        ],
                    ],
                ],

                'question_sort_manage' => [
                    'name' => '题库分类管理',
                    'permit' => 1,
                    'menu' => 1,
                    'icon' => '',
                    'url' => 'plugin.examination.admin.question-sort.index',
                    'url_params' => '',
                    'parents' => ['examination'],
                    'child' => [
                        'question_sort_edit' => [
                            'name' => '编辑题库分类',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.examination.admin.question-sort.edit',
                            'parents' => ['examination', 'question_sort_manage'],
                        ],
                        'question_sort_del' => [
                            'name' => '删除题库分类',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.examination.admin.question-sort.del',
                            'parents' => ['examination', 'question_sort_manage'],
                        ],
                    ],
                ],

                'paper_manage' => [
                    'name' => '试卷管理',
                    'permit' => 1,
                    'menu' => 1,
                    'icon' => '',
                    'url' => 'plugin.examination.admin.paper.index',
                    'url_params' => '',
                    'parents' => ['examination'],
                    'child' => [
                        'paper_edit' => [
                            'name' => '编辑试卷',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.examination.admin.paper.edit',
                            'parents' => ['examination', 'paper_manage'],
                        ],
                        'paper_del' => [
                            'name' => '删除试卷',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.examination.admin.paper.del',
                            'parents' => ['examination', 'paper_manage'],
                        ],
                    ],
                ],

                'examination_manage' => [
                    'name' => '考试管理',
                    'permit' => 1,
                    'menu' => 1,
                    'icon' => '',
                    'url' => 'plugin.examination.admin.examination.index',
                    'url_params' => '',
                    'parents' => ['examination'],
                    'child' => [
                        'examination_edit' => [
                            'name' => '编辑考试',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.examination.admin.examination.edit',
                            'parents' => ['examination', 'examination_manage'],
                        ],
                        'examination_status' => [
                            'name' => '考试状态',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.examination.admin.examination.status',
                            'parents' => ['examination', 'examination_manage'],
                        ],
                        'examination_answer' => [
                            'name' => '批阅答卷',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.examination.admin.examination.answer',
                            'parents' => ['examination', 'examination_manage'],
                        ],
                        'examination_member' => [
                            'name' => '考试人员',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.examination.admin.examination.member',
                            'parents' => ['examination', 'examination_manage'],
                        ],
                    ],
                ],

            ],
        ]);
    }

    public function boot()
    {
        // $events = app('events');
        // $events->listen(\Yunshop\FaceAnalysis\Events\NewAnalysisSubmit::class,\Yunshop\FaceAnalysis\Listener\AnalysisLogRanking::class);
    }
}
