<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style='color:red'>*</span> 海报名称</label>
    <div class="col-sm-9 col-xs-12">
        <input type="text" name="poster[title]" class="form-control" value="{{$poster['title']}}" />
    </div>
</div>

<!--预留给后期添加活动海报
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style='color:red'>*</span> 海报类型</label>
                    <div class="col-sm-9 col-xs-12">
                        <label class="radio-inline">
                            <input type="radio" name="poster[type]" value="1" checked onclick="showGoodsSelect(false)"/> 活动海报
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="poster[type]" value="2" @if($poster['type']==2)checked @endif"/> 长期海报
                        </label>
                    </div>
                </div>
-->

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style='color:red'>*</span> 关键词</label>
    <div class="col-sm-9 col-xs-12">
        <input type="text" name="poster[keyword]" class="form-control" value="{{$poster['keyword']}}" />
        <span class='help-block'>触发生成海报的关键词</span>
    </div>
</div>

<!-- 预留给"活动海报"-->
{{--<div class="form-group">--}}
    {{--<label class="col-xs-12 col-sm-3 col-md-2 control-label">活动有效期</label>--}}
    {{--<div class="col-sm-9 col-xs-12">--}}
            {{--{!!tpl_form_field_daterange('time', array(--}}
                    {{--'start'=>date('Y-m-d H:i', isset($poster['time_start']) ? $poster['time_start'] : strtotime('today')),--}}
                    {{--'end'=>date('Y-m-d H:i', isset($poster['time_end']) ? $poster['time_end'] : strtotime('+7 day'))--}}
                    {{--),true)!!}--}}
        {{--<span class='help-block'>粉丝在活动有效期外不能生成海报</span>--}}
        {{--<span class='help-block'>粉丝生成的海报有效期为生成日起到活动结束时间内最长7天</span>--}}
    {{--</div>--}}
{{--</div>--}}


