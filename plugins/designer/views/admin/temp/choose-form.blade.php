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
        height: 60px;
        table-layout:fixed;WORD-BREAK:break-all;WORD-WRAP:break-word;
    }
</style>
<div id="floating-form"  class="modal fade" tabindex="-1" style="z-index:99999">
    <div class="modal-dialog" style='width: 920px;'>
        <div class="modal-content">
            <div class="modal-header"><button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button><h3>选择表单</h3></div>
            <div class="modal-body" >
                <div class="row" style="padding:0px 15px;">
                    <div class="input-group">
                        <input type="text" class="form-control" name="keyword" value="" id="select-form" placeholder="请输入表单名称进行查询筛选" />
                        <span class='input-group-btn'><button type="button" class="btn btn-default" ng-click="selectform(focus);" id="selectform">搜索</button></span>
                    </div>
                </div>
                <div id="module-menus" style="padding-top:5px; overflow: auto;max-height:500px;">
                    <table class="article-table">
                        <thead>
                        <tr>
                            <th style="width: 3%">选择</th>
                            <th width="6%">ID</th>
                            <th width="18%">名称</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr ng-repeat="form in selectForm" >
                            <td><input type="radio" name = "designer-form" ng-click="pushForm(focus, form.id,$event)"></td>
                            <td>@{{form.form_id}}</td>
                            <td>@{{form.title}}</td>
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