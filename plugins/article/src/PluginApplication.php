<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/12/24
 * Time: 3:38 PM
 */

namespace Yunshop\Article;

use app\common\events\RenderingMyLink;

class PluginApplication extends \app\common\services\PluginApplication
{
    protected function setConfig()
    {

    }
    protected function setMenuConfig()
    {
        \app\backend\modules\menu\Menu::current()->setPluginMenu('article', [
            'name' => '文章营销',
            'type' => 'marketing',
            'url' => 'plugin.article.admin.article.index',// url 可以填写http 也可以直接写路由
            'url_params' => '',//如果是url填写的是路由则启用参数否则不启用
            'permit' => 1,//如果不设置则不会做权限检测
            'menu' => 1,//如果不设置则不显示菜单，子菜单也将不显示
            'icon' => '',//菜单图标
            'list_icon' => 'article',
            'parents' => [],
            'top_show' => 0,
            'left_first_show' => 0,
            'left_second_show' => 1,
            'child' => [
                'plugin.article.admin.set' => [
                    'name' => '基础设置',
                    'permit' => 1,
                    'menu' => 1,
                    'icon' => '',
                    'url' => 'plugin.article.admin.set.index',
                    'url_params' => '',
                    'parents' => ['article'],
                ],

                'plugin_article_writings_index' => [
                    'name' => '文章管理',
                    'permit' => 1,
                    'menu' => 1,
                    'icon' => '',
                    'url' => 'plugin.article.admin.article.index',
                    'url_params' => '',
                    'item' => 'plugin_article_writings_index',
                    'parents' => ['article'],
                    'child' => [

                        'plugin_article_writings_see' => [
                            'name' => '浏览列表',
                            'permit' => 1,
                            'menu' => 1,
                            'icon' => '',
                            'item' => 'plugin_article_writings_see',
                            'url' => 'plugin.article.admin.article.index',
                            'url_params' => '',
                            'parents' => ['article', 'plugin_article_writings_index'],
                        ],

                        'plugin_article_writings_add' => [
                            'name' => '添加文章',
                            'permit' => 1,
                            'menu' => 1,
                            'icon' => '',
                            'item' => 'plugin_article_writings_add',
                            'url' => 'plugin.article.admin.article.add',
                            'url_params' => '',
                            'parents' => ['article', 'plugin_article_writings_index'],
                        ],

                        'plugin_article_writings_edit' => [
                            'name' => '修改文章',
                            'permit' => 1,
                            'menu' => 1,
                            'icon' => '',
                            'item' => 'plugin_article_writings_edit',
                            'url' => 'plugin.article.admin.article.edit',
                            'url_params' => '',
                            'parents' => ['article', 'plugin_article_writings_index'],
                        ],

                        'plugin_article_writings_destroy' => [
                            'name' => '删除文章',
                            'permit' => 1,
                            'menu' => 1,
                            'icon' => '',
                            'item' => 'plugin_article_writings_destroy',
                            'url' => 'plugin.article.admin.article.deleted',
                            'url_params' => '',
                            'parents' => ['article', 'plugin_article_writings_index'],
                        ],

                        'plugin_article_writings_log' => [
                            'name' => '查看记录',
                            'permit' => 1,
                            'menu' => 1,
                            'icon' => '',
                            'item' => 'plugin_article_writings_log',
                            'url' => 'plugin.article.admin.article.log',
                            'url_params' => '',
                            'parents' => ['article', 'plugin_article_writings_index'],
                        ],

                        'plugin_article_writings_share' => [
                            'name' => '分享记录',
                            'permit' => 1,
                            'menu' => 1,
                            'icon' => '',
                            'item' => 'plugin_article_writings_share',
                            'url' => 'plugin.article.admin.article.share',
                            'url_params' => '',
                            'parents' => ['article', 'plugin_article_writings_index'],
                        ],
                    ]
                ],

                'plugin_article_category' => [
                    'name' => '文章分类',
                    'permit' => 1,
                    'menu' => 1,
                    'icon' => '',
                    'url' => 'plugin.article.admin.category.index',
                    'url_params' => '',
                    'parents' => ['article'],
                    'child' => [

                        'plugin_article_category_see' => [
                            'name' => '查看分类',
                            'permit' => 1,
                            'menu' => 1,
                            'icon' => '',
                            'item' => 'plugin_article_category_see',
                            'url' => 'plugin.article.admin.category.index',
                            'url_params' => '',
                            'parents' => ['article', 'plugin_article_category'],
                        ],

                        'plugin_article_category_add' => [
                            'name' => '添加分类',
                            'permit' => 1,
                            'menu' => 1,
                            'icon' => '',
                            'item' => 'plugin_article_category_add',
                            'url' => 'plugin.article.admin.category.add',
                            'url_params' => '',
                            'parents' => ['article', 'plugin_article_category'],
                        ],

                        'plugin_article_category_edit' => [
                            'name' => '修改分类',
                            'permit' => 1,
                            'menu' => 1,
                            'icon' => '',
                            'item' => 'plugin_article_category_edit',
                            'url' => 'plugin.article.admin.category.edit',
                            'url_params' => '',
                            'parents' => ['article', 'plugin_article_category'],
                        ],

                        'plugin_article_category_destroy' => [
                            'name' => '删除分类',
                            'permit' => 1,
                            'menu' => 1,
                            'icon' => '',
                            'item' => 'plugin_article_category_destroy',
                            'url' => 'plugin.article.admin.category.deleted',
                            'url_params' => '',
                            'parents' => ['article', 'plugin_article_category'],
                        ]
                    ]
                ],


                //临时取消文章采集功能
                /*'plugin.article.admin.article.collect' => [
                    'name' => '文章采集',
                    'permit' => 1,
                    'menu' => 0,
                    'icon' => '',
                    'url' => 'plugin.article.admin.article.collect',
                    'urlParams' => [],
                    'parents'=>['article'],
                ],*/
            ]
        ]);

    }

