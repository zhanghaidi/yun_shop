<!-- choose good start -->
<style>
    .article-table {
        width: 840px;
        margin: auto;
    }
    .article-table th{
        text-align: center;
        background-color: #eeeeee;
        height: 40px;
    }
    .article-table td{
        text-align: center;
        border-bottom: 1px #eeeeee solid;
        height: 50px;
        table-layout:fixed;WORD-BREAK:break-all;WORD-WRAP:break-word;
    }
</style>
<div id="floating-article"  class="modal fade" tabindex="-1" style="z-index:99999">
    <div class="modal-dialog" style='width: 920px;'>
        <div class="modal-content">
            <div class="modal-header"><button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button><h3>选择文章</h3></div>
            <div class="modal-body" >
                <div class="row" style="padding:0px 15px;">
                    <div class="input-group">
                        <input type="text" class="form-control" name="keyword" value="" id="select-article" placeholder="请输入文章名称进行查询筛选" />
                        <span class='input-group-btn'><button type="button" class="btn btn-default" ng-click="selectarticle(focus);" id="selectgood">搜索</button></span>
                    </div>
                </div>
                <div id="module-menus" style="padding-top:5px; overflow: auto;max-height:500px;">
                    <table class="article-table">
                        <thead>
                            <tr>
                                <th style="width: 3%">选择</th>
                                <th style="width: 6%">ID</th>
                                <th style="width: 12%">文章标题</th>
                                <th style="width: 6%">文章分类</th>
                                <th style="width: 6%">文章创建时间</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat="article in selectArticle" >
                                <td><input type="checkbox" ng-click="pushArticle(focus, article.id,$event)"></td>
                                <td>@{{article.id}}</td>
                                <td>@{{article.title}}</td>
                                <td>@{{article.category}}</td>
                                <td>@{{article.time}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer"><a href="#" class="btn btn-default" data-dismiss="modal" aria-hidden="true">关闭</a></div>
        </div>
    </div>
</div>
<!-- choose good end -->