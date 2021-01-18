@extends('layouts.base')
@section('title', '购物车挂件管理')
@section('content')
    <div class="w1200 m0a">
        <div class="rightlist">
            <!-- 新增加右侧顶部三级菜单 -->
            <div class="right-titpos">
                <ul class="add-snav">
                    <li class="active"><a href="{{yzWebUrl('live.live-room.index')}}">直播间列表</a></li>
                    <li><a href="#">&nbsp;<i class="fa fa-angle-double-right"></i> 购物车挂件管理</a></li>
                </ul>
            </div>
            <!-- 新增加右侧顶部三级菜单结束 -->
            <form action="" method='post' class='form-horizontal' enctype="multipart/form-data">
                <div class='panel panel-default'>
                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">挂件排序</label>
                            <div class="col-sm-9 col-xs-12"><input type="text" name="info[sort]" class="form-control" value="{{$info['sort'] ? $info['sort'] : 0}}" placeholder="请输入排序" /></div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">挂件名称： <span style="color:red;">*</span></label>
                            <div class="col-sm-9 col-xs-12"><input type="text" name="info[title]" class="form-control" value="{{$info['title']}}" placeholder="请输入挂件名称" required/></div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">封面图片 <span style="color:red;">*</span></label>
                            <div class="col-sm-9 col-xs-12 col-md-6 detail-logo">
                                {!! app\common\helpers\ImageHelper::tplFormFieldImage('info[thumb]', $info['thumb']) !!}
                                <span class="help-block">建议图片宽高比例为5:4</span>
                            </div>
                        </div>

                       <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">挂件描述： <span style="color:red;">*</span></label>
                            <div class="col-sm-9 col-xs-12"><input type="text" name="info[description]" class="form-control" value="{{$info['description']}}" placeholder="请输入挂件描述" required/></div>
                        </div>


                       {{--  <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">主播头像 <span style="color:red;">*</span></label>
                            <div class="col-sm-9 col-xs-12 col-md-6 detail-logo">
                                {!! app\common\helpers\ImageHelper::tplFormFieldImage('live[header_img]', $live['header_img']) !!}
                                <span class="help-block">建议正方形图片200*200</span>

                            </div>
                        </div>--}}


                        {{-- <div class="form-group">
                             <label class="col-xs-12 col-sm-3 col-md-2 control-label">关联商品</label>
                             <div class="col-sm-9 col-xs-12">
                                 <table class="table">
                                     <tbody id="param-itemsgoods">
                                     @if ($live['goods_ids'])
                                         @foreach ($live->goods() as $k=>$v)
                                             <tr>
                                                 <td style="text-align: center;width: 40px">
                                                     <a href="javascript:;" onclick="deleteParam(this)" style="margin-top:10px;"  title="删除"><i class='fa fa-times'></i></a>
                                                 </td>
                                                 <td  colspan="2">
                                                     <input type="hidden" class="form-control" name="live[goods_ids][]" data-id="{{$v['id']}}" data-name="goodsids"  value="{{$v['id']}}" style="width:200px;float:left"  />
                                                     <input class="form-control" type="text" name="live[goods_names][]" data-id="{{$v['id']}}" data-name="goodsnames" value="{{$v['title']}}" style="width:400px;float:left" readonly="true">
                                                     <span class="input-group-btn">
                                                         <button class="btn btn-default nav-link-goods" type="button" data-id="{{$v['id']}}" onclick="$('#modal-module-menus-goods').modal();$(this).parent().parent().addClass('focusgood')">选择商品</button>
                                                     </span>
                                                 </td>
                                                 <td colspan="2">
                                                  <span class="input-group-btn" style="width:100px;float:left">
                                                     <button class="btn btn-default nav-link-goods" type="button" data-id="" >商品排序:</button>
                                                 </span>
                                                     <input class="form-control" type="text" name="live[goods_sort][]" data-id="{{$v['id']}}" data-name="goods_sort" value="{{$live['goods_sort'][$k]}}" style="width:400px;float:left"/>
                                                     &nbsp; &nbsp;排序值越大，商品越靠前
                                                 </td>
                                             </tr>
                                         @endforeach
                                     @else
                                         <tr>
                                             <td style="text-align: center;width: 40px">
                                                 <a href="javascript:;" onclick="deleteParam(this)" style="margin-top:10px;"  title="删除"><i class='fa fa-times'></i></a>
                                             </td>
                                             <td  colspan="2">
                                                 <input type="hidden" class="form-control" name="live[goods_ids][]" data-id="" data-name="goodsids"  value="" style="width:200px;float:left"  />
                                                 <input class="form-control" type="text" name="live[goods_names][]" data-id="" data-name="goodsnames" value="" style="width:400px;float:left" readonly="true">
                                                 <span class="input-group-btn">
                                                     <button style="width:100px;float:left" class="btn btn-default nav-link-goods" type="button" data-id="" onclick="$('#modal-module-menus-goods').modal();$(this).parent().parent().addClass('focusgood')">选择商品</button>
                                                 </span>
                                             </td>
                                             <td colspan="2">
                                                  <span class="input-group-btn" style="width:100px;float:left">
                                                     <button class="btn btn-default nav-link-goods" type="button" data-id="" >商品排序:</button>
                                                 </span>
                                                 <input class="form-control" type="text" name="live[goods_sort][]" data-id="" data-name="goods_sort" value="0" style="width:400px;float:left"/>
                                                 &nbsp; &nbsp;排序值越大，商品越靠前
                                             </td>
                                         </tr>
                                     @endif
                                     </tbody>

                                     <tbody>
                                     <tr>
                                         <td colspan="3">
                                             <a href="javascript:;" id="add-param_goods" onclick="addParam('goods' )" style="margin-top:10px;" class="btn btn-primary" title="添加商品"><i class="fa fa-plus"></i> 添加商品</a>
                                         </td>
                                     </tr>
                                     </tbody>
                                 </table>
                             </div>
                         </div>--}}

                       {{-- <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">分享标题：<span style="color:red;">*</span></label>
                            <div class="col-sm-9 col-xs-12"><input type="text" name="live[share_title]" class="form-control" value="{{$live['share_title']}}" placeholder="请输入分享标题" required/></div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">分享图片 <span style="color:red;">*</span></label>
                            <div class="col-sm-9 col-xs-12 col-md-6 detail-logo">
                                {!! app\common\helpers\ImageHelper::tplFormFieldImage('live[share_img]', $live['share_img']) !!}
                                <span class="help-block">建议图片宽高比例为5:4</span>
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">直播间有效期</label>
                            <div class="col-sm-9 col-xs-12">
                                {!! app\common\helpers\DateRange::tplFormFieldDateRange('live[time]', [
                               'starttime'=>$live['start_time'] ? $live['start_time'] : date('Y-m-d H:i:s') ,
                               'endtime'=>$live['end_time']  ? $live['end_time'] : date('Y-m-d H:i:s', time() + 86400),
                               'start'=> 0,
                               'end'=> 0
                               ], true) !!}
                            </div>
                        </div>--}}

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">挂件价格</label>
                            <div class="col-sm-9 col-xs-12"><input type="text" name="info[price]" class="form-control" value="{{$info['price'] ? $info['price'] : '0.00'}}" placeholder="请输入价钱"/></div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">挂件路径</label>
                            <div class="col-sm-9 col-xs-12"><input type="text" name="info[page_path]" class="form-control" value="{{$info['page_path']}}" placeholder="请输入路径"/></div>
                        </div>

                    </div>

                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="submit"  name="submit" value="提交" class="btn btn-success"/>
                                {{--<input type="hidden" name="live[id]" value="{{$live['id']}}"/>--}}
                                <input type="button" class="btn btn-default" name="submit" onclick="history.go(-1)" value="返回" style='margin-left:10px;'/>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            {{-- <div id="modal-module-menus-goods" class="modal fade" tabindex="-1"> --}}{{--搜索商品的弹窗--}}{{--
                 <div class="modal-dialog" style='width: 920px;'>
                     <div class="modal-content">
                         <div class="modal-header">
                             <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                             <h3>选择商品</h3>
                         </div>
                         <div class="modal-body">
                             <div class="row">
                                 <div class="input-group">
                                     <input type="text" class="form-control" name="keyword" value="" id="search-kwd-goods" placeholder="请输入商品名称"/>
                                     <span class='input-group-btn'>
                                         <button type="button" class="btn btn-default" onclick="search_goods();">搜索</button>
                                     </span>
                                 </div>
                             </div>
                             <div id="module-menus-goods" style="padding-top:5px;"></div>
                         </div>
                         <div class="modal-footer">
                             <a href="#" class="btn btn-default" data-dismiss="modal" aria-hidden="true">关闭</a>
                         </div>
                     </div>
                 </div>
             </div>--}}
        </div>
    </div>

    {{-- <script>
         function addParam(type) {
             var data = '<tr>' +
                 '<td style="text-align: center;width: 40px">' +
                 '<a href="javascript:;" onclick="deleteParam(this)" style="margin-top:10px;" title="删除"><i class=\'fa fa-times\'></i></a>' +
                 '</td><td colspan="2">' +
                 '<input type="hidden" class="form-control" name="live[goods_ids][]"  data-id="{$id}" data-name="goodsids" value="" placeholder="按钮名称" style="width:200px;float:left"  />' +
                 '<input class="form-control" type="text" data-id="{$id}" data-name="goodsnames" placeholder="" value="" name="live[goods_names][]" style="width:400px;float:left" readonly="true">' +
                 '<span class="input-group-btn"><button class="btn btn-default nav-link-goods" type="button" data-id="{$id}" onclick="$(\'#modal-module-menus-goods\').modal();$(this).parent().parent().addClass(\'focusgood\')">选择商品</button>' +
                 '</span>' +
                 '</td>' +
                 '<td colspan="2">' +
                 '<span class="input-group-btn" style="width:100px;float:left">' +
                 '<button class="btn btn-default nav-link-goods" type="button" data-id="" >商品排序:</button>' +
                 '</span>' +
                 '<input class="form-control" type="text" name="live[goods_sort][]" data-id="" data-name="goods_sort" value="0" style="width:400px;float:left"/>&nbsp; &nbsp;排序值越大，商品越靠前' +
                 '</td>' +
                 '</tr>';
             $('#param-items'+type).append(data);
         }

         function deleteParam(o) {
             $(o).parent().parent().remove();
         }
         function saveadd(o) {
             $(o).parent().parent().remove();
         }

         --}}{{--搜索商品--}}{{--
         function search_goods() {
             if ($.trim($('#search-kwd-goods').val()) == '') {
                 Tip.focus('#search-kwd-goods', '请输入关键词');
                 return;
             }
             $("#module-menus-goods").html("正在搜索....");
             $.get('{!! yzWebUrl('goods.goods.get-search-goods') !!}', {
                     keyword: $.trim($('#search-kwd-goods').val())
                 }, function (dat) {
                     $('#module-menus-goods').html(dat);
                 }
             );
         }
         function select_good(o) {
             console.log(o);
             $(".focusgood:last input[data-name=goodsids]").val(o.id);
             $(".focusgood:last input[data-name=goodsnames]").val(o.title);
             $(".focusgood").removeClass("focusgood");
             $("#modal-module-menus-goods .close").click();
         }
     </script>--}}
@endsection