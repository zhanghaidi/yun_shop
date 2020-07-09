
<tr id="tp_item{{$kw}}" class='tp_item'>
    <td valign='top'>
        {{$data_type_config[$data_type]}}
    </td>
    <td valign='top'>
        <input type="hidden" value="{{$data_type}}" class="form-control" name="tp_type[{{$kw}}]" />

        <input type="text" value="@if($flag=1 && $data_type==6) 身份证 @elseif($data_type == 88){{账号}}@elseif($data_type == 99){{密码}}@else {{$v1['tp_name']}} @endif" @if($data_type == 88 || $data_type == 99) readonly @endif class="form-control tp_name" name="tp_name[{{$kw}}]" maxlength="10" placeholder='字段名' />
    </td>
    <td>
        @if($data_type == 99 || $data_type == 88)
            <input type="radio" name="tp_must[{{$kw}}]" value="1" checked >
        @else
            <input type="checkbox" name="tp_must[{{$kw}}]" value="1" @if($v1['tp_must']==1) checked @endif >
        @endif
    </td>

    <td>
        @if($data_type==0 ||  $data_type==1)
        @if($data_type==0)
            设置默认值&nbsp;
            <select id="tp_is_default{{$kw}}" name="tp_is_default[{{$kw}}]" onchange="tp_change_default('{{$kw}}')" class="form-control" style='width:100px;display: inline-block'>
                @foreach($default_data_config as $key => $value)
                <option value="{{$key}}" @if($v1['tp_is_default']==$key) selected @endif>{{$value}}</option>
                @endforeach
            </select>

            <input type="text" id="tp_default{{$kw}}" placeholder="请输入自定义默认值" value="{{$v1['tp_default']}}"
                   class="form-control tp_default" name="tp_default[{{$kw}}]"
                   style="width:150px;display:{if $v1['tp_is_default']==1}inline{else}none{/if};">
            @endif

            提示语&nbsp;
            <input type="text" id="placeholder{{$kw}}" placeholder="请输入提示语" value="{{$v1['placeholder']}}"
                   class="form-control" name="placeholder[{{$kw}}]"
                   style="width:150px;display:inline;">

        @elseif($data_type==5)

            最大数量&nbsp;
            <select name="tp_max[{{$kw}}]" class="form-control" style='width:120px;display: inline-block'>
                <option value="1" @if($v1['tp_max']==1 || !$v1['tp_max']) selected @endif>1</option>
                <option value="2" @if($v1['tp_max']==2) selected @endif>2</option>
                <option value="3" @if($v1['tp_max']==3) selected @endif>3</option>
                <option value="4" @if($v1['tp_max']==4) selected @endif>4</option>
                <option value="5" @if($v1['tp_max']==5) selected @endif>5</option>
            </select>

        @elseif($data_type==7)

            设置默认&nbsp;
            <select id="default_time_type{{$kw}}" name="default_time_type[{{$kw}}]" onchange="tp_change_default_time(this,'default_time{{$kw}}')" class="form-control" style="width:167px;display:inline;">
                @foreach($default_date_config as $key => $value)
                <option value="{{$key}}" @if($v1['default_time_type']==$key) selected @endif>{{$value}}</option>
                @endforeach
            </select>
            <input type="text" id="default_time{{$kw}}" name="default_time[{{$kw}}]" placeholder="" value=" @if(!empty($v1['default_time'])) {{$v1['default_time']}} @endif" class="datetimepicker1 form-control" style="width:120px;display: {if $v1['default_time_type']==2}inline{else}none{/if};">
        @elseif($data_type==2 || $data_type==3|| $data_type==4)
            <textarea class="form-control" name="tp_text[{{$kw}}]" placeholder="一行一个选项" style="height: 120px;">@if(!empty($v1['tp_text'])) @foreach($v1['tp_text'] as $k2 => $v2){{$v2."\n"}}@endforeach @endif</textarea>
        @elseif($data_type==8)
            设置默认起始日期&nbsp;
            <select id="default_btime_type{{$kw}}" name="default_btime_type[{{$kw}}]" onchange="tp_change_default_time(this,'default_btime{{$kw}}')" class="form-control input-sm" style="width:120px;display:inline;">
                @foreach($default_date_config as $key =>$value)
                <option value="{$key}" @if($v1['default_btime_type']==$key) selected @endif>{{$value}}</option>
                @endforeach
            </select>
            <input type="text" id="default_btime{{$kw}}" name="default_btime[{{$kw}}]" placeholder="" value="@if(!empty($v1['default_etime'])){{$v1['default_btime']}}@endif" class="datetimepicker1 form-control  input-sm" style="width:120px;display:@if($v1['default_btime_type']==2) inline @else none @endif;margin-right: 25px;">
            <br/>

            设置默认结束日期&nbsp;
            <select id="default_etime_type{{$kw}}" name="default_etime_type[{{$kw}}]" onchange="tp_change_default_time(this,'default_etime{{$kw}}')" class="form-control  input-sm" style="width:120px;display:inline;">
                @foreach($default_date_config as $key => $value)
                <option value="{$key}" @if($v1['default_etime_type']==$key) selected @endif>{{$value}}</option>
                @endforeach
            </select>
            <input type="text" id="default_etime{{$kw}}" name="default_etime[{{$kw}}]" placeholder="" value="@if(!empty($v1['default_etime'])){{$v1['default_etime']}}@endif" class="datetimepicker1 form-control  input-sm" style="width:120px;display:{if $v1['default_etime_type']==2}inline{else}none{/if};">

        @elseif($data_type==9)
        级别&nbsp;
        <select name="tp_area[{{$kw}}]" class="form-control" style='width:120px;display: inline-block'>
            <option value="0" @if(empty($v1['tp_area'])) selected @endif>省市</option>
            <option value="1" @if($v1['tp_area']==1) selected @endif>省市区</option>
        </select>
        @elseif($data_type==10)
            字段名2&nbsp;
            <input type="text" id="tp_name2{{$kw}}" placeholder="字段名2" value="{{$v1['tp_name2']}}"
                   class="form-control" name="tp_name2[{{$kw}}]"
                   style="width:150px;display:inline;">
        @elseif($data_type == 88 || $data_type == 99)
        提示语&nbsp;
            <input type="text" id="placeholder{{$kw}}" placeholder="请输入提示语" value="{{$v1['placeholder']}}"
                   class="form-control" name="placeholder[{{$kw}}]"
                   style="width:150px;display:inline;">
        @endif
    </td>


    <td>
        <a onclick="$(this).closest('.tp_item').remove()" class="btn btn-danger btn-sm" href="javascript:void(0);"><i class="fa fa-remove"></i></a>
    </td>
</tr>
