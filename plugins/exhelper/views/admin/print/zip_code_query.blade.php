@extends('layouts.base')

@section('content')
@section('title', trans('邮编查询'))
<style>
    .bootstrap-select{width:0;padding:0;margin:0;}
    .dropdown-toggle .pull-left{margin:0;height:20px;line-height:20px;padding:0;margin:0}
</style>
<div class="w1200 m0a" style="height:100vh">
    <div class="rightlist">
        <form action="" method="post" class='form-horizontal'>

            <div class="form-group" style="padding-top: 50px;">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">	省/州</label>
                <div class="col-sm-8 col-xs-12">
                    <select name='provinces_id' id="provinces_id" class='form-control diy-notice'>
                        <option  value="" selected  value="" >
                            请选择
                        </option>
                        @foreach($provinces as $item)
                            <option value="{{$item['provCode']}}" data-name=""  onClick="changePro(this)">{{$item['provName']}}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">城市(区/县)</label>
                <div class="col-sm-8 col-xs-12">
                    <select name='cityList' id="cityList" class='form-control diy-notice'>
                        <option  value="" selected  value="" >
                            请选择
                        </option>
                    </select>
                </div>
            </div>
            <div style="display:none" class="display" style="overflow: hidden;text-align: center;">
                <p style="margin: auto;width:300px;" id="tips"></p>
                <div id="code" style="height: 200px;width:250px;margin: auto;overflow:auto;overflow-y:scroll; "></div>
            </div>


            {{--</div>--}}
        </form>
    </div>
</div>
<script>

let optionstring = '';
let abc = '';
    $('#provinces_id').change(function(){
        let ul = $('.rewrite').find('div').children('ul');
        let sel = $('.rewrite').find('#cityList');
        console.log(sel)
        var provinces_id= $(this).val();

        $.ajax({
            url: "{!! yzWebUrl('plugin.exhelper.admin.print-once.city') !!}",
            type: "post",
            data: {provinces_id: provinces_id},
            cache: false,
            success: function (data) {
                console.log(data);
                $.each(data.data,function(key,value){  //循环遍历后台传过来的json数据
                    abc +=  `<option  value=`+value.cityCode+` >`+ value.cityName + `</option>`;
                });
                $('#cityList').append(abc);
            }
        })
        console.log($(this).children('option:selected')[0].innerText)
    });

    $('#cityList').change(function(){

        let zip_code = '';
        var city_id= $(this).val();
        var provinces_id= $('#provinces_id').val();

        $.ajax({
            url: "{!! yzWebUrl('plugin.exhelper.admin.print-once.query-post-code') !!}",
            type: "post",
            data: {city_id: city_id,provinces_id:provinces_id},
            cache: false,
            success: function (data) {
                $.each(data.data,function(key,value){  //循环遍历后台传过来的json数据
                    zip_code +=  `<p>`+ value + `</p>`;
                });

                $("#code").html(zip_code);

            }

        })
        var str = "您所查找的中国"+$('#provinces_id').children('option:selected')[0].innerText+$(this).children('option:selected')[0].innerText +"邮政编码为:";
        $("#tips").text(str);
        $(".display").css('display','block')
        console.log($(this).children('option:selected')[0].innerText)


    });
    console.log($(this));
    function changePro(e) {
        console.log(e)
    }



</script>
<script type="text/javascript">
    $('.diy-notice').select2();
</script>
@endsection