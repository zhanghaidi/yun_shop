@extends('layouts.base')
@section('title', '模板管理')
@section('content')
<style type="text/css">

img {height: 420px; width: 220px;}

.out-border {
    height: 470px;
    width: 232px;
    float: left;
    border: 1px solid #dfdddc;
    /*border: 1px solid black;*/
    border-radius: 6px;
    padding: 6px;
    margin: 20px 20px; 
}
.img-border {
    width: 220px;
    height: 420px;
    border: 1px solid #dfdddc;
    /*border: 1px solid black;*/
}
.click-border {
    /*border: 1px solid black;*/
    padding: 3px;
    float: left;
    width: 220px;
    height: 40px;
    margin-top: 5px;
}
.tip-font-style {
    width: 220px;
    text-align: left;
    line-height: 14px;
    font-size: 14px;
}
.use-position {
    /*border: 1px solid black;*/
    text-align: right;
    float: right;
    margin-top: -3px;
}
.mui-switch:checked:before {
    /*left: 21px; */
    left: 16px; 
}

.mui-switch:before {
    width: 23px;
    height: 23px;
}

.switch-style {
    width: 39px;
    height: 23px;
}

</style>
    <div class="w1200 m0a">
        <!-- 导入CSS样式 -->
        <div class="rightlist">
            <!-- 筛选区域 -->
            <div class="panel panel-info">
                <div class="right-titpos">
                    <ul class="add-snav">
                        <li class="active"><a href="#">模板设置</a></li>
                    </ul>
                </div>
                <div class="panel panel-info">
                    <ul class="add-shopnav" id="myTab">
                        <li id="all" class="active"><a href="#">全部</a></li>
                        <li id="member_center"><a href="#">会员中心</a></li>
                        <li id="extension_center"><a href="#">推广中心</a></li>
                        <li id="category_center"><a href="#">分类中心</a></li>
                        <li id="goods_module"><a href="#">商品模版</a></li>
                    </ul>
                </div>
            </div>
            <!-- 页面列表 -->
            <div class='panel panel-default'>
                <div class='panel-heading'></div>
                <div class='panel-body' id="extension">
                        <div class="out-border">
                            <div class="img-border">
                                <img src="{{resource_get('plugins/designer/assets/images/extension_01.png')}}">
                            </div>
                            <div style="float: clear;"></div>
                            <div class="click-border">
                                <span class="tip-font-style">推广中心_01</span> 
                                <div class="use-position">
                                    <input type="checkbox" class="mui-switch  switch-style mui-switch-animbg" @if(!empty($data['extension']) && $data['extension']['names'] == '01') checked @endif>
                                </div>
                            </div>
                        </div>
                        <div class="out-border">
                            <div class="img-border">
                                <img src="{{resource_get('plugins/designer/assets/images/extension_02.png')}}">
                            </div>
                            <div style="float: clear;"></div>
                            <div class="click-border">
                                <span class="tip-font-style">推广中心_02</span> 
                                <div class="use-position">
                                    <input type="checkbox" class="mui-switch  switch-style mui-switch-animbg" @if(!empty($data['extension']) && $data['extension']['names'] == '02') checked @endif>
                                </div>
                            </div>
                        </div>
                </div>
                <div class='panel-body' id="member">
                    <div class="out-border">
                        <div class="img-border">
                            <img src="{{resource_get('plugins/designer/assets/images/member_01.png')}}">
                        </div>
                        <div style="float: clear;"></div>
                        <div class="click-border">
                            <span class="tip-font-style">会员中心_01</span> 
                            <div class="use-position">
                                <input type="checkbox" class="mui-switch  switch-style mui-switch-animbg" @if(!empty($data['member']) && $data['member']['names'] == '01') checked @endif>
                            </div>
                        </div>
                    </div>
                    <div class="out-border">
                        <div class="img-border">
                            <img src="{{resource_get('plugins/designer/assets/images/member_02.png')}}">
                        </div>
                        <div style="float: clear;"></div>
                        <div class="click-border">
                            <span class="tip-font-style">会员中心_02</span> 
                            <div class="use-position">
                                <input type="checkbox" class="mui-switch  switch-style mui-switch-animbg" @if(!empty($data['member']) && $data['member']['names'] == '02') checked @endif>
                            </div>
                        </div>
                    </div>
                </div>

                <div class='panel-body' id="category">
                    <div class="out-border">
                        <div class="img-border">
                            <img src="{{resource_get('plugins/designer/assets/images/category_01.png')}}">
                        </div>
                        <div style="float: clear;"></div>
                        <div class="click-border">
                            <span class="tip-font-style">分类中心_01</span>
                            <div class="use-position">
                                <input type="checkbox" class="mui-switch  switch-style mui-switch-animbg" @if(!empty($data['category']) && $data['category']['names'] == '01') checked @endif>
                            </div>
                        </div>
                    </div>
                    <div class="out-border">
                        <div class="img-border">
                            <img src="{{resource_get('plugins/designer/assets/images/category_02.png')}}">
                        </div>
                        <div style="float: clear;"></div>
                        <div class="click-border">
                            <span class="tip-font-style">分类中心_02</span>
                            <div class="use-position">
                                <input type="checkbox" class="mui-switch  switch-style mui-switch-animbg" @if(!empty($data['category']) && $data['category']['names'] == '02') checked @endif>
                            </div>
                        </div>
                    </div>
                </div>

                <div class='panel-body' id="goods">
                    <div class="out-border">
                        <div class="img-border">
                            <img src="{{resource_get('plugins/designer/assets/images/goods_01.png')}}">
                        </div>
                        <div style="float: clear;"></div>
                        <div class="click-border">
                            <span class="tip-font-style">商品模版_01</span>
                            <div class="use-position">
                                <input type="checkbox" class="mui-switch  switch-style mui-switch-animbg" @if(!empty($data['goods']) && $data['goods']['names'] == '01') checked @endif>
                            </div>
                        </div>
                    </div>
                    <div class="out-border">
                        <div class="img-border">
                            <img src="{{resource_get('plugins/designer/assets/images/goods_02.png')}}">
                        </div>
                        <div style="float: clear;"></div>
                        <div class="click-border">
                            <span class="tip-font-style">商品模版_02</span>
                            <div class="use-position">
                                <input type="checkbox" class="mui-switch  switch-style mui-switch-animbg" @if(!empty($data['goods']) && $data['goods']['names'] == '02') checked @endif>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
    </div>


    <script type="text/javascript">

        $('#all').click(function() {
            $(this).addClass('active');
            $(this).siblings().removeClass('active');
            $('#member').show();
            $('#extension').show();
            $('#goods').show();
            $('#category').show();
        });

        $('#member_center').click(function() {
            $(this).addClass('active');
            $(this).siblings().removeClass('active');
            $('#extension').hide();
            $('#category').hide();
            $('#goods').hide();
            $('#member').show();
        });

        $('#extension_center').click(function() {
            $(this).addClass('active');
            $(this).siblings().removeClass('active');
            $('#member').hide();
            $('#category').hide();
            $('#goods').hide();
            $('#extension').show();
        });
        $('#category_center').click(function() {
            $(this).addClass('active');
            $(this).siblings().removeClass('active');
            $('#extension').hide();
            $('#member').hide();
            $('#goods').hide();
            $('#category').show();
        });
        $('#goods_module').click(function() {
            $(this).addClass('active');
            $(this).siblings().removeClass('active');
            $('#extension').hide();
            $('#member').hide();
            $('#category').hide();
            $('#goods').show();
        });
        //获取所选择的模板名称
        $(".use-position").click(function() {

            var str = $(this).prev().text();
            console.log(str);

            var type = str.split('_')[0];
            var names = '';

            var btnobj = $(this).parent().parent().siblings().children().eq(-1).children().eq(-1).children();

            //判断是否为选中，
            if ($(this).children().is(':checked')) {
                //如果为选中则获取该数组的值
                names = str.split('_')[1];

                if (btnobj.is(':checked')) {
                    btnobj.prop('checked',false);
                }
            } else {
                //否则获取另外一个的值
                names = $(this).parent().parent().siblings().children().eq(-1).children().eq(0).text().split('_')[1];
                console.log(names);
                if (btnobj.prop('checked') != true) {
                    btnobj.prop("checked",true);
                }
            }

            //获取图片地址
            var path = $(this).parent().prev().prev().children().attr('src');
            console.log('type:'+type+'-names:'+names+'+ path: '+path);

            $.ajax({
                dataType: 'json',
                type: 'post',
                url: "{!! yzWebUrl('plugin.designer.admin.Template.setTmp')!!}",
                data: {type: type, names: names, path: path},
                success: function(msg) {
                    // console.log(msg);
                    if (msg.result == 1) {
                        //成功
                        alert('操作成功');
                    } else {
                        alert(msg.msg);
                    }
                }
            });
        });
        
    </script>

@endsection