<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否启用</label>
    <div class="col-sm-9 col-xs-12">
        <label class="radio-inline">
            <input type="radio" name="poster[status]" value="1" checked /> 启用
        </label>
        <label class="radio-inline">
            <input type="radio" name="poster[status]" value="0" @if(isset($poster['status']) && ($poster['status']==0))checked @endif /> 禁用
        </label>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">会员中心显示</label>
    <div class="col-sm-9 col-xs-12">
        <label class="radio-inline">
            <input type="radio" name="poster[center_show]" value="1" @if($poster['center_show']==1) checked @endif /> 开启
        </label>
        <label class="radio-inline">
            <input type="radio" name="poster[center_show]" value="0" @if(empty($poster['center_show']) || ($poster['center_show'] != 1))checked @endif /> 关闭
        </label>
        <span class='help-block'>开启状态：会员中心推广二维码显示该海报图片</span>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">App分享页面显示</label>
    <div class="col-sm-9 col-xs-12">
        <label class="radio-inline">
            <input type="radio" name="poster[app_share_show]" value="1" @if($poster['app_share_show']==1) checked @endif /> 开启
        </label>
        <label class="radio-inline">
            <input type="radio" name="poster[app_share_show]" value="0" @if(empty($poster['app_share_show']) || ($poster['app_share_show'] != 1))checked @endif /> 关闭
        </label>
        <span class='help-block'>开启状态：App分享页面显示该海报图片</span>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style='color:red'>*</span> 海报设计</label>
    <input type="hidden" name="data" value="" /> <!--设计好海报后,通过JS将设计的数据保存到这个表单中提交-->
    <div class="col-sm-9 col-xs-12">
        <table style='width:100%;'>
            <tr>
                <td style='width:320px;padding:10px;' valign='top'>
                    <div id='poster'>
                        @if(!empty($poster['background']))
                            <img src="{{$poster['background']}}" class='bg'/>
                        @endif
                        @if(!empty($data))
                            @foreach($data as $key=>$value)
                                <div class="drag" type="{{$value['type']}}" index="{{$key+1}}" style="z-index:{{$key+1}};left:{{$value['left']}};
                                        top:{{$value['top']}}; width:{{$value['width']}}; height:{{$value['height']}};
                                @if(isset($value['src']))src={{$value['src']}}; @endif
                                @if(isset($value['size']))size={{$value['size']}}; @endif
                                @if(isset($value['color']))color={{$value['color']}}; @endif">
                                    @if($value['type']=='qr')
                                        <img src={!! resource_get('plugins/poster/assets/img/qr.png') !!} />
                                    @elseif($value['type']=='qr_shop')
                                        <img src={!! resource_get('plugins/poster/assets/img/qr_shop.png') !!} />
                                    @elseif($value['type']=='qr_app_share')
                                        <img src={!! resource_get('plugins/poster/assets/img/qr_app_share.png') !!} />
                                    @elseif($value['type']=='head')
                                        <img src={!! resource_get('plugins/poster/assets/img/head.jpg') !!} />
                                    @elseif($value['type']=='img' || $value['type']=='thumb')
                                        <img src="{{empty($value['src'])? resource_get('plugins/poster/assets/img/img.jpg') : tomedia($value['src'])}}"/>
                                        <?php $tpl_img = tomedia($value['src']); ?> {{--todo 权宜--}}
                                    @elseif($value['type']=='nickname')

                                        <div class=text style="font-size:{{$value['size']}};color:{{$value['color']}}">昵称</div>
                                    @elseif($value['type']=='time')
                                        <div class=text style="font-size:{{$value['size']}};color:{{$value['color']}}">到期时间</div>
                                    @endif
                                    <div class="rRightDown"> </div><div class="rLeftDown"> </div><div class="rRightUp"></div><div class="rLeftUp"> </div><div class="rRight"> </div><div class="rLeft"> </div><div class="rUp"> </div><div class="rDown"></div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </td>
                <td valign='top' style='padding:10px;'>
                    <div class='panel panel-default'>
                        <div class='panel-body poster-edit-body'>
                            <div class="form-group" id="bgset">
                                <label class="col-xs-12 col-sm-3 col-md-3 control-label">背景图片</label>
                                <div class="col-sm-9 col-xs-12">
                                    {!!app\common\helpers\ImageHelper::tplFormFieldImage('poster[background]',$poster['background'])!!}
                                    <span class='help-block'>背景图片尺寸: 640 * 1008</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-3 control-label">海报元素</label>
                                <div class="col-sm-9 col-xs-12">
                                    <button class='btn btn-default btn-com' type='button' data-type='head' style="margin-bottom: 4px" >头像</button>
                                    <button class='btn btn-default btn-com' type='button' data-type='nickname' style="margin-bottom: 4px">昵称</button>
                                    <button class='btn btn-default btn-com' type='button' data-type='qr' style="margin-bottom: 4px">关注二维码</button>
                                    <button class='btn btn-default btn-com' type='button' data-type='img' style="margin-bottom: 4px">图片</button>
                                    <button class='btn btn-default btn-com' type='button' data-type='qr_shop' style="margin-bottom: 4px">推广二维码</button>
                                    <button class='btn btn-default btn-com' type='button' data-type='qr_app_share' style="margin-bottom: 4px">APP分享</button>
                                    <!--<button class='btn btn-default btn-com' type='button' data-type='time' style="margin-bottom: 4px">失效时间(Y-m-d H:i)</button>-->
                                </div>
                            </div>
                            <div id='nameset' style='display:none'>{{--昵称设置--}}
                                <div class="form-group">
                                    <label class="col-xs-12 col-sm-3 col-md-3 control-label">文字颜色</label>
                                    <div class="col-sm-9 col-xs-12 wid100">
                                        {!!tpl_form_field_color('color')!!}
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-xs-12 col-sm-3 col-md-3 control-label">文字大小</label>
                                    <div class="col-sm-4">
                                        <div class='input-group wid100'>
                                            <input type="text" id="namesize" class="form-control namesize" placeholder="例如: 14,16"  />
                                            <div class='input-group-addon'>px</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group" id="imgset" style="display:none">{{--图片设置--}}
                                <label class="col-xs-12 col-sm-3 col-md-3 control-label">图片设置</label>
                                <div class="col-sm-9 col-xs-12">
                                    {!!app\common\helpers\ImageHelper::tplFormFieldImage('img', $tpl_img)!!}
                                </div>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</div>

<!--预留
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">活动未开始提示</label>
        <div class="col-sm-9 col-xs-12">
            <textarea name="poster_supplement[not_start_reminder]" class="form-control">{{$poster['supplement']['not_start_reminder']}}</textarea>
            <span class="help-block">默认：活动于 [starttime] 开始，请耐心等待...</span>
            <span class="help-block">变量：[starttime]活动开始时间 [endtime]活动结束时间</span>
        </div>
    </div>

    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">活动结束提示</label>
        <div class="col-sm-9 col-xs-12">
            <textarea name="poster_supplement[finish_reminder]" class="form-control">{{$poster['supplement']['finish_reminder']}}</textarea>
            <span class="help-block">默认：活动已结束，谢谢您的关注!</span>
            <span class="help-block">变量：[starttime]活动开始时间 [endtime]活动结束时间</span>
        </div>
    </div>
-->

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">用户生成海报时的等待文字</label>
    <div class="col-sm-9 col-xs-12">
        <textarea name="poster_supplement[wait_reminder]" class="form-control">{{$poster['supplement']['wait_reminder']}}</textarea>
        <span class="help-block">默认：您的专属海报正在拼命生成中，请稍候片刻...</span>
    </div>
</div>