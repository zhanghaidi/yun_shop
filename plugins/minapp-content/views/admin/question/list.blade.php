@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-body">
        
            <div class="top" style="margin-bottom:20px">
                <ul class="add-shopnav" id="myTab">
                    <li><a href="{{yzWebUrl('plugin.minapp-content.admin.somato-type.index')}}">体质管理</a></li>
                    <li><a href="{{yzWebUrl('plugin.minapp-content.admin.label.index')}}">症状标签</a></li>
                    <li class="active"><a href="{{yzWebUrl('plugin.minapp-content.admin.question.index')}}">测评题库</a></li>
                    <li><a href="{{yzWebUrl('plugin.minapp-content.admin.answer.index')}}">用户测评</a></li>
                </ul>
            </div>

            <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                <a href="{{ yzWebUrl('plugin.minapp-content.admin.question.edit') }}" class="btn btn-info">添加题库</a>
            </div>
        </div>
    </div>

    <div class="panel panel-defualt">
        <div class="panel-body">
            <table class="table">
                <thead>
                    <tr>
                        <th width="50">排序</th>
                        <th width="150">题目类型</th>
                        <th width="600">题目</th>
                        <th>选项一<br>A、没有（根本不）</th>
                        <th>选项二<br>B、很少（有一点）</th>
                        <th>选项三<br>C、有时（有些）</th>
                        <th>选项四<br>D、经常（相当）</th>
                        <th>选项五<br>E、总是（非常）</th>
                        <th>创建时间</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $value)
                    <tr>
                        <td>{{$value['list_order']}}</td>
                        <td>{{$value['somato_type_name']}}</td>
                        <td>{{$value['title']}}</td>
                        <td>{{$value['option1_score']}}</td>
                        <td>{{$value['option2_score']}}</td>
                        <td>{{$value['option3_score']}}</td>
                        <td>{{$value['option4_score']}}</td>
                        <td>{{$value['option5_score']}}</td>
                        <td>{{$value['create_time']}}</td>
                        <td>
                            <a href="{{ yzWebUrl('plugin.minapp-content.admin.question.edit', ['id' => $value['id']]) }}" title="编辑"><i class="fa fa-edit"></i></a> &nbsp; 
                            <a href="{{ yzWebUrl('plugin.minapp-content.admin.question.delete', ['id' => $value['id']]) }}" onclick="return confirm('确定删除吗');return false;"  title="删除"><i class="fa fa-trash-o"></i></a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

