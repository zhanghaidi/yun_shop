<div class="fe-mod fe-mod-11 fe-mod-nohover">
    <div class="fe-mod-11-title" ng-style="{'background-color':page.params.bgcolor,'color':page.params.color}">
        {{page.params.title || '默认标题'}}
    </div>
</div>
<div class="maintopmenu" ng-show="page.params.top_menu == 1" style="background: #fafafa;border-color: #fafafa;">
    <div class="show-topmenu-input">
        <input type="text" name="" id="" value="搜索：输入关键字在店内搜索" />
        <i class="fa fa-search"></i>
    </div>
    <div id="topmenu" class="maincate" >
        <a class="lis-a" href="#" style="background: #fafafa;">菜单一</li>
        <a class="lis-a" href="#">菜单2</a>
        <a class="lis-a" href="#">菜单3</a>
        <a class="lis-a" href="#">菜单4</a> 
    </div>
</div>
<script type="text/javascript">
	$(function(){
		var lis = $("#topmenu .lis-a").length;
		if(lis==1){
			$("#topmenu .lis-a").css("width","97.5%");
		}
		if(lis==2){
			$("#topmenu .lis-a").css("width","47.5%");
		}
		if(lis==3){
			$("#topmenu .lis-a").css("width","30.8%");
		}
		if(lis==4){
			$("#topmenu .lis-a").css("width","22.5%");
		}
		if(lis>=5){
			$("#topmenu .lis-a").css("width","17.5%");
		}
	});
</script>