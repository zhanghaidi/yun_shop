@extends('layouts.base')

@section('content')
@section('title', trans('自定义表单数据'))
<div class="right-titpos">
    <ul class="add-snav">
        <li class="active"><a href="#">自定义表单数据</a></li>
        <a class='btn btn-primary' href="{{yzWebUrl('plugin.diyform.admin.diyform-data.export',['id' => $formId, 'form_data_id' => $formDataId])}}"
           style="margin-bottom:5px;">导出</a>
        <a href="{{yzUrl("plugin.diyform.admin.diyform.manage")}}">
            <span class="btn btn-default" style='margin-left:10px;'>返回列表</span>
        </a>
    </ul>
</div>


<div class='panel panel-default'>

    <div class='panel-body'>
        <table class="table table-responsive">
            <thead>
            <tr>
                <th>ID</th>
                <th>创建时间</th>
                <th>会员信息</th>
                @foreach($fields as $field)
                    <th>{{$field['tp_name']}}</th>
                @endforeach

                @if (!empty($fields)) <th>详情</th> @endif

            </tr>
            </thead>
            <tbody>
            @foreach($items as $item)
                <tr>
                    <td>{{$item['id']}}</td>
                    <td>{{$item['created_at']}}</td>
                    <td>
                        <img src='{{yz_tomedia($item['member']['avatar'])}}' style='width:30px;height:30px;padding:1px;border:1px solid #ccc' />
                        <br/>
                        {{$item['member_id'] ?: '未更新'}}
                        <br/>
                        {{$item['member']['nickname'] ?: '未更新'}}
                    </td>
                    @foreach($fields as $fname => $field)

                        <td>
                            @foreach($item['form_data'] as $key => $val)
                                @if($key == $fname)
                                    @if(is_array($val) && (strexists($val[0], 'image') || strexists($val[0], 'images') || strexists($val[0], 'newimage')))
                                        @foreach($val as $v)
                                            <img src='{{yz_tomedia($v)}}' style='width:30px;height:30px;padding:1px;border:1px solid #ccc' />
                                        @endforeach
                                    @elseif(is_array($val) && (!strexists($val[0], 'image') || !strexists($val[0], 'images') || !strexists($val[0], 'newimage')))
                                        @foreach($val as $v)
                                            <span>{{ $v }}</span>
                                        @endforeach
                                    @else
                                        {{$val}}
                                    @endif
                                @endif
                            @endforeach
                        </td>
                    @endforeach
                    @if (!empty($item['form_data']))
                            <td><a href="{{yzWebUrl('plugin.diyform.admin.diyform-data.get-form-data-detail',['form_data_id' => $item->id])}}">查看详情</a></td>
                        @endif
                </tr>
            @endforeach

            </tbody>
        </table>

        {!! $pager !!}
    </div>
</div>
<div style="width:100%;height:150px;"></div>

@endsection