    public function boot()
    {
        $events = app('events');
        $events->listen(RenderingMyLink::class, function ($event) {


            $event->addContent(' <div role="tabpanel" class="tab-pane link_cate" id="link_article">
                    <div class="input-group">
                             <span class="input-group-addon" style=\'padding:0px; border: 0px;\'>
                                 <select class="form-control tpl-category-parent" name="article_category" id="select-article-ca" style=\'width: 150px; border-radius: 4px 0px 0px 4px; border-right: 0px;\'>
                                     <option value="" selected="selected">全部分类</option>
                                     @foreach ($mylink_data[\'categorys\'] as $category)
                                         <option value="{{ $category[\'id\'] }}">{{ $category[\'category_name\'] }}</option>
                                     @endforeach
                                 </select>
                             </span>
                        <input type="text" class="form-control" value="" id="select-article-kw" placeholder="请输入文章标题进行搜索">
                        <span class="input-group-btn"><button type="button" class="btn btn-default" id="select-article-btn">搜索</button></span>
                    </div>
                    <div class="mylink-con" style="height:266px;">
                        <div class="mylink-line">
                            <label class="label label-primary" style="margin-right:5px;">文章列表</label>
                            {{ $mylink_data[\'article_sys\'][\'article_title\'] }}
                            <div class="mylink-sub">
                                <a href="javascript:;" class="mylink-nav" data-href="{php echo $this->createPluginMobileUrl(\'article\',array(\'method\'=>\'article\'))}">选择</a>
                            </div>
                        </div>
                        <div id="select-articles"></div>
                    </div>
                    <script>
                        // ajax 选择文章
                        $("#select-article-btn").click(function(){
                            var category = $("#select-article-ca option:selected").val();
                            var keyword = $("#select-article-kw").val();
                            $.ajax({
                                type: \'POST\',
                                url: "{php echo $this->createPluginWebUrl(\'article\',array(\'method\'=>\'api\',\'apido\'=>\'selectarticles\'))}",
                                data: {category:category,keyword:keyword},
                                dataType:\'json\',
                                success: function(data){
                                    //console.log(data);
                                    $("#select-articles").html("");
                                    if(data){
                                        $.each(data,function(n,value){
                                            var html = \'<div class="mylink-line">[\'+value.category_name+\'] \'+value.article_title;
                                            html+=\'<div class="mylink-sub">\';
                                            html+=\'<a href="javascript:;" class="mylink-nav" data-href="\'+"{php echo $this->createPluginMobileUrl(\'article\')}&aid="+value.id+\'">选择</a>\';
                                            html+=\'</div></div>\';
                                            $("#select-articles").append(html);
                                        });
                                    }
                                }
                            });
                        });
                    </script>
                </div>
');
        });
    }

}